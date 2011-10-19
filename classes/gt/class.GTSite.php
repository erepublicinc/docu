<?
class GTSite extends Website
{
    function __construct()
    {
        // Check if this page already exists in our cache of complete pages
        $fname = str_replace('/', '#',  $_SERVER['REDIRECT_URL']);
        $fname = rtrim($fname,'# ');
        
        //$this->mDefaultModules = array(....);    // set the sitewide modules here
        
        
        parent::__construct();
        global $CONFIG;
        $CONFIG->SetValue('tpl_path','/var/www/newgt/html/gt/tpl');
        $CONFIG->SetValue('site_code','GT');
    }
    
    
    protected function _InitClassMapping()
    {
        $this->_mClassMapping = array(
            "/"                             => "HomePage"
           ,"/bids/"                        => "BidPage"
           ,"/legacy/"                      => "BidPage"
         );
    }
    
      
  //   public  function UnauthorizedHandler($error_msg)     {}
}
