<?
/*
issues to be addressed:
1. dont allow multiple logins for the same user
2. when a person closes his browser (loosing his session cookie) and opening it again when his session is still open, use this old session
   to avoid that this is seen as a separate computer .   Do this by sessing a cookie with a half hour lifespan, and keep this updated !

3. allow transfer from another domain ( navigatorgov to ed)  while reusing the session, (use the url parameter SAVED_SESSION to transfer the cookie)

4. allow login-as 
5. redirect to Ingram once a day





 */
// this needs to be run before the user class is initiated
require_once('class.SessionHandler.php');

class NavUser
{
    
    public static $errorMessage;          // holds the error massage
    
    private static $mInitialized = false;
    protected $mFields; // the object with all the fields
    
    static function Init()
    {       
        if(self::$mInitialized)
            return;            
              
        if(! empty($_REQUEST['SAVED_SESSION']))
            session_id($_REQUEST['SAVED_SESSION']);
       
        $ret = session_start();

        // this cookie is used when the person closed his browser and opened it again (and lost his session cookie)
        // putting it here will make sure it gets refreshed with every page load
        // the session lasts 25 minutes so this cookie lasts longer, so we can be sure that if there is a session open we reuse it
        // so we don't get a "terminate other session" msg
        $result = setcookie('SAVED_SESSION', session_id() , time() + THIRTY_MINUTES, '/');
        self::$mInitialized = true;     
    }
    
    public function __construct($params)
    {
    	if(is_array($params))
            $params = (object) $params;
            
    	$this->mFields = $params;
    }
    
    public function Save()
    { //dump($this->mFields);
    	$email = Query::Escape($this->mFields->users_email);
    	
    	$pw    = "";
    	$fname = Query::Escape($this->mFields->users_first_name);
    	$lname = Query::Escape($this->mFields->users_last_name);
    	$ad    = Query::Escape($this->mFields->users_ad_user);
    	$notes    = Query::Escape($this->mFields->users_notes);
    	
    	$active = empty($this->mFields->users_active)? 0: 1;
    	$id    = intval($this->mFields->users_id);
    	
    	$sql = array();
    	if($id == 0)
    	{
    	    $pw    =  Query::Escape($this->mFields->users_password);
        	$sql[] = "INSERT INTO users (users_email, users_password, users_first_name, users_last_name, users_active, users_ad_user, users_notes) 
                 values('$email', '$pw','$fname', '$lname', $active, '$ad', '$notes')";
        	$sql[] = "SELECT LAST_INSERT_ID() as id";
    	}
        else 
        {
            if(!empty($this->mFields->users_password)) 
        	    $pw    = " users_password = '". Query::Escape($this->mFields->users_password)."', ";
        	
        	$sql[] = "UPDATE users SET users_email = '$email', users_first_name = '$fname', users_last_name = '$lname', users_notes = '$notes', $pw
        			users_ad_user = '$ad', users_active = $active WHERE users_id = $id";
        	$sql[] = "SELECT $id as id";
        }
        
        $r = Query::sTransaction($sql);
       
        if($r)
        {
            if($this->mFields->groups)
            {
                self::SetGroups($r->id, $this->mFields->groups);
            }
        	return $r->id ;   // success
        }                  
        return false;
    }
    
    public static function GetDetails($id)
    {
        $sql = "SELECT users_id, users_last_name, users_first_name, users_email,  users_active, users_notes, usergroups_fcode
                FROM users LEFT JOIN users_x_usergroups ON users_id = users_fid WHERE users_id = $id";
        $result = new Query($sql);
        
        $groups = '';
        foreach($result as $r)
        {
            $groups .= "$r->usergroups_fcode,";
        }
        $groups = rtrim($groups,',');
        
        $result->rewind();   // set the value on the first record
        $result->SetValue('users_groups',  $groups);
        
        return $result;
    }
    
    public static function GetUsers()
    {
        $sql = "SELECT users_id, users_last_name, users_first_name, users_email, users_active
                FROM users ";
        return new Query($sql);
    }
    
