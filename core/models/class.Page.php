<?
/**
 * 
 * Page  fills the role of the Clickability 'Website Section'
 * @author michael
 *
 */
class Page extends Model
{
    protected $mFields = null;            // object of database field values

     protected static $mFieldDescriptions = array(
            'pages_rev'                => array('type'=>'pk'),
            'pages_id'   			   => array('type'=>'int', 'insert_only'=>true, 'required'=>true, 'do_not_validate'=>true),         
            'pages_rev_users_id'   => array('type'=>'int', 'insert_only'=>true, 'required'=>true),  
   //         'pages_rev_date'       => array('type'=>'datetime', 'insert_only'=>true,'do_not_validate'=>true),  // NOW()       done by system automatically
            'pages_rev_comment'    => array('type'=>'varchar'),  
         //   'pages_rev_status'     => array('type'=>'varchar', 'required'=>true),  
            'pages_is_preview'         => array('type'=>'bit' ), 
            'pages_is_live'            => array('type'=>'bit'),
            'pages_site_code'          => array('type'=>'varchar', 'required'=>true), 
            'pages_title'              => array('type'=>'varchar', 'required'=>true),   
            'pages_display_title'      => array('type'=>'varchar'),    
            'pages_url'                => array('type'=>'varchar', 'required'=>true),   
            'pages_type'               => array('type'=>'varchar'  ),   
            'pages_no_robots'          => array('type'=>'bit'), 
            'pages_password'           => array('type'=>'varchar'),   
            'pages_php_class'          => array('type'=>'varchar'), 
            'pages_body'               => array('type'=>'varchar') 
            );
    
    public function __construct($params)
    {
      if(is_object($params))
          $this->mFields = $params;  
      elseif(is_array($params))
          $this->mFields = (object) $params;
 
    }
    /**
     * setting live_rev or preview_rev to 0 , will cause these revisions to be set to the most recent revision
     * update_rev is the revision to update, if set to 0 we will create a new revision if -1 we will not update the text field
     * @param  bool $returnRev [default=false returns the Id] 
     * @return rev or id of the page
     */
    public function Save($returnRev = false)
    {   
        $this->mFields->pages_rev_users_id = $_SESSION['user_id'];         
        $this->mFields->pages_rev_date     = "NOW()";
          
        if($this->mFields->pages_rev  > 0)
            return $this->SaveExisting();
        return $this->SaveNew();    
    }

    protected function SaveNew($returnRev = false)
    {   
        $this->mFields->pages_id = '@page_id';       
        $values = $this->FormatUpdateString(self::$mFieldDescriptions, SQL_INSERT); 
               
        $sql = array(); 
        $sql[]= "SELECT @page_id := MAX(pages_id) +1 FROM pages";
        $sql[]= "INSERT INTO pages $values ";
 /*       
        $sql[]= "INSERT INTO pages (pages_id, pages_is_live, pages_is_preview, pages_rev, pages_site_code, pages_title, pages_display_title, 
                pages_url, pages_type, pages_no_robots, pages_status, pages_php_class, pages_rev_users_id, pages_body, pages_rev_comment, pages_rev_date) 
                  values(@page_id,$p->is_live,$p->is_preview,1,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type',
                     $p->robots,'$p->status','$p->phpClass',$p->uid, '$p->body','$p->comment', NOW())";   
 */       
        $sql[] = "SELECT LAST_INSERT_ID() as rev, @page_id as id"  ;
                   
        $ret = Query::sTransaction($sql);
        if($returnRev)
            return $ret->rev;
        return $ret->id;    
    }
    
    
    protected function SaveExisting($returnRev = false)
    {  
        $rev =  intval($this->mFields->pages_rev);
        $id  = intval($this->mFields->pages_id);
                
        $sql = array();
     
        if($this->mFields->new_rev == 0) //  means: keep the same rev
        {    
            $values = $this->FormatUpdateString(self::$mFieldDescriptions, SQL_UPDATE); 
/*            
             $sql[] = "UPDATE pages SET pages_title = '$p->title', pages_display_title = '$p->dtitle', pages_url='$p->url', pages_type = '$p->type', 
                    pages_no_robots = $p->robots, pages_password = '$p->password', pages_status = '$p->status', pages_php_class = '$p->phpClass', 
                    pages_rev_users_id = $p->uid, pages_body = '$p->body'             
        		where pages_rev = $p->rev";
*/        	
            
             $sql[] = "UPDATE pages SET $values WHERE pages_rev = $rev";	
             $sql[] = "SELECT  $rev as rev, $id as id";

        }
        else  // create a new rev
        {                                   
            if($this->mFields->is_live) // make sure none of the other revs is live
            {
                 $sql[]= "UPDATE pages set pages_is_live = 0 where pages_id = $id";
            }
            if($this->mFields->is_proof)
            {
                 $sql[]= "UPDATE pages set pages_is_preview = 0 where pages_id = $id";
            }
                        
            $sql[]= "SELECT @rev := MAX(pages_rev) +1 FROM pages WHERE pages_id =  $id ";
            // add the new record
/*            
            $sql[]= "INSERT INTO pages (pages_id,pages_is_live,pages_is_preview,pages_rev,pages_site_code,pages_title,pages_display_title,pages_url,
                      pages_type,pages_no_robots, pages_status, pages_php_class,pages_rev_users_id,pages_body, pages_rev_comment, pages_rev_date)  
                  values($p->id,$p->is_live,$p->is_preview,@rev,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type',$p->robots,
                           '$p->status','$p->phpClass',$p->uid, '$p->body','$p->comment', NOW())";  
  */
            $values = $this->FormatUpdateString(self::$mFieldDescriptions, SQL_INSERT); 
            
            $sql[]= "INSERT INTO pages $values";
                       
            // get the new record
            $sql[] = "SELECT LAST_INSERT_ID() as rev, $id as id";
           
        } 
        
        
 // dump($sql);           
        $ret =  Query::sTransaction($sql);
      
        if($returnRev)
            return $ret->rev;
        return $ret->id;           
    }
    
    
    /**
     * Makes this rev of the page live
     * @param int $pages_id
     * @param int $rev
     */    
    static public function SetLiveRevision($pages_id, $rev)
    { 
        $sql = array();
        $sql[]= "UPDATE pages set pages_is_live = 0 where pages_id = $pages_id";
        $sql[]= "UPDATE pages set pages_is_live = 1 where pages_id = $pages_id and pages_rev = $rev";  
        return Query::sTransaction($sql);       
    }

