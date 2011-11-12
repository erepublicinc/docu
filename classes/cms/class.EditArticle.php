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
        //dump($_POST);      
     
        global $CONFIG;     
        parent::__construct($websiteObject, $arguments); 
        
        $site    = $CONFIG->cms_site_code;
        $command = $arguments[0];
        $pk      = 0 + $arguments[1];       
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('record_type', 'article');
   
        
        if($command == 'articles') // list articles
        {
            $this->_listArticles($site);  
            $this->mPageTitle = getSiteName($site) . " List Articles";
            return;     
        }
        
        
        
        if(!empty($_POST['contents_title']))   // save article
        {
            $this->_saveArticle($pk);  
            return;         
        }
        
        if($command == 'new_article')
            $this->mPageTitle = getSiteName($site) . " - New - Article";
        else    
            $this->mPageTitle = getSiteName($site) . " - Edit - Article";
        
            $this->_editArticle($pk, $command);  // edit new or existing article
        return;      
    }

    
    private function _listArticles($site)
    {
        if($_POST['makelive'])
        {
            Content::setLiveVersion(intval($_POST['pk']), intval($_POST['version']));
        }
        elseif($_POST['makepreview'])
        {
             Content::setPreviewVersion(intval($_POST['pk']), intval($_POST['version']));
        }
        
        $arts = Article::GetArticles($site,null,50,0,'ALL');
        //  foreach($arts as $a) echo $a->contents_pk;     die;   
        $this->mSmarty->assign('contents', $arts );
        $this->mSideModules['left'] = array('searchModule.tpl','selectSiteModule.tpl','contentTypesModule.tpl','recentlyModifiedModule.tpl');
        $this->mMainTpl = 'listContent.tpl';
    }

    
    private function _saveArticle($pk)
    {
//dump($_POST);        
        $pk = Article::sYaasSave($_POST);                   
        $targets = json_decode($_POST['changed_targets']);
   
        foreach($targets as $params)
        {
            $params->targets_contents_fk = $pk;
           //dump($params);     
            if($params->record_state != 'clean')
                Page::sYaasSaveTarget($params);
        }
/*           
        $p = new stdClass();
        $p->targets_pages_fk = 1;
        $p->targets_contents_fk = 10;           
        Page::sYaasCreateTarget($p);
*/         
        header("LOCATION: /cms/gt/articles");
        die; 
    }
    
    
    private function _editArticle($pk, $command)
    {
        if($command == 'new_article' || $pk == 0)  // new article
        { //die($_SESSION['user_first_name']);
            $article = new stdClass();
            $article->contents_main_author_fk = $_SESSION['user_pk'];
            $article->users_first_name        = $_SESSION['user_first_name'];
            $article->users_last_name         = $_SESSION['user_last_name'];
            $article->contents_create_date    = time(); //date();
            $history = array();
        } 
        else // edit existing article
        {
             $article = Article::GetArticle($pk, LATEST_VERSION);             
             $targets = Article::GetTargets($pk);      
             $history = Content::GetVersionHistory($pk, "articles");    
             $this->mSmarty->assign('targets', $targets);
            
        }
        
        // for versionHistoryModule
        $this->mSmarty->assign('history', $history);
        $this->mSmarty->assign('pk',$pk);  
        $this->mSmarty->assign('live_version',    $article->contents_live_version);
        $this->mSmarty->assign('preview_version', $article->contents_preview_version);      
        
        
        $this->mSmarty->assign('pages', Page::getPages('ALL'));       
        $this->mSmarty->assign('content',$article);
        
        $this->mSideModules['left'] = array('contentStatusModule.tpl','contentMediaModule.tpl','relatedItemsModule.tpl','versionHistoryModule.tpl');
        $this->mMainTpl = 'editArticle.tpl';  
    }
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
