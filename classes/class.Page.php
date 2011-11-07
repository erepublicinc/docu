<?
/**
 * 
 * Page  fills the role of the Clickability 'Website Section'
 * @author michael
 *
 */
class Page 
{
    protected $mFields = null;            // object of database field values
     
    public function __construct($params)
    {
      if(is_object($params))
          $this->mFields = $params;  
      elseif(is_array($params))
          $this->mFields = (object) $params;
 
    }
    /**
     * setting live_version or proof_version to 0 , will cause these versions to be set to the most recent version
     * update_version is the version to update, if set to 0 we will create a new version if -1 we will not update the text field
     */
    public function Save()
    {           
        // clean it up before saving
        $f = $this->mFields;
        $p            = new stdClass(); 
        $p->site_code = $f->site_code;
        $p->title     = Query::Escape($f->pages_title);
        $p->dtitle    = Query::Escape($f->pages_display_title);
        $p->url       = Query::Escape($f->pages_url);
        $p->status    = Query::Escape(strtoupper($f->pages_status));
        $p->type      = Query::Escape(strtoupper($f->pages_type));
        $p->phpClass  = Query::Escape($f->pages_php_class);
        $p->apk       = $_SESSION['user_pk'];
        $p->is_live   = $f->pages_is_live? 1:0;
        $p->is_preview  = $f->pages_is_preview? 1:0;
        $p->version   = intval($f->pages_version);
        $p->pk        = intval($f->pages_pk);
        $p->id        = intval($f->pages_id);
        $p->robots    = $f->pages_no_robots ? 1:0;
        $p->new_version = $f->new_version ? 1:0;
                
        $body = trim($f->pages_body);
        if(!empty($body))
        {
            $p->body      = Query::Escape($body);
        }
       
        if($this->mFields->pages_pk  > 0)
            return $this->SaveExisting($p);
        return $this->SaveNew($p);    
    }

