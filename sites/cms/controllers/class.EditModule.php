<?php

class EditModule extends Controller
{
    
      
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'pages' 'new_page' 'page' ( the first one produces a list the other fo editing
     *          2:   [optional] id of the page 
     */
    public function __construct($routerObject, $arguments)
    {   
        global $CONFIG;    
        parent::__construct($routerObject, $arguments); 

        $site        = $CONFIG->cms_site_code;        
        $model_name  = $arguments[0];
        $id          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                     
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('model_name', $model_name);
                  
                      
        if(!empty($_POST['contents_title']))
        {
             $this->_SaveRecord($site,$model_name);
             return; //================================>
        }
        
        if($isNew || $id >0)
        {
            $this->_EditRecord($id);
            return;
        }
        
        $this->_ListRecords($site);
        return;
      
    }
   
    
    
    private function _SaveRecord($site,$model_name)
    {
        //dump($_POST);
        $m = new Module($_POST);
        $id = $m->Save();
       
        header("LOCATION: /cms/$site/$model_name");
        die;             
    }

    
    private function _EditRecord($id)
    {
        
        
        if($id == 0)
        { //die($_SESSION['user_first_name']);
            $m = new stdClass();
            $m->contents_create_date    = time(); //date();
           
            $this->mPageTitle = getSiteName($site) . " - New Module";
        } 
        else 
        {  
             $rev = intval($_GET['rev']) > 0 ?  intval($_GET['rev']): LATEST_REV ;
             $this->mPageTitle = getSiteName($site) . " - Edit Module";
             $m    = Module::GetDetails($id, $rev);    
        }
        

        // for the moduleUsageModule
        $pagelinks =  Module::GetPageLinks($id); 
        
        
        $this->mSmarty->assign('pageModuleLinks', $pagelinks);
        
        // for RevisionHistoyModule
        $this->mSmarty->assign('id', $id);
        $this->mSmarty->assign('live_rev',    $history->live_rev);
        $this->mSmarty->assign('preview_rev', $history->preview_rev);      
        $this->mSmarty->assign('history', $history);
        
        
//dump($pagelinks);
               
        $this->mSmarty->assign('content',$m); //NOTE   the Smarty var "page"  is already set as the current page
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'),                                      
                                        CMS::CreateRevisionHistoryModule($m), 
                                        CMS::CreateModuleUsageModule($id), 
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
        $this->mMainTpl = 'editModule.tpl';  
    }
    
    
    private function _ListRecords($site)
    { 
        $this->mPageTitle = getSiteName($site) . " - List Modules";
        
        if($_POST['makelive'])
        {
            Content::setLiveRevision(intval($_POST['id']), intval($_POST['rev']));
        }
        elseif($_POST['makepreview'])
        {
             Content::setPreviewRevision(intval($_POST['id']), intval($_POST['rev']));
        }
        
        $modules = Module::GetModules($site, TRUE);
       // dump($modules);
        //  foreach($arts as $a) echo $a->contents_id;     die;   
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


