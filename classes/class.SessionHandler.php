<?
/**
 * Stores all sessions in a MySQL database
 *
 * This class should be auto-prepended. It will override the default behavior of
 * storing sessions in files, and will instead store them in a MySQL database.
 *
 * Note: This file must be kept in sync between nodebase and legacy2!
 *
 * @author Joel Barker <jbarker@erepublic.com>
 * @version 7.12.12
 */
class SessionHandler
{
    /**
     * Database connection resource
     *
     * This variable stores the connection resource for all database calls,
     * in order to avoid conflict with other database connections.
     */
    private static $_msConnection = null;


    /**
     * Enable debugging
     */
    private static $_msDebug = false;


    /**
     * Connect to the database
     *
     * @return          Always returns null
     *
     * This function will be called when needed to create a database connection. It will die() on error.
     */
    private static function _sConnect()
    {
        if (self::$_msConnection)
        {
            return null;
        }

        $liveHosts = array('arachne.erepublic.com', 'arachne2.erepublic.com', 'arachne3.erepublic.com', 'bids.erepublic.com');

        $hostname = !empty($_ENV['HOSTNAME']) ? $_ENV['HOSTNAME'] : php_uname("n");

        $server = 'arachne.erepublic.com';

        if (!in_array($hostname, $liveHosts))
        {
            $server = 'epimetheus.erepublic.com';
        }
//$server = 'arachne.erepublic.com';  //emergency fix mtel 7/22/2011

        self::$_msConnection = mysql_connect($server, 'webuser', 'webapp123');

        if (! self::$_msConnection)
        {
            die('Could not connect: ' . mysql_error());
        }

        $result = mysql_select_db('site_sessions', self::$_msConnection);
        if (! $result)
        {
            die('Could not select database.');
        }

        return null;
    }


    /**
     * Log a debug message via error_log(), if debugging is enabled
     *
     * @param $msg      A debugging message
     *
     * @return          Always returns null
     */
    private static function _sDebug($msg)
    {
        if (self::$_msDebug)
        {
            error_log($msg);
        }

        return null;
    }


    /**
     * Open the session
     *
     * @param $save_path        The session.save_path from php.ini
     * @param $session_name     The session name (usually 'PHPSESSID')
     *
     * @return                  Always returns true
     *
     * This function does nothing, since the database connection will only be made when it is actually needed.
     */
    public static function sOpen($save_path, $session_name)
    {
        self::_sDebug(__FUNCTION__);
        return true;
    }


    /**
     * Close the session
     *
     * @return      Always returns true
     *
     * This function will be called when a script ends, or needs to explicitly close a
     * session. Though it's probably unnecessary, we'll close the database connection.
     */
    public static function sClose()
    {
        if (self::$_msConnection)
        {
            mysql_close(self::$_msConnection);
            self::$_msConnection = null;
        }

        self::_sDebug(__FUNCTION__);
        return true;
    }


    /**
     * Read the session from the database
     *
     * @param $key      The session key
     *
     * @return          The session data, or an empty string if no data or database error
     */
    public static function sRead($key)
    {
        self::_sConnect();

        $sql    = "SELECT * FROM sessions WHERE session_key = '$key'";
        $result = mysql_query($sql, self::$_msConnection);

        self::_sDebug(__FUNCTION__ . ': ' . $key);

        if ($result && mysql_num_rows($result))
        {
            $row = mysql_fetch_assoc($result);
            return $row['session_data'];
        }
        else
        {
            return '';
        }
    }

