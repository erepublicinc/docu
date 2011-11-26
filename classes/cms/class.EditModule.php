<?php

class EditModule extends WebPage
{
    
      
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'pages' 'new_page' 'page' ( the first one produces a list the other fo editing
     *          2:   [optional] pk of the page 
     */
    public function __construct($websiteObject, $arguments)
    {   
        global $CONFIG;    
        parent::__construct($websiteObject, $arguments); 
        
        $site    = $CONFIG->cms_site_code;
        $command = $arguments[0];
        $pk      = 0 + $arguments[1]; 
//die("site: $site  command: $command  pk: $pk");  
   
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('record_type','module');
          
        if($command == 'modules')
        {
           $this->_ListModules($site);
            return; //================================>
        }
               
       
        if(!empty($_POST['contents_title']))
        {
             $this->_SaveModule();
             return; //================================>
        }
        
        $this->_EditModule($pk, $command);
        return;
      
    }
   
    
    
    private function _SaveModule()
    {
        //dump($_POST);
        $m = new Module($_POST);
        $pk = $m->Save();
       
        header("LOCATION: /cms/$site/modules");
        die;             
    }

    
    private function _EditModule($pk, $command)
    {
        
        
        if($command == 'new_module' || $pk == 0)
        { //die($_SESSION['user_first_name']);
            $m = new stdClass();
            $m->contents_main_author_fk = $_SESSION['user_pk'];
            $m->users_first_name        = $_SESSION['user_first_name'];
            $m->users_last_name         = $_SESSION['user_last_name'];
            $m->contents_create_date    = time(); //date();
            $history = array();
        } 
        else 
        {  
             $m    = Module::GetDetails($pk, LATEST_VERSION);
             $history = Content::GetVersionHistory($pk, "modules");    
             
        }
        

        // for the moduleUsageModule
        $pagelinks =  Module::GetPageLinks($pk); 
        
        
        $this->mSmarty->assign('pageModuleLinks', $pagelinks);
        
        // for versionHistoyModule
        $this->mSmarty->assign('pk', $pk);
        $this->mSmarty->assign('live_version',    $history->live_version);
        $this->mSmarty->assign('preview_version', $history->preview_version);      
        $this->mSmarty->assign('history', $history);
        
        
//dump($pagelinks);
               
        $this->mSmarty->assign('content',$m); //NOTE   the Smarty var "page"  is already set as the current page
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateDummyModule('selectSiteModule.tpl'), 
                                        CMS::CreateDummyModule('contentTypesModule.tpl'), 
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
        $this->mMainTpl = 'editModule.tpl';  
    }
    
    
    private function _ListModules($site)
    { 
        if($_POST['makelive'])
        {
            Content::setLiveVersion(intval($_POST['pk']), intval($_POST['version']));
        }
        elseif($_POST['makepreview'])
        {
             Content::setPreviewVersion(intval($_POST['pk']), intval($_POST['version']));
        }
        
        $modules = Module::GetModules($site, TRUE);
        //  foreach($arts as $a) echo $a->contents_pk;     die;   
        $this->mSmarty->assign('contents', $modules );
        
        // create the left side modules for this page
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateDummyModule('selectSiteModule.tpl'), 
                                        CMS::CreateDummyModule('contentTypesModule.tpl'), 
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
        $this->mMainTpl = 'listContent.tpl';
    }
    
     protected function _InitCaching(){
        $this->_mAllowCaching = false;
        $this->_mMainTplCaching = false;
     
     }
     protected function _InitPage(){}
   
}


