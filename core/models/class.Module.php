<?
/*
 * Modules are a hybrid object. They are a content item, so that they can use the revisioning of the content items
 * However it does not use targeting but has its own link table that links it to a specific revision of a page
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
        $this->mExtraFieldDescriptions = array(      
        	'modules_php_class'   => array('type'=>'varchar', 'label'=> 'PHP Class') ,  
            'modules_json_params'    => array('type'=>'varchar', 'label'=> 'JSON Params') ,
            'modules_body'        => array('type'=>'varchar', 'label'=> 'BOdy') ,
            'modules_site_code'   => array('type'=>'varchar', 'label'=> 'site code') ,
        );   
    }
    
    /**
     * returns the id
     * @see Content::Save()
     */
    
    public function Save()
    {   
     /*                        
        //dump ($this->mFields);       
        $id      = $this->mId; 
        $eclass  = Query::Escape($this->mFields->modules_php_class);
        $ejson   = Query::Escape($this->mFields->modules_json_params);
        $ebody   = Query::Escape($this->mFields->modules_body); 
        $ecode   = Query::Escape($this->mFields->modules_site_code);
        $ecomment= Query::Escape($this->mFields->contents_rev_comment);
        $uid     = $_SESSION['user_id'];    
        
        // make sure this field is set
        $this->mFields->contents_authors_id = $this->mFields->contents_authors_id >0 ? $this->mFields->contents_authors_id : 0;
        
        $rev = 1;
        
        if($id > 0)
            $rev = $this->mFields->contents_latest_rev + 1;   
        else     
            $id ='@id';          // the parent adds a sql query to set this sql variable
            
         $this->mSqlStack[]  = "INSERT INTO modules(contents_fid, contents_rev, contents_rev_users_id, contents_rev_date, contents_rev_comment, 
                               		modules_body, modules_php_class, modules_json_params, modules_site_code) 
                 				VALUES($id, $rev, $uid, NOW(), '$ecomment','$ebody','$eclass','$ejson','$ecode')";
        if($id > 0)    
            $result = parent::SaveExisting();  
        else
            $result = parent::SaveNew();
        return $result;    
     */
        $newrev = true;  // always create a new rev
        return parent::Save($newrev);
    }    

    public function Delete()
    {
        die('Module::Delete() is not implemented');
    }
 
    /**
	 *	returns the modules for a particular page
	 * @param int $pages_rev [default = current page]
	 * @param bool $includeDetails options: [TRUE default], false (id, title, placement, link_order)  
     */
    public static function GetPageModules($pages_rev = 0, $includeDetails = true)
    {
        global $CONFIG;
        $pages_rev = $pages_rev > 0 ? $pages_rev : $CONFIG->current_pages_rev;
        
        $liveRev = $CONFIG->mode == 'PREVIEW'? 'contents_preview_rev' : 'contents_live_rev';  
        if($includeDetails)
          $sql="SELECT * FROM (contents  JOIN modules  ON contents_fid = contents_id AND contents_rev = $liveRev )              
                JOIN modules__pages ON mp_contents_id = contents_id 
                WHERE mp_pages_rev = $pages_rev ORDER BY mp_placement, mp_link_order";
        
        else
          $sql="SELECT contents_id, contents_title, mp_placement, mp_link_order FROM (contents JOIN modules  ON contents_fid = contents_id AND contents_rev = $liveRev )              
                JOIN modules__pages ON mp_contents_id = contents_id 
                WHERE mp_pages_rev = $pages_rev ORDER BY mp_placement, mp_link_order";
//dump($sql);                 
        $r = new Query($sql);
      
        return $r;          
    }
    
	/**
	 * gets all pages proof and live that this module is linked to
	 * @param int module_id 
	 * @return array of pages
     */
    public static function GetPageLinks($module_id)
    {
        $sql = "SELECT  max(pages_rev) as pages_rev, pages_id, pages_title, pages_site_code 
                FROM pages JOIN modules__pages ON pages_rev = mp_pages_rev 
                WHERE mp_contents_id = $module_id  group by pages_id";
        return  new Query($sql);
      
    }
    
    /**
     * for CMS use
     * returns the latest rev of the modules
     */
    public static function GetModules($site_code, $includeCommon = false)
    {
  
        if($includeCommon)
           $sql = "SELECT * FROM contents JOIN modules ON contents_id = contents_fid
					WHERE modules_site_code in ('$site_code', 'COMMON')
					AND contents_rev = contents_latest_rev "; 
        else 
             $sql = "SELECT * FROM contents JOIN modules ON contents_id = contents_fid
					WHERE   modules_site_code = '$site_code' 
					AND contents_rev = contents_latest_rev ";
        return new Query($sql);    
    }

    /**
     * returns the module details
     * @param int $id module id (id of the contents object)
     * @param int $rev [default = 0 gets the live rev]
     * @param bool $includeAuthor [default false]
     */
    public static function GetDetails($id, $rev = LIVE_rev, $includeAuthor = false)  
    {
        return Content::getAllData($id, "modules", $rev, $includeAuthor);
    }
   
    
    /**
     * links a array of module id's to a page
     * @param int page rev
     * @param array of objects with the following fields:contents_id,  placement, link_order
     */
    public static function LinkModules($pages_rev, $moduleArray)
    {
        $sql= array();
        $sql[] = "DELETE FROM modules__pages WHERE mp_pages_rev = $pages_rev  ";
        
 //dump($moduleArray) ;      
        foreach($moduleArray as $mod)
        {
            $sql[] = "INSERT INTO modules__pages (mp_contents_id, mp_pages_rev, mp_placement, mp_link_order) values($mod->contents_id, $pages_rev, '$mod->placement', $mod->link_order)";
        }
        return Query::sTransaction($sql);
    }
    
    
   
}
