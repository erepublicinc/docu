<?
/*

migration   decisions:
- dont migrate k12 
- import all school districts from nces list 

- don't migrate libraries
- import libraries when reloading erates from the source files

- don't migrate erates
- reload erates from the source files

- when to fold a jurisdiction with a single agency into a single org  yes
  copy addres into a "puchasing contact", HEAD of PURCHASING

- if a issueing ag is not linked to a contact, it can be removed, link the bid to the 'using agency'

- when migrating bid:
	- copy the contact info into the bid
	
- add phone number to org

- functional org types:      for city, county, state , univ, k12, (all but regional)   = ADMIN
  for REGIONAL   : TRANPORTATION,  UTILITIES OR something else


*/



/*

REPLACE INTO `item` (`item_name`, items_in_stock) VALUES( 'A', 27)

-- this will isert OR delete and insert (I expect problems with foreign keys etc
-- so as an alternative do:

INSERT INTO `item` (`item_name`, items_in_stock) VALUES( 'A', 27)
ON DUPLICATE KEY UPDATE  `new_items_count` = `new_items_count` + 27

TRUNCATE TABLE navigator.nav_accounts; 
ALTER TABLE navigator.nav_accounts AUTO_INCREMENT = 1;


 */


class MigrationController
{
    private $oldDB ;
    private $batch = array(); // we write the records in batches
    private $numResults;
    private $result;
    
    function __construct()
    {
        global $CONFIG;
        $this->batch = array();
        
        Query::SetAdminMode();
      //  Query::IgnoreUniqueErrors();
        
        $server = 'sqltest';
        $user   = $CONFIG->db_user;
        $pw     = $CONFIG->db_password;
        $dbname = 'navigator_dev';
        
        $this->oldDB = mssql_connect($server, $user, $pw);
        if($this->oldDB == false)
        {
            $err = mssql_get_last_message();
            logerror("cannot connect to old db $server $user $pw <hr>$err");
        }
        if(! mssql_select_db($dbname, $this->oldDB))
        {
            $err = mssql_get_last_message();
            logerror("cannot select database: $dbname <hr>$err");
        }
    }
    
    
    function migrateStates()
    {
        echo "migrating states <br><br>";
       
        
        $this->loadRows('select * from states');
        while(true)
        {
            $r = $this->nextRow();
            if($r === false)
                break;
           echo "$r->code <br>";  
           $title = trim($r->title);  
           $sql = "INSERT INTO states (states_code,states_title,states_pk) VALUES('$r->code','$title', $r->pk)";
           $this->save($sql);
        } 
        
        
        $this->finish();
        
        $sql="update states ,reference.k12_districts 
			set states_fips_code = substr(state_fips,1,2) 
			where states_code = mail_state";

        $this->save($sql);
        $this->finish();
        
    }
    
