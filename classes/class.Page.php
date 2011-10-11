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
            $sql .= " WHERE site_code = '$site' ";
            $WHERE_AND = "AND";
        }
        
        if($onlyLivePages)
        {
            $sql .= " $WHERE_AND status = 'LIVE' ";
        }   
        $sql .= ' ORDER BY site_code';
        
        return new Query($sql);
    }    
   
    
    static public function sYaasCreateTarget($params)
    {
        if(! User::Authorize('ADMIN'))
        {
            return('unauthorized');
        }
       
        $sql = "INSERT INTO targets (pages_fk, contents_fk) values($params->page_pk, $params->content_pk)";
        return new Query($sql);
    }
    
}

