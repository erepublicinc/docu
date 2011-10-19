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
        
        $site    = $arguments[0];
        $command = $arguments[1];
        $pk      = 0 + $arguments[2]; 
//die("site: $site  command: $command  pk: $pk");        

        if($command == 'articles')
        {
            $this->mSmarty->assign('articles',  Article::GetArticles());
            $this->mMainTpl = 'listContent.tpl';
            return; //================================>
        }
        
        // check if we are saving
        if(!empty($_POST['title']))
        {
            Article::sYaasSave($_POST);
            header("LOCATION: /cms/gt/articles");
            die; //============================>
        }
        
        if($command == 'new_article' || $pk == 0)
        {
            $a = new stdClass();
            $a->main_author_fk = $_SESSION['user_pk'];
            $a->first_name     = $_SESSION['user_first_name'];
            $a->last_name      = $_SESSION['user_last_name'];
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
