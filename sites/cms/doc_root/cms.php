<?
require_once('inc.basic.php');

// set the site_code before we call authorize,  because when we log the login we need the site_code
$CONFIG->SetValue('tpl_path', $CONFIG->install_path . '/sites/cms/tpl');
$CONFIG->SetValue('site_code', 'CMS');

if(! User::Authorize('EDITOR'))
   die('unauthorized'); 
 
Query::SetAdminMode();

$site = new CMSRouter(); 
$site->Render();
