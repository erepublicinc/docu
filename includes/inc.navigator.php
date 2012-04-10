<?
require_once 'inc.basic.php';


// figure out the site_code
if( stripos($_SERVER['SERVER_NAME'],'vigatorgov' ) !== false)
    $code = "DGN";
elseif( stripos($_SERVER['SERVER_NAME'],'vigatored' ) !== false)
    $code = "DEN";
elseif( stripos($_SERVER['SERVER_NAME'],'vigatorem' ) !== false)
    $code = "EMN";
else 
    $code = "NAV";    

$CONFIG->SetValue('site_code', $code);