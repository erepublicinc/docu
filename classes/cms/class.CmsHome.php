<?php 
class CmsHome extends WebPage
{
    
        
    public function __construct($websiteObject, $arguments)
    {        
            
        parent::__construct($websiteObject, $arguments);
        
        $this->mMainTpl = 'cmsHome.tpl';
        
        
        $site    = strtoupper($arguments[0]);
        //die($site);
        
        $p = Page::GetPages($site, TRUE);
        $this->mSmarty->assign('pages',$p);
        $this->mSmarty->assign('site_code',$arguments[0]);
    }
    
  
    
    
    
   protected function _InitCaching(){}
   protected function _InitPage(){}
}

