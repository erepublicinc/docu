<?php 
class CmsHome extends WebPage
{
    
        
    public function __construct($data)
    {        
        parent::__construct($data);
    }
    
    public function Display()
    {
       
     //   $this->mSmarty->assign('articles', $arts);
        $this->mMainTpl = 'cmsHome.tpl';
      
    }
    
    
    
   protected function _InitCaching(){}
   protected function _InitPage(){}
}

