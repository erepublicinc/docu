<?
require_once 'inc.basic.php';
if(!NavUser::Authorize('SUPER_ADMIN',false))
    die('you must be admin');

phpinfo();