    /**
     * Write the session into the database
     *
     * @param $key      The session key
     * @param $data     The session data
     *
     * @return          True on success, false on database error
     *
     * note that all time calculations are done in SQL, to keep all sessions on the same clock.
     */
    public static function sWrite($key, $data)
    {
        self::_sConnect();

        // user pk format:  ul_suser|s:6:"264250"
        $idx= stripos($data,'ul_suser|') ;
        if($idx === false)
        {
                $userPk = 0;
        }
        else
        {
            $idx= stripos($data,'"',$idx)+1 ;
            $idx2=stripos($data,'"',$idx) ;
            $userPk = substr($data, $idx, $idx2-$idx);
            $userPk = 0 + intval($userPk);
        }
        $key  = mysql_real_escape_string($key, self::$_msConnection);
        $data = mysql_real_escape_string($data, self::$_msConnection);

        $maxlifetime = 1800;  // 30 minutes  

        if ('' == $data)
        {
            $sql = "DELETE FROM sessions
                    WHERE session_key = '$key'";
        }
        else
        {
            $pieces = explode(".", str_replace('.com', '', $_SERVER['HTTP_HOST']));
            $mServer = array_shift($pieces);
            $mServer = str_replace("secure", "www", $mServer);
            $mSite = implode('.',$pieces);
            $siteCode = "$mServer.$mSite.com";

            // session_key must be PRIMARY or UNIQUE
            $sql = "REPLACE sessions
                    (session_key, session_data, session_expires, user_pk, site_code)
                    VALUES
                    ('$key', '$data', DATE_ADD(CURRENT_TIMESTAMP, INTERVAL $maxlifetime SECOND), $userPk, '$siteCode')";
        }




        $result = mysql_query($sql, self::$_msConnection);

        self::_sDebug(__FUNCTION__ . ': ' . $key);
        return (bool)$result;
    }


    /**
     * Deletes the session from the database
     *
     * @param $key      The session key
     *
     * @return          True on success, false on database error
     */
    public static function sDestroy($key)
    {
        self::_sConnect();

        $key    = mysql_real_escape_string($key, self::$_msConnection);
        $sql    = "DELETE FROM sessions WHERE session_key = '$key'";
        $result = mysql_query($sql, self::$_msConnection);

        self::_sDebug(__FUNCTION__ . ': ' . $key);
        return (bool)$result;
    }


    /**
     * Garbage collection: deletes old sessions from the database
     *
     * @param $maxlifetime      The allowed max life time for sessions, in seconds
     *
     * @return                  True on success, false on database error
     *
     * Note that we ignore the $maxlifetime here; @see sWrite for the rationale.
     *
     * Also note that all time calculations are done in SQL, to keep all sessions on the same clock.
     */
    public static function sGc($maxlifetime)
    {
        self::_sConnect();

        $sql    = "DELETE FROM sessions WHERE session_expires < CURRENT_TIMESTAMP";
        $result = mysql_query($sql, self::$_msConnection);

        return (bool)$result;
    }

    /*
     * Checks if this user is logged on on another computer
     * used by Authentication::Login()
     */
    public static function sCheckForConcurrentSessions($key, $pk)
    {
        self::_sConnect();
        $siteCode = $_SERVER['SERVER_NAME'];

        $ret = false;

        $sql = "SELECT * FROM sessions
                WHERE user_pk = '$pk'
                AND session_expires > CURRENT_TIMESTAMP
                AND site_code = '$siteCode'
                AND session_key != '$key' ";

        $result = mysql_query($sql, self::$_msConnection);
        if ($result && mysql_num_rows($result))
        {
            $ret = true;
        }

        self::sClose();

        return $ret;
    }

    public static function sTerminateConcurrentSessions($key, $userPk)
    {
        self::_sConnect();

        $siteCode = $_SERVER['SERVER_NAME'];

        $sql = "DELETE FROM sessions
                WHERE user_pk = '$userPk'
                AND session_key != '$key'
                AND site_code = '$siteCode'  ";
        $result = mysql_query($sql, self::$_msConnection);

        self::sClose();

        return $ret;
    }

    public static function sTerminateSession($key, $userPk)
    {
    	self::_sConnect();

    	$siteCode = $_SERVER['SERVER_NAME'];

        $sql = "DELETE FROM sessions
                WHERE user_pk = '$userPk'
                AND session_key = '$key'
                AND site_code = '$siteCode'  ";
        $result = mysql_query($sql, self::$_msConnection);

        self::sClose();

        return $result;
    }
}


session_set_save_handler(
    array('SessionHandler', 'sOpen'),
    array('SessionHandler', 'sClose'),
    array('SessionHandler', 'sRead'),
    array('SessionHandler', 'sWrite'),
    array('SessionHandler', 'sDestroy'),
    array('SessionHandler', 'sGc')
);

