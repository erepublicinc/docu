<?php

class CMS
{
    
    static function Init()
    {
        echo "init cms <br>";
        
        if(! User::Authorize("cms"))
           die("you are not authorized to use the cms");
    }
    

    
    //======================== constructor functions for the side module objects ==========
    
    /**
     * returns a module object
     * @param object $c  content object
     * @param String $extra_table for the content object
     * @return object  module object
     */
    static function CreateVersionHistoryModule($c, $extra_table)
    { 
        $m = new stdClass();
        
        $m->template        = 'versionHistoryModule.tpl';
        
        if($c->contents_pk)    
        {
            $m->history         = Content::GetVersionHistory($c->contents_pk, $extra_table);
            $m->pk              = $c->contents_pk;  
            $m->live_version    = $c->contents_live_version;
            $m->preview_version = $c->contents_preview_version;      
        }
        return $m;
    }
    
   /**
     * returns a module object for version history of this page
     * @param object $p  page object
     * @return object  module object
     */
    static function CreatePageHistoryModule($pages_id)
    { 
        $m = new stdClass();
        
        $m->template        = 'versionHistoryModule.tpl';
        
        if($pages_id)    
        {
            $m->history         = Page::GetVersionHistory($pages_id);
          //  $m->pk              = $pk;  
            $m->live_version    = $m->history->live_version;
            $m->preview_version = $m->history->preview_version;      
        }
        return $m;
    }
    
    
    
   /**
     * returns a module object
     * @param int $pk of content object
     * @return object  module object
     */
    static function CreateTargetsModule($pk)
    {  
        $m = new stdClass();
        
        $m->template = 'targetsModule.tpl';
        $m->pages    = Page::getPages('ALL'); 
       
        if($pk > 0) 
            $m->targets  = Article::GetTargets($pk);
                         
        return $m;         
    }
    
    
    
    
   /**
     * returns a module object
     * @param int $pk of page object
     * @return object  module object
     */
    static function CreateModule2PageModule($page_pk)
    {  
        global $CONFIG; 
        $m = new stdClass();
        $m->template = 'module2PageModule.tpl';

        // list of all modules for this site plus common modules
        $m->modules  = Module::GetModules($CONFIG->cms_site_code, true);        
        
        if($page_pk)    
        {        
            $m->page_modules = Module::GetPageModules($page_pk,false); 
        }    
        
        return $m;         
    }    
    
    
   /**
     * returns a module object  for all pages that use this module
     * @param int $pk of module object
     * @return object  module object
     */
    static function CreateModuleUsageModule($module_pk)
    {  
        global $CONFIG; 
        $m = new stdClass();
        $m->template = 'moduleUsageModule.tpl';
      
        if($module_pk)    
        {        
            $m->pages = Module::GetPageLinks($module_pk); 
            //dump( $module_pk);  
        }    
        
        return $m;         
    }    
     
    /**
     * returns a module object
     * @return object  module object
     */
    static function CreateContentTypesModule()
    {  
        global $CONFIG; 
        $m = new stdClass();
        $m->template = 'contentTypesModule.tpl';

        $m->pages    = Page::getPages('ALL'); 
       // dump($m->pages);
        return $m;         
    }      
     
    
    
    /**
     * returns a module object
     * @param String template
     * @return object  module object
     */
    static function CreateDummyModule($template)
    {
         $m = new stdClass();
         $m->template  = $template;
         return $m;
    }
}