 function migrateAccounts()
 {
     echo "migrate Accounts <br><br>";
            
        $this->loadRows("select  * from accounts  ");
        
        $sql=array();
        $sql[] ="delete from  navigator.licenses where licenses_accounts_fid > 1";
        //$sql[] ="ALTER TABLE navigator.nav_accounts AUTO_INCREMENT = 5";
                
        $sql[] ="delete from  navigator.accounts where accounts_id > 1"; 
        //$sql[] ="ALTER TABLE navigator.accounts AUTO_INCREMENT = 5 ";
        Query::sTransaction($sql);
      
        while(true)
        {
            $sql = array();
            $found = false;
            
            $r = $this->nextRow();
            if($r === false)
                break;
                
           $num++;     
                
           echo "<br>$num $r->title ";  
           
           if($r->pk   ==  417102 )  // skip erepublic
              continue;       
           
           $title = trim($r->title);  
           $etitle = Query::Escape($title);
           $sql[] = "INSERT INTO accounts (accounts_title, accounts_sf_id, accounts_sf_deleted,accounts_is_navigator, accounts_pk) 
                  VALUES('$etitle', '$r->sf_account_id', $r->sf_deleted, true, $r->pk)";    
           
           
           $sql[] ="SELECT @acc_id:= LAST_INSERT_ID()";
           
           if($r->dgn_access_level == 'STANDARD' || $r->dgn_access_level == 'PREMIUM')
           {
               $time =  strtotime( $r->dgn_contract_expiration);
               $date =  date("Y-m-d" ,$time);
               $sql[] = "INSERT INTO licenses (licenses_accounts_fid, licenses_site_code, licenses_access_level,licenses_number_of_users,licenses_status,licenses_expiration,licenses_monthly_bonus_hours,licenses_account_rep_fid) 
                      VALUES(@acc_id,'DGN','$r->dgn_access_level',$r->dgn_licenses,'$r->dgn_contract_status','$date',$r->dgn_monthly_bonus_hours,1 )";      
               echo"DGN ";
               $found = true;
           }
           if($r->den_access_level == 'STANDARD' || $r->den_access_level == 'PREMIUM')
           {
               $time =  strtotime( $r->den_contract_expiration);
               $date =  date("Y-m-d" ,$time);
               $sql[] = "INSERT INTO licenses (licenses_accounts_fid, licenses_site_code, licenses_access_level,licenses_number_of_users,licenses_status,licenses_expiration,licenses_monthly_bonus_hours,licenses_account_rep_fid) 
                                     VALUES(@acc_id,'DEN','$r->den_access_level',$r->den_licenses,'$r->den_contract_status','$date',$r->den_monthly_bonus_hours,1 )";      
               echo"DEN ";
               $found = true;
           }
           if($r->emn_access_level == 'STANDARD' || $r->emn_access_level == 'PREMIUM')
           {
               $time =  strtotime( $r->emn_contract_expiration);
               $date =  date("Y-m-d" ,$time);
               $sql[] = "INSERT  INTO licenses (licenses_accounts_fid, licenses_site_code, licenses_access_level,licenses_number_of_users,licenses_status,licenses_expiration,licenses_monthly_bonus_hours,licenses_account_rep_fid) 
                                     VALUES(@acc_id,'EMN','$r->emn_access_level',$r->emn_licenses,'$r->emn_contract_status','$date',$r->emn_monthly_bonus_hours,1 )";      
               echo"EMN ";
               $found = true;
           }
           
           
           // we migrate only the accounts that are active 
           if($found)
           {
           
             Query::sTransaction($sql);
           }
         
             
           flush();
        } 
        
 }    
    
    
function migrateUsers()
{ 

   // create array from accounts with id and pk
   $accounts = array();
    $sql="select accounts_pk, accounts_id from accounts";
   $ac = new Query($sql);
   foreach($ac as $a)
       $accounts[$a->accounts_pk] = $a->accounts_id;

    
echo "creating UserGroups <br><br>";       
    $this->loadRows("select * from groups where not code in ('ADMIN','SUPER_ADMIN')");
    while(true)
    {
        $r = $this->nextRow();
        if($r === false)
            break;
       echo "$r->code <br>";  
       $title = trim($r->title);  
       $sql = "insert into usergroups(usergroups_code, usergroups_title, usergroups_summary) values('$r->code', '$title','$r->summary')";
       $this->save($sql);
    } 
    $this->finish();
        

       
echo "migrate Users <br><br>";
        
    $this->loadRows('select * from users u join accounts#users ac on  ac.fk2 = u.pk  ');
    while(true)
    {
        $r = $this->nextRow();
        if($r === false)
            break;
       echo "$r->code <br>";  

       
       $lastname   = Query::Escape($r->last_name);
       $firstname  = Query::Escape($r->first_name);
       $jobtitle   = Query::Escape($r->job_title);
       $email      = Query::Escape($r->email);
       $account_id = $accounts[$r->fk1];
       
       if($r->pk == 3)  // skip mtel
           continue;
       
       if($account_id > 0)
       {
           $sql = "INSERT INTO users (users_pk,users_last_name,users_first_name,users_job_title,users_salesforce_fid,users_password,users_email,users_ad_user,users_accounts_fid,users_active) 
           VALUES($r->pk,'$lastname','$firstname','$jobtitle','$r->sf_nav_user_id','$r->password','$email','$r->ad_user',$account_id,$r->user_active)";
           
           echo "$r->email <br>";
           $this->save($sql);
       }
       //else         echo " NOT $r->pk $r->email <br>";
    } 
    
   $this->finish();
    
    
}
        
