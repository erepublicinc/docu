<?php

class EditAuthor extends Controller
{
     
    /**
     * @param Router object
     * @param array  with the following values
     *          0:  'gt', 'gov', or 'all'
     *          1:   'pages' 'new_page' 'page' ( the first one produces a list the other fo editing
     *          2:   [optional] pk of the page or 'new'  
     */
    public function __construct($routerObject, $arguments)
    {   
        global $CONFIG;    
        parent::__construct($routerObject, $arguments); 

        $site        = $CONFIG->cms_site_code;        
        $record_type = $arguments[0];
        $pk          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
                     
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        $this->mSmarty->assign('record_type', $record_type);
                  
                      
        if(!empty($_POST['authors_name']))
        {
             $this->_SaveRecord($site,$record_type);
             return; //================================>
        }
        
        if($isNew || $pk >0)
        {
            $this->_EditRecord($pk);
            return;
        }
        
        $this->_ListRecords($site);
        return;
      
    }
   
    
    
    private function _SaveRecord($site,$record_type)
    {
        //dump($_POST);
        $m = new Author($_POST);
        $pk = $m->Save();
       
        header("LOCATION: /cms/$site/$record_type");
        die;             
    }

    
    private function _EditRecord($pk)
    {       
        if($pk == 0)
        { 
            $this->mPageTitle =  "New Author Profile";
            $m = new stdClass();
        } 
        else 
        {  
             $this->mPageTitle = "Edit Author Profile";
             $author           = Author::GetDetails($pk);    
        }        

        $users = $this->mSmarty->assign('users', User::GetUsers());
        
        $this->mSmarty->assign('author',$author); //NOTE   the Smarty var "page"  is already set as the current page
        
        // create the left side modules
        $this->mModules['left'] = array(CMS::CreateDummyModule('searchModule.tpl'),                                                                                                                     
                                        CMS::CreateDummyModule('recentlyModifiedModule.tpl') );
        $this->mMainTpl = 'editAuthor.tpl';  
    }
    
    
    private function _ListRecords($site)
    { 
        $this->mPageTitle = "List Author Profiles";
        
        $authors = Author::GetAuthors();
        $authors->SetAlias(array('contents_pk' => 'authors_pk', 'contents_title' => 'authors_name'));
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


