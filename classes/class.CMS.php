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
     * returns a module object
     * @param int $pk of content object
     * @return object  module object
     */
    static function CreateTargetsModule($pk)
    {  
        $m = new stdClass();
        
        $m->template = 'targetsModule.tpl';
        $m->pages    = Page::getPages('ALL'); 
        
        if($c->contents_pk) 
            $m->targets  = Article::GetTargets($pk);      
           
       
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
