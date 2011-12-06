<?php
class CMSSite extends Website
{    
    function __construct()
    {
        // Check if this page already exists in our cache of complete pages
        $fname = str_replace('/', '#',  $_SERVER['REDIRECT_URL']);
        $fname = rtrim($fname,'# ');
       
        global $CONFIG;
        
        $CONFIG->SetValue('tpl_path', $CONFIG->install_path . '/sites/cms/tpl');
        $CONFIG->SetValue('site_code', 'CMS');
        parent::__construct();
    }
    
    // this is a stub because we don't use this , we'll overide _GetPageClassName ( see below)
    protected function _InitClassMapping()
    {
        $this->_mClassMapping = array();
    }
    
    
    /**
     * We override this function  because we use a diffent way to do the class mapping
     * needs more work
     *
     */
    protected function _GetPageClassName()
    {  
        global $CONFIG;
        $uri            = strtolower( $_SERVER['REQUEST_URI']);
        $uri            = parse_url($uri);
        $path           = trim($uri['path'], "/");
        strtolower($path);
        $pathSegments   = explode("/", $path);
        $numOfSegments  = count($pathSegments);
        $site           = 'ALL'; 
        $this->_mClassName = 'CmsHome'; // default
        
        if($pathSegments[0] == 'preview')
            array_shift($pathSegments);
            
        if($pathSegments[0] == 'cms')
            array_shift($pathSegments);
        
        if(in_array($pathSegments[0], array('gt','gov','em','cv','all','er','cdg','cde','dc')))
        {
            $site = strtoupper($pathSegments[0]);
            $CONFIG->SetValue('cms_site_code',$site);
            array_shift($pathSegments);
        }

        // the default map
        $map = array( 'users'       => 'EditUser',
        			  'new_users'    => 'EditUser',
        			  'articles'    => 'EditArticle', 
                      'article'     => 'EditArticle',
                      'new_article' => 'EditArticle',
                      'modules'     => 'EditModule', 
                      'module'      => 'EditModule',
                      'new_module'  => 'EditModule',
                      'pages'       => 'EditPage', 
                      'page'        => 'EditPage',
                      'new_page'    => 'EditPage',
                      'placement'	=> 'EditPlacement'
        ) ;
        
        // allow overwrite of the default map according to the website
        switch($site)
        {          
            case 'EM' :
                $map['articles'] = 'EditArticle' ;
            break;       
        }
            
        if(count($pathSegments) > 0 && $map && isset($map[$pathSegments[0]]))
        {
            $this->_mClassName = $map[$pathSegments[0]] ;
        }
      
        $this->_mClassArguments = $pathSegments;
//die  ("page class ".$this->_mClassName);       
        return true;
    }
      
}


