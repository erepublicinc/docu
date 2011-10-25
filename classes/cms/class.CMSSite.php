<?php
class CMSSite extends Website
{
     
    
    function __construct()
    {
        // Check if this page already exists in our cache of complete pages
        $fname = str_replace('/', '#',  $_SERVER['REDIRECT_URL']);
        $fname = rtrim($fname,'# ');
        
        //$this->mDefaultModules = array(....);    // set the sitewide modules here
        
        
        parent::__construct();
        global $CONFIG;
        $CONFIG->SetValue('tpl_path','/var/www/newgt/html/cms/tpl');
        $CONFIG->SetValue('site_code', 'CMS');
    }
    
    
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
        $uri            = parse_url($_SERVER['REQUEST_URI']);
        $path           = trim($uri['path'], "/");
        strtolower($path);
        $pathSegments   = explode("/", $path);
        $numOfSegments  = count($pathSegments);
        $site           = 'ALL'; 
        $this->_mClassName = 'CmsHome'; // default
        
        
        if($pathSegments[0] == 'cms')
            array_shift($pathSegments);
        
        if(in_array($pathSegments[0], array('gt','gov','em','cv')))
        {
            $site = strtoupper($pathSegments[0]);
            array_shift($pathSegments);
        }

        
        // allow mappings of classes  according to the website
        switch($site)
        {          
            case 'EM' :
                        $map = array('articles' => 'EditArticle') ;
            break;
            
            default: 
                        $map = array('articles' => 'EditArticle', 'article' => 'EditArticle','new_article' => 'EditArticle',
                                      'pages' => 'EditPage', 'page' => 'EditPage','new_page' => 'EditPage') ;
        }
            
        if(count($pathSegments) > 0 && $map && isset($map[$pathSegments[0]]))
        {
            $this->_mClassName = $map[$pathSegments[0]] ;
        }
      
        array_unshift($pathSegments, $site);
        $this->_mClassArguments = $pathSegments;
//die  ("page class ".$this->_mClassName);       
        return true;
    }
      
}


