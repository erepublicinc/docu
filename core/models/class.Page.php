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
        $p->site_code = Query::Escape($f->pages_site_code);
        $p->title     = Query::Escape($f->pages_title);
        $p->dtitle    = Query::Escape($f->pages_display_title);
        $p->url       = Query::Escape($f->pages_url);
        $p->status    = Query::Escape(strtoupper($f->pages_status));
        $p->type      = Query::Escape(strtoupper($f->pages_type));
        $p->phpClass  = Query::Escape($f->pages_php_class);
        $p->comment   = Query::Escape($f->pages_version_comment);
        $p->uid       = $_SESSION['user_id'];
        $p->is_live   = $f->pages_is_live? 1:0;
        $p->is_preview  = $f->pages_is_preview? 1:0;
        $p->version   = intval($f->pages_version);
        $p->rev       = intval($f->pages_rev);
        $p->id        = intval($f->pages_id);
        $p->robots    = $f->pages_no_robots ? 1:0;
        $p->new_version = $f->new_version ? 1:0;
                
        $body = trim($f->pages_body);
        if(!empty($body))
        {
            $p->body      = Query::Escape($body);
        }
        
        if($this->mFields->pages_rev  > 0)
            return $this->SaveExisting($p);
        return $this->SaveNew($p);    
    }

    protected function SaveNew($p)
    {          
        $sql = array(); 
        $sql[]= "SELECT @page_id := MAX(pages_id) +1 FROM pages";
        $sql[]= "INSERT INTO pages (pages_id, pages_is_live, pages_is_preview, pages_version, pages_site_code, pages_title, pages_display_title, 
                pages_url, pages_type, pages_no_robots, pages_status, pages_php_class, pages_version_users_id, pages_body, pages_version_comment, pages_version_date) 
                  values(@page_id,$p->is_live,$p->is_preview,1,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type',
                     $p->robots,'$p->status','$p->phpClass',$p->uid, '$p->body','$p->comment', NOW())";   
        
        $sql[] = "SELECT LAST_INSERT_ID() as rev"  ;
                   
        $ret = Query::sTransaction($sql);
        return $ret->rev;
    }
    
    
    protected function SaveExisting($p)
    {  
               
        $sql = array();
     
        if($p->new_version == 0) //  means: keep the same version
        {    
               
             $sql[] = "UPDATE pages SET pages_title = '$p->title', pages_display_title = '$p->dtitle', pages_url='$p->url', pages_type = '$p->type', 
                    pages_no_robots = $p->robots, pages_password = '$p->password', pages_status = '$p->status', pages_php_class = '$p->phpClass', 
                    pages_version_users_id = $p->uid, pages_body = '$p->body'             
        		where pages_rev = $p->rev";
             $sql[] = "SELECT  $p->rev as new_rev";
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
            
            
            $sql[]= "SELECT @version := MAX(pages_version) +1 FROM pages WHERE pages_id =  $p->id ";
            // add the new record
            $sql[]= "INSERT INTO pages (pages_id,pages_is_live,pages_is_preview,pages_version,pages_site_code,pages_title,pages_display_title,pages_url,
                      pages_type,pages_no_robots, pages_status, pages_php_class,pages_version_users_id,pages_body, pages_version_comment, pages_version_date)  
                  values($p->id,$p->is_live,$p->is_preview,@version,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type',$p->robots,
                           '$p->status','$p->phpClass',$p->uid, '$p->body','$p->comment', NOW())";  
            
            // get the new record
            $sql[] = 'SELECT LAST_INSERT_ID() as new_id';

            
        } 
        
        
        
 // dump($sql);           
        $ret =  Query::sTransaction($sql);
        return $ret->new_id;              
    }
    
    
    /**
     * Makes this version of the page live
     * @param int $pages_id
     * @param int $version
     */    
    static public function SetLiveVersion($pages_id, $version)
    { 
        $sql = array();
        $sql[]= "UPDATE pages set pages_is_live = 0 where pages_id = $pages_id";
        $sql[]= "UPDATE pages set pages_is_live = 1 where pages_id = $pages_id and pages_version = $version";  
        return Query::sTransaction($sql);       
    }

    /**
     * Makes this version of the page Preview
     * @param int $pages_id
     * @param int $version
     */            
    static public function SetPreviewVersion($pages_id, $version)
    {
        $sql = array();
        $sql[]= "UPDATE pages set pages_is_preview = 0 where pages_id = $pages_id";
        $sql[]= "UPDATE pages set pages_is_preview = 1 where pages_id = $pages_id and pages_version = $version";  
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
        
        if($allPages == true ) // get the latest version
        {
            $sql = "SELECT * FROM pages  
            			JOIN max_page_version ON mpv_pages_id = pages_id  AND  pages_version = mpv_pages_version "; 
            if($site != 'ALL')
            {
                $sql .= " WHERE pages_site_code = '$site' ";
            }
        }
        else    // get live / preview versions
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
        
        $sql="SELECT pages_id, pages_rev, pages_php_class, pages_url
              FROM pages WHERE pages_site_code = '$site' AND  $liveField = 1";
        
        $pages=new Query($sql);
        
        $pageArray = array();
        foreach($pages as $p)
        {
            $url = trim($p->pages_url, '/ '); // remove the slashes
            $pageArray[$url] = array('class'=> $p->pages_php_class,'pages_id'=> $p->pages_id, 'pages_id'=> $p->pages_id );        
        }
        return $pageArray;
    }
    
    /**
     * gets page details for current environment
     * @param String $site_code 
     * @param String $url
     * @param int $version [default = 0   returns the live version for the current environment ] 
     */