    protected function SaveNew($p)
    {          
        $sql = array(); 
        $sql[]= "SELECT @page_id := MAX(pages_id) +1 FROM pages";
        $sql[]= "INSERT INTO pages (pages_id,pages_is_live,pages_is_preview,pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,pages_no_robots, pages_status, pages_php_class,pages_authors_fk,pages_body) 
                  values(@page_id,$p->is_live,$p->is_preview,1,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type',$p->robots,'$p->status','$p->phpClass',$p->apk, '$p->body')";   
        return Query::sTransaction($sql);
    }
    
    
    protected function SaveExisting($p)
    {               
        $sql = array();
     
        if($p->new_version == 0) //  means: keep the same version
        {    
               
             $sql[] = "UPDATE pages SET pages_title = '$p->title', pages_display_title = '$p->dtitle', pages_url='$p->url', pages_type = '$p->type', 
                    pages_no_robots = $p->robots, pages_password = '$p->password', pages_status = '$p->status', pages_php_class = '$p->phpClass', 
                    pages_authors_fk = $p->apk, pages_body = '$p->body'             
        		where pages_pk = $p->pk";
        }
        else  // create a new version
        {
        
            if($p->is_live) // make sure none of the other versions is live
            {
                 $sql[]= "UPDATE pages set pages_is_live = 0 where pages_id = $p->id";
            }
            if($p->is_proof)
            {
                 $sql[]= "UPDATE pages set pages_is_preview = 0 where pages_id = $p->id";
            }
            
            // add the new record
            $sql[]= "INSERT INTO pages (pages_id,pages_is_live,pages_is_preview,pages_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,pages_no_robots, pages_status, pages_php_class,pages_authors_fk,pages_body) 
                  values($p->id,$p->is_live,$p->is_preview,$p->version+1,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type',$p->robots,'$p->status','$p->phpClass',$p->apk, '$p->body')";
            
            // get the new record
            $sql[] = 'SELECT @new_pk:= LAST_INSERT_ID()';
            
            // now we have to copy all module links from the previous version to this version
            $sql[] = "INSERT INTO modules__pages (modules_fk, pages_fk,link_type,link_order) SELECT modules_fk, @new_pk,link_type,link_order FROM modules__pages WHERE pages_fk = $p->pk";
        }      
        return Query::sTransaction($sql);       
    }
    
    
    /**
     * Makes this version of the page live
     * @param int $pages_id
     * @param int $version
     */    
    static public function MakeLive($pages_id, $version)
    {
        $sql = array();
        $sql[]= "UPDATE pages set pages_is_live = 0 where pages_id = $p->pages_id";
        $sql[]= "UPDATE pages set pages_is_live = 1 where pages_id = $p->pages_id and pages_version = $version";  
        return Query::sTransaction($sql);    
    }

    /**
     * Makes this version of the page Preview
     * @param int $pages_id
     * @param int $version
     */            
    static public function MakePreview($pages_id, $version)
    {
        $sql = array();
        $sql[]= "UPDATE pages set pages_is_preview = 0 where pages_id = $p->pages_id";
        $sql[]= "UPDATE pages set pages_is_preview = 1 where pages_id = $p->pages_id and pages_version = $version";  
        return Query::sTransaction($sql);    
    }
   
    
    /**
     * Gets all pages default: only live pages for current site
     * @param char $sitecode  "ALL" for all 
     * @param bool $allPages  // if false, you only get the live pages
     */
    static public function getPages($sitecode = null, $allPages = false)
    {
        global $CONFIG;
        $site = $sitecode ? $sitecode : $CONFIG->site_code;
        
        if($allPages == true )
        {
            $sql = "SELECT * FROM pages  
            			JOIN max_page_version ON mpv_pages_id = pages_id  AND  pages_version = mpv_pages_version "; 
            if($site != 'ALL')
            {
                $sql .= " WHERE pages_site_code = '$site' ";
            }
        }
        else
        {
            $WHERE_AND = "WHERE";
        
            $sql = "SELECT * FROM pages ";
        
            if($site != 'ALL')
            {
                $sql .= " WHERE pages_site_code = '$site' ";
                $WHERE_AND = "AND";
            }
            
            $liveField = ($CONFIG->mode == 'PREVIEW') ? 'pages_is_preview' : 'pages_is_live';  
            $sql .= " $WHERE_AND $liveField = 1 ";
        }   
        $sql .= ' ORDER BY pages_site_code, pages_url, pages_version';
       
        return new Query($sql);
    }    
   
    /**
     * returns a class mapp array for current site
     * @param String sitecode
     */
    static public function  GetClassMapping ($sitecode = NULL)
    {
        global $CONFIG;
        $site = $sitecode ? $sitecode : $CONFIG->site_code;
        
        $liveField = $CONFIG->mode == 'PREVIEW'? 'pages_is_preview' : 'pages_is_live';  
        
        $sql="SELECT pages_id, pages_pk, pages_php_class
              FROM pages WHERE pages_site_code = '$site' AND  $liveField = 1";
        
        $pages=new Query($sql);
        
        $pageArray = array();
        foreach($pages as $p)
        {
            $pageArray[$p->pages_url] = array('class'=> $p->pages_php_class,'pages_id'=> $p->pages_id, 'pages_pk'=> $p->pages_pk );        
        }
        return $pageArray;
    }
    
    /**
     * gets page details for current environment
     * @param String $site_code 
     * @param String $url
     * @param int $version [default = 0   returns the live version for the current environment ] 
     */
    static public function  GetPageDetails($site_code, $pages_url, $version = 0)
    {
        global $CONFIG;
        $liveField = $CONFIG->mode == 'PREVIEW'? 'pages_is_preview' : 'pages_is_live'; 
       
        if($version == 0)
        {
            $sql="SELECT * FROM pages WHERE pages_site_code = '$site' and pages_url = '$url' AND $liveField = 1 ";
        }
        else 
        {
             $sql="SELECT * FROM pages WHERE pages_site_code = '$site' and pages_url = '$url'  and pages_version = $version";
        }
        return new Query($sql);       
    }
    
    /**
     * returns all fields
     * @param int $pk
     */
    static public function  GetDetails($pk = 0)
    {
        global $CONFIG;
        if($pk == 0) 
            $pk = intval($CONFIG->current_page_pk);
        $r =  new Query("SELECT * FROM pages WHERE pages_pk = $pk");
        //dump($r); 
        return $r;     
    }

    /**
     * Deletes the page
     * @param unknown_type $pk
     */
    static public function  DeletePage($pk)
    {
        $sql="UPDATE pages SET pages_status='DELETED', pages_is_live = 0, pages_is_preview = 0 WHERE pages_pk = $pk";
        return new Query($sql);
    }
    
    
// ====================== TARGETS ==========================
    /**
     * Gets all targets for a page
     * @param int pages_id
     */
    static public function GetTargets($id)
    {
        $sql = "SELECT targets_pages_id, targets_contents_fk, targets_pin_position, targets_live_date, targets_archive_date, targets_dead_date, pages_title 
                FROM targets JOIN pages ON pages_id = targets_pages_id WHERE pages_id = $id";
        return new Query($sql);
    }    
    
    /**
     * Creates a target 
     * @param array or object $params  with:  targets_pages_id   and targets_contents_fk
     */
    static public function sYaasSaveTarget($params)
    {
        if(! User::Authorize('ADMIN'))
        {
            return('unauthorized');
        }
        
        if(is_array($params))
            $params = (object)$params;
//dump($params);            
        $pk        = intval($params->targets_pk);
        $page      = intval( $params->targets_pages_id);
        $content   = intval( $params->targets_contents_fk);
        $live_date = Query::Escape($params->targets_live_date);
        $dead_date = Query::Escape($params->targets_dead_date);
        $archive_date = Query::Escape($params->targets_archive_date);
        $pin       = $params->targets_pin_position ? intval($params->targets_pin_position) : 999999;
        
        if($params->record_state == 'dirty')
        {
            $sql = "UPDATE targets SET  targets_live_date= '$live_date', targets_dead_date = '$dead_date',  targets_archive_date= '$archive_date',  targets_pin_position = $pin 
                    WHERE targets_pages_id = $page AND targets_contents_fk = $content";
        }
        elseif ($params->record_state == 'new')
        {
            $sql = "INSERT INTO targets (targets_pages_id, targets_contents_fk, targets_live_date,  targets_archive_date, targets_dead_date, targets_pin_position) 
                                  VALUES($page, $content, '$live_date', '$archive_date', '$dead_date', $pin)";
        }
        else 
        {
            logerror(" unexpected state: $params->record_state "  ,__FILE__ . __LINE__);
        }
        return new Query($sql);
    }
    
   
    
}

