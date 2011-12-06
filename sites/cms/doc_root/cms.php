<?
require_once('inc.basic.php');
if(! User::Authorize('EDITOR'))
   die('unauthorized'); 
 
Query::SetAdminMode();

$site = new CMSSite(); 
$site->Render();