/* not used ?    
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
*/
    
    
    /**
     * returns all fields
     * @param int $rev [default = 0 returns the current page]
     * @param int $id [default = 0] if set returns live or preview version of this page
     */
    static public function  GetDetails($rev = 0, $id = 0)
    {
        global $CONFIG;
        
        if($id > 0)
        {
            $liveField = ($CONFIG->mode == 'PREVIEW') ? 'pages_is_preview' : 'pages_is_live';  
            $r =  new Query("SELECT * FROM pages WHERE pages_id = $id  AND $liveField = 1");
        }
        else
        { 
            if($rev == 0) 
                $rev = intval($CONFIG->current_pages_rev);
            $r =  new Query("SELECT * FROM pages WHERE pages_rev = $rev");
        }
        return $r;     
    }

    /**
     * Deletes the page
     * @param unknown_type $rev
     */
    static public function  DeletePage($rev)
    {
        $sql="UPDATE pages SET pages_status='DELETED', pages_is_live = 0, pages_is_preview = 0 WHERE pages_rev = $rev";
        return new Query($sql);
    }
    
    /**
     * Gets the version history for a particular page
     * @param int $id pages_id
     */
    static public function GetVersionHistory($id)
    {
        if(empty($id))
          return ;
        
        $sql = array();
                 // get the live and preview versions 
       $sql[] = "SELECT pages_version as live_version, 0 as preview_version from pages WHERE pages_is_live = 1 AND pages_id = $id 
                 UNION SELECT 0 as live_version, pages_version as preview_version from pages WHERE pages_is_preview = 1 AND pages_id = $id ";
        
        
        $sql[]="SELECT pages_rev , pages_version as version, pages_version_date as version_date, pages_version_comment as version_comment, users_first_name, users_last_name FROM pages 
              JOIN users on pages_version_users_id = users_id
        	  WHERE pages_id = $id  ORDER BY pages_version DESC";

        $result = new Query($sql);
 

        
        // we conduct 2 queries, and put information from the first query into the first record of the second 
        
        $live_rev = 0;
        $preview_rev = 0;

        foreach($result as $r)
        {
            if($r->live_version > 0)
               $live_rev = $r->live_version;
            if($r->preview_version > 0)
               $preview_rev = $r->preview_version;   
        }
 
       $result->NextResultSet();

        $result->SetValue('live_version', $live_rev);
        $result->SetValue('preview_version', $preview_rev);
      
        //dump($result);
        return $result;
    }
    
    
// ====================== TARGETS ==========================
    /**
     * Gets all targets for a page
     * @param int pages_id
     */
    static public function GetTargets($id)
    {
        $sql = "SELECT targets_pages_id, targets_contents_id, targets_pin_position, targets_live_date, targets_archive_date, targets_dead_date, pages_title ,pages_site_code
                FROM targets JOIN pages ON pages_id = targets_pages_id WHERE pages_id = $id";
        return new Query($sql);
    }    
    
    /**
     * Creates a target 
     * @param array or object $params  with:  targets_pages_id   and targets_contents_id
     */
    static public function sYaasSaveTarget($params)
    {
   
        if(! User::Authorize('ADMIN'))
        {
            return('unauthorized');
        }
        
        if(is_array($params))
            $params = (object)$params;
        
        $id        = intval($params->targets_id);
        $page      = intval( $params->targets_pages_id);
        $content   = intval( $params->targets_contents_id);
        $live_date = Query::Escape($params->targets_live_date);
        $dead_date = Query::Escape($params->targets_dead_date);
        $archive_date = Query::Escape($params->targets_archive_date);
        $pin       = $params->targets_pin_position ? intval($params->targets_pin_position) : 999999;
        
        if($params->record_state == 'DIRTY')
        {
            $sql = "UPDATE targets SET  targets_live_date= '$live_date', targets_dead_date = '$dead_date',  targets_archive_date= '$archive_date',  targets_pin_position = $pin 
                    WHERE targets_pages_id = $page AND targets_contents_id = $content";
        }
        elseif ($params->record_state == 'NEW')
        {
            $sql = "INSERT INTO targets (targets_pages_id, targets_contents_id, targets_live_date,  targets_archive_date, targets_dead_date, targets_pin_position) 
                                  VALUES($page, $content, '$live_date', '$archive_date', '$dead_date', $pin)";
        }
        else 
        {
            logerror(" unexpected state: $params->record_state "  ,__FILE__ . __LINE__);
        }
        return new Query($sql);
    }
    
   
    
}

