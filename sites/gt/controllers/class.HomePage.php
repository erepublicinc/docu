<?php


class HomePage extends Controller
{
        
    public function Display(){
        $arts = Content::GetPageContents();
//echo"<pre>"; print_r($arts); die;               
        
        $this->mSmarty->assign('articles', $arts);
        $this->mMainTpl = 'homePage.tpl';

        parent::Display();      
    }
    
    protected  function _InitCaching(){}

    protected  function _InitPage(){}    
}
