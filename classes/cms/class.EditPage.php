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
        parent::__construct($websiteObject, $arguments); 
        
        $site    = strtoupper($arguments[0]);
        $command = $arguments[1];
        $pk      = 0 + $arguments[2]; 
//die("site: $site  command: $command  pk: $pk");        

        if($command == 'pages')
        {
            $pages = Pages::GetPages($site, false);
          //  foreach($arts as $a) echo $a->contents_pk;     die;   
            $this->mSmarty->assign('pages', $pages );
            $this->mMainTpl = 'listPages.tpl';
            return; //================================>
        }
        
        
        
//echo"<pre>"; print_r($_POST); die;        
        // check if we are saving
        if(!empty($_POST['pages_title']))
        {
            
            $p = new Page($_POST);
            $pk = $p.Save();
       
            header("LOCATION: /cms/$site/pages");
            die; //============================>
        }
        
        
        
        
        if($command == 'new_page' || $pk == 0)
        { //die($_SESSION['user_first_name']);
            $page = new stdClass();
            $page->pages_authors_fk     = $_SESSION['user_pk'];
            $page->users_first_name     = $_SESSION['user_first_name'];
            $page->users_lastname      = $_SESSION['user_last_name'];
           
        } 
        else 
        {
             $page =  Article::GetArticle($pk);
        }
        
        $this->mSmarty->assign('page',$page);
        $this->mMainTpl = 'editPage.tpl';  
       
    }
   
    
     protected function _InitCaching(){
        $this->_mAllowCaching = false;
        $this->_mMainTplCaching = false;
     
     }
     protected function _InitPage(){}
     
}

