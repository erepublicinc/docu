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
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateDummyModule('selectSiteModule.tpl'), 
                                        CMS::CreateDummyModule('contentTypesModule.tpl'), 
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
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
            if($params->record_state != 'CLEAN')
                Page::sYaasSaveTarget($params);
        }
        
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
             $article = Article::GetDetails($pk, LATEST_VERSION);                        
        }

        // create the center module
        $this->mModules['center'] = array(CMS::CreateTargetsModule($pk));
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('contentStatusModule.tpl'), 
                                        CMS::CreateDummyModule('contentMediaModule.tpl'), 
                                        CMS::CreateDummyModule('relatedItemsModule.tpl'), 
                                        CMS::CreateVersionHistoryModule($article, "articles") 
                                        );
                                        
        $this->mMainTpl = 'editArticle.tpl';  
           
        $this->mSmarty->assign('content',$article);
    }
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