    /**
     * sets the users groups to the set of groups being supplied, any others are removed
     * @param int id
     * @param array of $groups
     */
    static function SetGroups($id, $groups)
    {
    	$id = intval($id);
    	if($id <= 0)
    		return logerror("User:SetGroups() invalid id");
    	if(! is_array($groups))	 
    	    return logerror("User:SetGroups() invalid groups array");

    	$sql = array();
    	$sql[] = "DELETE from users_x_usergroups where users_fid = $id";
    	foreach($groups as $group)
    	{
    		$egroup = Query::Escape($group);
    		$sql[] = "INSERT INTO users_x_usergroups (users_fid,usergroups_fcode) VALUES($id, '$egroup')";
    	}    
    	$return = Query::sTransaction($sql);
    }

    
    
    
    static function Login($email, $pw, $remember_me = false,  $method = 'PASSWORD')
    { 
       global $CONFIG;
       $site_code = $CONFIG->site_code;
       self::$errorMessage = '';

       $sql =  "SELECT *  FROM users 
                LEFT JOIN users_x_usergroups ON users_id = users_fid    
                LEFT JOIN accounts ON accounts_id = users_accounts_fid
                LEFT JOIN licenses ON  licenses_accounts_fid = users_accounts_fid and licenses_site_code = '$site_code'
                WHERE users_email = '$email' ";
                 
       $userdata = new Query($sql);

       // check password
       if(! ( $userdata && $userdata->users_password == $pw ))
       {
           UsageReports::LogLogin($userdata->users_id, $userdata->users_accounts_fid, "PASSWORD_INVALID", $site_code);      
           self::$errorMessage = "email / password combination not valid"; 
           return false; 
       }   

       // check if user is active
       if($userdata->users_active != 1)
       {
           self::$errorMessage = "user: $email  account is inactive";
           return false; 
       }

       //check account status   
       if( ! in_array( $userdata->licenses_status, array('ACTIVE','RENEWAL','TRIAL')) )
       {  
          self::$errorMessage = "account status: $userdata->licenses_status";       
          return false;       
       }
       
       // we don't allow concurrent use
       if( self::check4ConcurrentSessions($userdata->users_id, $site_code))      
           return false;
                    
       self::doIngramMagic($userdata);
           
       self::writeSessionData($userdata);
                 
       if($remember_me)
       { 
           setcookie('user_email', $email , time() + YEAR_IN_SECONDS, '/');
           $result = setcookie('user_password', $pw , time() + YEAR_IN_SECONDS, '/');
           if(! $result)
               self::$errorMessage = "could not set cookie";
       }
         
                              
       $userdata->rewind();
       UsageReports::LogLogin($userdata->users_id, $userdata->users_accounts_fid, $method, $site_code);      
       
       if(! empty(self::$errorMessage ))
            die(self::$errorMessage );
            
       return true;
       
    }
    
    
    static private function writeSessionData($userdata)
    {
       global $CONFIG;
       $_SESSION['user_email']      = $userdata->users_email;
//       $_SESSION['user_password']   = $userdata->users_password;  do I need this ?
       $_SESSION['user_id']         = $userdata->users_id;
       $_SESSION['user_first_name'] = $userdata->users_first_name;
       $_SESSION['user_last_name']  = $userdata->users_last_name;
       $_SESSION['user_acccounts_id']  = $userdata->users_accounts_fid;
       
       $_SESSION['site_code']       = $CONFIG->site_code;
       // get usergroups and save them in the session object
       foreach($userdata as $u)
             $groups[] = $u->usergroups_fcode ;
       $_SESSION['user_groups'] = $groups; 
       
    }
    
     
    static public function loginAsOtherUser($email)
    {
        $sql = "SELECT *  FROM users 
                 LEFT JOIN users_x_usergroups ON users_id = users_fid                 
                 WHERE users_email = '$email'"; 
       
        $userdata = new Query($sql);
        
        self::writeSessionData($userdata);
        $_SESSION['site_code'] = 'LOGIN_AS';  // this is to avoid the terminate other session
    }
    
    
    static private function check4ConcurrentSessions($users_id, $site_code)
    {
       if(SessionHandler::sCheckForConcurrentSessions(session_id(), $users_id, $site_code))
       { 
           if($_SESSION['concurrent_session'] == 'terminate')
           {
               SessionHandler::sTerminateConcurrentSessions(session_id(), $users_id, $site_code); 
               unset($_SESSION['concurrent_session']);
           }
           else 
           {
               self::$errorMessage = "Concurrent session for user: $email ";
               $_SESSION['concurrent_session'] = 'found';
               return true;
           } 
       }  
       return false ;
    }
    
    
    /**
     * This is the only call you have to make when you want to authorize a user
     * it will also try to log the user in when he is not logged in 
     * @param string Permission / user groups
     * @param bool login_screen [default = true , will send user to login screen]
     * @return bool    true if the user is authorized
     */
    static function Authorize($group = 'LOGGED_IN', $login_screen = true)
    {
        global $CONFIG;
                 
        // If you are not logged in, try to login using a cookie
        if(!isset($_SESSION['user_email']) && !empty($_COOKIE['user_password']))       
           self::Login($_COOKIE['user_email'], $_COOKIE['user_password'], true, 'COOKIES');
 
        // Are you now logged in?   
        if( empty($_SESSION['user_email']))
        { // NO
            if($login_screen)
            { 
                if(! empty($CONFIG->site_code))
                    $_SESSION['site_code'] = $CONFIG->site_code;
                $_SESSION['redirect'] = $_SERVER['REQUEST_URI'];
                    
                $loc = "http://".$_SERVER['SERVER_NAME']."/login.php?SAVED_SESSION=".session_id();               
                header("LOCATION: $loc");
            }
            else
            { 
                self::$errorMessage = "user  is not logged in";
            }    
            return false;
        }
        else 
        { // YES, you are logged in so lets check your groups
            
            if($group == 'LOGGED_IN') // we just want to make sure this person is logged in
                return true;
                    
            if(in_array($group, $_SESSION['user_groups'])  ||  in_array('SUPER_ADMIN', $_SESSION['user_groups'] ))
                return true;
        }
        
        self::$errorMessage = "user: {$_SESSION['user_email']} does not have the proper permissions";
        return false;
    }   
    
 
    
