<?php
require_once 'inc.navigator.php';


//$CONFIG->dump();

//die('hello migration');
//NavUser::authorize('MIGRATION');

set_time_limit(0);

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
    case 'orgs1':     
        $page->migrateOrgs_part1();
        break;
    case 'orgs2':     
        $page->migrateOrgs_part2();
        break;
    case 'orgs3':     
        $page->migrateOrgs_part3();
        break;
    case 'orgs4':     
        $page->migrateOrgs_part4();
        break;                   
    case 'orgs5':     
        $page->migrateOrgs_part5();
        break;                   
        case 'all':    
         $page->migrateStates();
         $page->migrateAccounts();
         $page->migrateUsers();
         $page->migrateOrgs_part1();
         $page->migrateOrgs_part2();
         break;
    default:
        die("missing or incorrect 't' query parameter example    ?t=bids ");    
}

die("<br> --- DONE --- \n");
