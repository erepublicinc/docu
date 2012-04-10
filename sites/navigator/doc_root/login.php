<?
require_once 'inc.navigator.php';
NavUser::Init();
$mSmarty = new Smarty();

/*
echo "<br>cookies:<br>"; dump($_COOKIE,false);
echo "<br>session:<br>"; dump($_SESSION,false);
die('done');
*/

if( !empty($_POST['email']))
{
  
    if($_POST['submit'] == 'Terminate other Session')  
    {
        $_SESSION['concurrent_session'] = 'terminate';
    }
    
    
    $email       = Query::Escape($_POST['email']);
    $remember_me = $_POST['remember_me'] == 1 ? 1 : 0;
    if(isset($_POST['password']))
        $ep = md5($_POST['password']);
    else 
        $ep = $_POST['ep'];    
    
    if(NavUser::Login($email, $ep, $remember_me))
    {  
        $loc = isset($_SESSION['redirect']) ? $_SESSION['redirect'] : '/';   
        header("LOCATION: $loc");
        exit; //---------------------------------> EXIT
    }
    else
    {
         $mSmarty->assign('error_msg', NavUser::$errorMessage);     
         $mSmarty->assign('email', $email);
    }   
}

if($_SESSION['concurrent_session'] == 'found')
{
    $mSmarty->assign('CONCURRENT_USER', 1);
    $mSmarty->assign('ep',$ep);
    $mSmarty->assign('email', $email);
    $mSmarty->assign('remember_me',$remember_me);
    
}



$mSmarty->assign('mShortSiteCode', $CONFIG->site_code);
$mSmarty->assign('mSiteTitle', getSiteName($CONFIG->site_code));

$css = '/css/'.strtolower($CONFIG->site_code).'.css';

$mSmarty->assign('mHtmlStylesheets', array('/css/navigator_main.css', $css));


$dir = $CONFIG->install_path.'/sites/navigator/tpl';
$mSmarty->setTemplateDir($dir);
$mSmarty->setCompileDir($dir.'/templates_c');
$mSmarty->setCacheDir($dir.'/cache'); 
$mSmarty->setConfigDir($dir.'/configs'); 

$mSmarty->display('nav_login.tpl');

