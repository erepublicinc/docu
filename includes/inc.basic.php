<?
require_once('Smarty.class.php'); // !!! needs to be loaded before registering our autoload function

// *********** GLOBALS
$CONFIG;

function __new_autoload($class_name)
{
    if ('parent' == $class_name)
    {
        return null;
    }
   
    return require_once "class.$class_name.php";
}
spl_autoload_register('__new_autoload'); // needed because of the new smarty (version 3)code 

// do the machine specific setup later the website may add additional settings 
$CONFIG = Configuration::InitConfig();
//$CONFIG->Dump();


// removes ; and '  to avoid sql injection 
function sanitize($str)
{
 return $str;   
    $str = str_replace("'", ' ', $str);
    return str_replace(';', ' ', $str);
}

function logerror($txt, $location)
{
    die("<h1>error</h1> $txt <br> $location"  );
}


/** like print_r   for objects
 * @param objct $p 
 */
function print_o($p)
{
    echo "<br>\n";
    $keys = get_object_vars($p);          
    foreach($keys as $field => $value)
    {
        echo("$field -> $value <br>\n");
    }
    echo "<br>\n";
}



function setup_smarty($test=false)
{
    global $CONFIG;
    require_once('Smarty.class.php');
    $smarty = new Smarty();
    $dir = $CONFIG->tpl_path;  
    $smarty->setTemplateDir($dir);
    $smarty->setCompileDir("$dir/templates_c");
    $smarty->setCacheDir($dir.'/cache'); 
    $smarty->setConfigDir($dir.'/configs');  
    
    // this line should be commented out for dev environments 
    if($CONFIG->environment != 'dev')
    {
       $smarty->setCompileCheck(false); 
       $smarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
       $smarty->setCacheLifetime(3600); // set the default to 1 hour
    }
    
    if($test)
        $smarty->testInstall(); // to check
    return $smarty;    
}
    
//  this debug class adds timing info to the bottom of the page in a comment
//  usage:    put this anywhere in the code    PageTimer::timeIt("MY label"); 
class PageTimer
{
    static  $labels=array();
    public static function timeIt($label)
    {
        self::$labels[] = array( $label, microtime(true));
    }
    
    // prints the timing results , called from website->Render()
    public static function printResults()
    {
        self::timeIt("end"); // this is the final timing record
        
        echo"\n <!--\n";
        $size= count(self::$labels);
        echo("0  (since last)  Start \n");
        for($i=1 ; $i < $size; $i++)
        {
           echo round(self::$labels[$i][1] - self::$labels[0][1],2);    echo "   (". round(self::$labels[$i][1] - self::$labels[$i-1][1],2). ")  " . self::$labels[$i][0] ."\n" ;
        }
        echo"  -->";
    }
}
PageTimer::timeIt("start"); // this is the first timing record


