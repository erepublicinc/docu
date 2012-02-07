<?
// we need this to be included BEFORE the User class is initiated (that's where start_session is being called)
class SessionHandler
{  
    static private $mSessionDB; 
    static private $mDefaultDB; 
    
    public static function sRead($key)
    { 
        $db = self::$mSessionDB;
        $r = new Query("SELECT * FROM {$db}.sessions WHERE sessions_id = '$key' AND sessions_expires > CURRENT_TIMESTAMP");  
        Query::changeDB(self::$mDefaultDB);  // change it back                      
        return $r->sessions_data ; //unserialize($r->sessions_data);
    }
    
    
    public static function sWrite($key, $data)
    { 
        $db          = self::$mSessionDB;
        $maxlifetime = 1800;  // 30 minutes  
        $users_id    = intval($_SESSION['user_id']);
        $site_code   = $_SESSION['site_code'];
        $data        = Query::Escape($data);
        $r = new Query("INSERT INTO {$db}.sessions (sessions_id, sessions_data, sessions_expires, sessions_users_id, sessions_site_code) 
                        VALUES('$key', '$data', DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $maxlifetime SECOND), $users_id , '$site_code')
        				ON DUPLICATE KEY UPDATE sessions_data = '$data', sessions_expires = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $maxlifetime SECOND), sessions_users_id = $users_id, sessions_site_code = '$site_code' " );
        return true;
    }
    
    
    public static function sDestroy($key)
    { 
        $db = self::$mSessionDB;
        $r = new Query("DELETE FROM {$db}.sessions WHERE session_key = '$key'"); 
        return true;
    }
    
    
    public static function sOpen()
    {  
        global $CONFIG;
        self::$mSessionDB = $CONFIG->session_dbname;
        self::$mDefaultDB = $CONFIG->db_dbname;
        return TRUE;
    }
    
    
    public static function sClose()
    { return TRUE;  }  
    
    
    public static function sGc()
    { 
        $db = self::$mSessionDB;
        return new Query("DELETE FROM {$db}.sessions WHERE sessions_expires < CURRENT_TIMESTAMP");
        Query::changeDB(self::$mDefaultDB);  // change it back k        
    }       
}
session_set_save_handler(array('SessionHandler', 'sOpen'),  array('SessionHandler', 'sClose'),   array('SessionHandler', 'sRead'),
                         array('SessionHandler', 'sWrite'), array('SessionHandler', 'sDestroy'), array('SessionHandler', 'sGc') );

