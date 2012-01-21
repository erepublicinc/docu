<?
/* A core model class 
 *  Content are all items that can be targeted
 * Revisioning:
 * "contents"   doe not have a revision, instead a live_rev fields specifies which revision is live
 * "extra data" does have a  revision
 */
class Content extends Model
{
    public    $mTargets;
    protected $mFields;
    protected $mId;
    
       
    protected $mContentType;             // set by derived class in constructor    ex. 'ARTICLE' or 'DOCUMENTATION'   used for content_type field
    protected $mSqlStack;                // to combine sql with sql from children
    
    // this describes the fields that can be set by the "user"
    // this gets passed into : FormatUpdateString()
    protected static $mContentFieldDescriptions = array(
            'contents_clk_id'          => array('type'=>'int', 'insert_only'=>true),
            'contents_id'              => array('type'=>'int', 'insert_only'=>true),
            'contents_title'           => array('type'=>'varchar', 'label'=>'Title', 'required'=>true),
            'contents_display_title'   => array('type'=>'varchar', 'label'=>'Display title'),
            'contents_create_date'     => array('type'=>'datetime', 'insert_only'=>true,'do_not_validate'=>true),  // NOW()
          //  'contents_mod_date'     => array('type'=>'datetime', 'do_not_validate'=>true),  // NOW()   updated by system
            'contents_pub_date'        => array('type'=>'datetime', 'label'=>'Publication Date' ), 
            'contents_type'            => array('type'=>'varchar', 'insert_only'=>true, 'required'=>true),
            'contents_summary'         => array('type'=>'varchar', 'label'=>'Summary'),
            'contents_url_name'        => array('type'=>'varchar', 'label'=>'URL Name'),
            'contents_mod_users_id'    => array('type'=>'int', 'required'=>true),   
            'contents_authors_id'      => array('type'=>'int', 'label'=>'Author', 'form_element' =>'select'),   
            'contents_extra_table'     => array('type'=>'varchar', 'insert_only'=>true, 'required'=>true),
            'contents_live_rev'        => array('type'=>'int', 'do_not_validate'=>true),   // could be  @newrev
            'contents_preview_rev'     => array('type'=>'int', 'do_not_validate'=>true),   // could be  @newrev
            'contents_latest_rev'      => array('type'=>'int', 'do_not_validate'=>true)    // could be  @newrev
            );
    
            
    // these are the fields that a derived class must have        
    protected static $mStandardFieldDescriptions = array(            
            'contents_fid'          => array('type'=>'int', 'do_not_validate'=>true, 'insert_only'=>true) , // rev is set with variable @id 
            'contents_rev'          => array('type'=>'int', 'do_not_validate'=>true, 'insert_only'=>true) , // rev is set with variable @newrev
            'contents_rev_users_id' => array('type'=>'int', 'insert_only'=>true) , 
            'contents_rev_date'     => array('type'=>'datetime', 'do_not_validate'=>true, 'insert_only'=>true) , // set with NOW()
            'contents_rev_comment'  => array('type'=>'varchar', 'insert_only'=>true) ,  
            'contents_rev_status'   => array('type'=>'varchar')  
    );        

    // these are the "extra" fields that a derived class has.  this array must be instantiated by the derived class
    protected $mExtraFieldDescriptions;
    protected $mExtraTable;                     // and this is the table for the derived class
        
    
    /** 
     * initialized with a row from the contents table, or with a id
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
            $this->mId     = ($params->contents_id >0)? $params->contents_id :0;
        }      
        elseif( is_integer($params))
        {
            $this->mId =$params;
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

    public function GetExtraTableName()
    {
        return $this->mExtraTable;
    }
    
    /**
     * For templates that auto-generate  from this  field info array 
     * @param bool $includeAuthors
     */
    public function GetFieldDescriptions($includeAuthors = false)
    {
        $fields = array_merge(self::$mContentFieldDescriptions, self::$mStandardFieldDescriptions, $this->mExtraFieldDescriptions ); 
        if($includeAuthors)
        {
            $authors = Author::getAuthors();
            $authors->SetAlias(array('id' => 'authors_id','title' => 'authors_display_name' ));  // slect lists template uses id and title           
            $fields['contents_authors_id']['options'] = $authors;   // set the options for the authors select list         
        }   
        return $fields;     
    }
    
    
    public  function GetData($id, $rev = LIVE_REV, $includeAuthor = true)
    {
        return self::GetAllData($id, $this->mExtraTable, $rev , $includeAuthor );
    }
    