    /**
     * Makes this rev of the page Preview
     * @param int $pages_id
     * @param int $rev
     */            
    static public function SetPreviewRevision($pages_id, $rev)
    {
        $sql = array();
        $sql[]= "UPDATE pages set pages_is_preview = 0 where pages_id = $pages_id";
        $sql[]= "UPDATE pages set pages_is_preview = 1 where pages_id = $pages_id and pages_rev = $rev";  
        return Query::sTransaction($sql);    
    }
   
    
    /**
     * Gets all pages default: only live pages for current site
     * @param char $sitecode  "ALL" for all ,  null for current site
     * @param bool $allPages  // if false, you only get the live pages
     */
    static public function getPages($sitecode = null, $allPages = false)
    {
        global $CONFIG;
        $site = $sitecode ? $sitecode : $CONFIG->site_code;
        
        if($allPages == true ) // get the latest rev
        {
            $sql = "SELECT * FROM pages  
            			JOIN max_page_revisions ON mpr_pages_id = pages_id  AND  pages_rev = mpr_pages_rev "; 
            if($site != 'ALL')
            {
                $sql .= " WHERE pages_site_code = '$site' ";
            }
        }
        else    // get live / preview revs
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
        $sql .= ' ORDER BY pages_site_code, pages_url, pages_rev';
       
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
            $pageArray[$url] = array('class'=> $p->pages_php_class,'pages_id'=> $p->pages_id, 'pages_rev'=> $p->pages_rev );        
        }
        return $pageArray;
    }
    
    /**
     * gets page details for current environment
     * @param String $site_code 
     * @param String $url
     * @param int $rev [default = LIVE_REV   returns the live rev for the current environment ] 
     */
/* not used ?    
    static public function  GetPageDetails($site_code, $pages_url, $rev = 0)
    {
        global $CONFIG;
        $liveField = $CONFIG->mode == 'PREVIEW'? 'pages_is_preview' : 'pages_is_live'; 
       
        if($rev == 0)
        {
            $sql="SELECT * FROM pages WHERE pages_site_code = '$site' and pages_url = '$url' AND $liveField = 1 ";
        }
        else 
        {
             $sql="SELECT * FROM pages WHERE pages_site_code = '$site' and pages_url = '$url'  and pages_rev = $rev";
        }
        return new Query($sql);       
    }
*/
    
    
    /**
     * returns all fields
     * @param int $id  [default = 0] if set returns live or preview rev of this page
     * @param int $rev [default = LIVE_REV returns the current page]
     */
    static public function  GetDetails($id = 0, $rev = LIVE_REV)
    {
        global $CONFIG;
        
        if($rev > 0)  // we ask for a very specific revision
        {
            $sql = "SELECT * FROM pages WHERE pages_rev = $rev";
        }
        elseif ($id == 0) // we get the current page
        {
            $rev = intval($CONFIG->current_pages_rev);
            $sql = "SELECT * FROM pages WHERE pages_rev = $rev";
        }
        elseif( $rev == LATEST_REV )  // the user supplied the id  and wants the latest rev
        {
            $sql = "SELECT * FROM pages WHERE pages_id = $id  ORDER BY pages_rev DESC LIMIT 1";
        }
        else // get live rev  for a particular page id  ($rev == LIVE_REV)
        { 
            $liveField = ($CONFIG->mode == 'PREVIEW') ? 'pages_is_preview' : 'pages_is_live';           
            $sql = "SELECT * FROM pages WHERE pages_id = $id  AND $liveField = 1";
        }
        return new Query($sql);     
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
     * Gets the rev history for a particular page
     * @param int $id pages_id
     */
    static public function GetRevisionHistory($id)
    {
        if(empty($id))
          return ;
        
        $sql = array();
                 // get the live and preview revs 
       $sql[] = "SELECT pages_rev as live_rev, 0 as preview_rev from pages WHERE pages_is_live = 1 AND pages_id = $id 
                 UNION SELECT 0 as live_rev, pages_rev as preview_rev from pages WHERE pages_is_preview = 1 AND pages_id = $id ";
        
        
        $sql[]="SELECT pages_rev , pages_rev as rev, pages_rev_date as rev_date, pages_rev_comment as rev_comment, users_first_name, users_last_name FROM pages 
              JOIN users on pages_rev_users_id = users_id
        	  WHERE pages_id = $id  ORDER BY pages_rev DESC";

        $result = new Query($sql);
 

        
        // we conduct 2 queries, and put information from the first query into the first record of the second 
        
        $live_rev = 0;
        $preview_rev = 0;

        foreach($result as $r)
        {
            if($r->live_rev > 0)
               $live_rev = $r->live_rev;
            if($r->preview_rev > 0)
               $preview_rev = $r->preview_rev;   
        }
 
       $result->NextResultSet();

        $result->SetValue('live_rev', $live_rev);
        $result->SetValue('preview_rev', $preview_rev);
      
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
            logerror(" unexpected state: $params->record_state " );
        }
        return new Query($sql);
    }
    
   
    
}

