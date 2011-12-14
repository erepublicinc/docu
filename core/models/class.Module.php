<?
/*
 * Modules are a hybrid object. They are a content item, so that they can use the versioning of the content items
 * However it does not use targeting but has its own link table that links it to a specific version of a page
 * 
 */
class Module extends Content
{
 	/*
     *  creates a new article from an array or object
     */
    public function __construct($fieldsObjectOrArray)
    {
        
        parent::__construct($fieldsObjectOrArray);
        $this->mExtraTable  = 'modules';
        $this->mContentType = 'MODULE';        
    }
    
    /**
     * returns the pk
     * @see Content::Save()
     */
    public function Save()
    {                        
//dump ($this->mFields);       
        $pk      = $this->mPk; 
        $eclass  = Query::Escape($this->mFields->modules_php_class);
        $ejson   = Query::Escape($this->mFields->modules_json_params);
        $ebody   = Query::Escape($this->mFields->modules_body); 
        $ecode   = Query::Escape($this->mFields->modules_site_code);
        $ecomment= Query::Escape($this->mFields->contents_version_comment);
        $apk     = $_SESSION['user_pk'];    
        
        // make sure this field is set
        $this->mFields->contents_author_fk = $this->mFields->contents_author_fk >0 ? $this->mFields->contents_author_fk : 0;
        
        $version = 1;
        
        if($pk > 0)
            $version = $this->mFields->contents_latest_version + 1;   
        else     
            $pk ='@pk';          // the parent adds a sql query to set this sql variable
            
         $this->mSqlStack[]  = "INSERT INTO modules(contents_fk, contents_version, contents_version_users_fk, contents_version_date, contents_version_comment, 
                               		modules_body, modules_php_class, modules_json_params, modules_site_code) 
                 				VALUES($pk, $version, $apk, NOW(), '$ecomment','$ebody','$eclass','$ejson','$ecode')";
        if($pk > 0)    
            $result = parent::SaveExisting();  
        else
            $result = parent::SaveNew();
     
        return $result;
    }    

    public function Delete()
    {
        die('Module::Delete() is not implemented');
    }
 
    /**
	 *	returns the modules for a particular page
	 * @param int $pages_pk [default = current page]
	 * @param bool $includeDetails options: [TRUE default], false (pk, title, placement, link_order)  
     */
    public static function GetPageModules($pages_pk= 0, $includeDetails = true)
    {
        global $CONFIG;
        $pages_pk = $pages_pk > 0 ? $pages_pk : $CONFIG->current_page_pk;
        
        $liveField = $CONFIG->mode == 'PREVIEW'? 'contents_preview_version' : 'contents_live_version';  
        if($includeDetails)
          $sql="SELECT * FROM (contents JOIN modules m ON m.contents_fk = contents_pk AND m.contents_version = $liveField )              
                JOIN modules__pages l ON l.contents_fk = contents_pk 
                WHERE pages_fk = $pages_pk ORDER BY placement, link_order";
        
        else
          $sql="SELECT contents_pk, contents_title, placement, link_order FROM (contents JOIN modules m ON m.contents_fk = contents_pk AND m.contents_version = $liveField )              
                JOIN modules__pages l ON l.contents_fk = contents_pk 
                WHERE pages_fk = $pages_pk ORDER BY placement, link_order";
                 
        $r = new Query($sql);
        return $r;          
    }
    
	/**
	 * gets all pages proof and live that this module is linked to
	 * @param int module_pk 
	 * @return array of pages
     */
    public static function GetPageLinks($module_pk)
    {
        $sql = "SELECT  max(pages_pk) as pages_pk, pages_id, pages_title, pages_site_code FROM pages JOIN modules__pages ON pages_pk = pages_fk 
                WHERE contents_fk = $module_pk  group by pages_id";
        return  new Query($sql);
      
    }
    
    /**
     * for CMS use
     * returns the latest version of the modules
     */
    public static function GetModules($site_code, $includeCommon = false)
    {
  
        if($includeCommon)
           $sql = "SELECT * FROM contents JOIN modules ON contents_pk = contents_fk
					WHERE contents_type = 'MODULE' AND  modules_site_code in ('$site_code', 'COMMON')
					AND contents_version = contents_latest_version "; 
        else 
             $sql = "SELECT * FROM contents JOIN modules ON contents_pk = contents_fk
					WHERE contents_type = 'MODULE' AND  modules_site_code = '$site_code' 
					AND contents_version = contents_latest_version ";
        return new Query($sql);    
    }

    /**
     * returns the module details
     * @param int $pk module pk (pk of the contents object)
     * @param int $version [default = 0 gets the live version]
     * @param bool $includeAuthor [default false]
     */
    public static function GetDetails($pk, $version = LIVE_VERSION, $includeAuthor = false)  
    {
        return Content::getAllData($pk, "modules", $version, $includeAuthor);
    }
   
    
    /**
     * links a array of module pk's to a page
     * @param int page pk
     * @param array of objects with the following fields:contents_fk,  placement, link_order
     */
    public static function LinkModules($page_pk, $moduleArray)
    {
        $sql= array();
        $sql[] = "DELETE FROM modules__pages WHERE pages_fk = $page_pk  ";
        
 //dump($moduleArray) ;      
        foreach($moduleArray as $mod)
        {
            $sql[] = "INSERT INTO modules__pages (contents_fk, pages_fk, placement, link_order) values($mod->contents_fk, $page_pk, '$mod->placement', $mod->link_order)";
        }
        return Query::sTransaction($sql);
    }
    
    
   
}
