<?php
class FormRouter extends Router
{   

    public $theForm; 
    
    function __construct()
    { 
        // Check if this page already exists in our cache of complete pages
        $fname = str_replace('/', '#',  $_SERVER['REDIRECT_URL']);
        $fname = rtrim($fname,'# ');
       
        global $CONFIG;
        
        $CONFIG->SetValue('tpl_path', $CONFIG->install_path . '/sites/forms/tpl');
        $CONFIG->SetValue('site_code', 'FORMS');
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
        
        $this->_mClassArguments = $pathSegments;    

        $formRecord = Form::GetDetailsByName($pathSegments[1]);
        if($formRecord)
        {
            // check if this form requires a special class
            if(! empty($formRecord->forms_php_class ))
               $this->_mClassName = $formRecord->forms_php_class ;
            else 
               $this->_mClassName = 'DefaultFormController';               
             
            $this->theForm = $formRecord;  // store it so the controller can grab it     
        }
        else 
           die('unknown form'); 
        return true;

    }
      
}


