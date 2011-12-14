<?
/* A core model class 
 *  Content are all items that can be targeted
 * Versioning:
 * "contents"   doe not have a version, instead a live_version fields specifies which version is live
 * "extra data" does have a  version
 */
class Content
{
    public    $mTargets;
    protected $mFields;
    protected $mPk;
    
       
    protected $mContentType;             // set by derived class in constructor    ex. 'ARTICLE' or 'DOCUMENTATION'   used for content_type field
    protected $mSqlStack;                // to combine sql with sql from children
    
    // this describes the fields that can be set by the "user"
    // this gets passed into : FormatUpdateString()
    protected static $mContentFieldDescriptions = array(
          //'contents_pk'   we never need this, will be set by autoincrement and never updated
            'contents_title'           => array('type'=>'varchar', 'required'=>true),
            'contents_display_title'   => array('type'=>'varchar'),
            'contents_create_date'     => array('type'=>'date', 'insert_only'=>true,'do_not_validate'=>true),  // NOW()
            'contents_update_date'     => array('type'=>'date', 'do_not_validate'=>true),  // NOW()
            'contents_type'            => array('type'=>'varchar', 'insert_only'=>true, 'required'=>true),
            'contents_summary'         => array('type'=>'varchar'),
            'contents_url_name'        => array('type'=>'varchar'),
          //'contents_status'          => array('type'=>'varchar'),    
            'contents_update_users_fk' => array('type'=>'int', 'required'=>true),   
            'contents_author_fk'       => array('type'=>'int'),   
            'contents_extra_table'     => array('type'=>'varchar', 'insert_only'=>true, 'required'=>true),
            'contents_live_version'    => array('type'=>'int', 'do_not_validate'=>true),   // could be  @newversion
            'contents_preview_version' => array('type'=>'int', 'do_not_validate'=>true),   // could be  @newversion
            'contents_latest_version'  => array('type'=>'int', 'do_not_validate'=>true)    // could be  @newversion
            );
    
            
    // these are the fields that a derived class must have        
    protected static $mStandardFieldDescriptions = array(            
            'contents_fk'               => array('type'=>'int', 'do_not_validate'=>true, 'insert_only'=>true) , // version is set with variable @pk 
            'contents_version'          => array('type'=>'int', 'do_not_validate'=>true, 'insert_only'=>true) , // version is set with variable @newversion
            'contents_version_users_fk' => array('type'=>'int') , 
            'contents_version_date'     => array('type'=>'date', 'do_not_validate'=>true) , // set with NOW()
            'contents_version_comment'  => array('type'=>'varchar') ,  
            'contents_version_status'   => array('type'=>'varchar')  
    );        

    // these are the "extra" fields that a derived class has.  this array must be instantiated by the derived class
    protected $mExtraFieldDescriptions;
    protected $mExtraTable;                     // and this is the table for the derived class
    
    
    
    /** 
     * initialized with a row from the contents table, or with a pk
     * @param array, object or int 
     */
    protected function __construct($params)
    { //print_r($params); die;
        $this->mSqlStack = array();
        
        if(is_array($params))
            $params = (object) $params;
        
        if(is_object($params))
        {
            $this->mFields =  $params; //echo('create contetn'.$row->title);
            $this->mPk     = ($params->contents_pk >0)? $params->contents_pk :0;
        }      
        elseif( is_integer($params))
        {
            $this->mPk =$params;
        }
        
    }
    
    
    public function __get($field)
    {
        return $this->mFields->$field;
    }

    
    public function getAllFields()
    {
        return $this->mFields;
    }

    
        /** 
     * This static class can be called by a yaas function 
     * @param int $pk of the object
     * @param string $extraTable the table to be joined
     * @param int $version  the requested version 0 = the live version
     * @return a Query object with 2 result sets:  1. content item,   2. its targets
     */
    public static function GetAllData($pk, $extraTable, $version = LIVE_VERSION, $includeAuthor = true)
    {  
        global $CONFIG;  
        if($version == LATEST_VERSION)
            $version = "(SELECT MAX(contents_version)from $extraTable WHERE contents_fk = $pk)";
        elseif($version == LIVE_VERSION)
            $version = $CONFIG->mode == 'PREVIEW'? "contents_preview_version" : "contents_live_version"; 
         
     

         if($includeAuthor)
             $sql = "SELECT * FROM   contents c 
                     JOIN $extraTable t 
                     ON contents_pk = t.contents_fk and t.contents_version = $version     
                     JOIN authors au
                     ON au.authors_pk = c.contents_author_fk              
                     WHERE c.contents_pk = $pk ";
 		  else 
              $sql = "SELECT * FROM   contents c 
                     JOIN $extraTable t 
                     ON contents_pk = t.contents_fk and t.contents_version = $version                 
                     WHERE c.contents_pk = $pk ";
		  
		$result =  new Query($sql);            
//dump($result->ToArray());				       
        return $result;
    }   
    

