<?
class DCRouter extends Router
{
    function __construct()
    {
        // Check if this page already exists in our cache of complete pages
        $fname = str_replace('/', '#',  $_SERVER['REDIRECT_URL']);
        $fname = rtrim($fname,'# ');    
       
        global $CONFIG;
        $CONFIG->SetValue('tpl_path', $CONFIG->install_path . '/sites/dc/tpl');
        $CONFIG->SetValue('site_code','DC');    
        parent::__construct();
    }
    
    
    protected function _InitClassMapping()
    {
       
        $this->_mClassMapping = Page::GetClassMapping();
        //dump( $this->_mClassMapping); 
    }
    

}
