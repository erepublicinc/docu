<?

class Page 
{
    
    
    
    /**
     * Gets all pages default: only live pages for current site
     * @param bool $onlyLivePages
     * @param char $sitecode  "ALL" for all 
     */
    static public function getPages($onlyLivePages = true, $sitecode = null)
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

