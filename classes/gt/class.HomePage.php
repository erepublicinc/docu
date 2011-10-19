<?php


class HomePage extends WebPage
{
    
    public function __construct($data)
    {
       global $CONFIG; 
        // temporarily
         $CONFIG->SetValue('current_page_pk', '1' );
        
        parent::__construct($data);
    }
    
    public function Display(){
       
       
        
        

        $arts = Content::GetPageContents();
      
        
        $this->mSmarty->assign('articles', $arts);
        $this->$mMainTpl = 'hompage.tpl';

   
        
      
    }
}
