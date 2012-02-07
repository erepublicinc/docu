<?
class UsageReports
{
    
     /**
     * we log all logins in the user_logins table
     * @param int $users_id
     * @parma int $account_id
     * @param string login method values: COOKIES, PASSWORD 
     * @param string site_code 
     */
    public static function LogLogin($id, $acc_id, $method, $site_code = '')
    {
        Query::SetAdminMode();
        $browser = Query::Escape($_SERVER['HTTP_USER_AGENT']); 
        $http    = Query::Escape($_SERVER['REMOTE_ADDR']);
        $port    = intval($_SERVER['REMOTE_PORT']); 
        $sql     = "INSERT INTO logins (logins_users_id, logins_accounts_id, logins_site_code, logins_method, logins_browser, logins_http_address, logins_http_port) 
                    VALUES($id, $acc_id, '$site_code', '$method', '$browser', '$http', $port)";
        
        return new Query($sql); 
    } 
    
    /**
     * Gets the logins for this user
     * @param int users_id
     * @param int limit the number of records default = 20
     * @return array of login objects
     */
    public static function GetLoginsByUser($id, $limit = 20, $skip = 0)
    {
        $id    = intval($id); 
        $skip  = intval($skip);
        $limit = intval($limit);
        return new Query( "SELECT * FROM user_logins WHERE users_id = $id ORDER BY login_date DESC LIMIT $skip, $limit");
    }
    
    // by site number per month last year , unique visitors
    // by id  number per month last year
    // by account
}
