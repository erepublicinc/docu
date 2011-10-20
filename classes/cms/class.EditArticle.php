<?php

class EditArticle extends WebPage
{
    
   
    
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'articles' 'new_article' 'article' ( the first one produces a list the other fo editing
     *          2:   [optional] pk of the article 
     */
    public function __construct($websiteObject, $arguments)
    {       
        parent::__construct($websiteObject, $arguments); 
        
        $site    = strtoupper($arguments[0]);
        $command = $arguments[1];
        $pk      = 0 + $arguments[2]; 
//die("site: $site  command: $command  pk: $pk");        

        if($command == 'articles')
        {
            $arts = Article::GetArticles($site,null,50,0,'ALL');
          //  foreach($arts as $a) echo $a->contents_pk;     die;   
            $this->mSmarty->assign('contents', $arts );
            $this->mMainTpl = 'listContent.tpl';
            return; //================================>
        }
//echo"<pre>"; print_r($_POST); die;        
        // check if we are saving
        if(!empty($_POST['contents_title']))
        {
            
            $pk = Article::sYaasSave($_POST);
 /*           
            $p = new stdClass();
            $p->targets_pages_fk = 1;
            $p->targets_contents_fk = 10;           
            Page::sYaasCreateTarget($p);
   */         
            header("LOCATION: /cms/gt/articles");
            die; //============================>
        }
        
        if($command == 'new_article' || $pk == 0)
        { //die($_SESSION['user_first_name']);
            $a = new stdClass();
            $a->contents_main_author_fk = $_SESSION['user_pk'];
            $a->users_first_name     = $_SESSION['user_first_name'];
            $a->users_last_name      = $_SESSION['user_last_name'];
            $a->contents_create_date = time(); //date();
        } 
        else 
        {
             $a =  Article::GetArticle($pk);
        }
        
        $this->mSmarty->assign('article',$a);
        $this->mMainTpl = 'editArticle.tpl';  
       
    }

    
  
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
