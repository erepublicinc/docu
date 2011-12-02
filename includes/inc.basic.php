<?
//r_reporting(E_ERROR | E_WARNING | E_PARSE);

require_once('Smarty.class.php'); // !!! needs to be loaded before registering our autoload function

// *********** GLOBALS
$SITENAMES = array( 'GT'=>'Government Technology','GOV'=>'Governing', 'ALL'=> 'All eRepublic sites', 
                    'EM'=>'Emergency Management Magazine', 'CV'=>'Converge Magazine', 'ER'=>'eRepublic',
                    'CDG'=>'Center for Digital Government', 'CDE'=>'Center for Digital Education',
                    'DC'=>'Digital Communities');
$CONFIG;

define('LATEST_VERSION', -1);
define('LIVE_VERSION', 0);



function __new_autoload($class_name)
{
    if ('parent' == $class_name)
    {
        return null;
    }
   
    return require_once "class.$class_name.php";
}
spl_autoload_register('__new_autoload'); // needed because of the new smarty (version 3)code 

// do the machine specific setup. Later the website may add additional settings 
$CONFIG = Configuration::InitConfig();
//$CONFIG->Dump();  // to checkout the settings


// removes ; and '  to avoid sql injection 
function sanitize($str)
{
 return $str;   
    $str = str_replace("'", ' ', $str);
    return str_replace(';', ' ', $str);
}

/**
 * removes everything but:   a-z A-Z 0-9 - _
 * @param string
 * @return string
 */
function filter_ABC123($str)
{
    $pattern = '/[^a-zA-Z0-9_]/i';
    echo preg_replace($pattern, '', $str);
}

function logerror($txt)
{
	global $CONFIG;
	echo("\n<h1>error</h1><b> $txt </b><br>\n");
	
	$frames = debug_backtrace();
	$first = array_shift($frames);
	$pfile =  $file = array_pop(explode('/',$first['file']));
	foreach($frames as $f)
	{   
	   $file = array_pop(explode('/',$f['file']));
	   $arguments = implode(' , ',$f['args']);
	   echo("<b> $file </b>line: <b>". $f['line'] ."</b> calls: <b>{$pfile}:: ". $f['function']."</b> ( $arguments ) <br>\n");
	   $pfile = $file;   
	}
	
	if( ! $CONFIG->is_test )
    	die("<br>die");
}



/*
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
*/

function getSiteName($code)
{
    global $SITENAMES;
    return $SITENAMES[$code];
}


function dump($v, $die = true)
{
	global $CONFIG;
	
    if(is_object($v) &&  $v instanceof Query)
        $v = $v->ToArray();

    $frames = debug_backtrace();    
    $f= $frames[0];    
    echo ("dump() called from: ".$f['file'].' line: '. $f['line']);
    
    echo("<pre>"); 
    //var_dump($v);
    print_r($v);
    if($die )
    {
     flush();   die;
    }
}

// =================================== PAGE TIMER ====================================================    
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
        
        $sname = getenv('HOSTNAME');
        echo "server = $sname ". $_SERVER['SERVER_ADDR'];
        
        echo"  -->";
    }
}
PageTimer::timeIt("start"); // this is the first timing record