    /**
     * retrieves the correct extra data as an object
     * @param datatable
     * @param [optional]  the version
     * @return a Query object
     */
    protected function GetExtraData($extraTable, $version=0)
    {
        $pk = $this->pk;
        $theversion = $version > 0 ? $version : $this->mFields->contents_live_version; 
       
        $sql = "SELECT * from $extraTable t 
        	    WHERE t.contents_fk = $pk     
                AND contents_version = $theversion ";
        	  			
        return  new Query($sql); 
    } 
    
    
    /** 
     * GetContentByType , primarily used by cms
     * @param content type  content types you want
     * @param Site [optional] default = null is all sites
     * @param limit [optional] default =10 , number of items to return
     * @param startAt [optional] default =0 , where to start (for paging)
     * @param string status 
     * @return array of contentItems
     */  
    public static function GetContentByType($contentType , $site = null, $orderby = null, $limit = 10, $skip = 0, $status = 'ALL')    
    {   
       global $CONFIG;
        
        $contents =  array();       
        $topX     = "LIMIT $skip, $limit ";
        $types    = '';  
        $status   = '';     

        
        if($status != 'ALL')
        {
            $status = " AND contents_status = '$status' ";
        }

                
/*        
        if($orderby)
        {
            $order = " ORDER BY "
        }
  */      
        if($site && $site != 'ALL')
        {
            // need an extra join to figure out which page we are looking for
            $sql="SELECT contents_pk, contents_title, contents_display_title, contents_summary, contents_status , contents_author_fk  
                FROM  (contents  JOIN targets ON targets_contents_fk = contents_pk )
                           JOIN pages   ON pages_id = targets_pages_id           
               WHERE contents_type = '$contentType'  and   pages_site_code = '$site'    $status
               GROUP BY contents_pk, contents_title, contents_display_title, contents_summary, contents_status , contents_author_fk  
               $topX ";
        }
        else 
        {
            // use the current page
           $sql="SELECT * FROM contents WHERE contents_type = '$contentType'   $status $topX ";
        }
//dump($sql);   
        return new Query($sql);          
    } 
    
    
    /**
     * Gets all data in case you only know the urlname   ( like a detail page)
     * @param String $urlName ( also allows for a pk   or   12324.html)
     */
    public static function GetContentByUrl($urlName)
    {
        // strip off the .html
        if(stripos($urlName, '.html') > 0)
        $urlName = substr($urlName,0,stripos($urlName, '.html'));

        if(is_int($urlName))   
        {
           $sql = "SELECT contents_pk, contents_live_version, contents_url_name, contents_title, contents_display_title, contents_summary, contents_create_date, contents_type, contents_status, contents_extra_table, users_last_name, users_first_name
                  FROM contents JOIN users on users_pk = contents_author_fk 
                  WHERE contents_pk = $urlName"; 
        }
        else 
        {
            $sql = "SELECT contents_pk, contents_live_version, contents_url_name, contents_title, contents_display_title, contents_summary, contents_create_date, contents_type, contents_status, contents_extra_table, users_last_name, users_first_name
                  FROM contents JOIN users on users_pk = contents_author_fk 
                  WHERE contents_url_name = '$urlName'";
        }
//dump($sql);        
        $r = new Query($sql);        
        
        $sql = "SELECT * FROM $r->contents_extra_table WHERE contents_fk = $r->contents_pk AND contents_version = $r->contents_live_version";
        $r2 = new Query($sql);
     
        // combine the 2 results into 1 content object  
        $c = new Content( array_merge($r->ToArray(), $r2->ToArray() ));
        // dump($c);  
        return $c;
    }

        
    /**
     * this functions is called by pages who want to get their data
     * it supports automatic paging through the 'pg' query string parameter  
     * @param String content type [optional] default: ALL   returneparated list of content types you want
     * @param String Url [optional] default = 0 is current url like /blogs/joesblog
     * @param number limit [optional] default =10 , number of items to return
     * @param number startAt [optional] default =0 , where to start (for paging)
     * @param String status
     * @return array of contentItems
     */
    public static function GetPageContents($contentType = 'ALL', $pages_id=0, $url = null, $limit = 0, $skip = 0, $status = 'READY')
    {
        global $CONFIG;
        
        
        if($limit == 0) 
        {   // default paging
            $limit  = $CONFIG->page_size;
            $paging = intval($_GET[pg]);
            $skip   = $paging * $limit;  
        }
        
        
        $contents  =  array();       
        $topX      = "LIMIT $skip, $limit ";
        $types     = '';  
        $statusStr = '';     
        $orderStr  = ' ORDER BY targets_pin_position, targets_live_date desc' ;
        if($contentType != 'ALL')
        {  
            $types = " AND contents_type = '$contentType' ";
        }
/*        
        if($status != 'ALL')
        {
            $statusStr = " AND contents_status = '$status' ";
        }
*/        
        if(!empty($url))
        {
            // need an extra join to figure out which page we are looking for
 
             $sql="SELECT  * FROM 
               ( contents JOIN targets ON targets_contents_fk = contents_pk )
                          JOIN pages   ON pages_id = targets_pages_id             
               WHERE pages_url = '$url' AND targets_live_date < NOW() AND (targets_archive_date > NOW() OR targets_archive_date <'2000-01-01') 
                 $types $statusStr  $orderStr $topX ";
        }
        else 
        {
            // use the current page if the page_id is not specified
            $pages_id = $pages_id > 0 ? $pages_id :  $CONFIG->current_page_id;
            
            $sql="SELECT  * FROM contents 
                  JOIN targets  ON targets_contents_fk = contents_pk 
                  WHERE targets_pages_id = ". $pages_id ." 
                  AND targets_live_date < NOW() AND (targets_archive_date > NOW() OR targets_archive_date <'2000-01-01') 
                  $types $statusStr   $orderStr $topX ";
        }
      
//dump($sql);      
        
        $result = new Query($sql);
  //return $result;      
        foreach($result as $content)
        { 
            switch($content->contents_type)
            { 
                case 'ARTICLE':      $contents[] = new Article($content);     break;
                case 'MODULE':        $contents[]= new Module($content);       break;
                case 'LIBRARY_ITEM': $contents[] = new LibraryItem($content); break;
                default:             $contents[] = new Content($content);     break;
            }
        }
        return $contents;
    }