 function migrateUsers_part2()        
 {      
/*
        echo "link users to groups <br><br>";
        
        $this->loadRows("select  * from users u  join groups#users gu  join groups g on g.pk = gu.fk1  on u.pk = gu.fk2 order by u.pk");
        while(true)
        {
            $r = $this->nextRow();
            if($r === false)
                break;
           echo "$r->code <br>";  
           $sql = "INSERT INTO users_x_usergroups (users_fid,usergroups_fcode) 
           VALUES($r->users_id,'$r->groups_code')";
           
           $this->save($sql);
        } 
        $this->finish();
*/        
        
  }
    
    
function migrateOrgs_part1()
{
    
   
    $this->save("delete from navigator.ed_org_properties");
    $this->save("delete from   navigator.gov_org_properties");
    $this->save("delete from  navigator.orgs");
     $this->save("delete from  navigator.org_types");
    $this->save("delete from  navigator.org_functional_types");
    $this->finish();
    
    // org_functional types
    $this->loadRows("SELECT * FROM agency_types");
    while(true)
    {
        $r = $this->nextRow();
        if($r === false)
            break;
       echo "$r->code <br>";  
       $title = trim($r->title);  
       $sql  = "insert into org_functional_types(oft_code, oft_description, oft_den,oft_dgn,oft_emn,oft_cms_only,oft_pk)
               values('$r->code', '$title',$r->den,$r->dgn,$r->emn,$r->cms_only,$r->pk)";
       $this->save($sql);
    } 
    $this->finish();
    
    
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('CITY','City',1,1,1,0)"; 
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('COUNTY','County',1,1,1,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('STATE','Stae',1,1,1,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('CONSOLIDATED','Consolidated',1,1,1,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('REGIONAL','Regional',1,1,1,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('UASI','UASI',0,0,1,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('FUSION_CENTER','Fusion Center',0,0,1,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('MMRS','MMRS',0,0,1,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('K12','K12 School',0,1,0,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('DOE','DOE',0,1,0,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('COMMUNITY_COLLEGE','Community College',0,1,0,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('PRIVATE_UNIVERSITY','Private University',0,1,0,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('PUBLIC_UNIVERSITY','Public University',0,1,0,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('AGENCY','Agency',0,1,0,0)";
    $this->batch[]  = "INSERT INTO org_types (ot_code,ot_description,ot_dgn,ot_den,ot_emn,ot_cms_only)  VALUES('UNIVERSITY_SYSTEM','University System',0,1,0,0)";
    
    
    $this->finish();
    // org_types
    
}

// migrate the counties and consolidated
function migrateOrgs_part2()
{
   echo "<h2>migrating COUNTY jurisdictions and adding fips codes</h2>"; flush();
    
    $counter =0; 
         
    //counties
    $sql = "select j.pk, j.title, j.abbreviation, j.subtype, j.url,j.population, j.government_employees,j.executive_departments,j.boards_and_commisions,j.judicial_departments,j.budget_cycle,j.legislative_departments,j.subtype, j.fiscal_year_begins,j.funding_tier,s.code as statecode
            from jurisdictions j
            join jurisdictions#states js  
            join states s on s.pk = js.fk2
            on js.fk1 = j.pk          
            where subtype IN ('COUNTY','CONSOLIDATED')  ";
    $this->loadRows($sql);
    while(true)
    {
       
        $r = $this->nextRow();
        if($r === false)
            break;
            
       $counter++;     
       echo "$counter $r->title <br>";
       
       $title = Query::Escape($r->title);
       $stitle = strtoupper($title);
        $stitle = str_replace(' (CONSOLIDATED)','',$stitle);  
       $stitle = str_replace(' COUNTY','',$stitle); 
       $stitle = str_replace(' PARISH','',$stitle);
       $stitle = str_replace('SAINT ','ST. ',$stitle);  
       $stitle = str_replace(' BOROUGH',' (B)',$stitle);   
       $year_begins = intval($r->fiscal_year_begins);
       $tier = intval($r->funding_tier);
       $population = intval($r->population);
      // if($r->statecode =='LA')echo "$title  <---> $stitle  $r->statecode<br>"; 
       
       $sql = "INSERT INTO orgs (orgs_canonical_title, orgs_title, orgs_abbreviation, orgs_type_code, orgs_functional_type_code, orgs_state_code,orgs_fiscal_year_begins,orgs_budget_cycle,orgs_url,orgs_pk)
       					 VALUES('$stitle','$title','$r->abbreviation','$r->subtype','ADMIN','$r->statecode',  $year_begins, '$r->budget_cycle','$r->url',$r->pk)";
       //echo($sql." <br>\n") ;
       $this->save($sql);
       
       if($population > 0)
       {
           $this->save("SELECT @orgs_id:= LAST_INSERT_ID()");
           
           $sql = "INSERT INTO gov_org_properties(orgs_fid,population,government_employees,executive_departments,boards_and_commissions,judicial_departments,legislative_departments,funding_tier) 
                 VALUES(@orgs_id, $population, $r->government_employees, $r->executive_departments, $r->boards_and_commisions, $r->judicial_departments, $r->legislative_departments, $tier)";
           $this->save($sql);
       }
        $this->finish();
    } 
   
    
    
   
    // find the problem counties
    $sql= "select orgs_canonical_title, orgs_title, orgs_state_code from orgs where not orgs_id in
			(select orgs_id from orgs join reference.counties_fips r on r.county = orgs.orgs_canonical_title) order by orgs_state_code";    
    $results = new Query($sql);
    $c= 0;
    foreach($results as $r)
    {
        if($c == 0)
           echo "<br>the following counties do not match up with the counties_fips <br><br>";
        $c++;   
        echo(" $r->orgs_state_code $r->orgs_canonical_title  $r->orgs_title  <br>");
    }

    
   
    $sql="select distinct orgs_canonical_title, orgs_state_code from orgs
        where not orgs_canonical_title = ''
        group by orgs_canonical_title, orgs_state_code
        having count(*) >1";
    $results = new Query($sql);
    $c = 0;
    foreach($results as $r)
    { 
        if ($c == 0)
            echo "<br> the following counties have a duplicate canonical title<br>";  
        $c++;
        echo("$c state: $r->orgs_state_code  canonical: $r->orgs_canonical_title  title: $r->orgs_title  <br>"); flush();
    }
    
    if($c < 1 )   // no duplicates to make this fail
    {    // put a fipscode in the counties
         $sql="update orgs, reference.counties_fips
    		set orgs_fips_code = fipscode
    		where orgs_type_code = 'COUNTY'
    		and orgs_canonical_title = county
    		and orgs_state_code    = state";
         $results = new Query($sql);
    
 //Query::$echo_sql =true; 
            echo"<br> now we add the rest of the counties from the reference table <br>";
            $sql= "select * from reference.counties_fips";    
            $results = new Query($sql);
            foreach($results as $r)
            {
               $title = ucwords(strtolower(Query::Escape($r->county))); 
             
               $sql = "INSERT IGNORE INTO orgs ( orgs_title, orgs_type_code, orgs_state_code,orgs_fips_code)
                VALUES('$title','COUNTY','$r->state','$r->fipscode')";
               $this->save($sql);      
                echo(" $r->state  $title  <br>");
            }
            $this->finish();
    
    }
}    
     
     
////////////// the rest of the gov 
function migrateOrgs_part3()
{  
   echo "<h2>migrating STATE, CITY and REGIONAL jurisdictions</h2>"; flush();
    
    $sql = "select j.pk, j.title, j.abbreviation, j.subtype, j.url,j.population, j.government_employees,j.executive_departments,j.boards_and_commisions,j.judicial_departments,j.budget_cycle,j.legislative_departments,j.subtype, j.fiscal_year_begins,j.funding_tier,s.code as statecode
            from jurisdictions j
            join jurisdictions#states js  
            join states s on s.pk = js.fk2
            on js.fk1 = j.pk          
            --where subtype in  ('STATE', 'CITY','REGIONAL' ) 
            where NOT  subtype IN ('COUNTY','CONSOLIDATED')
            and j.pk not in 
            (   -- exclude stand-alone libraries
                select i.pk   
                from agencies a 
                join agencies#jurisdictions ai
                    join jurisdictions i on i.pk = ai.fk2
                on a.pk = ai.fk1
                join agencies#agency_types aat on aat.fk1 = a.pk
                where aat.fk2  = 12605 -- libraries
                and i.title = a.title   -- standalone libraries
                and i.subtype = 'REGIONAL'          
            ) ";
    $this->loadRows($sql);
    
    while(true)
    {
        $r = $this->nextRow();
        if($r === false)
            break;
       
        $s = trim($r->subtype)  ;  
        if(empty($s))
        {
            echo "skip $r->title   empty subtype";
            continue;
        }    
            
        $title = Query::Escape($r->title);

        $tier = intval($r->funding_tier);
        $year_begins = intval($r->fiscal_year_begins);
        $population = intval($r->population);
        
        $org_type = $r->subtype;
        if(stripos($r->title,'Library') >0 )
           continue;     // we take the libray info from the agency     $org_type = 'LIBRARY';
        
        if(strpos($r->title,'Lib') >0 )
        {
           echo"=== Considered to be a library:  $r->title <br>";
           continue;     // we take the libray info from the agency     $org_type = 'LIBRARY';   
        }  

        $function_code = 'ADMIN';
        if($org_type == 'REGIONAL')
        {
                        
            if(stripos($r->title,'transport') !== FALSE )     $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,'aviation') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,'transit') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,'airport') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,'airport') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,' toll ') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,' tolway ') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,'turnpike') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif(stripos($r->title,'railroad') !== FALSE )  $function_code = 'TRANSPORTATION';
             
            elseif(stripos($r->title,' port ') !== FALSE )  $function_code = 'TRANSPORTATION';
            elseif (stripos($r->title,'water quality') !== FALSE )    $function_code = 'ENVIRONMENT';
            elseif (stripos($r->title,'water reclamation') !== FALSE )    $function_code = 'ENVIRONMENT';
            elseif (stripos($r->title,'water') !== FALSE )    $function_code = 'UTILITIES';
            elseif (stripos($r->title,'sewer') !== FALSE )    $function_code = 'UTILITIES';   
            elseif (stripos($r->title,'utilit') !== FALSE )   $function_code = 'UTILITIES'; 
            elseif (stripos($r->title,'power') !== FALSE )    $function_code = 'UTILITIES';      
            elseif (stripos($r->title,'gas') !== FALSE )    $function_code = 'UTILITIES';      
            elseif (stripos($r->title,'correction')!== FALSE) $function_code = 'CORRECTIONS';      
            elseif (stripos($r->title,'jail')!== FALSE) $function_code = 'CORRECTIONS';      
            elseif (stripos($r->title,'parks') !== FALSE )    $function_code = 'ENVIRONMENT';      
            elseif (stripos($r->title,'park ') !== FALSE )    $function_code = 'ENVIRONMENT';      
            elseif (stripos($r->title,'housing') !== FALSE )  $function_code = 'PLANNING';      
            elseif (stripos($r->title,'planning') !== FALSE )    $function_code = 'PLANNING';      
            elseif (stripos($r->title,'development') !== FALSE ) $function_code = 'CORRECTIONS';      
            elseif (stripos($r->title,'health') !== FALSE ) $function_code = 'HEALTH';      
            elseif (stripos($r->title,'hospital') !== FALSE ) $function_code = 'HEALTH';      
            elseif (stripos($r->title,'medical') !== FALSE ) $function_code = 'HEALTH';      
            elseif (stripos($r->title,'librar') !== FALSE ) $function_code = 'LIBRARIES';      
            elseif (stripos($r->title,'fire') !== FALSE ) $function_code = 'FIRE';      
            elseif (stripos($r->title,'police') !== FALSE ) $function_code = 'POLICE';      
            elseif (stripos($r->title,'sheriff') !== FALSE ) $function_code = 'POLICE';      
            
        }
        $counter++;    
        echo "$counter $r->title <br>";  flush(); 
        $sql = "INSERT INTO orgs ( orgs_title, orgs_abbreviation, orgs_type_code, orgs_functional_type_code, orgs_state_code,orgs_fiscal_year_begins,orgs_budget_cycle,orgs_url,orgs_pk)
       					 VALUES('$title','$r->abbreviation','$org_type','$function_code','$r->statecode',  $year_begins, '$r->budget_cycle','$r->url',$r->pk)";
        //echo($sql." <br>\n") ;
        $this->save($sql);
        
        if($population > 0)
        {
        
            $this->save("SELECT @orgs_id:= LAST_INSERT_ID()");
       
            $sql = "INSERT INTO gov_org_properties(orgs_fid,population,government_employees,executive_departments,boards_and_commissions,judicial_departments,legislative_departments,funding_tier) 
             VALUES(@orgs_id, $population, $r->government_employees, $r->executive_departments, $r->boards_and_commisions, $r->judicial_departments, $r->legislative_departments, $tier)";
           $this->save($sql);
        }
        $this->finish();
    } 
   
    
}
 


// institutions all but k12   
function migrateOrgs_part4()
{
       echo "<h2>migrating All institutions except K12 and libraries </h2>"; flush();
    
    //$this->save("delete from  navigator.orgs");
    $counter =0;
    
        $sql = "select j.pk, j.title, j.abbreviation, j.subtype, j.url,j.enrollment, j.total_k12_spending,j.classroom_teachers,j.spending_per_student,j.students_per_computer_ratio,j.budget_cycle,j.school_districts,j.fiscal_year_begins,s.code as statecode
            from institutions j
            join institutions#states js  
            join states s on s.pk = js.fk2
            on js.fk1 = j.pk          
            where not subtype = 'K12' 
            and j.pk not in 
            (   -- exclude stand-alone libraries
                select i.pk   
                from agencies a 
                join agencies#institutions ai
                    join institutions i on i.pk = ai.fk2
                on a.pk = ai.fk1
                join agencies#agency_types aat on aat.fk1 = a.pk
                where aat.fk2  = 12605 -- libraries
                and i.title = a.title   -- standalone libraries
                and i.subtype = 'REGIONAL'          
            ) ";
            
    $this->loadRows($sql);
    
    while(true)
    {
        $r = $this->nextRow();
        if($r === false)
            break;
       
        $title = Query::Escape($r->title);   
        $year_begins = intval($r->fiscal_year_begins);
        $ratio = intval($r->students_per_computer_ratio);
        $spending = intval($r->total_k12_spending);
        $enrollment =intval($r->enrollment);
        $teachers =intval($r->classroom_teachers);
        $spending_student = intval($r->spending_per_student);
        $districts = intval($r->school_districts);
        
        $org_type = $r->subtype;
        if(stripos($r->title,'Library') !== FALSE )
           continue;     // we take the libray info from the agency     $org_type = 'LIBRARY';
        
        if(strpos($r->title,'Lib') !== FALSE )
        {
           echo"=== Considered to be a library:  $r->title <br>";
           continue;     // we take the libray info from the agency     $org_type = 'LIBRARY';   
        }  
           
        $counter++;    
        echo "$counter $r->title <br>";  flush(); 
        $sql = "INSERT INTO orgs ( orgs_title, orgs_abbreviation, orgs_type_code, orgs_state_code,orgs_fiscal_year_begins,orgs_budget_cycle,orgs_url,orgs_pk)
       					 VALUES('$title','$r->abbreviation','$org_type','$r->statecode',  $year_begins, '$r->budget_cycle','$r->url',$r->pk)";
        //echo($sql." <br>\n") ;
        $this->save($sql);
        
        if($ratio + $spending + $enrollment + $teachers + $spending_student + $districts  > 1 )
        {
            $this->save("SELECT @orgs_id:= LAST_INSERT_ID()");
           
            $sql = "INSERT INTO ed_org_properties(orgs_fid,total_k12_spending,enrollment,classroom_teachers,spending_per_student,students_per_computer_ratio,school_districts) 
                      VALUES(@orgs_id, $spending, $enrollment, $teachers, $spending_student, $ratio, $districts)";
            $this->save($sql);
        }
        $this->finish();
    } 
    
}
    
// load our 1000 active k12  with the nces number 
function migrateOrgs_part5()
{
    echo("Load the about 1000 k12's that have non erate bids attached to them, and add nces and entity_id's to them");
 
    echo" not ready yet ";
    $sql="  select i.pk, i.title, i.abbreviation, i.subtype, i.url, i.enrollment, i.total_k12_spending, i.classroom_teachers, 
             i.spending_per_student, i.students_per_computer_ratio, i.budget_cycle, i.school_districts, 
             i.fiscal_year_begins,s.code as statecode 
            from institutions i 
            join institutions#states ist
                 join states s on s.pk = ist.fk2
            on i.pk = ist.fk1     
            join  agencies#institutions ai
               join agencies#bids ab
                   join bids b on b.pk = ab.fk2
               on ai.fk1 = ab.fk1
            ON i.pk = ai.fk2           
            where  i.subtype = 'K12'
            and NOT b.subtype  = 'ERATE' 
            order by i.pk ";
    
        $this->loadRows($sql);
        
    $currentPk = 0;
    while(true)
    {
        $r = $this->nextRow();
        if($r === false)
            break;
       
        // the query returns many duplicates  so we have to skip those    
        if($currentPk == $r->pk )
            continue;         
        $currentPk = $r->pk ;       

        
        $title = Query::Escape($r->title);   
        $year_begins = intval($r->fiscal_year_begins);
        $ratio = intval($r->students_per_computer_ratio);
        $spending = intval($r->total_k12_spending);
        $enrollment =intval($r->enrollment);
        $teachers =intval($r->classroom_teachers);
        $spending_student = intval($r->spending_per_student);
        $districts = intval($r->school_districts);
        
        $org_type = $r->subtype;
           
        $counter++;    
        echo "$counter $r->title <br>";  flush(); 
        $sql = "INSERT INTO orgs ( orgs_title, orgs_abbreviation, orgs_type_code, orgs_state_code,orgs_fiscal_year_begins,orgs_budget_cycle,orgs_url,orgs_pk)
       					 VALUES('$title','$r->abbreviation','$org_type','$r->statecode',  $year_begins, '$r->budget_cycle','$r->url',$r->pk)";
        //echo($sql." <br>\n") ;
        $this->save($sql);
        
        if($ratio + $spending + $enrollment + $teachers + $spending_student + $districts  > 1 )
        {
            $this->save("SELECT @orgs_id:= LAST_INSERT_ID()");
           
            $sql = "INSERT INTO ed_org_properties(orgs_fid,total_k12_spending,enrollment,classroom_teachers,spending_per_student,students_per_computer_ratio,school_districts) 
                      VALUES(@orgs_id, $spending, $enrollment, $teachers, $spending_student, $ratio, $districts)";
            $this->save($sql);
        }
        $this->finish();
    } 
   
}

// load the school districts from the nces table.  if  k12 already exist, update the address 
function migrateOrgs_part6()
{  
       echo "<h2>create K12 districts from nces reference table </h2>
             if  k12 already exist, update the address "; flush();
    
    $counter =0;
    //   exclude   dod overseas  fipscode : 58
    $sql = "select * from reference.k12_districts where NOT substr(state_fips,1,2) = '58' AND NOT mail_state ='AP' ";
    $results = new Query($sql);    
        
    foreach($results as $r)
    {
       
        $title = ucwords(strtolower(Query::Escape($r->name)));
        $title .= ' ';   
        $title = str_replace(" Isd ", " Independent School District ", $title);
        $title = str_replace(" Co ", " County ", $title);
        $title = str_replace(" Schs ", " Schools ", $title);
        $title = str_replace(" Pblc ", " Public ", $title);
        $title = str_replace(" Sd ", " School District ", $title);
              
        $city  = ucwords(strtolower($r->mail_city));
        $address = ucwords(strtolower(Query::Escape($r->mail_adress)));
        //$year_begins = intval($r->fiscal_year_begins);
        //$ratio = intval($r->students_per_computer_ratio);
        //$spending = intval($r->total_k12_spending);
        $enrollment =intval($r->students);
        //$teachers =intval($r->classroom_teachers);
        //$spending_student = intval($r->spending_per_student);
        //$districts = intval($r->school_districts);
        $spending = $teachers = $spending_student = $spending_student = $districts =0;
        
           
        $counter++;    
        echo "$counter $title <br>";  flush(); 
        
        // NOTE: we store the county fips in the url (temporary)
        $sql = "INSERT INTO orgs ( orgs_title, orgs_type_code, orgs_state_code, orgs_nces_code,orgs_address,orgs_city,orgs_state,orgs_zip,orgs_phone,orgs_subtype,orgs_tmp_county_fips)
       					 VALUES('$title','K12','$r->mail_state','$r->ncescode','$address','$city','$r->mail_state','$r->mail_zip','$r->phone','$r->district_type','$r->county_fips')
                ON DUPLICATE KEY UPDATE  orgs_address = '$address' ,orgs_city = '$city' , orgs_zip = '$r->mail_zip', orgs_phone = '$r->phone', orgs_tmp_county_fips = '$r->county_fips' ";
        //echo($sql." <br>\n") ;
        $this->save($sql);
        
       
        $this->save("SELECT @orgs_id:= LAST_INSERT_ID()");
       
        // this will fail silently if the previous operation was an update
        $sql = "INSERT IGNORE INTO ed_org_properties(orgs_fid,total_k12_spending,enrollment,classroom_teachers,spending_per_student,students_per_computer_ratio,school_districts) 
                  VALUES(@orgs_id, $spending, $enrollment, $teachers, $spending_student, $spending_student, $districts)";
        $this->save($sql);

        $this->finish();
             
    } 
    
    
     // now we link the records up to the county through the fips number

    $sql = "UPDATE orgs o1, orgs o2
			SET o1.orgs_parent = o2.orgs_id
			WHERE o1.orgs_tmp_county_fips = o2.orgs_fips_code";
    $this->save($sql);   

   
    
    $this->finish();
    
}
 


function agencies ()
{
    
    
     $this->save("delete from orgs where orgs_type_code = 'AGENCY'");
     $this->finish();
    
    echo "creates the agencies that have at least 1 person connected to them <br>";
    /* these are agencies that we didnot bring over
     select * from agencies a
   join agencies#agency_types aat
     join agency_types att on att.pk = aat.fk2
  on a.pk = aat.fk1
where is_jurisdiction_agency = 1
and not a.pk in ( select fk1 from agencies#contacts )
order by att.pk
    
     */
    
    
    // question : do we merge the EXECUTIVE agencies into the org ? (instead of making a separate agency)
    
    
    // only get agencies with a contact // or the uasi agencies marked as jurisdiction agency
    $sql = "select  att.code as func_code, coalesce(aj.fk2, ai.fk2) as orgs_pk , a.title as a_title, a.pk as a_pk , aj.fk2 as j_pk, j.subtype , coalesce(j.title, i.title ) as jurinst_title
            from agencies a
               join agencies#agency_types aat
                 join agency_types att on att.pk = aat.fk2
               on a.pk = aat.fk1
              
              left join agencies#jurisdictions aj 
                 join jurisdictions j on j.pk =aj.fk2
              ON a.pk = aj.fk1
                 
              left join agencies#institutions ai 
                  join institutions i on i.pk =ai.fk2
              ON a.pk = ai.fk1
              
            where is_jurisdiction_agency = 1
            and  (  a.pk in ( select fk1 from agencies#contacts )
                   or j.subtype in('UASI','FUSION_CENTER')
                 )     ";
     $this->loadRows($sql);   
    while(true)
    {
        
        $r = $this->nextRow();
        if($r === false)
            break;

      //  if($r->subtype == 'K12')
      //     continue;    
            
            
        $pk = $r->orgs_pk;
           
        $o = new Query("select * from orgs where orgs_pk = $r->orgs_pk");    
        if($pk != $o->orgs_pk)
        {
          echo " **** NOT FOUND ***  $r->orgs_pk  $r->jurinst_title  -- FOR AGENCY -- $r->a_pk, $r->a_title <br>";
          continue;
        }
 //      else            echo "FOUND $o->orgs_title  for  $r->a_title <br>"   ;
        
    
        $address = Query::Escape($r->address);
        $address2 = Query::Escape($r->address2);
        $city = Query::Escape($r->city);
 
        $title = Query::Escape($r->a_title);
        $sql = "INSERT INTO orgs (orgs_type_code,orgs_functional_type_code,orgs_title, orgs_parent, orgs_url, orgs_address, orgs_address2, orgs_city,orgs_state,orgs_state_code,orgs_zip, orgs_pk )
        	VALUES('AGENCY','$r->func_code','$title', $o->orgs_id , '$r->orgs_url', '$address', '$address2','$city','$r->state','$o->orgs_state_code', '$r->zip', $r->a_pk)";
        
        $this->save($sql);
       
    
    }  
      $this->finish();
}

function bids1()
{
    $this->save("delete from bids");
    $this->finish();
    // get the non-ereate bids   1000 at a time
    $sqlx = "SELECT top 1000  * ,  a.title as a_title, a.pk as a_pk, a.address as a_adress, a.address2 as a_address2, a.city as a_city, a.zip as a_zip, a.state as a_state,  
                             coalesce(aj.fk2, ai.fk2) as j_pk  ,att.code as using_ag_type, 
                              b.pk as b_pk, b.title as b_title, b.subtype as bidtype
            FROM bids b          
            join agency_types att on att.pk = b.agency_type_pk 
            JOIN agencies#bids ab
              JOIN agencies a ON a.pk =ab.fk1
              LEFT JOIN agencies#institutions ai ON ai.fk1 = ab.fk1
              LEFT JOIN agencies#jurisdictions aj ON aj.fk1 = ab.fk1
            ON b.pk = ab.fk2                
            
            JOIN bids#contacts bc       
              JOIN contacts c ON c.pk = bc.fk2
            ON bc.fk1 = b.pk      
            WHERE NOT b.subtype = 'ERATE'
            AND  convert(varchar,due_date,12) > '100101' -- only bids from the last 3 years
            and b.pk > startpk
            ORDER BY b.pk asc";

    
    $startPk = 21730;
    $counter =0;
    while(true)
    {
        
        $sql = str_replace('startpk', $startPk, $sqlx) ;
    
        $this->loadRows($sql);
        
        if($this->numResults == 0)
           break;
        
        while(true)
        {
            $r = $this->nextRow();
            if($r === false)
                break;
            if($startPk == $r->b_pk)
            {
                echo("************************************ $r->b_pk   bid with multiple agencies <br>");  
                continue; // linked to multiple agencies
            }   
            $startPk = $r->b_pk;
            $this->addBid($r);
        }    
        $this->finish();
        
        $counter ++;
        echo "$counter last pk = $startPk --------<br>"; flush();
    }
}

function addBid($r)
{
     // if agency exists, we will link the bid to this agency
        // otherwise we link it to the org
                   
        // Notice that the 'order by org_type_code' will give the agency link preference
        
         if(empty($r->j_pk))
         {
             echo("<br> ************************************ $r->b_pk  $r->b_title  bid without jurisdiction or agency <br><br>");
         die;    return;
         }
         
    
        $sql = "SELECT * FROM orgs WHERE orgs_pk = $r->a_pk  OR  orgs_pk = $r->j_pk ORDER BY orgs_type_code";
        //dump($sql, false);
        $org = new Query($sql);    
         
         $parent = $org->orgs_id;  
         if(empty($parent))
         {
             echo("<br> ************************************ $r->b_pk  $r->b_title  bid without parent <br><br>");
             return;
         }
         
         
         
         $description =  Query::Escape($r->description);
         $title = Query::Escape($r->b_title);
         
         $conf_location = Query::Escape($r->conference_location);
         $name =     Query::Escape( "$r->first_name $r->last_name");
         $address =   Query::Escape($r->a_address );
         $address2 =   Query::Escape( $r->a_address2);
         $city =   Query::Escape( $r->a_city);
         $bid_url = Query::Escape( $r->bid_url);
         $using_org = Query::Escape( $r->using_agency);
         $email = Query::Escape( $r->email);
         $pcode = Query::Escape( $r->project_code);
         
      //   echo "  ($org->orgs_title)  ??  ($using_org) ";
         if(strcasecmp($org->orgs_title, $using_org) == 0)
         {
           $using_org_fid = $parent;  //echo(" --MATCH-- <br>");
         }
         else
         { 
           $using_org_fid = 'null';  //echo(" NO <br>");
         }
         
         $due_date =  date("Y-m-d" ,strtotime( $r->due_date));
         $pub_date =  date("Y-m-d" ,strtotime( $r->pub_date));
         $mod_date =  date("Y-m-d" ,strtotime( $r->mod_date));
         $award_date = date("Y-m-d" ,strtotime( $r->award_date));
         $conf_date=  date("Y-m-d" ,strtotime( $r->conference_date));
         $fiscal_year = $r->fiscal_year > 0 ? $r->fiscal_year: 'null';
  //       echo "$title <br>";
         
         $sql = "INSERT INTO bids (bids_title, bids_project_code, bids_description, bids_contract_value, bids_contract_value_estimated,
                 bids_due_date, bids_pub_date, bids_mod_date, bids_create_date, bids_award_date, bids_conference, bids_conference_mandatory, bids_conference_location,bids_conference_date,
                 bids_preferred_contact_method, bids_using_org_title, bids_using_org_type, bids_using_org_fid, bids_issuing_org_fid, bids_pdf, bids_url, bids_type,
                 bids_fiscal_year,bids_published, bids_pushed_hourly, bids_pushed_dayly, bids_pushed_weekly, bids_contact_name, bids_contact_email,
                 bids_address, bids_address2, bids_city, bids_state, bids_zip, bids_phone, bids_fax, bids_pk)
                 VALUES('$title','$pcode', '$description','$r->contract_value',$r->contract_value_estimated, 
                 '$due_date',  '$pub_date',   '$mod_date',   NOW(),'$award_date',              $r->conference,    $r->conference_mandatory,'$conf_location', '$conf_date',
				 '$r->preferred_contact_method', '$using_org','$r->using_ag_type',$using_org_fid,    $parent,      '$r->pdf','$bid_url','$r->bidtype',
				 $fiscal_year,$r->published,  1,1,1, '$name', '$email', 
				 '$address', '$address2','$city', '$r->a_state', '$r->a_zip', '$r->phone', '$r->fax',$r->b_pk  )";
				 
          $this->save($sql);
         // echo($sql);  
}


///////////////////////// utility functions ////////////////////////////////////    
    
    /**
     * 
     * load from the old database
     * @param unknown_type $sql
     */
    private function loadRows($sql)
    {
        $this->result = mssql_query($sql, $this->oldDB);
        if($this->result === false)
        {
            $err = mssql_get_last_message();
            logerror("SQL ERROR: $err <hr> $sql");
        }
        $this->numResults =  mssql_num_rows($this->result);
    }
    
   
    
    private function nextRow()
    {
        return mssql_fetch_object($this->result);
    }  
    
    private function save($sql)
    {
        $this->batch[] = $sql;
        if(count($this->batch) >= 10)
        {
            $this->finish();
        }
    }
    
    private function finish()
    {
       if(count($this->batch) > 0)
          $r = Query::sTransaction($this->batch);
       
        $this->batch = array();
    }
}
