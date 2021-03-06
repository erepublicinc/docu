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
     * @param object  the content object
     * @return object  module object
     */
    static function CreateRevisionHistoryModule($c)
    {
       
        $m = new stdClass();
        
        $m->template        = 'revisionHistoryModule.tpl';
        
        if($c->contents_fid > 0)    
        {
            $m->history         = Content::GetRevisionHistory($c->contents_fid, $c->contents_extra_table);
            $m->id              = $id;  
            $m->live_rev    = $c->contents_live_rev;
            $m->preview_rev = $c->contents_preview_rev;     
            
        }
        return $m;
    }
    
   /**
     * returns a module object for rev history of this page
     * @param object $p  page object
     * @return object  module object
     */
    static function CreatePageHistoryModule($pages_id)
    { 
        $m = new stdClass();
        
        $m->template        = 'revisionHistoryModule.tpl';
        
        if($pages_id)    
        {
            $m->history         = Page::GetRevisionHistory($pages_id);
          //  $m->id              = $id;  
            $m->live_rev    = $m->history->live_rev;
            $m->preview_rev = $m->history->preview_rev;      
        }
        return $m;
    }
    
    
    
   /**
     * returns a module object
     * @param int $id of content object
     * @return object  module object
     */
    static function CreateTargetsModule($id)
    {  
        $m = new stdClass();
        
        $m->template = 'targetsModule.tpl';
        $m->pages    = Page::getPages('ALL', true); 
       
        if($id > 0) 
            $m->targets  = Content::GetTargets($id);
                         
        return $m;         
    }
    
    
    
    
   /**
     * returns a module object
     * @param int $pages_rev of page object
     * @return object  module object
     */
    static function CreateModule2PageModule($pages_rev)
    {  
        global $CONFIG; 
        $m = new stdClass();
        $m->template = 'module2PageModule.tpl';

        // list of all modules for this site plus common modules
        $m->modules  = Module::GetModules($CONFIG->cms_site_code, true);        
      
        if($pages_rev)    
        {        
            $m->page_modules = Module::GetPageModules($pages_rev,false); 
        }    
        
        return $m;         
    }    
    
    
   /**
     * returns a module object  for all pages that use this module
     * @param int $id of module object
     * @return object  module object
     */
    static function CreateModuleUsageModule($module_id)
    {  
        global $CONFIG; 
        $m = new stdClass();
        $m->template = 'moduleUsageModule.tpl';
      
        if($module_id)    
        {        
            $m->pages = Module::GetPageLinks($module_id); 
            //dump( $module_id);  
        }    
        
        return $m;         
    }    
     
    
   /**
     * returns a module object  for all pages that use this module
     * @param int $id of module object
     * @return object  module object
     */
    static function CreateListAuthorsModule($id)
    {  
        global $CONFIG; 
        $m = new stdClass();
        $m->template = 'listModule.tpl';
      
        if($id)    
        {      
            $m->items      = Author::GetAuthors4User($id); 
            
            // the listModule.tpl uses the fields: $id and title   , so we have to crete aliases for these
            $m->items->SetAlias(array('id'=>'authors_id', 'title'=>'authors_display_name'));
            $m->title      = "Author Profiles for this User";
            $m->path       = "/cms/authors/";
            // add a button to the module
            $m->buttons    = array(array('url'=>'/cms/authors/new', 'text'=>'new Profile'));
        }         
        // dump( $m); 
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
