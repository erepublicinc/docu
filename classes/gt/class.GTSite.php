<?
class GTSite extends Website
{
    function __construct()
    {
        // Check if this page already exists in our cache of complete pages
        $fname = str_replace('/', '#',  $_SERVER['REDIRECT_URL']);
        $fname = rtrim($fname,'# ');
        
        //$this->mDefaultModules = array(....);    // set the sitewide modules here
        
        
       
        //die( __CLASS__ .' '. __FUNCTION__ );
        global $CONFIG;
        $CONFIG->SetValue('tpl_path','/var/www/newgt/html/gt/tpl');
        $CONFIG->SetValue('site_code','GT');
        
        parent::__construct();
    }
    
    
    protected function _InitClassMapping()
    {
       
        $this->_mClassMapping = Page::GetClassMapping();
         
    }
    
      
  //   public  function UnauthorizedHandler($error_msg)     {}
}