    public function logout()
    {
    	global $CONFIG;
        SessionHandler::sTerminateConcurrentSessions(session_id(), $_SESSION['user_id'], $CONFIG->site_code); 
        
        $cookie_deathtime  = time() - 1000;
       
        foreach(array_keys($_COOKIE) as $cookie_name)
        {
            if ($cookie_name != self::IMK_NAME && strpos($cookie_name, "__") === false)          
                setcookie($cookie_name, '', $cookie_deathtime, '/');           
        }
       
        session_destroy();

        $_SESSION = NULL;
        $_COOKIE  = NULL;

        return;
    }
    
    
    private function doIngramMagic($userdata)
    {
        $INGRAM_MICRO_URL  = 'http://www.im-publicsector.com/navigator.html';
        $INGRAM_MICRO_ID   = 484388;
         
        if ($INGRAM_MICRO_ID != $userdata->users_accounts_fid)
        {
            return;
        }

        $navImkKey = md5(date('Y-n-j'));

        if ($_REQUEST['imk'] != $navImkKey )
        {
            //$params = Environment::GetNavDomainPrefix() !== 'www' ? '?imkTest=1' : '';
            //header('location: '.self::INGRAM_MICRO_URL.$params);
            header('location: '.$INGRAM_MICRO_URL);
            exit();
        }

        if (isset($_REQUEST['imk']))
        {
            setcookie('imk', $_REQUEST['imk'], time() + DAY_IN_SECONDS, '/');
        }

        return TRUE;
    }
    
}

// initialize upon load
NavUser::Init();

