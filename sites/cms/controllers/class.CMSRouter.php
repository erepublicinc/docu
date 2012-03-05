<?php
class CMSRouter extends Router
{   
      
    function __construct()
    { 
        // Check if this page already exists in our cache of complete pages
        $fname = str_replace('/', '#',  $_SERVER['REDIRECT_URL']);
        $fname = rtrim($fname,'# ');
       
        parent::__construct();

    }
    
    /**
     * maps from url-segment (or tablename) to model class
     * @param $url-segment string
     * @return the model class
     */
 /*   
    function getModelClass($segment)
    {
       switch($segment)
       {
           case 'articles': 
               return 'Article'; 
           case 'modules' : 
               return 'Module';
       }  
      
    }
 */   
   
    
    // this is a stub because we don't use this , we'll overide _GetPageClassName ( see below)
    protected function _InitClassMapping()
    {
        $this->_mClassMapping = array();
    }

    
    // the default map, maps from url-segment to Controller and the main Model
    static private $map = array( // for content types , use the table name 
    //                URL SEGMENT           CONTROLLER       MODEL
        			  'User'       => array('EditUser',     'User'),      			  
        			  'Article'    => array('EditContent',  'Article'),                                          
                      'Module'     => array('EditModule',   'Module'),                    
                      'Page'       => array('EditPage',     'Page'),                     
                      'Placement'  => array('EditPlacement',''),
                      'Author'     => array('EditAuthor',   'Author'),
                      'Form'       => array('EditForm',     'Form')
                      
    ) ;    
    
    /**
     * maps from extra-tablename to model class
     * used by Content 
     * @param string table name
     * @return string Model class name
     */
    public function GetModelFromTable($table)
    {
        return self::$map[$table][1];
    }
    
    
    /**
     * We override this function  because we use a diffent way to do the class mapping
     * needs more work
     *
     */
    protected function _GetPageClassName()
    {  
        global $CONFIG;
        $uri            = $_SERVER['REQUEST_URI'];
        $uri            = parse_url($uri);
        $path           = trim($uri['path'], "/");
        
        $pathSegments   = explode("/", $path);
        $numOfSegments  = count($pathSegments);
        $site           = 'ALL'; 
        $this->_mClassName = 'CmsHome'; // default
        
        $piece = strtolower($pathSegments[0]);
        if($piece == 'preview')
            array_shift($pathSegments);

        $piece = strtolower($pathSegments[0]);    
        if($piece == 'cms')
            array_shift($pathSegments);
        
        $piece = strtolower($pathSegments[0]);    
        if(in_array($piece, array('gt','gov','em','cv','all','er','cdg','cde','dc')))
        {
            $site = strtoupper($piece);
            $CONFIG->SetValue('cms_site_code',$site);
            array_shift($pathSegments);
        }


        
        // allow overwrite of the default map according to the website
        switch($site)
        {          
            case 'EM' :
              //  self::$map['articles'][0] = 'EditArticle' ;
            break;       
        }
            
        if(count($pathSegments) > 0  && isset(self::$map[$pathSegments[0]][0]))
        {
            $this->_mClassName = self::$map[$pathSegments[0]][0] ;
        }
      
        $this->_mClassArguments = $pathSegments;
//die  ("page class ".$this->_mClassName);       
        return true;
    }
      
   
    
}


