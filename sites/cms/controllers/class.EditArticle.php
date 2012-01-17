<?php

class EditArticle extends Controller
{
    
   
    
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'articles' 'new_article' 'article' ( the first one produces a list the other fo editing
     *          2:   [optional] id of the article 
     */
    public function __construct($websiteObject, $arguments)
    {
        //dump($_POST);      
     
        global $CONFIG;     
        parent::__construct($websiteObject, $arguments); 
        
        $site        = $CONFIG->cms_site_code;
        $model_name = $arguments[0];
        $id          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                     
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('model_name', $model_name);
   
        
        if(!empty($_POST['contents_title']))   // save article
        {
            $this->_saveArticle($id, $site, $model_name);  
            return;         
        }
        
        if($isNew || $id > 0 )
        {
            $this->_editArticle($id);  // edit new or existing article
            return; 
        }
                
        $this->_listArticles($site);  // list articles b
               
        return;      
    }

    
    private function _listArticles($site)
    {
        if($_POST['makelive'])
        {
            Content::setLiveVersion(intval($_POST['id']), intval($_POST['version']));
        }
        elseif($_POST['makepreview'])
        {
             Content::setPreviewVersion(intval($_POST['id']), intval($_POST['version']));
        }
        
        $arts = Article::GetArticles($site,null,50,0,'ALL');
        //  foreach($arts as $a) echo $a->contents_id;     die;   
        $this->mSmarty->assign('contents', $arts );
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateContentTypesModule(),  
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
                                        
        $this->mMainTpl = 'listContent.tpl';
        $this->mPageTitle = getSiteName($site) . " - List Articles";
    }

    
    private function _saveArticle($id, $site,$model_name)
    {
//dump($_POST);        
        $id = Article::sYaasSave($_POST);                   
        $targets = json_decode($_POST['changed_targets']);
   
        foreach($targets as $params)
        {
            $params->targets_contents_id = $id;
           //dump($params);     
            if($params->record_state != 'CLEAN')
                Page::sYaasSaveTarget($params);
        }
        
        header("LOCATION: /cms/{$site}/$model_name");
        die; 
    }
    
    
    private function _editArticle($id)
    {
        if($id == 0)  // new article
        { //die($_SESSION['user_first_name']);
            $this->mPageTitle = getSiteName($site) . " - New Article";
            
            $article = new stdClass();
            $article->contents_pub_date    = time(); //date();
           // $history = array();
        } 
        else // edit existing article
        {
            $version = intval($_GET['version']) > 0 ?  intval($_GET['version']): LATEST_VERSION ;
            $this->mPageTitle = getSiteName($site) . " - Edit Article";
            $article = Article::GetDetails($id, $version);                        
        }

        // create the center module
        $this->mModules['center'] = array(CMS::CreateTargetsModule($id));
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('contentStatusModule.tpl'), 
                                        CMS::CreateDummyModule('contentMediaModule.tpl'), 
                                        CMS::CreateDummyModule('relatedItemsModule.tpl'), 
                                        CMS::CreateVersionHistoryModule($article, "articles") 
                                        );
                                        
                                        
        $this->mMainTpl = 'editArticle.tpl';  
        $this->mSmarty->assign('content',$article);
        $this->mSmarty->assign('authors', Author::getAuthors());
    }
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
