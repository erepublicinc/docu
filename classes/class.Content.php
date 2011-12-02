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
    protected $mExtraTable;              // set by erived type
    protected $mContentType;             // set by derived type in constructor    ex. 'ARTICLE' or 'DOCUMENTATION'   used for content_type field
    protected $mSqlStack;                // to combine sql with sql from children
   
    // this describes the fields that can be set by the "user"
    // this gets passed into : FormatUpdateString()
    protected static $mContentFieldDescriptions = array(
            'contents_title'         => array('type'=>'varchar', 'required'=>true),
            'contents_display_title' => array('type'=>'varchar'),
            'contents_summary'       => array('type'=>'varchar'),
//            'contents_status'        => array('type'=>'varchar'),
            'contents_url_name'      => array('type'=>'varchar'),
            'contents_main_author_fk'=> array('type'=>'int'),   
            'contents_extra_table'   => array('type'=>'varchar'), 
            'contents_live_version'  => array('type'=>'int'),
            'contents_preview_version'  => array('type'=>'int')
            );
    
    
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
      
    
    protected function Save()
    {
        if($this->mPk)
          return $this->SaveExisting(); 
        else        
          return $this->SaveNew();
    }
    
    /**
     * Makes a version  live
     * @param int $version
     */
    public static function setLiveVersion($pk, $version)
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
    public static function setPreviewVersion($pk, $version)
    {
         if(!$pk)
            return logerror('called setLiveVersion, pk == 0');
        Query::SetAdminMode();
        $q = new Query("UPDATE contents SET contents_preview_version = $version WHERE contents_pk = $pk");
        return $q->version;
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
    public static function GetContentByType($contentType , $site = null, $orderby = null, $limit = 10, $skip = 0, $status = 'READY')    
    {   
       global $CONFIG;
        
        $contents =  array();       
        $topX     = "LIMIT $skip, $limit ";
        $types    = '';  
        $status   = '';     

/*        
        if($status != 'ALL')
        {
            $sql .= " AND contents_status = '$status' ";
        }
*/
                
/*        
        if($orderby)
        {
            $order = " ORDER BY "
        }
  */      
        if($site && $site != 'ALL')
        {
            // need an extra join to figure out which page we are looking for
             // this is the mssql version
            /* $sql="SELECT  * FROM contents   
               JOIN targets
                      JOIN pages   ON pages_id = targets_pages_id 
               ON targets_contents_fk = contents_pk 
               WHERE contents_type = '$contentType'  and   pages_site_code = '$site'    $status $topX ";
               */
            
            // this is the mysql version
            $sql="SELECT contents_pk, contents_title, contents_display_title, contents_summary, contents_status , contents_main_author_fk  
                FROM  (contents  JOIN targets ON targets_contents_fk = contents_pk )
                           JOIN pages   ON pages_id = targets_pages_id           
               WHERE contents_type = '$contentType'  and   pages_site_code = '$site'    $status
               GROUP BY contents_pk, contents_title, contents_display_title, contents_summary, contents_status , contents_main_author_fk  
               $topX ";
        }
        else 
        {
            // use the current page
           $sql="SELECT  * FROM contents 
               WHERE contents_type = '$contentType'   $status $topX ";
        }
//dump($sql);   
        return new Query($sql);          
    } 
    
    
    /**
     * this functions is called by pages who want to get their data
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
            // the mssql version
            /*
             $sql="SELECT  * FROM contents 
               JOIN targets
                  JOIN pages
                  ON pages_id = targets_pages_id 
               ON targets_contents_fk = contents_pk 
               WHERE page_url = '$url'   $types $status $topX ";
            */
            // the mysql version
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
                  FROM contents JOIN users on users_pk = contents_main_author_fk 
                  WHERE contents_pk = $urlName"; 
        }
        else 
        {
            $sql = "SELECT contents_pk, contents_live_version, contents_url_name, contents_title, contents_display_title, contents_summary, contents_create_date, contents_type, contents_status, contents_extra_table, users_last_name, users_first_name
                  FROM contents JOIN users on users_pk = contents_main_author_fk 
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
     * 
     * Creates a new content item
     * @param stdClass object with the field values
     * the following fields are mandatory:  title, contents_type, author_pk 
     * @param array of strings $extra_sql     if supplied, this sql will run in the same call as a transaction
     */
    protected function SaveNew()
    {
        if( !isset($this->mFields->contents_title) )
        {
            print_o($this->mFields);
            logerror('create content required parameter(s) missing', __FILE__.' '.__LINE__);
            return false;
        }

        if( !isset($this->mFields->contents_main_author_fk) )
        {
            $this->mFields->contents_main_author_fk = $_SESSION['user_pk'];
        }
       
        // the default status is  'DRAFT'; 
 //       $this->mFields->contents_status  = $this->mFields->contents_status == 'READY' |  $this->mFields->contents_status == 'REVIEW' ? $this->mFields->contents_status : 'DRAFT';
                
        // escape text fields
        $etitle         = Query::Escape($this->mFields->contents_title);
        $edisplay_title = Query::Escape($this->mFields->contents_display_title); 
        $esummary       = Query::Escape($this->mFields->contents_summary); 
        $eurl_name      = Query::Escape($this->mFields->contents_url_name);
        $apk            = $this->mFields->contents_main_author_fk;
        $userpk         = $_SESSION['user_pk'];
        $extra          = $this->mExtraTable;
        $live           = $this->mFields->make_live == 1 ? 1: 0;
        $preview        = $this->mFields->make_preview == 1 ? 1: 0;
        
        
        $sql = 'SELECT @pk:= LAST_INSERT_ID()';
        array_unshift($this->mSqlStack, $sql);
        
        $sql="INSERT INTO contents (contents_title, contents_display_title, contents_live_version,contents_preview_version, contents_url_name, contents_summary,
                                   contents_create_date, contents_update_date,contents_type, contents_status, contents_main_author_fk, 
                                   contents_update_users_fk, contents_extra_table, contents_latest_version)
              VALUES('$etitle','$edisplay_title',$live, $preview,'$eurl_name','$esummary', NOW(),NOW(),'$this->mContentType','$status',$apk, $userpk,'$extra',1)";
     
        array_unshift($this->mSqlStack, $sql);

        // to return the pk
        $sql = 'SELECT @pk as pk';
        array_push($this->mSqlStack, $sql);
        
        $result =  Query::sTransaction($this->mSqlStack);
        if($result != false)
            return $result->pk;
        return false;    
    }

   
    /** Update a content item
     *    we will only update the fields that are present and leave the rest alone
     *    @param bool $newVersion to see if we have to increase the live version 
     *    @return   false (in case of failure,  otherwise the pk of the content item
     */