     /** 
     * This static class can be called by a yaas function 
     * @param int $id of the object
     * @param string $extraTable the table to be joined
     * @param int $rev  the requested rev 0 = the live rev
     * @return a Query object with 2 result sets:  1. content item,   2. its targets
     */
    public static function GetAllData($id, $extraTable, $rev = LIVE_REV, $includeAuthor = true)
    {  
        global $CONFIG;  
        if($rev == LATEST_REV)
            $rev = "(SELECT contents_latest_rev from contents WHERE contents_id = $id)";
        elseif($rev == LIVE_REV)
            $rev = $CONFIG->mode == 'PREVIEW'? "contents_preview_rev" : "contents_live_rev"; 
        else 
           $rev = intval($rev); 
     

         if($includeAuthor)
             $sql = "SELECT * FROM   contents 
                     JOIN $extraTable  ON contents_id = contents_fid and contents_rev = $rev     
                     JOIN authors      ON authors_id = contents_authors_id              
                     WHERE contents_id = $id ";
 		  else 
              $sql = "SELECT * FROM   contents  
                     JOIN $extraTable  ON contents_id = contents_fid and contents_rev = $rev                 
                     WHERE contents_id = $id ";
//dump($sql);		  
		$result =  new Query($sql);            
//dump($result->ToArray());				       
        return $result;
    }   
    

    
    /**
     * retrieves the correct extra data as an object
     * @param datatable
     * @param [optional]  the rev
     * @return a Query object
     */
/*  NOT USED !   
    protected function GetExtraData( $rev = LIVE_rev)
    {
        $id = $this->mId;
        $therev = $rev > 0 ? $rev : $this->mFields->contents_live_rev; 
       
        $sql = "SELECT * from $this->mExtraTable t 
        	    WHERE t.contents_id = $id     
                AND contents_rev = $therev ";
        	  			
        return  new Query($sql); 
    } 
*/  
    
    /** 
     * GetContentByType , primarily used by cms
     * @param content type / model_name content type you want
     * @param Site [optional] default = null is all sites
     * @param limit [optional] default =10 , number of items to return
     * @param startAt [optional] default =0 , where to start (for paging)
     * @param string status 
     * @return array of contentItems
     */  
    public static function GetContentByType($contentType , $site = 'ALL', $orderby = null, $limit = 10, $skip = 0, $status = 'ALL')    
    {   
       global $CONFIG;
        
        $contents  =  array();       
        $topX      = "LIMIT $skip, $limit ";
        $types     = '';     
        $statusStr = '';
        
        if($status != 'ALL')
        { dump();
            $liverev = ($CONFIG->mode == 'PREVIEW') ? 'contents_preview_rev' : 'contents_live_rev';  
            $statusStr = " AND $liverev > 0";
        }
               
/*        
        if($orderby)
        {
            $order = " ORDER BY "
        }
  */ 
             
        if($site != 'ALL')
        {
            // need an extra join to figure out which page we are looking for
            $sql="SELECT contents_id, contents_title, contents_display_title, contents_summary,  contents_authors_id , 
                  contents_preview_rev, contents_live_rev, contents_latest_rev
                FROM  (contents  JOIN targets ON targets_contents_id = contents_id )
                           JOIN pages   ON pages_id = targets_pages_id           
               WHERE contents_type = '$contentType'  and   pages_site_code = '$site'    $statusStr
               GROUP BY contents_id, contents_title, contents_display_title, contents_summary,  contents_authors_id  
               $topX ";
        }
        else 
        {
            // use the current page
           $sql="SELECT * FROM contents WHERE contents_type = '$contentType'   $statusStr $topX ";
        }
//dump($sql);   
        return new Query($sql);          
    } 
    
    
    /**
     * Gets all data in case you only know the urlname   ( like a detail page)
     * @param String $urlName ( also allows for a id   or   12324.html)
     */
    public static function GetContentByUrl($urlName)
    {
        // get rid of everything before the last '/'
        $pieces = explode('/',$urlName);
        $urlName = array_pop($pieces);
        
        // strip off the .html
        if(stripos($urlName, '.html') > 0)
        $urlName = substr($urlName,0,stripos($urlName, '.html'));

        if(is_int($urlName))   
        {
           $sql = "SELECT contents_id, contents_live_rev, contents_url_name, contents_title, contents_display_title, contents_summary, contents_create_date, contents_type,  contents_extra_table, users_last_name, users_first_name
                  FROM contents JOIN users on users_id = contents_authors_id 
                  WHERE contents_id = $urlName"; 
        }
        else 
        {
            $sql = "SELECT contents_id, contents_live_rev, contents_url_name, contents_title, contents_display_title, contents_summary, contents_create_date, contents_type,  contents_extra_table, users_last_name, users_first_name
                  FROM contents JOIN users on users_id = contents_authors_id 
                  WHERE contents_url_name = '$urlName'";
        }
//dump($sql);        
        $r = new Query($sql);        
        
        $sql = "SELECT * FROM $r->contents_extra_table WHERE contents_id = $r->contents_id AND contents_rev = $r->contents_live_rev";
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
            $statusStr = " AND contents_rev_status = '$status' ";
        }
*/        
        if(!empty($url))
        {
            // need an extra join to figure out which page we are looking for
 
             $sql="SELECT  * FROM 
               ( contents JOIN targets ON targets_contents_id = contents_id )
                          JOIN pages   ON pages_id = targets_pages_id             
               WHERE pages_url = '$url' 
               AND targets_live_date < NOW() 
               AND (targets_archive_date > NOW() OR targets_archive_date <'2000-01-01') 
               $types $statusStr  $orderStr $topX ";
        }
        else 
        {
            // use the current page if the page_id is not specified
            $pages_id = $pages_id > 0 ? $pages_id :  $CONFIG->current_pages_id;
            
            $sql="SELECT  * FROM contents 
                  JOIN targets  ON targets_contents_id = contents_id 
                  WHERE targets_pages_id = ". $pages_id ." 
                  AND targets_live_date < NOW() AND (targets_archive_date > NOW() OR targets_archive_date <'2000-01-01') 
                  $types $statusStr   $orderStr $topX ";
        }
      
//dump($sql);      
        
