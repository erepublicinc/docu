<?php


class ChannelPage extends WebPage
{
    
    public function __construct($data)
    {
        parent::__construct($data);
    }
    
    public function Render(){
        $contents = Content::GetPageContents(); 
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


