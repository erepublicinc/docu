<?

class Page 
{
    protected $mFields = null;
     
    public function __construct($params)
    {
      if(is_object($params))
          $this->mFields = $params;  
      elseif(is_array($params))
          $this->mFields = (object) $params;

      $p->pages_site_code = strtoupper($p->pages_site_code);
      $p->pages_title     = Query::Escape($p->pages_title);
      $p->display_title   = Query::Escape($p->mFields->pages_display_title);
 /*       
        if(is_object($params))
        {
            $this->mFields =  $params; 
        }      
        elseif(is_array($params)) 
        {
            $this->mFields = new stdClass();
            foreach($params as $key => $value )
            {
                $this->mFields->$key = $value;
            }
            $this->mFields = $fields;
        }
 */    
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
        $p->site_code = strtoupper($f->pages_site_code);
        $p->title     = Query::Escape($f->pages_title);
        $p->dtitle    = Query::Escape($f->mFields->pages_display_title);
        $p->url       = Query::Escape($f->mFields->pages_url);
        $p->status    = strtoupper($f->pages_status);
        $p->phpClass  = $f->pages_php_class;
        $p->apk       = intval($f->pages_authors_fk);
               
        $body = trim($f->pages_body);
        if(!empty($body))
        {
            $p->body      = Query::Escape($body);
        }
        $p->robots    = intval($f->pages_no_robots);
        
        
        if($this->mFields->pages_pk  > 0)
            return $this->SaveExisting($p);
        return $this->SaveNew($p);    
    }
    
    protected function SaveExisting($p)
    {
        $pk = $this->mFields->pages_pk;
        
        $live_version = $this->mFields->pages_live_version > 0   ? $this->mFields->pages_live_version: '@latest';
        $proof_version = $this->mFields->pages_proof_version > 0 ? $this->mFields->pages_proof_version: '@latest';
        
        $sql = array();
        
        // we need to find the latest version available
        $sql[] = "SELECT @latest := max(textfields_version) FROM textfields WHERE textfields_table = 'pages' AND textfields_table_fk = $pk ";
     
        if($this->mFields->update_version > -1) // -1 means: don't update the text body
        {    
            
            if($this->mFields->update_version > 0) // we will update an existing record
            { 
                $v = $this->mFields->update_version;
                 $sql[] = "UPDATE textfields SET textfields_body = '$p->body' WHERE textfields_table = 'pages' AND textfields_table_fk = $pk AND text_fields_version = $v"; 
            }
            else  // create a new record
            {
                $live_version = $this->mFields->pages_live_version > 0   ? $this->mFields->pages_live_version: '@latest +1';
                $proof_version = $this->mFields->pages_proof_version > 0 ? $this->mFields->pages_proof_version: '@latest +1';
                
                $sql[] = "INSERT INTO textfields(textfields_table, textfields_table_pk, textfields_version, textfields_body, textfields_authors_fk, textfields_date) 
                   values('pages', $pk, @latest + 1, '$p->body', $p->apk, NOW())";
                
            }
            
        }   
         $sql[] = "UPDATE pages SET pages_live_version = $live_version, pages_proof_version = $proof_version, pages_title = '$p->title', 
                    pages_display_title = '$p->dtitle', pages_url='$p->url', pages_type = '$p->type', pages_no_robots = $p->robots,
                    pages_password = '$p->password', pages_status = '$p->status', pages_php_class = '$p->phpClass', pages_authors_fk = $p->apk             
        		where pages_pk = $pk";
        
        return Query::sTransaction($sql);       
    }
    
    protected function SaveNew($p)
    {   
       if(empty($p->body))
       { 
        $sql= "INSERT INTO pages (pages_live_version, pages_proof_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,pages_no_robots, pages_status, pages_php_class,pages_authors_fk) 
                  values(1,1,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type','$p->robots','$p->status','$p->phpClass',$p->apk)";
        }
        else
        {
            $sql = array();
            $sql[]= "INSERT INTO pages (pages_live_version, pages_proof_version,pages_site_code,pages_title,pages_display_title,pages_url,pages_type,pages_no_robots, pages_status, pages_php_class,pages_authors_fk) 
                  values(1,1,'$p->site_code','$p->title','$p->dtitle','$p->url','$p->type','$p->robots','$p->status','$p->phpClass',$p->apk)";

            $sql[] = 'SELECT @pk:= LAST_INSERT_ID()';
     
            $sql[] = "INSERT INTO textfields(textfields_table, textfields_table_pk, textfields_version, textfields_body, textfields_authors_fk, textfields_date) 
                   values('pages', @pk, 1, '$p->body', $p->apk, NOW())";

        }
        return Query::sTransaction($sql);
    }
    
    /**
     * Gets all pages default: only live pages for current site
     * @param bool $onlyLivePages
     * @param char $sitecode  "ALL" for all 
     */
    static public function getPages($sitecode = null, $onlyLivePages = true)
    {
        global $CONFIG;
        $site = $sitecode ? $sitecode : $CONFIG->site_code;
        
        $WHERE_AND = "WHERE";
        
        $sql = "SELECT * FROM pages ";
        
        if($site != 'ALL')
        {
            $sql .= " WHERE pages_site_code = '$site' ";
            $WHERE_AND = "AND";
        }
        
        if($onlyLivePages)
        {
            $sql .= " $WHERE_AND pages_status = 'LIVE' ";
        }   
        $sql .= ' ORDER BY pages_site_code';
        
        return new Query($sql);
    }    
   
    /*
     * returns a class mapp array for current site
     */
    static public function  GetClassMapping ()
    {
        global $CONFIG;
        $site = $sitecode ? $sitecode : $CONFIG->site_code;
        
        $sql="SELECT * FROM pages WHERE pages_site_code = '$site' and pages_status = 'LIVE'";
        $pages=new Query($sql);
        
        $pageArray = array();
        foreach($pages as $p)
        {
            $pageArray[$p->pages_url] = $p->pages_php_class;        
        }
        return $pageArray;
    }
    
    /**
     * gets page details for current environment
     * @param String $site_code 
     * @param String $url
     * @param int $version [default = 0   returns the live version for the current environment ] 
     */
    static public function  GetPageDetails($site_code, $url, $version = 0)
    {
        global $CONFIG;
        $versionField  = $CONFIG->environment == 'LIVE' ? 'pages_live_version' : 'pages_proof_version';
       
        if($version == 0)
        {
            $sql="SELECT * FROM pages
                JOIN textfields on $versionField = textfields_version and textfields_table = 'pages'  
                WHERE pages_site_code = '$site' and pages_url = '$url' ";
        }
        else 
        {
             $sql="SELECT * FROM pages
                JOIN textfields on textfields_table = 'pages'  
                WHERE pages_site_code = '$site' and pages_url = '$url'  and textfields_version = $version";
        }
        $r = new Query($sql);
        $r->SetValue('pages_body', $r->textfields_body);
    }
    
    
     static public function  DeletePage($pk)
     {
         $sql="UPDATE pages SET pages_status='DELETED' WHERE pages_pk = $pk";
         return new Query($sql);
     }
    
    
    
    static public function sYaasCreateTarget($params)
    {
        if(! User::Authorize('ADMIN'))
        {
            return('unauthorized');
        }
        $page    = $params->targets_pages_fk;
        $content = $params->targets_contents_fk;
        $sql = "INSERT INTO targets (targets_pages_fk, targets_contents_fk) values($page, $content)";
        return new Query($sql);
    }
    
}

