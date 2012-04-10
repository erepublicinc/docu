<?php
require_once 'inc.basic.php';
$CONFIG->SetValue('db_dbname','navigator', true);
$CONFIG->SetValue('site_code', 'NAV');

//$CONFIG->dump();

//die('hello migration');
NavUser::authorize('MIGRATION');


$page = new MigrationController();
switch($_GET['t'])
{
    case 'states':
        $page->migrateStates();
        break;
    case 'accounts':    
        $page->migrateAccounts();
        break;
    case 'users':    
        $page->migrateUsers();
        break;
    case 'all':    
         $page->migrateStates();
         $page->migrateAccounts();
         $page->migrateUsers();
    default:
        die("missing or incorrect 't' query parameter example    ?t=bids ");    
}