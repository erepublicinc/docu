<?
/* This configuration object is a Singleton only 1 instance is allowed
 * 
 * 
 */
class Configuration
{
    private $_mFields;
    private static $_mInstance = null; // the only instance
   
    public function __get($k)
    { 
       if(isset($this->_mFields[$k]))
            return $this->_mFields[$k];
       return false;     
    }
    
    public function __set($k,$v)
    {   
        logerror("trying to set config param: $k   use SetValue instead", 'Configuration');
        return null;
    }
    
    public function Dump()
    {
        echo("<pre>\nCONFIG<br>\n");
        foreach($this->_mFields as $key => $value)
        {
            if(stripos($key,'password')=== false)
               echo("$key => $value <br>\n");
            else 
               echo("$key => ******* <br>\n");
        }
        echo("</pre>\n");
    }
    
    
    
    /** 
     * Allow values to be set only when they are not present, or you have to specify 'FORCE'.
     * Using this function instead of __set()  eliminates accidental asignments
     * @param String $key
     * @param any   $value
     * @param overwrite default[ null]  should be set to 'FORCE' to overwrite a previous value
     */
    public function SetValue($k, $v, $overwrite=null)
    {   //echo("CONFIG set $k, $v  <br>");
        if($overwrite == 'FORCE' || ! isset($this->_mFields[$k]))
        {
            $this->_mFields[$k] = $v;
            return true;
        }
        return false;
    }
    
    /* This is the only way to get the config object
     * 
     */
    public static function InitConfig()
    {
        if(self::$_mInstance)
           return self::$_mInstance;
                 
        $fields = array();
        
        // set the environment
        
        $fields['environment'] =    true          ? 'LIVE': 'PREVIEW'; 
        
        
        $fileName =  PHP_OS === "WINNT" ? "C:/Program Files/Apache Software Foundation/Apache2.2/htdocs/web_db_config.ini"  
                                    :  "/var/www/html/web_db_config.ini";  ;        

        /* Reads a config file  in the format: key = value  , 
         * One key value pair per line
         * Lines starting with # are ignored
         * 
         */                                    
        $handle = @fopen($fileName, "r");
        if ($handle) {
            while (($buffer = fgets($handle, 4096)) !== false) {
                $buffer = trim($buffer);
                if(strlen($buffer) < 3)
                    continue;
                if(substr($buffer,0,1) == '#')
                    continue;
                $parts = explode('=',$buffer,2);
                $fields[$parts[0]] = $parts[1];
            }
            if (!feof($handle)) {
               logerror("Error(s) occurred while initializing the config file: ".$fileName);
            }
            fclose($handle);
        }
        else 
        {
             logerror("can't read the config file: ".$fileName);
             return false;
        }
        
       self::$_mInstance =  new Configuration();
       self::$_mInstance->_mFields = $fields;
         
       return self::$_mInstance;
    }
}