/*    
    protected function SaveExisting($newVersion = TRUE)
    { 
 
        // the default status is  'IN_PROGRESS'; 
        $this->mFields->contents_status  = isset($this->mFields->contents_status) ? $this->mFields->contents_status: 'IN_PROGRESS';
   
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions); 
        if($this->mFields->contents_status == 'READY')
        {
            if($newVersion)  
               $newvalues .= ',contents_live_version = @v ';
            else  
               $newvalues .= ',contents_live_version = @v -1';  
        }
        $newvalues .= ', contents_update_date = NOW() ';
        $newvalues .= ', contents_update_users_fk = '.$_SESSION['user_pk'];
        
        // this needs to be first,   get the version number by looking for the highest and adding 1
        $extraTable = $this->mExtraTable;
        array_unshift($this->mSqlStack, " SELECT @v := max(contents_version)+1 FROM $this->mExtraTable GROUP BY contents_fk HAVING contents_fk= $this->mPk");
        
        $this->mSqlStack[] = "UPDATE contents SET $newvalues where contents_pk =$this->mPk";   

        $result = Query::sTransaction($this->mSqlStack);
        if($result != false)
            return $this->mFields->contents_pk;
        return false;  
    }
*/      
    protected function SaveExisting($newVersion = TRUE)
    { 

        // the default status is  'DRAFT'; 
 //       $this->mFields->contents_status  = $this->mFields->contents_status == 'READY' |  $this->mFields->contents_status == 'REVIEW' ? $this->mFields->contents_status : 'DRAFT';
   
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions); 
        
        $thisVersion = 'contents_latest_version';
        if($newVersion)
        {  
            $newVersion = $this->mFields->contents_latest_version + 1;
            $newvalues .= ", contents_latest_version = $newVersion "  ;
            $thisVersion =  $newVersion; 
        }  
               
        if($this->mFields->make_live == 1)
           $newvalues .= ",contents_live_version =  $thisVersion "  ;
        if($this->mFields->make_preview == 1)
           $newvalues .= ",contents_preview_version =  $thisVersion "  ; 
           
        
        $newvalues .= ', contents_update_date = NOW() ';
        $newvalues .= ', contents_update_users_fk = '.$_SESSION['user_pk'];
        
        
        $this->mSqlStack[] = "UPDATE contents SET $newvalues where contents_pk =$this->mPk";   
//dump($this->mSqlStack);
        $result = Query::sTransaction($this->mSqlStack);
        if($result != false)
            return $this->mFields->contents_pk;
        return false;  
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
        {
            $version = "(SELECT MAX(contents_version)from $extraTable WHERE contents_fk = $pk)";
        }
        elseif($version == LIVE_VERSION)
            $version = "contents_live_version"; 
         
         $liveField = $CONFIG->mode == 'PREVIEW'? 'pages_is_preview' : 'pages_is_live';  

         if($includeAuthor)
             $sql = "SELECT * FROM   contents c 
                     JOIN $extraTable t 
                     ON contents_pk = t.contents_fk and t.contents_version = $version     
                     JOIN users u
                     ON u.users_pk = c.contents_main_author_fk              
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
     * creates a string used for updating a record like: "body='hello', version=12"
     * typical use:    
     * $str = FormatUpdateString(GetFieldTypes(false), $params);
     * $sql = "UPDATE events SET $str WHERE pk = $pk";
     * 
     * @param array of field types
     * @param stdClass of parameters
     */
    protected  function FormatUpdateString($Fieldsarray)
    {
        $newvalues = ''; 
        $keys = get_object_vars($this->mFields);
              
        foreach($keys as $field =>$value)
        {
            if(isset($Fieldsarray[$field])) 
            {  
                if($Fieldsarray[$field]['type'] == 'varchar' )
                {
                    $evalue = Query::Escape($value);
                    $newvalues .= "$field = '$evalue',";
                }
                else //if($Fieldsarray[$field]['type'] == 'int' )
                {
                    $newvalues .= "$field = $value,";
                }
            }
        }
        // remove last comma    
        $newvalues = substr($newvalues, 0, strlen($newvalues)-1); 
// dump($newvalues);       
        return  $newvalues;   
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
    
} // end of class


