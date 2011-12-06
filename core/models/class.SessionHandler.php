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
        return unserialize($r->sessions_data);
    }
    public static function sWrite($key, $data)
    {
        $db = self::$mSessionDB;
        $maxlifetime = 1800;  // 30 minutes  
        $data = serialize($data);
        $data = Query::Escape($data);
        $r = new Query("INSERT INTO {$db}.sessions (sessions_id, sessions_data, sessions_expires) 
                        VALUES('$key', '$data', DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $maxlifetime SECOND))
        				ON DUPLICATE KEY UPDATE sessions_data = '$data', sessions_expires = DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $maxlifetime SECOND) " );
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

