<?

// this needs to be run before the user class is initiated
require_once('class.SessionHandler.php');

class User
{
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
    {
    	$email = Query::Escape($this->mFields->users_email);
    	$pw    = Query::Escape($this->mFields->users_password);
    	$lname = Query::Escape($this->mFields->users_first_name);
    	$fname = Query::Escape($this->mFields->users_last_name);
    	$ad    = Query::Escape($this->mFields->users_ad_user);
    	$active = intval($this->mFields->users_active);
    	$pk    = intval($this->mFields->users_pk);
    	
    	$sql = array();
    	if($pk == 0)
    	{
        	$sql[] = "insert into users (users_email, users_password, users_first_name, users_last_name, users_active, users_ad_user) 
                 values('$email', '$pw','$fname', '$lname', $active, '$ad')";
        	$sql[] = "SELECT LAST_INSERT_ID() as pk";
    	}
        else 
        {
        	$sql[] = "UPDATE users SET users_email = '$email', users_first_name = '$fname', users_last_name = '$lname' users_password = '$pw',
        			users_ad_user = '$ad', users_active = $active WHERE users_pk = $pk";
        	$sql[] = "SELECT $pk as pk";
        }
        
        $r = Query::sTransaction($sql);
        if($r)
        	return $r->pk ;   // success                  
        return false;
    }
    /**
     * sets the users roles to the set of roles being supplied, any others are removed
     * @param int pk
     * @param array of $roles
     */
    static function SetRoles($pk, $roles)
    {
    	$pk = intval($pk);
    	if($pk <= 0)
    		return logerror("User:SetRoles() invalid pk");
    	if(! is_array($roles))	 
    	    return logerror("User:SetRoles() invalid roles array");

    	$sql = array();
    	$sql[] = "DELETE from roles where roles_user_fk = $pk";
    	foreach($roles as $role)
    	{
    		$erole = Query::Escape($role);
    		$sql[] = "INSERT INTO roles (roles_users_fk,roles_code) VALUES($pk, '$erole')";
    	}    
    }
    
    static function Login($email, $pw)
    {
        
       $sql = "SELECT *
                 FROM users 
                 LEFT JOIN roles ON users_pk = roles_users_fk                 
                 WHERE users_email = '$email' "; 
       
       $userdata = new Query($sql);
//die('asa'.$userdata->password);       
       if($userdata && $userdata->users_password == $pw)
       {         
                
           $_SESSION['user_email'] = $userdata->users_email;
           $_SESSION['user_password'] = $pw;
           $_SESSION['user_pk'] = $userdata->users_pk;
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
    
}

// initialize upon load
User::Init();

