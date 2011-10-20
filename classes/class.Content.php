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
            'contents_status'        => array('type'=>'varchar'),
            'contents_url_name'      => array('type'=>'varchar'),
            'contents_main_authors_fk'=> array('type'=>'int'),
            'contents_live_version'  => array('type'=>'int')
            );
    
    
    // initialized with a row from the contents table, or with a pk
    protected function __construct($params)
    { //print_r($params); die;
        $this->mSqlStack = array();
        if(is_object($params))
        {
            $this->mFields =  $params; //echo('create contetn'.$row->title);
            $this->mPk     = ($params->contents_pk >0)? $params->contents_pk :0;
        }      
        elseif(is_array($params) )
        {
            $this->mFields = new stdClass();
            foreach($params as $key => $value )
            {
                $this->mFields->$key = $value;
            }
            $this->mPk = $params['contents_pk'] >0 ? $params['contents_pk'] : 0;
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
    
    public function getTargets()
    {
        
    }
    
    
    protected function Save()
    {
        if($this->mPk)
          return $this->SaveExisting(); 
        else        
          return $this->SaveNew();
    }
    
    

    public function setLiveVersion( $version)
    {
        if(! $this->mPk)
            return logerror('called setLiveVersion, pk == 0');
        Query::SetAdminMode();
        $q = new Query("UPDATE contents SET contents_live_version = $version WHERE contents_pk = $this->mPk");
        return $q->version;
    }

    
    /** GetContentByType , primarily used by cms
     * @param content type  content types you want
     * @param Site [optional] default = null is all sites
     * @param limit [optional] default =10 , number of items to return
     * @param startAt [optional] default =0 , where to start (for paging)
     * @param string status 
     * @return array of contentItems
     */
    
    public static function GetContentByType($contentType , $site = null, $orderby = null, $limit = 10, $skip = 0, $status = 'LIVE')    
    {   
       global $CONFIG;
        
        $contents =  array();       
        $topX     = "LIMIT $skip, $limit ";
        $types    = '';  
        $status   = '';     
                
        if($status != 'ALL')
        {
            $sql .= " AND contents_status = '$status' ";
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
             // this is the mssql version
            /* $sql="SELECT  * FROM contents   
               JOIN targets
                      JOIN pages   ON pages_pk = targets_pages_fk 
               ON targets_contents_fk = contents_pk 
               WHERE contents_type = '$contentType'  and   pages_site_code = '$site'    $status $topX ";
               */
            
            // this is the mysql version
            $sql="SELECT  * FROM  
                (contents  JOIN targets ON targets_contents_fk = contents_pk )
                           JOIN pages   ON pages_pk = targets_pages_fk           
               WHERE contents_type = '$contentType'  and   pages_site_code = '$site'    $status $topX ";
        }
        else 
        {
            // use the current page
           $sql="SELECT  * FROM contents 
               WHERE contents_type = '$contentType'   $status $topX ";
        }
   
       
        return new Query($sql);          
    } 
    
    
    
    /**
     * @param String content type [optional] default: ALL   returneparated list of content types you want
     * @param String Url [optional] default = 0 is current url like /blogs/joesblog
     * @param number limit [optional] default =10 , number of items to return
     * @param number startAt [optional] default =0 , where to start (for paging)
     * @param String status
     * @return array of contentItems
     */
    public static function GetPageContents($contentType = 'ALL', $url = null, $limit = 10, $skip = 0, $status = 'LIVE')
    {
        global $CONFIG;
        
        $contents =  array();       
        $topX     = "LIMIT $skip, $limit ";
        $types    = '';  
        $status   = '';     
        
        if($contentType != 'ALL')
        {  
            $types = " AND contents_type = '$contentType' ";
        }
        
        if($status != 'ALL')
        {
            $sql .= " AND contents_status = '$status' ";
        }
        
        if($url)
        {
            // need an extra join to figure out which page we are looking for
            // the mssql version
            /*
             $sql="SELECT  * FROM contents 
               JOIN targets
                  JOIN pages
                  ON pages_pk = targets.page_fk 
               ON targets.contents_fk = contents_pk 
               WHERE page_url = '$url'   $types $status $topX ";
            */
            // the mysql version
             $sql="SELECT  * FROM 
               ( contents JOIN targets ON targets.contents_fk = contents_pk )
                          JOIN pages   ON pages_pk = targets.page_fk             
               WHERE page_url = '$url'   $types $status $topX ";
        }
        else 
        {
            // use the current page
            $sql="SELECT  * FROM contents 
               JOIN targets 
               ON targets.contents_fk = contents_pk 
               WHERE targets.pages_fk = ". $CONFIG->current_page_pk ."  $types $status $topX ";
        }
      
       
        
        $result = new Query($sql);
        foreach($result as $content)
        {
            switch($content->contents_type)
            {
                case 'ARTICLE':      $contents[] = new Article($content);     break;
                case 'EVENT':        $contents[] = new Event($content);       break;
                case 'LIBRARY_ITEM': $contents[] = new LibraryItem($content); break;
            }
        }
        return $contents;
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

        if( !isset($this->mFields->contents_main_authors_fk) )
        {
            $this->mFields->contents_main_authors_fk = $_SESSION['user_pk'];
        }
        
        $status = empty($this->mFields->status) ? 'NEW': $this->mFields->status;
        
        // escape text fields
        $etitle         = Query::Escape($this->mFields->contents_title);
        $edisplay_title = Query::Escape($this->mFields->contents_display_title); 
        $esummary       = Query::Escape($this->mFields->contents_summary); 
        $eurl_name      = Query::Escape($this->mFields->contents_url_name);
        $apk            = $this->mFields->contents_main_authors_fk;
        
        
        $sql = 'SELECT @pk:= LAST_INSERT_ID()';
        array_unshift($this->mSqlStack, $sql);
        
        $sql="INSERT INTO contents (contents_title, contents_display_title, contents_live_version, contents_url_name, contents_summary,contents_create_date, contents_type, contents_status, contents_main_authors_fk)
              VALUES('$etitle','$edisplay_title',1,'$eurl_name','$esummary', NOW(),'$this->mContentType','$status',$apk)";
     
        array_unshift($this->mSqlStack, $sql);

//        $sql = 'SELECT @pk as pk';
//        array_push($this->mSqlStack, $sql);
        
        $result = Query::sTransaction($this->mSqlStack);
   
        return true;
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
        
        return  $newvalues;   
    }
   
    
    /** Update a content item
     *    we will only update the fields that are present and leave the rest alone
     *    @param bool $newVersion to see if we have to increase the live version 
     */
    protected function SaveExisting($newVersion = TRUE)
    { 
 // echo("<pre>"); print_r($this->mFields);   die; 
        // the default status is  'IN_PROGRESS'; 
        $this->mFields->contents_status  = isset($this->mFields->contents_status) ? $this->mFields->contents_status: 'IN_PROGRESS';
   
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions); 
        if($this->mFields->contents_status == 'LIVE')
        {
            if($newVersion)  
               $newvalues .= ',contents_live_version = @v ';
            else  
               $newvalues .= ',contents_live_version = @v -1';  
        }
        // this needs to be first,   get the version number by looking for the highest and adding 1
        $extraTable = $this->mExtraTable;
        array_unshift($this->mSqlStack, " SELECT @v := max(${extraTable}_version)+1 FROM $this->mExtraTable GROUP BY ${extraTable}_contents_fk HAVING ${extraTable}_contents_fk= $this->mPk");
        
        $this->mSqlStack[] = "UPDATE contents SET $newvalues where contents_pk =$this->mPk";   

        return Query::sTransaction($this->mSqlStack);
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
                AND ${extraTable}_version = $theversion ";
        	  			
        return  new Query($sql); 
    }
    
    /** 
     * This static class can be called by a yaas function 
     * @param int $pk of the object
     * @param string $extraTable the table to be joined
     * @param int $version  the requested version 0 = the live version
     * @return a Query object with 2 result sets:  1. content item,   2. its targets
     */
    public static function GetAllData($pk, $extraTable, $version=0)
    {    
         $theversion = $version > 0 ? $version : "contents_live_version"; 
         $sql=array();
  /*   mssql    
         $sql[] = "SELECT * FROM  contents c 
                 JOIN $extraTable t 
                 ON contents_pk = t.contents_fk and t.${extraTable}_version = $theversion     
                 JOIN users u
                 ON u.users_pk = c.contents_main_authors_fk              
                 WHERE c.contents_pk = $pk ";
  */          			
         
         $sql[] = "SELECT * FROM   contents c 
                 JOIN $extraTable t 
                 ON contents_pk = t.${extraTable}_contents_fk and t.${extraTable}_version = $theversion     
                 JOIN users u
                 ON u.users_pk = c.contents_main_authors_fk              
                 WHERE c.contents_pk = $pk ";
         
		 $sql[] = "SELECT * 
		          FROM targets  JOIN pages ON pages_pk = targets_pages_fk	
		          WHERE targets_contents_fk = $pk "; 
		          
		            
		          
        return new Query($sql); 
    }   
    
} // end of class


