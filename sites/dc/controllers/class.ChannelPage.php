<?
class ChannelPage extends Controller
{
  
    public function __construct($website, $args)
    {
        parent::__construct($website, $args);   
        //dump($this->mArguments);
    }
    
    public function Display(){
        global $CONFIG;
        //dump($_POST,false);  
        
        $site = $CONFIG->cms_site_code;   
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('environment', $CONFIG->environment);
        $this->mSmarty->assign('iframe_ads', TRUE);
        $this->mSmarty->assign('ad_specs', AdSystem::GetSpecs());
        
        //$this->mSmarty->assign('designMediaPath', $CONFIG->install_path . '/html/gt/media');
         $this->mSmarty->assign("designMediaPath","http://media2.govtech.com/designimages");
         
         
        // for example full url is www.governing.com/news/local/cool-article.html
        // $CONFIG->current_page_url would be: news/local    $this->mArguments[0] would be: cool-article.html
        $pathParts = explode('/',$CONFIG->current_page_url);
        $this->mSmarty->assign('topLevelSection', $pathParts[0]);
  
        $page = Page::GetDetails();
        $this->mSmarty->assign('page',$page);
        
        // this is going to be a detaial page
        if(! empty( $this->mArguments[0]))
        {            
            $article = Content::GetContentByUrl($this->mArguments[0]);
            $comments = Comment::getComments($article->contents_id)  ;
            
            $this->mSmarty->assign('redirect_url', $_SERVER['REQUEST_URI']);
            $this->mSmarty->assign('comments', $comments);
            
            
            $this->mSmarty->assign('article', $article);
            $this->mSmarty->assign('page_title', $article->contents_display_title);
            $this->mMainTpl = 'articleDetailPage.tpl';
        }
        
        // its going to be a listing page
        else 
        {   
                
            //load the modules
            $this->LoadModules('LISTING');
                        
            $contents = Content::GetPageContents();    
            
            $this->mSmarty->assign('contents', $contents);
            $this->mMainTpl = 'channelListingPage.tpl';
            //dump($contents);
        }
        
        $this->mSmarty->assign('page', $contents);
        
        parent::Display();     
    }
    
    protected function _InitCaching(){  }
    protected function _InitPage(){}
}