    /**
     * returns the number of pages
     * @param int   page id
     * @param array  of article types
     * @param int  page Size   [default = default pagesize for this site]
     */
    public static function GetNumPages($page_id, $types_array, $pageSize = 0)
    {
        global $CONFIG;
        $page_size = $page_size > 0 ? $page_size : $CONFIG->page_size;
        
        $page_pk = intval($page_pk);
        
        $liveversion = ($CONFIG->mode == 'PREVIEW') ? 'contents_preview_version' : 'contents_live_version';  
        
        $type_list = implode("','",$types_array);
        $type_list = " '$type_list' ";
        
        
        $sql = "SELECT COUNT(*) as num  FROM contents JOIN targets ON contents_pk = targets_contents_fk 
                WHERE contents_type in ($type_list) 
                AND targets_pages_id = $page_id  
                AND $liveversion > 0
                AND targets_live_date < NOW() 
                AND (targets_archive_date > NOW() OR targets_archive_date <'2000-01-01' )" ;
        
        $ret = new Query($sql);
        return  ceil($ret->num / $page_size);
    }
        
    /**
     * Gets targets for this item
     * @param int $pk
     */
    public static function GetTargets($pk, $onlyActiveTargets = true)
    {  
       global $CONFIG;  
                    
       $liveField = ($CONFIG->mode == 'PREVIEW') ? 'pages_is_preview' : 'pages_is_live';   
       $sql = "SELECT targets_pages_id, targets_contents_fk, targets_pin_position, targets_live_date, targets_archive_date, targets_dead_date, pages_title, pages_site_code
       			FROM targets  JOIN pages ON pages_id = targets_pages_id AND $liveField = 1	
		        WHERE targets_contents_fk = $pk  "; 
  
       return new Query($sql); 
    }   
    
    
    public static function GetVersionHistory($pk,$extraTable)
    {
        $sql = "SELECT contents_version  as version, contents_version_date as version_date, contents_version_comment as version_comment, users_first_name, users_last_name, users_email
                FROM {$extraTable} JOIN users ON contents_version_users_fk = users_pk
                WHERE contents_fk = $pk ORDER BY contents_version DESC";
//   dump($sql);     
        return new Query($sql);
    }
 
        
    
