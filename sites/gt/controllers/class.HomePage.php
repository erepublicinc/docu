<?php


class HomePage extends Controller
{
    
    public function __construct($data)
    {         
        //die( __CLASS__ .' '. __FUNCTION__ ); 
        parent::__construct($data);
    }
    
    public function Display(){
        //die( __CLASS__ .' '. __FUNCTION__ );
        $arts = Content::GetPageContents();
//echo"<pre>"; print_r($arts); die;               
        
        $this->mSmarty->assign('articles', $arts);
        $this->mMainTpl = 'homePage.tpl';

   
        parent::Display();
      
    }
    
    
    protected  function _InitCaching(){}

    /**
     * Initialize the page settings -- additional smarty vars, etc...
     */
    protected  function _InitPage(){}
    
}
