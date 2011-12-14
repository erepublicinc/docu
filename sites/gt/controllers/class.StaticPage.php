<?php
class StaticPage extends Controller
{
    public function Display()
    {   
        $p = Page::GetDetails();
        //dump($p);               
        $this->mSmarty->assign('page', $p);
        $this->mMainTpl = 'staticPage.tpl';
  
        parent::Display();     
    }
       
    protected  function _InitCaching(){}
    protected  function _InitPage(){}
}

