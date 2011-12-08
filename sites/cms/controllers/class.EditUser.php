<?php

class EditUser extends Controller
{
    
   
    
    /**
     * @param Controller object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'articles' 'new_article' 'article' ( the first one produces a list the other fo editing
     *          2:   [optional] pk of the article 
     */    
    public function __construct($RouterObject, $arguments)
    {
        //dump($_POST);         
        global $CONFIG;     
        parent::__construct($RouterObject, $arguments); 
        
        $site        = $CONFIG->cms_site_code;
        $record_type = $arguments[0];
        $pk          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                 
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('record_type', $record_type);
        
        
        if(!empty($_POST['users_email']))   // save user
        {
            $this->_saveUser($pk, $site);  
            return;         
        }     
        
        if($isNew || $pk > 0)
        {
             $this->_editUser($pk);
             return; 
        }
        
        $this->_listUsers($site);       
        return;      
    }

    
    private function _listUsers($site)
    {       
        $users = User::GetUsers();
        $this->mSmarty->assign('users', $users );

        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateContentTypesModule(),  
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
                                        
        $this->mMainTpl   = 'listUsers.tpl';
        $this->mPageTitle = getSiteName($site) . " - List Users";
    }

    
    private function _saveUser($pk, $site)
    {
        
        if($_POST['users_password'])
        {
            // send email
            
           $_POST['users_password'] =  md5($_POST['users_password']) ;
        }
        
        // create an array of roles
        $roles = array();        
        foreach($_POST as $key => $value)
        {
            $pos = stripos($key, "user_role_");
            if($pos === 0)
            {
                $roles[] = substr($key, $pos+10);   
            }
        }               
        $_POST['roles'] = $roles;
//dump($_POST);       
        $u  = new User($_POST) ;        
        $pk = $u->Save();                   

        header("LOCATION: /cms/{$site}/users");
        die; 
    }
    
    
    private function _editUser($pk)
    {
        if($pk == 0)  // new user
        { //die($_SESSION['user_first_name']);
            $this->mPageTitle = getSiteName($site) . " - New User";
            $user = new stdClass();
            $user->users_active = 1;
        } 
        else // edit existing article
        {
            $this->mPageTitle = getSiteName($site) . " - Edit User";
            $user = User::GetDetails($pk);                        
        }
//dump($user);
        
        // create the left side modules
        $this->mModules['left'] = array(
                                        CMS::CreateDummyModule('contentMediaModule.tpl'),
                                        CMS::CreateListauthorsModule($pk)
                                        );
                                        
        $this->mMainTpl = 'editUser.tpl';  

        
        
        $roles = array(
            "SUPER_ADMIN" => 0,
            "GT_EDITOR" => 0,
            "GOV_EDITOR" => 0,
            "WRITER" => 0,
            "BLOGGER" => 0
        );
      
        foreach($roles as $key=>$value)
        {
            if( stripos($user->users_roles, $key ) !== false)
                $roles[$key] = 1;
        }
        
        $this->mSmarty->assign('user',$user);
        $this->mSmarty->assign('roles', $roles );
    }
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
