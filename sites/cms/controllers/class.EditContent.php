<?php

class EditContent extends Controller
{
    
   
    
    /**
     * @param website object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   content type  (name of the extra_table)
     *          2:   [optional] id of the content item if 'new'  we will create a new item, if blank we will list
     */
    public function __construct($websiteObject, $arguments)
    {
        //dump($_POST);      
     
        global $CONFIG;     
        parent::__construct($websiteObject, $arguments); 
        
        $site        = $CONFIG->cms_site_code;
        $model_name  = $arguments[0];
        $id          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                     
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('model_name', $model_name);
   
        
        if(!empty($_POST['contents_title']))   // save content item
        {
            $this->_save($id, $site, $model_name);  
            return;         
        }
        
        if($isNew || $id > 0 )
        {
            $this->_edit($id, $model_name);  // edit new or existing item
            return; 
        }
                
        $this->_list($site, $model_name);  // list content items
               
        return;      
    }

    
    private function _list($site, $model_name)
    {
        if($_POST['makelive'])
        {
            Content::setLiveVersion(intval($_POST['id']), intval($_POST['version']));
        }
        elseif($_POST['makepreview'])
        {
             Content::setPreviewVersion(intval($_POST['id']), intval($_POST['version']));
        }

        if($model_name == 'Module')
            $items = Module::GetModules($site, TRUE);
        else
            $items = Content::GetContentByType($model_name, $site, null,50,0,'ALL');
              
        //  foreach($items as $a) echo $a->contents_id;     die;   
        $this->mSmarty->assign('contents', $items );
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateContentTypesModule(),  
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
                                        
        $this->mMainTpl = 'listContent.tpl';
        $this->mPageTitle = getSiteName($site) . " - List $model_name";
    }

    
    private function _save($id, $site,$model_name)
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
    
    
    private function _edit($id, $model_name)
    {
        
        if($id == 0)  // new article
        { //die($_SESSION['user_first_name']);
            $this->mPageTitle = getSiteName($site) . " - New Article";
        } 
        else // edit existing article
        {
            $version = intval($_GET['version']) > 0 ?  intval($_GET['version']): LATEST_VERSION ;
            $this->mPageTitle = getSiteName($site) . " - Edit Article";            
        }
        
        $model = new $model_name();
        
        $formData = Content::GetFormData($id, $model_name, $version);     
       
        // create the center module
        $this->mModules['center'] = array(CMS::CreateTargetsModule($id));
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('contentStatusModule.tpl'), 
                                        CMS::CreateDummyModule('contentMediaModule.tpl'), 
                                        CMS::CreateDummyModule('relatedItemsModule.tpl'), 
                                        CMS::CreateVersionHistoryModule($formData['contents_id'][value], $formData['contents_live_version'][value], $formData['contents_preview_version'][value], $model->GetExtraTableName()) 
                                        );
        $this->mMainTpl = 'editContent.tpl'; 
                                                                 
        $this->mSmarty->assign('form_data',$formData);
    }
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
