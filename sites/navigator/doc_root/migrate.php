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
    case 'orgs6':     
        $page->migrateOrgs_part6();
        break;       
    case 'agencies':     
        $page->agencies();
        break;                   
   case 'budgets':     
        $page->migrateBudgets();
        break;   
   case 'org-docs':
        $page->migrateOrgsDocs();     
        break;
    case 'contacts':    
        $page->migrateContacts();
        break;
        
   case 'bids1':     
        $page->bids1();    
        break;     
    case 'bids2':     
        $page->bids2();
        break;   
    case 'bids3':     
        $page->bids3();
        break;   
        
        
    case 'all':    
         $page->migrateStates();
         $page->migrateAccounts();
         $page->migrateUsers();
         $page->migrateOrgs_part1();
         $page->migrateOrgs_part2();
         $page->migrateOrgs_part3();
         $page->migrateOrgs_part4();
         $page->migrateOrgs_part5();
         $page->migrateOrgs_part6();
         $page->agencies();
         $page->migrateBudgets();
         $page->migrateOrgsDocs();        
         $page->migrateContacts();
   /*      
         $page->bids1();
         $page->bids2();
         $page->bids3();
   */      
         break;
    default:
        die("missing or incorrect 't' query parameter example    ?t=bids ");    
}

die("<br> --- DONE --- \n");
