<?php


class StaticPage extends Controller
{
    
    public function __construct($data)
    {         
        //die( __CLASS__ .' '. __FUNCTION__ ); 
        parent::__construct($data);
    }
    
    public function Display(){
       
        $p = Page::GetDetails();
        
//echo"<pre>"; print_r($p); die;               
        
        $this->mSmarty->assign('page', $p);
        $this->mMainTpl = 'staticPage.tpl';

   
        parent::Display();
      
    }
    
    
    protected  function _InitCaching(){}

    /**
     * Initialize the page settings -- additional smarty vars, etc...
     */
    protected  function _InitPage(){}
    
}

