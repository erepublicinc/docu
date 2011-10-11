<?
class User
{
    private static $mInitialized = false;
    
    static function Init()
    {
        if(!self::$mInitialized)
        {
            session_start();
            self::$mInitialized = true;
        }
    }
    
    static function Login($email, $pw)
    {
       $sql = "SELECT pk, password, role_code, email 
                 FROM users 
                 LEFT JOIN roles ON users.pk = roles.users_fk                 
                 WHERE email = '$email' "; 
       
       $userdata = new Query($sql);
//die('asa'.$userdata->password);       
       if($userdata && $userdata->password == $pw)
       {         
                
           $_SESSION['user_email'] = $userdata->email;
           $_SESSION['user_password'] = $pw;
           $_SESSION['user_pk'] = $userdata->pk;
           $_COOKIE['user_email'] = $email;
           $_COOKIE['password'] = $pw;
           
           // get permissions and save them in the session object
           foreach($userdata as $u)
                 $permissions[] = $u->role_code ;
           $_SESSION['user_permissions'] = $permissions;       
           return true;
       }

       return false;
    }
    
    static function Authorize($permission = 'LOGGED_IN')
    {
        
       // print_r($_SESSION); die;
        // try to login using a cookie
        if(!isset($_SESSION['user_email']) && isset( $_COOKIE['password']))       
           Authorization::Login($_COOKIE['user_email'], $_COOKIE['password']);
        
        // still not logged in?  Redirect the user to the login page   
        if(!isset($_SESSION['user_email']))        
            header("LOCATION: /common/login.php");
        
        if($permission == 'LOGGED_IN') // we just want to make sure this person is logged in
            return true;
                
        if(in_array($permission, $_SESSION['user_permissions'])  ||  in_array('SUPER_ADMIN', $_SESSION['user_permissions'] ))
            return true;
            
        return false;
    }
    
    
    static function newUser($email, $pw, $fname, $lname)
    {
        $sql = "insert into users (email,password, first_name, last_name, user_active) 
                 values('$email', '$pw','$fname', '$lname', 1)";
        
        
        $userdata = new Query($sql);               
        return true;
    }
    
}

// initialize upon load
User::Init();

