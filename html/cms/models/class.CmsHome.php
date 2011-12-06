<?php 
class CmsHome extends WebPage
{       
    public function __construct($websiteObject, $arguments)
    {        
        global $CONFIG;     
        parent::__construct($websiteObject, $arguments);
        $site = $CONFIG->cms_site_code;
        $this->mPageTitle = getSiteName($site) ;
            
        $this->mMainTpl = 'cmsHome.tpl';
        $this->mMainTpl = 'listPages.tpl';
        
           
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateContentTypesModule(),                                        
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl'));
       
        //dump($site);
        $this->mSmarty->assign('site_code', $site);
    }
    
  
    
    
    
   protected function _InitCaching(){}
   protected function _InitPage(){}
}

