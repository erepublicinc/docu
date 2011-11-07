<?php

class EditPage extends WebPage
{
    
   
    
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'pages' 'new_page' 'page' ( the first one produces a list the other fo editing
     *          2:   [optional] pk of the page 
     */
    public function __construct($websiteObject, $arguments)
    {   
        global $CONFIG;    
        parent::__construct($websiteObject, $arguments); 
        
        $site    = $CONFIG->cms_site_code;
        $command = $arguments[0];
        $pk      = 0 + $arguments[1]; 
//die("site: $site  command: $command  pk: $pk");  
   
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
//$CONFIG->Dump(); die( getSiteName($site));           
        if($command == 'pages')
        {
           $this->_ListPages($site);
            return; //================================>
        }
        
        
        
//echo"<pre>"; print_r($_POST); die;        
       
        if(!empty($_POST['pages_title']))
        {
             $this->_SavePage();
             return; //================================>
        }
        
        $this->_EditPage($pk, $command);
        return;
        
        
       
       
    }
   
    
    
    private function _SavePage()
    {
        $p = new Page($_POST);
        $pk = $p->Save();
       
        header("LOCATION: /cms/$site/pages");
        die;             
    }

    
    private function _EditPage($pk, $command)
    {
        if($command == 'new_page' || $pk == 0)
        { //die($_SESSION['user_first_name']);
            $page = new stdClass();
            $page->pages_authors_fk     = $_SESSION['user_pk'];
            $page->users_first_name     = $_SESSION['user_first_name'];
            $page->users_lastname      = $_SESSION['user_last_name'];
           
        } 
        else 
        {  
             $page =  Page::GetDetails($pk);
        }
//dump($page);        
        $this->mSmarty->assign('p',$page); //NOTE   the Smarty var "page"  is already set as the current page
        $this->mMainTpl = 'editPage.tpl';  
    }
    
    
    private function _ListPages($site)
    {
          $pages = Page::GetPages($site, TRUE);
          //dump($pages);   
          $this->mSmarty->assign('pages', $pages );
          $this->mMainTpl = 'listPages.tpl';
    }
    
     protected function _InitCaching(){
        $this->_mAllowCaching = false;
        $this->_mMainTplCaching = false;
     
     }
     protected function _InitPage(){}
   
}