        $result = new Query($sql);
        
  return $result;
  
      /* not sure if we need to do this 
        foreach($result as $content)
        { 
            // remember: the contents_type is the same as the model name
            $contents[] = new $content->contents_type($content);            
        }
        return $contents;
      */  
    }

    
    /**
     * used by listing pages
     * returns the number of pages of content items for a page (wss)
     * @param int   page id      
     * @param array  of article types
     * @param int  page Size   [default = default pagesize for this site]
     */
    public static function GetNumPages($page_id, $types_array, $pageSize = 0)
    {
        global $CONFIG;
        $page_size = $page_size > 0 ? $page_size : $CONFIG->page_size;
        
        $page_id = intval($page_id);
        
        $liverev = ($CONFIG->mode == 'PREVIEW') ? 'contents_preview_rev' : 'contents_live_rev';  
        
        $type_list = implode("','",$types_array);
        $type_list = " '$type_list' ";
        
        
        $sql = "SELECT COUNT(*) as num  
                FROM contents JOIN targets ON contents_id = targets_contents_id 
                WHERE contents_type in ($type_list) 
                AND targets_pages_id = $page_id  
                AND $liverev > 0
                AND targets_live_date < NOW() 
                AND (targets_archive_date > NOW() OR targets_archive_date <'2000-01-01' )" ;
        
        $ret = new Query($sql);
        return  ceil($ret->num / $page_size);
    }

    
    /**
     * Gets targets for this content item
     * @param int $contents_id
     */
    public static function GetTargets($contents_id, $onlyActiveTargets = true)
    {  
       global $CONFIG;  
                    
       $liveField = ($CONFIG->mode == 'PREVIEW') ? 'pages_is_preview' : 'pages_is_live';   
       $sql = "SELECT distinct targets_pages_id, targets_contents_id, targets_pin_position, targets_live_date, targets_archive_date, targets_dead_date, pages_title, pages_site_code
       			FROM targets  JOIN pages ON pages_id = targets_pages_id AND $liveField = 1	
		        WHERE targets_contents_id = $contents_id  "; 
       //dump($sql);
       return new Query($sql); 
    }   

    
    /**
     * retrieves the history of this content item
     * @param unknown_type $id   
     * @param unknown_type $extraTable
     */
    public static function GetRevisionHistory($id, $extraTable)
    {
        
        $sql = "SELECT contents_rev  as rev, contents_rev_date as rev_date, contents_rev_comment as rev_comment, users_first_name, users_last_name, users_email
                FROM {$extraTable} JOIN users ON contents_rev_users_id = users_id
                WHERE contents_fid = $id ORDER BY contents_rev DESC";
//   dump($sql);     
        return new Query($sql);
    }
        
    
    /**
     *      
     * @param bool $newrev [default=true], so you can choose to update the current rev or create a new rev
     */  
    public function Save($newrev= true)
    {
        $fid = intval( $this->mFields->contents_id);

        $newContent =   $fid == 0 ? true: false;
          
        if($newrev || $newContent )     
            $newrev = true;

        //add sql for the extra_table and then call  saveNew() or saveExisting() which adds the sql for the 'contents' record      
            
        // merge the Extra with the Standard field descriptions to get all fields of the ExtraTable    
        $fieldDescriptions = array_merge(self::$mStandardFieldDescriptions, $this->mExtraFieldDescriptions);    
          
        // set the Standard fields of derived class   for which we are resposible        
        $this->mFields->contents_rev_status   = $this->mFields->contents_rev_status == 'READY' || $this->mFields->contents_rev_status == 'REVIEW' ? $this->mFields->contents_rev_status : 'DRAFT';                   
        $this->mFields->contents_rev_date     = "NOW()";
        $this->mFields->contents_rev_users_id = $_SESSION['user_id']; 
     
        if($newContent)  
        { 
             $this->mFields->contents_rev = 1;
             $this->mFields->contents_fid = '@id';
             $newvalues = $this->FormatUpdateString($fieldDescriptions, SQL_INSERT);
             array_push($this->mSqlStack, "INSERT INTO $this->mExtraTable $newvalues ");
          //   dump($this->mSqlStack);
             return self::SaveNew();
        }
        else 
        {
            if($newrev) 
            {
                $this->mFields->contents_fid = $fid;
                $this->mFields->contents_rev = '@newrev';
                //dump($this->mFields); 
                $newvalues = $this->FormatUpdateString($fieldDescriptions, SQL_INSERT);
               
                array_push($this->mSqlStack, "SELECT  @newrev:= MAX(contents_rev) +1  from $this->mExtraTable where contents_fid = $fid");
                array_push($this->mSqlStack, "INSERT INTO $this->mExtraTable $newvalues ");
            }
            else
            {
                $rev = intval($this->mFields->contents_rev);
                dump($this->mFields); 
                $newvalues = $this->FormatUpdateString($fieldDescriptions, SQL_UPDATE);
                array_push($this->mSqlStack, "UPDATE $this->mExtraTable  SET $newvalues WHERE contents_fid = $fid AND contents_rev = $rev");
            }
         //   dump($this->mSqlStack); 
            return self::SaveExisting();           
        }   
        
        
    }
    
    
    /**
     * Creates a new content item
     * @param stdClass object with the field values
     * the following fields are mandatory:  title, contents_type, authors_id 
     * @param array of strings $extra_sql     if supplied, this sql will run in the same call as a transaction
     * @return the id of the new item, or false
     */
    protected function SaveNew()
    {
 
        $this->mFields->contents_latest_rev  = 1;
        $this->mFields->contents_live_rev    = $this->mFields->make_live == 1 ? 1: 0;
        $this->mFields->contents_preview_rev = $this->mFields->make_preview == 1 ? 1: 0;
        $this->mFields->contents_extra_table     = $this->mExtraTable;
        $this->mFields->contents_type            = $this->mContentType;
        $this->mFields->contents_create_date     = 'NOW()';        
        $this->mFields->contents_authors_id      = $this->mFields->contents_authors_id > 0 ?  $this->mFields->contents_authors_id : SYSTEM_AUTHOR_ID ;
        
        
        // do we need these 3 fields, they would be similar to the fields on the latest rev
        $this->mFields->contents_mod_date     = 'NOW()';
        $this->mFields->contents_mod_users_id = $_SESSION['user_id'];
//        $this->mFields->contents_rev_status          = empty($this->mFields->contents_rev_status) ? 'DRAFT' : $this->mFields->contents_rev_status ;
        
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions, SQL_INSERT); 
        
        array_unshift($this->mSqlStack, "SELECT @id:= LAST_INSERT_ID()");
        array_unshift($this->mSqlStack, "INSERT INTO contents $newvalues ");
        array_push($this->mSqlStack,    "SELECT @id as id");
        /* now the sqlStack has 4 items in the following order:
            0. create the content record
            1. 'SELECT @id:= LAST_INSERT_ID()'
            2. create the extra table record , which is using @id
            3. 'SELECT @id as id' , so we can return this
        */
        
         //  dump($this->mSqlStack);
        $result =  Query::sTransaction($this->mSqlStack);
        if($result != false)
            return $result->id;
        return false;    
    }

  
    /** 
     * Update a content item
     * we will only update the fields that are present and leave the rest alone
     * @param bool $newrev to see if we have to increase the live rev 
     * @return   false (in case of failure,  otherwise the id of the content item
     */   
    protected function SaveExisting($newrev = TRUE)
    { 
            
        $this->mFields->contents_latest_rev  =  $newrev ? '@newrev': 'contents_latest_rev';
        $this->mFields->contents_live_rev    =  $this->mFields->make_live == 1 ? '@newrev': ' contents_live_rev';
        $this->mFields->contents_preview_rev =  $this->mFields->make_preview == 1 ? '@newrev': ' contents_preview_rev';

        // do we need these 3 fields, they would be similar to the fields on the latest rev
        $this->mFields->contents_mod_date     = 'NOW()';
        $this->mFields->contents_mod_users_id = $_SESSION['user_id'];
//        $this->mFields->contents_rev_status          = empty($this->mFields->contents_rev_status) ? 'DRAFT' : $this->mFields->contents_rev_status ;
        
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions, SQL_UPDATE); 
        
        $this->mSqlStack[] = "UPDATE contents SET $newvalues where contents_id =$this->mId";   
   //     dump($this->mSqlStack);

        $result = Query::sTransaction($this->mSqlStack);
        if($result != false)
            return $this->mFields->contents_id;
        return false;  
    }
     
       
    /**
     * Makes a rev  live
     * @param int $rev
     */
    public static function SetLiveRevision($id, $rev)
    {
        if(!$id)
            return logerror('called setLiveRevision, id == 0');
        Query::SetAdminMode();
        $q = new Query("UPDATE contents SET contents_live_rev = $rev WHERE contents_id = $id");
        return $q->rev;
    }
    
    
 	/**
     * Makes a rev  Preview
     * @param int $rev
     */
    public static function SetPreviewRevision($id, $rev)
    {
         if(!$id)
            return logerror('called setLiveRevision, id == 0');
        Query::SetAdminMode();
        $q = new Query("UPDATE contents SET contents_preview_rev = $rev WHERE contents_id = $id");
        return $q->rev;
    }

    
    /**
     * link content
     * @param int    id of main item 
     * @param string type of this item
     * @param int or int array  array of items to be linked
     * @param string  type of these. all elements to be linked in this call have to be of the same type
     * @param string link type
     */
    public static function LinkContent($id1, $type1, $id_array, $type2, $link_type ='')
    {
        $type_one = 'type1';
        $type_two = 'type2';
        $id_one   = 'id1';
        $id_two   = 'id2';
        
         // order it alphabetically by type 
        if( $type1 > $type2)
        {
            $type_one = 'type2';
            $type_two = 'type1';
            $id_one   = 'id2';
            $id_two   = 'id1';         
        }
        
    
        if(! is_array($id_array))
            $id_array = array($id_array);
            
        foreach ($id_array as $id2)
        {
            $sql[] = "INSERT INTO contents__contents (contents_id1, contents_type1 , contents_id2, contents_type2 , link_type)
                      VALUES(". $$id_one. ", '". $$type_one."', ".$$id_two. ", '".$$type_two."', '$link_type')";
        }
        dump($sql);
        return new Query($sql);
    }
    
    
    /**
     * returns the content records linked 
     * @param int $id
     * @param array or string of types [default 'ALL' returns all linked content]
     */
    public static function GetLinkedContent($id, $types_array = "ALL")
    {    
       $liverev = ($CONFIG->mode == 'PREVIEW') ? 'contents_preview_rev' : 'contents_live_rev';   
        
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
       
       $sql = "SELECT * FROM contents__contents  JOIN contents on contents_id =  contents_id1
        		WHERE contents_id2 = $id  AND $liverev > 0  $type_str
               UNION 
               SELECT * FROM contents__contents  JOIN contents on contents_id =  contents_id2
                WHERE contents_id1 = $id  AND $liverev > 0  $type_str ";
    }

    
    /**
     * unlink  content items
     * @param int $id1
     * @param array or int $id_array
     */
    public static function UnlinkContent($id1,$id_array)
    {
        if(is_array($id_array))
            $id_array = implode("','",$id_array);
        $sql[] = "DELETE FROM contents_contents WHERE contents_id1 = $id1 AND contents_id2 in ($id_array)";
        $sql[] = "DELETE FROM contents_contents WHERE contents_id2 = $id1 AND contents_id1 in ($id_array)";
        return new Query($sql);
    }
    

} // end of class


