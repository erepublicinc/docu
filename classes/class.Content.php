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
            'title'         => array('type'=>'varchar', 'required'=>true),
            'display_title' => array('type'=>'varchar'),
            'summary'       => array('type'=>'varchar'),
            'status'        => array('type'=>'varchar'),
            'url_name'      => array('type'=>'varchar'),
            'main_author_fk'=> array('type'=>'int'),
            'live_version'  => array('type'=>'int')
            );
    
    
    // initialized with a row from the contents table, or with a pk
    protected function __construct($params)
    {
        $this->mSqlStack = array();
        if(is_object($params))
        {
            $this->mFields =  $params; //echo('create contetn'.$row->title);
            $this->mPk     = isset($params->pk)? $params->pk :0;
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
        $q = new Query("UPDATE contents SET live_version = $version WHERE pk = $this->mPk");
        return $q->version;
    }
    
    /**
     * @param content type [optional] default: ALL   comma separated list of content types you want
     * @param Url [optional] default = 0 is current url like /blogs/joesblog
     * @param limit [optional] default =10 , number of items to return
     * @param startAt [optional] default =0 , where to start (for paging)
     * @return array of contentItems
     */
    public static function GetPageContents($contentType = 'ALL', $url = null, $limit = 10, $startAt = 0)
    {
        global $CONFIG;
        
        $contents =  array();       
        $topX     = "LIMIT $limit ";
        $types    = '';       
        
        if($contentType != 'ALL')
        {  
            $types = " AND content_type = '$contentType' ";
        }
        
        if($url)
        {
            // need an extra join to figure out which page we are looking for
             $sql="SELECT  * FROM contents 
               JOIN targets
                  JOIN pages
                  ON pages.pk = targets.page_fk 
               ON targets.content_fk = contents.pk 
               WHERE pages.url = '$url'   $types  $topX ";
        }
        else 
        {
            // use the current page
            $sql="SELECT  * FROM contents 
               JOIN targets 
               ON targets.contents_fk = contents.pk 
               WHERE targets.pages_fk = ". $CONFIG->current_page_pk ."  $types  $topX ";
        }
        
        $result = new Query($sql,'array');
        foreach($result as $content)
        {
            switch($content->content_type)
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
     * the following fields are mandatory:  title, content_type, author_pk 
     * @param array of strings $extra_sql     if supplied, this sql will run in the same call as a transaction
     */
    protected function SaveNew()
    {
        if( !isset($this->mFields->title)  || !isset($this->mFields->author_fk)    )
        {
            print_o($this->mFields);
            logerror('create content required parameter(s) missing', __FILE__.' '.__LINE__);
            return false;
        }
        
        $status = empty($this->mFields->status) ? 'NEW': $this->mFields->status;
        
        // escape text fields
        $etitle         = Query::Escape($this->mFields->title);
        $edisplay_title = Query::Escape($this->mFields->display_title); 
        $esummary       = Query::Escape($this->mFields->summary); 
        $eurl_name      = Query::Escape($this->mFields->url_name);
        $apk            = $this->mFields->author_fk;
        
        $sql="INSERT INTO contents (title, display_title, live_version, url_name, summary,create_date, content_type, status, main_author_fk)
              VALUES('$etitle','$edisplay_title',1,'$eurl_name','$esummary', NOW(),'$this->mContentType','$status',$apk)";
     
        array_unshift($this->mSqlStack, $sql);  
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
   
    
    /** Update a contant item
     *    we will only update the fields that are present and leave the rest alone
     *    
     */
    protected function SaveExisting()
    { 
        // the default status is  'IN_PROGRESS'; 
        $this->mFields->status  = isset($this->mFields->status) ? $this->mFields->status: 'IN_PROGRESS';
         
        $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions); 
        if($this->mFields->status == 'LIVE')  
            $newvalues .= ',live_version = @v';
        
        // this needs to be first,   get the version number by looking for the highest and adding 1
        array_unshift($this->mSqlStack, " SELECT @v := max(version)+1 FROM $this->mExtraTable GROUP BY contents_fk HAVING contents_fk= $this->mPk");
        
        $this->mSqlStack[] = "UPDATE contents SET $newvalues where pk =$this->mPk";   

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
        $theversion = $version > 0 ? $version : $this->mFields->live_version; 
        
        $sql = "SELECT * from $extraTable t 
        	    WHERE t.contents_fk = $pk     
                AND version = $theversion ";
        	  			
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
         $theversion = $version > 0 ? $version : "c.live_version"; 
         $sql=array();
         $sql[] = "SELECT * FROM contents c 
                 JOIN $extraTable t 
                 ON c.pk = t.contents_fk and t.version = $theversion                   
                 WHERE pk = $pk ";
            			
		 $sql[] = "SELECT pin_position, live_date, dead_date, pages.title 
		          FROM targets
		          JOIN pages ON pages.pk = targets.pages_fk	
		          WHERE contents_fk = $pk ";   
        return new Query($sql); 
    }   
    
} // end of class


