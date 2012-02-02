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
    
    static function Login($email, $pw)
    {
        
       $sql = "SELECT *
                 FROM users 
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
           $_COOKIE['user_email'] = $email;
           $_COOKIE['password'] = $pw;
           
           // get permissions and save them in the session object
           foreach($userdata as $u)
                 $permissions[] = $u->roles_code ;
           $_SESSION['user_permissions'] = $permissions;       
           return true;
       }
       
       self::$errorMessage = "email / password combination not valid"; 
       return false;
    }
    
    static function Authorize($permission = 'LOGGED_IN')
    {
 
        // try to login using a cookie
        if(!isset($_SESSION['user_email']) && !empty($_COOKIE['password']))       
           Authorization::Login($_COOKIE['user_email'], $_COOKIE['password']);
 
        // still not logged in?  Redirect the user to the login page   
        if( empty($_SESSION['user_email'])){        
            header("LOCATION: /common/login.php?redirect=".$_SERVER['REQUEST_URI']);
            die;
        }
        if($permission == 'LOGGED_IN') // we just want to make sure this person is logged in
            return true;
                
        if(in_array($permission, $_SESSION['user_permissions'])  ||  in_array('SUPER_ADMIN', $_SESSION['user_permissions'] ))
            return true;
            
        return false;
    }   
    
    /**
     * we log all logins in the user_logins table
     * @param int $users_id
     */
    private static function LogLogin($id)
    {
        $sql[] = "INSERT INTO user_logins (users_fid, users_password, users_first_name, users_last_name, users_active, users_ad_user, users_notes) 
                 values('$email', '  )";
        
        return new Query($sql);
    } 
    
    
}

// initialize upon load
User::Init();

