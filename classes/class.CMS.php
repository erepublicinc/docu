<?php

class CMS
{
    
    static function Init()
    {
        echo "init cms <br>";
        
        if(! User::Authorize("cms"))
           die("you are not authorized to use the cms");
    }
    
    
}