    /**
     *      
     * @param bool $newVersion [default=true], so you can choose to update the current version or create a new version
     */  
    protected function Save($newVersion= true)
    {
        $pk = intval( $this->mFields->contents_pk);
        $newContent =   $pk == 0 ? true: false;
          
        if($newVersion || $newContent )     
            $newVersion = true;

        //add sql for the extra_table and then call  saveNew() or saveExisting() which adds the sql for the 'contents' record      
            
        // merge the Extra with the Standard field descriptions to get all fields of the ExtraTable    
        $fieldDescriptions = array_merge(self::$mStandardFieldDescriptions, $this->mExtraFieldDescriptions);    
          
        // set the Standard fields of derived class   for which we are resposible        
        $this->mFields->contents_version_status   = $this->mFields->contents_version_status == 'READY' || $this->mFields->contents_version_status == 'REVIEW' ? $this->mFields->contents_version_status : 'DRAFT';                   
        $this->mFields->contents_version_date     = "NOW()";
        $this->mFields->contents_version_users_fk = $_SESSION['user_pk']; 

        $fk = intval($this->mFields->contents_pk);
        $this->mFields->contents_fk = $fk;
       
        if($newContent)  
        { 
             $this->mFields->contents_version = 1;
             $this->mFields->contents_fk = '@pk';
             $newvalues = $this->FormatUpdateString($fieldDescriptions, $newVersion);
             array_push($this->mSqlStack, "INSERT INTO $this->mExtraTable $newvalues ");
          //   dump($this->mSqlStack);
             return self::SaveNew();
        }
        else 
        {
            if($newVersion) 
            {
                $this->mFields->contents_version = '@newversion';
                //dump($this->mFields); 
                $newvalues = $this->FormatUpdateString($fieldDescriptions, $newVersion);
               
                array_push($this->mSqlStack, "SELECT  @newversion:= MAX(contents_version) +1  from $this->mExtraTable where contents_fk = $fk");
                array_push($this->mSqlStack, "INSERT INTO $this->mExtraTable $newvalues ");
            }
            else
            {
                $version = intval($this->mFields->contents_version);
                dump($this->mFields); 
                $newvalues = $this->FormatUpdateString($fieldDescriptions, $newVersion);
                array_push($this->mSqlStack, "UPDATE $this->mExtraTable  SET $newvalues WHERE contents_fk = $fk AND contents_version = $version");
            }
         //   dump($this->mSqlStack); 
            return self::SaveExisting();           
        }   
        
        
    }
    
    
    /**
     * Creates a new content item
     * @param stdClass object with the field values
     * the following fields are mandatory:  title, contents_type, author_pk 
     * @param array of strings $extra_sql     if supplied, this sql will run in the same call as a transaction
     * @return the pk of the new item, or false
     */
    protected function SaveNew()
    {
 
        $this->mFields->contents_latest_version  = 1;
        $this->mFields->contents_live_version    = $this->mFields->make_live == 1 ? 1: 0;
        $this->mFields->contents_preview_version = $this->mFields->make_preview == 1 ? 1: 0;
        $this->mFields->contents_extra_table     = $this->mExtraTable;
        $this->mFields->contents_type            = $this->mContentType;
        $this->mFields->contents_create_date     = 'NOW()';        
        
        // do we need these 3 fields, they would be similar to the fields on the latest version
        $this->mFields->contents_update_date     = 'NOW()';
        $this->mFields->contents_update_users_fk = $_SESSION['user_pk'];
//        $this->mFields->contents_status          = empty($this->mFields->contents_status) ? 'DRAFT' : $this->mFields->contents_status ;
        
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions, true); 
        
