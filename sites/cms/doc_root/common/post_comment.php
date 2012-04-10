<?
require_once('inc.basic.php');

Comment::addComment($_POST);
$redir = $_POST['redirect_url']."?reload";
if(!empty($_POST['redirect_url']))
    header("LOCATION: $redir");

die('thanks for commenting');
