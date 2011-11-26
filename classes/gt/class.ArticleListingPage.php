<?
class ArticleListingPage extends WebPage
{
  
    public function __construct($website, $args)
    {
        parent::__construct($website, $args);   
    }
    
    public function Display(){
       dump($_POST,false);  
        if(isset($_POST['comments_body']))
        {
            Comment::addComment($_POST);
        }
        
        if(! empty( $this->mArguments[1]))
        {
            $article = Content::GetContentByUrl($this->mArguments[1]);
            $this->mSmarty->assign('article', $article);
            $this->mSmarty->assign('page_title', $article->contents_display_title);
            $this->mMainTpl = 'articleDetailPage.tpl';
        }
        else 
        {       
            $articles = Content::GetPageContents();       
            $this->mSmarty->assign('articles', $articles);
            $this->mMainTpl = 'articleListingPage.tpl';
        }
        parent::Display();     
    }
    
    protected function _InitCaching(){  }
    protected function _InitPage(){}
}
