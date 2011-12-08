<?php

class EditModule extends Controller
{
    
      
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'pages' 'new_page' 'page' ( the first one produces a list the other fo editing
     *          2:   [optional] pk of the page 
     */
    public function __construct($routerObject, $arguments)
    {   
        global $CONFIG;    
        parent::__construct($routerObject, $arguments); 

        $site        = $CONFIG->cms_site_code;        
        $record_type = $arguments[0];
        $pk          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                     
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('record_type', $record_type);
                  
                      
        if(!empty($_POST['contents_title']))
        {
             $this->_SaveModule($site,$record_type);
             return; //================================>
        }
        
        if($isNew || $pk >0)
        {
            $this->_EditModule($pk);
            return;
        }
        
        $this->_ListModules($site);
        return;
      
    }
   
    
    
    private function _SaveModule($site,$record_type)
    {
        //dump($_POST);
        $m = new Module($_POST);
        $pk = $m->Save();
       
        header("LOCATION: /cms/$site/$record_type");
        die;             
    }

    
    private function _EditModule($pk)
    {
        
        
        if($pk == 0)
        { //die($_SESSION['user_first_name']);
            $m = new stdClass();
            $m->contents_create_date    = time(); //date();
           
            $this->mPageTitle = getSiteName($site) . " - New Module";
        } 
        else 
        {  
             $this->mPageTitle = getSiteName($site) . " - Edit Module";
             $m    = Module::GetDetails($pk, LATEST_VERSION);    
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
                                        CMS::CreateVersionHistoryModule($m,'modules'), 
                                        CMS::CreateModuleUsageModule($pk), 
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
        $this->mMainTpl = 'editModule.tpl';  
    }
    
    
    private function _ListModules($site)
    { 
        $this->mPageTitle = getSiteName($site) . " - List Modules";
        
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
                                        CMS::CreateContentTypesModule(),  
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
        $this->mMainTpl = 'listContent.tpl';
    }
    
     protected function _InitCaching(){
        $this->_mAllowCaching = false;
        $this->_mMainTplCaching = false;
     
     }
     protected function _InitPage(){}
   
}


