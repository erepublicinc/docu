<?php

class EditPlacement extends WebPage
{  
    /**
     * @param website object
     * @param array  with the following values
     *          0:   'placement' 
     *          1:   the id of the page (wss)
     */
    public function __construct($websiteObject, $arguments)
    {
        //dump($arguments);      

        global $CONFIG;      
        
        // we force the page size to 50
        $CONFIG->SetValue('page_size', 50, 'FORCE');   
        
        parent::__construct($websiteObject, $arguments); 
        
        $pages_id      = 0 + $arguments[1];    
        
        $site = $CONFIG->cms_site_code;
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        
        
        $page = Page::GetDetails(0,$pages_id);
        $this->mPageTitle = " $site Placement List: $page->pages_title ($page->pages_url)";
        
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'),                                        
                                        CMS::CreateContentTypesModule(), 
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
   
        $contents =  Content::GetPageContents('ALL', $pages_id) ;  
        $this->mSmarty->assign('contents', $contents );    
        $this->mMainTpl  = 'placementList.tpl';  
    }

    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
