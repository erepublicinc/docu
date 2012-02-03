<?

// this needs to be run before the user class is initiated
require_once('class.SessionHandler.php');

class User
{
    public static $errorMessage;  // holds the error massage
    private static $mInitialized = false;
    protected $mFields; // the object with all the fields
    
    static function Init()
    {
        if(!self::$mInitialized)
        {
            session_start();
            self::$mInitialized = true;
        }
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
            if($this->mFields->roles)
            {
                self::SetRoles($r->id, $this->mFields->roles);
            }
        	return $r->id ;   // success
        }                  
        return false;
    }
    
    public static function GetDetails($id)
    {
        $sql = "SELECT users_id, users_last_name, users_first_name, users_email,  users_active, roles_code, users_notes
                FROM users LEFT JOIN roles ON users_id = roles_users_id WHERE users_id = $id";
        $result = new Query($sql);
        
        $roles = '';
        foreach($result as $r)
        {
            $roles .= "$r->roles_code,";
        }
        $roles = rtrim($roles,',');
        
        $result->rewind();   // set the value on the first record
        $result->SetValue('users_roles',  $roles);
        
        return $result;
    }
    
    public static function GetUsers()
    {
        $sql = "SELECT users_id, users_last_name, users_first_name, users_email, users_active
                FROM users ";
        return new Query($sql);
    }
    
    /**
     * sets the users roles to the set of roles being supplied, any others are removed
     * @param int id
     * @param array of $roles
     */
    static function SetRoles($id, $roles)
    {
    	$id = intval($id);
    	if($id <= 0)
    		return logerror("User:SetRoles() invalid id");
    	if(! is_array($roles))	 
    	    return logerror("User:SetRoles() invalid roles array");

    	$sql = array();
    	$sql[] = "DELETE from roles where roles_users_id = $id";
    	foreach($roles as $role)
    	{
    		$erole = Query::Escape($role);
    		$sql[] = "INSERT INTO roles (roles_users_id,roles_code) VALUES($id, '$erole')";
    	}    
    	$return = Query::sTransaction($sql);
    }
    
    static function Login($email, $pw, $remember_me = false,  $method = 'PASSWORD')
    { 
       global $CONFIG;
       
       $sql = "SELECT *  FROM users 
                 LEFT JOIN roles ON users_id = roles_users_id                 
                 WHERE users_email = '$email'"; 
       
       $userdata = new Query($sql);
            
       if($userdata && $userdata->users_password == $pw)
       {     
           if($userdata->users_active != 1)
           {
               self::$errorMessage = "user: $email  account is inactive";
                  return false; 
           }
                          
           $_SESSION['user_email'] = $userdata->users_email;
           $_SESSION['user_password'] = $pw;
           $_SESSION['user_id'] = $userdata->users_id;
           $_SESSION['user_first_name'] = $userdata->users_first_name;
           $_SESSION['user_last_name']  = $userdata->users_last_name;
           $_SESSION['user_acccounts_id']  = $userdata->users_accounts_id;
           
           if($remember_me)
           {
               $_COOKIE['user_email'] = $email;
               $_COOKIE['password'] = $pw;
           }
           
           // get permissions and save them in the session object
           foreach($userdata as $u)
                 $permissions[] = $u->roles_code ;
           $_SESSION['user_permissions'] = $permissions; 
           
           // when the user comes from a login screen the site_code was stored in the session
           $site_code = $CONFIG->site_code;
           if(empty($site_code))
               $site_code = $_SESSION['site_code'];
           $userdata->rewind();
           UsageReports::LogLogin($userdata->users_id, $userdata->users_accounts_id, $method, $site_code);      
           return true;
       }
       
       self::$errorMessage = "email / password combination not valid"; 
       return false;
    }
    
    /**
     * This is the only call you have to make when you want to authorize a user
     * it will also try to log the user in when he is not logged in 
     * @param string Permission 
     * @param bool login_screen [default = true , will send user to login screen]
     * @return bool    true if the user is authorized
     */
    static function Authorize($permission = 'LOGGED_IN', $login_screen = true)
    {
        global $CONFIG;
                 
        // If you are not logged in, try to login using a cookie
        if(!isset($_SESSION['user_email']) && !empty($_COOKIE['password']))       
           self::Login($_COOKIE['user_email'], $_COOKIE['password'], true, 'COOKIES');
 
        // Are you now logged in?   
        if( empty($_SESSION['user_email'])){
            if(login_screen)
            { 
                $_SESSION['site_code'] = $CONFIG->site_code;       
                header("LOCATION: /common/login.php?redirect=".$_SERVER['REQUEST_URI'] . "&site_code={$CONFIG->site_code}");
            }
            else 
                self::$errorMessage = "user  is not logged in";    
            return false;
        }
        else { // yes, you are logged in so lets check your permissions
            
            if($permission == 'LOGGED_IN') // we just want to make sure this person is logged in
                return true;
                    
            if(in_array($permission, $_SESSION['user_permissions'])  ||  in_array('SUPER_ADMIN', $_SESSION['user_permissions'] ))
                return true;
        }
        
        self::$errorMessage = "user: {$_SESSION['user_email']} does not have the proper permissions";
        return false;
    }   
    
 
}

// initialize upon load
User::Init();

