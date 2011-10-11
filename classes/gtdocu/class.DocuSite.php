<?
class DocuSite extends Website
{
    function __construct()
    {
        global $CONFIG;
        $CONFIG->SetValue('tpl_path','/var/www/newgt/html/gtdocu/tpl');
        $CONFIG->SetValue('site_code','DOCU');
    }
    
    
    protected function _InitClassMapping()
    {
        /*
        $this->_mClassMapping = array(
            "/"                             => "HomePage"
           ,"/bids/"                        => "BidPage"
           ,"/legacy/"                      => "BidPage"
         );
         */
    }
    
    
    
     public  function UnauthorizedHandler($error_msg)
     {}
}
