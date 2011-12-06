<?php

class EditPage extends WebPage
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
//dump($_POST);
   
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('record_type','page');
           
        if($command == 'pages')
        {
           $this->_ListPages($site);
            return; //================================>
        }
        
       
        if(!empty($_POST['pages_title']))
        {
             $this->_SavePage($site);
             return; //================================>
        }
        
        $this->_EditPage($pk, $command);
        return;      
    }
   
    
    
    private function _SavePage($site)
    {
        $p = new Page($_POST);
        $pk = $p->Save();
        Module::LinkModules($pk, json_decode($_POST['json_module_data']));
       
        header("LOCATION: /cms/$site/pages");
        die;             
    }

    
    private function _EditPage($pk, $command)
    {
        
        
        if($command == 'new_page' || $pk == 0)
        { //die($_SESSION['user_first_name']);
              $this->mPageTitle = getSiteName($site) . " - New Page";
            
            $page = new stdClass();
            $page->pages_authors_fk     = $_SESSION['user_pk'];
            $page->users_first_name     = $_SESSION['user_first_name'];
            $page->users_lastname       = $_SESSION['user_last_name'];
            $history = array();
        } 
        else 
        {  
             $this->mPageTitle = getSiteName($site) . " - Edit Page";
             $page    = Page::GetDetails($pk);       
        }
        
        $this->mSmarty->assign('p',$page);     //NOTE: the Smarty var "page"  is already set as the current page

       // create the center module
        $this->mModules['center'] = array(CMS::CreateModule2PageModule($pk));
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                       // CMS::CreateDummyModule('selectSiteModule.tpl'), 
                                       // CMS::CreateDummyModule('contentTypesModule.tpl'),                                        
                                         CMS::CreatePageHistoryModule($page->pages_id),
                                         CMS::CreateDummyModule('recentlyModifiedModule.tpl'));
        
        $this->mMainTpl = 'editPage.tpl';  
    }
    
    
    private function _ListPages($site)
    { 
  //dump($_POST);
        $this->mPageTitle = getSiteName($site) . " - List Pages";
        if($_POST['makelive'])
        {
            Page::setLiveVersion(intval($_POST['id']), intval($_POST['version']));
        }
        elseif($_POST['makepreview'])
        {
             Page::setPreviewVersion(intval($_POST['id']), intval($_POST['version']));
        }
          $pages = Page::GetPages($site, TRUE);
          //dump($pages);   
          $this->mSmarty->assign('pages', $pages );
          
          // create the left side modules
          $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'),                                        
                                          CMS::CreateContentTypesModule(), 
                                          CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
          $this->mMainTpl = 'listPages.tpl';
    }
    
     protected function _InitCaching(){
        $this->_mAllowCaching = false;
        $this->_mMainTplCaching = false;
     
     }
     protected function _InitPage(){}
   
}

