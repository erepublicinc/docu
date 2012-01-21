<?php

class EditAuthor extends Controller
{
     
    /**
     * @param Router object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'pages' 'new_page' 'page' ( the first one produces a list the other fo editing
     *          2:   [optional] id of the page or 'new'  
     */
    public function __construct($routerObject, $arguments)
    {   
        global $CONFIG;    
        parent::__construct($routerObject, $arguments); 

        $site        = $CONFIG->cms_site_code;        
        $model_name  = $arguments[0];
        $id          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                     
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('model_name', $model_name);
                  
                      
        if(!empty($_POST['authors_name']))
        {
             $this->_SaveRecord($site, $model_name);
             return; //================================>
        }
        
        if($isNew || $id >0)
        {
            $this->_EditRecord($id, $model_name);
            return;
        }
        
        $this->_ListRecords($site);
        return;
      
    }
   
    
    
    private function _SaveRecord($site, $model_name)
    {
        //dump($_POST);
        $m = new Author($_POST);
        $id = $m->Save();
       
        header("LOCATION: /cms/$site/$model_name");
        die;             
    }

    
    private function _EditRecord($id, $model_name)
    {       
        if($id == 0)
        { 
            $this->mPageTitle =  "New Author Profile";
            $m = new stdClass();
        } 
        else 
        {  
             $this->mPageTitle = "Edit Author Profile";
             $author           = Author::GetDetails($id);    
        }        

        $formData = Author::GetFieldDescriptions(true); // includes users
//dump($formData) ;       
    

    //* 
        $this->mSmarty->assign('users', User::GetUsers());
        $this->mSmarty->assign('author',$author); //NOTE   the Smarty var "page"  is already set as the current page
        $this->mMainTpl = 'editAuthor.tpl'; 
        
   // */    

         /* make this work 
        $this->mMainTpl = 'editContent.tpl';      
        $this->mSmarty->assign('form_data',$formData);
        $author->SetAlias(array('contents_title'=>'sds', 'contents_type'=>'sds'));
        $this->mSmarty->assign('content', $author);          // needed for side modules ?
        $this->mSmarty->assign('value', $author->ToArray()); // needed for compatibility with formdata
       */ 
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'),                                                                                                                     
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
      
      
                            
    }
    
    
    private function _ListRecords($site)
    { 
        $this->mPageTitle = "List Author Profiles";
        
        $authors = Author::GetAuthors();
        $authors->SetAlias(array('contents_id' => 'authors_id', 'contents_title' => 'authors_name'));
        $this->mMainTpl = 'listContent.tpl';
        
        $this->mSmarty->assign('contents', $authors );
        
        // create the left side modules for this page
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'), 
                                        CMS::CreateContentTypesModule(),  
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );       
    }
    
     protected function _InitCaching(){
        $this->_mAllowCaching = false;
        $this->_mMainTplCaching = false;
     
     }
     protected function _InitPage(){}
   
}


