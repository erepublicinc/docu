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
       
        $contents = Content::GetPageContents(); 
        
        
        echo("homepage");
    }
}
