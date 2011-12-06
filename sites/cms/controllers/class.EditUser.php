<?php

class EditUser extends WebPage
{
    
   
    
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'articles' 'new_article' 'article' ( the first one produces a list the other fo editing
     *          2:   [optional] pk of the article 
     */
    
   
    
    
    public function __construct($websiteObject, $arguments)
    {
        //dump($_POST);      
     
        global $CONFIG;     
        parent::__construct($websiteObject, $arguments); 
        
        $site    = $CONFIG->cms_site_code;
        $command = $arguments[0];
        $pk      = 0 + $arguments[1];       
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('record_type', 'users');
//dump(" site: $site  command: $command  pk:$pk") ;  
        
        if(!empty($_POST['users_email']))   // save user
        {
            $this->_saveUser($pk, $site);  
            return;         
        }
        
        if($command == 'users') // list users
        { 
            if($pk > 0)
                $this->_editUser($pk, $command);  // edit new or existing article   
            else    
                $this->_listUsers($site);  
            return;     
        }
        elseif($command == 'new_users')
             $this->_editUser($pk, $command); 
        
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
//dump($_POST);

        
        if($_POST['users_password'])
        {
            // send email
            
           $_POST['users_password'] =  md5($_POST['users_password']) ;
        }
        
        $u  = new User($_POST) ;        
        $pk = $u->Save();                   

        header("LOCATION: /cms/{$site}/users");
        die; 
    }
    
    
    private function _editUser($pk, $command)
    {
        if($command == 'new_users' || $pk == 0)  // new article
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
//dump($pk);
        
        // create the left side modules
        $this->mModules['left'] = array(
                                        CMS::CreateDummyModule('contentMediaModule.tpl'), 
                                        );
                                        
        $this->mMainTpl = 'editUser.tpl';  
           
        $this->mSmarty->assign('user',$user);
        $this->mSmarty->assign('roles', array("SUPER_ADMIN", "GT_EDITOR", "GOV_EDITOR", "WRITER", "BLOGGER") );
    }
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
