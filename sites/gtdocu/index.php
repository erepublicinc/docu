<?
require_once 'inc.basic.php';
$site = new DocuSite();

User::Authorize(); 

$smarty = setup_smarty();

$smarty->assign('docu', Documentation::GetDocumentation());

//$smarty->debugging = true;

$smarty->display('docu.tpl');