        array_unshift($this->mSqlStack, "SELECT @pk:= LAST_INSERT_ID()");
        array_unshift($this->mSqlStack, "INSERT INTO contents $newvalues ");
        array_push($this->mSqlStack,    "SELECT @pk as pk");
        /* now the sqlStack has 4 items in the following order:
            0. create the content record
            1. 'SELECT @pk:= LAST_INSERT_ID()'
            2. create the extra table record , which is using @pk
            3. 'SELECT @pk as pk' , so we can return this
        */
        
        //   dump($this->mSqlStack);
        $result =  Query::sTransaction($this->mSqlStack);
        if($result != false)
            return $result->pk;
        return false;    
    }

  
    /** 
     * Update a content item
     * we will only update the fields that are present and leave the rest alone
     * @param bool $newVersion to see if we have to increase the live version 
     * @return   false (in case of failure,  otherwise the pk of the content item
     */   
    protected function SaveExisting($newVersion = TRUE)
    { 
            
        $this->mFields->contents_latest_version  =  $newVersion ? '@newversion': 'contents_latest_version';
        $this->mFields->contents_live_version    =  $this->mFields->make_live == 1 ? '@newversion': intval(contents_live_version);
        $this->mFields->contents_preview_version =  $this->mFields->make_preview == 1 ? '@newversion': intval(contents_preview_version);

        // do we need these 3 fields, they would be similar to the fields on the latest version
        $this->mFields->contents_update_date     = 'NOW()';
        $this->mFields->contents_update_users_fk = $_SESSION['user_pk'];
//        $this->mFields->contents_status          = empty($this->mFields->contents_status) ? 'DRAFT' : $this->mFields->contents_status ;
        
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions, false); 
        
        $this->mSqlStack[] = "UPDATE contents SET $newvalues where contents_pk =$this->mPk";   
        //dump($this->mSqlStack);

        $result = Query::sTransaction($this->mSqlStack);
        if($result != false)
            return $this->mFields->contents_pk;
        return false;  
    }
     
       
    /**
     * Makes a version  live
     * @param int $version
     */
    public static function SetLiveVersion($pk, $version)
    {
        if(!$pk)
            return logerror('called setLiveVersion, pk == 0');
        Query::SetAdminMode();
        $q = new Query("UPDATE contents SET contents_live_version = $version WHERE contents_pk = $pk");
        return $q->version;
    }
    
    
 	/**
     * Makes a version  Preview
     * @param int $version
     */
    public static function SetPreviewVersion($pk, $version)
    {
         if(!$pk)
            return logerror('called setLiveVersion, pk == 0');
        Query::SetAdminMode();
        $q = new Query("UPDATE contents SET contents_preview_version = $version WHERE contents_pk = $pk");
        return $q->version;
    }

    
    /**
     * link content
     * @param int    pk of main item 
     * @param string type of this item
     * @param int or int array  array of items to be linked
     * @param string  type of these. all elements to be linked in this call have to be of the same type
     * @param string link type
     */
    public static function LinkContent($pk1, $type1, $pk_array, $type2, $link_type ='')
    {
        $type_one = 'type1';
        $type_two = 'type2';
        $pk_one   = 'pk1';
        $pk_two   = 'pk2';
        
         // order it alphabetically by type 
        if( $type1 > $type2)
        {
            $type_one = 'type2';
            $type_two = 'type1';
            $pk_one   = 'pk2';
            $pk_two   = 'pk1';         
        }
        
    
        if(! is_array($pk_array))
            $pk_array = array($pk_array);
            
        foreach ($pk_array as $pk2)
        {
            $sql[] = "INSERT INTO contents__contents (contents_fk1, contents_type1 , contents_fk2, contents_type2 , link_type)
                      VALUES(". $$pk_one. ", '". $$type_one."', ".$$pk_two. ", '".$$type_two."', '$link_type')";
        }
        dump($sql);
        return new Query($sql);
    }
    
    
    /**
     * returns the content records linked 
     * @param int $pk
     * @param array or string of types [default 'ALL' returns all linked content]
     */
    public static function GetLinkedContent($pk, $types_array = "ALL")
    {    
       $liveversion = ($CONFIG->mode == 'PREVIEW') ? 'contents_preview_version' : 'contents_live_version';   
        
       $type_str = '';
       if($types_array != 'ALL')
       {
           if(is_array($types_array))
           {
               $type_str = implode("','", $types_array);
               $type_str = " AND contents_type IN ('$type_str') ";
           }
           else
           {
               $type_str = " AND contents_type = '$types_array' ";
           }  
       }
       
       $sql = "SELECT * FROM contents__contents  JOIN contents on contents_pk =  contents_fk1
        		WHERE contents_fk2 = $pk  AND $liveversion > 0  $type_str
               UNION 
               SELECT * FROM contents__contents  JOIN contents on contents_pk =  contents_fk2
                WHERE contents_fk1 = $pk  AND $liveversion > 0  $type_str ";
    }

    
    /**
     * unlink  content items
     * @param int $pk1
     * @param array or int $pk_array
     */
    public static function UnlinkContent($pk1,$pk_array)
    {
        if(is_array($pk_array))
            $pk_array = implode("','",$pk_array);
        $sql[] = "DELETE FROM contents_contents WHERE contents_fk1 = $pk1 AND contents_fk2 in ($pk_array)";
        $sql[] = "DELETE FROM contents_contents WHERE contents_fk2 = $pk1 AND contents_fk1 in ($pk_array)";
        return new Query($sql);
    }
    
    

    
    /**
     * creates a string used for updating a record like:  "body='hello', version=12"
     * creates a string used for inserting a record like: "(body,version) VALUES('hello', 12) "
     * typical use:    
     * $str = FormatUpdateString(GetFieldTypes(false), $params);
     * $sql = "UPDATE events SET $str WHERE pk = $pk";
     * 
     * @param array of field types
     * @param bool  [default =  false] means that we request a string suitable for 
     *                                 the UPDATE command like: "body='hello', version=12"
     */
    protected  function FormatUpdateString($Fieldsarray, $insert = false)
    {
        $insertStr = '(';         //  (body,version)
        $newvalues = '';          //  VALUES('hello', 12)  or body='hello', version=12"
                    
        foreach($Fieldsarray as $field => $Description)
        {
            if($insert && $Description['required']  && ! isset($this->mFields->$field) )            
                logerror("required field $field is missing" );

            if($insert == false && $Description['insert_only'] )  // these fields cannot be updated
                continue;  
                
            $value = $this->mFields->$field;
                
            if(! $Description['do_not_validate'] )
            {
                if($Description['type'] == 'varchar' )
                    $value = "'". Query::Escape($value) ."'";   
                
                if($Description['type'] == 'int' )
                    $value =  intval($value);
            }                   
            if($insert)
            {
                $insertStr .= "$field,";
                $newvalues .= "$value,";
            }
            else 
            {
                $newvalues .= "$field = $value,";
            }
        }
        
        if($insert)
        {               
            $insertStr = substr($insertStr, 0, strlen($insertStr)-1); // remove last comma  
            $newvalues =  $insertStr . ') VALUES(' . $newvalues;             // combine the strings
          
            $newvalues  = substr($newvalues, 0, strlen($newvalues)-1); // remove last comma  
            $newvalues .= ')';
        }
        else 
        {   
            $newvalues = substr($newvalues, 0, strlen($newvalues)-1); // remove last comma  
        } 
 //dump($newvalues);       
        return  $newvalues;   
    }
   
     

} // end of class


