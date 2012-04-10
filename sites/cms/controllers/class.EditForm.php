<?php

class EditForm extends Controller
{
    
   
    
    /**
     * @param Controller object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'articles' 'new_article' 'article' ( the first one produces a list the other fo editing
     *          2:   [optional] id of the article 
     */    
    public function __construct($RouterObject, $arguments)
    {
        //dump($_POST);         
        global $CONFIG;     
        parent::__construct($RouterObject, $arguments); 
        
        $site        = $CONFIG->cms_site_code;
        $model_name  = $arguments[0];
        $id          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                 
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('model_name', $model_name);
        
          
        if(!empty($_POST['forms_title']))   // save user
        {
            $this->_saveForm($id, $site);  
            return;         
        }     
        
        if($isNew || $id > 0)
        {
             $this->_editForm($id);
             return; 
        }
        
        $this->_listForms($site);       
        return;      
    }

    
    private function _listForms($site)
    {    
        $forms = Form::GetForms();
        $forms->SetAlias(array("contents_title"=>"forms_title", "contents_id"=>"forms_id" ));  // so we can reuse the listContent.tpl
        
        $this->mSmarty->assign('contents', $forms );
        
//dump($forms);
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateContentTypesModule(),  
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
                                        
        $this->mMainTpl   = 'listContent.tpl';
        $this->mPageTitle = getSiteName($site) . " - List Forms";
    }

    
    private function _saveForm($id, $site)
    {
         
       
//dump($_POST);       
        $f  = new Form($_POST) ;        
        $id = $f->Save();     

        $fields = json_decode($_POST['changed_fields']);
        //dump($fields);
        foreach($fields as $parms)
        {
            //sleep(1);
            $parms->forms_fid = $id;
        //   dump($parms, false);     
            if($parms->record_state != 'CLEAN')
            {           
               Form::SaveField($parms);   
            }
        }
        
     
        header("LOCATION: /cms/{$site}/Form");
        die; 
    }
    
    
    private function _editForm($id)
    {
        if($id == 0)  // new form
        { //die($_SESSION['user_first_name']);
            $this->mPageTitle = getSiteName($site) . " - New Form";
            $form = new stdClass();
            $form_fields = array();
        } 
        else // edit existing form
        {
            $this->mPageTitle = getSiteName($site) . " - Edit Form";
            $form = Form::GetDetails($id);   
            $form_fields = Form::GetFields($id)  ;                   
        }
//dump($form);
        
        
        $masters = Form::GetFieldMasters($id);
//dump($masters);        
        // create the left side modules
        $this->mModules['left'] = array( CMS::CreateDummyModule('recentlyModifiedModule.tpl')  );                                       
        $this->mMainTpl = 'editForm.tpl';   
       
        $this->mSmarty->assign('form',$form);

        $this->mSmarty->assign('masters', $masters );
        
        $this->mSmarty->assign('form_fields', $form_fields );
    }
    
    
     protected function _InitCaching(){}
     protected function _InitPage(){}
     
}
