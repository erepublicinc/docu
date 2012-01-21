<?php


class HomePage extends Controller
{
        
    public function Display(){
        
        //load the modules
        $this->LoadModules('LISTING');
        
        $arts = Content::GetPageContents();
//echo"<pre>"; print_r($arts); die;               
        
        $this->mSmarty->assign('articles', $arts);
        $this->mMainTpl = 'mockup.tpl';

        parent::Display();      
    }
    
    protected  function _InitCaching(){}

    protected  function _InitPage(){}    
}
