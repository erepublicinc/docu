<?
/*

REPLACE INTO `item` (`item_name`, items_in_stock) VALUES( 'A', 27)

-- this will isert OR delete and insert (I expect problems with foreign keys etc
-- so as an alternative do:

INSERT INTO `item` (`item_name`, items_in_stock) VALUES( 'A', 27)
ON DUPLICATE KEY UPDATE  `new_items_count` = `new_items_count` + 27



 */


class MigrationController
{
    private $oldDB ;
    private $batch; // we write the records in batches
    
    function __construct()
    {
        global $CONFIG;
        $this->batch = array();
        
        Query::SetAdminMode();
        Query::IgnoreUniqueErrors();
        
        $server = 'sqltest';
        $user   = $CONFIG->db_user;
        $pw     = $CONFIG->db_password;
        $dbname = 'navigator_proof';
        
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
           $sql = "INSERT INTO states (states_code,states_title) VALUES('$r->code','$title')";
           $this->save($sql);
        } 
        $this->finish();
    }
    
 function migrateAccounts()
 {
     echo "migrate Accounts <br><br>";
        
        $this->loadRows('select * from accounts');
        while(true)
        {
            $r = $this->nextRow();
            if($r === false)
                break;
           echo "$r->title <br>";  
           
           $title = trim($r->title);  
           $title = Query::Escape($title);
           $sql = "INSERT INTO accounts (accounts_title, accounts_sf_id, accounts_sf_deleted,accounts_is_navigator, accounts_pk) 
                  VALUES('$r->title', '$r->sf_account_id', $r->sf_deleted, true, $r->pk)";    
           $this->save($sql);  
           $this->save("SELECT @acc_id:= LAST_INSERT_ID()");
           
           if($r->dgn_access_level == 'STANDARD' || $r->dgn_access_level == 'PREMIUM')
           {
               $time =  strtotime( $r->dgn_contract_expiration);
               $date =  date("Y-m-d" ,$time);
               $sql = "INSERT INTO nav_accounts (nava_accounts_fid, nava_site_code, nava_access_level,nava_licenses,nava_contract_status,nava_contract_expiration,nava_monthly_bonus_hours,nava_account_rep_id) 
                      VALUES(@acc_id,'GOV','$r->dgn_access_level','$r->dgn_licenses','$r->dgn_contract_status','$date',$r->dgn_monthly_bonus_hours,1 )";      
               $this->save($sql);
           }
           if($r->den_access_level == 'STANDARD' || $r->den_access_level == 'PREMIUM')
           {
               $time =  strtotime( $r->den_contract_expiration);
               $date =  date("Y-m-d" ,$time);
               $sql = "INSERT INTO nav_accounts (nava_accounts_fid, nava_site_code, nava_access_level,nava_licenses,nava_contract_status,nava_contract_expiration,nava_monthly_bonus_hours,nava_account_rep_id) 
                      VALUES(@acc_id,'ED','$r->den_access_level','$r->den_licenses','$r->den_contract_status','$date',$r->den_monthly_bonus_hours,1 )";      
               $this->save($sql);
           }
           if($r->dgn_access_level == 'STANDARD' || $r->dgn_access_level == 'PREMIUM')
           {
               $time =  strtotime( $r->emn_contract_expiration);
               $date =  date("Y-m-d" ,$time);
               $sql = "INSERT INTO nav_accounts (nava_accounts_fid, nava_site_code, nava_access_level,nava_licenses,nava_contract_status,nava_contract_expiration,nava_monthly_bonus_hours,nava_account_rep_id) 
                      VALUES(@acc_id,'EM','$r->emn_access_level','$r->emn_licenses','$r->emn_contract_status','$date',$r->emn_monthly_bonus_hours,1 )";      
               $this->save($sql);
           }
           
           $this->finish();  // every account is its own transaction
           flush();
        } 
        
 }    
    
    
 function migrateUsers()
    { die('not ready yet');
    
        echo "creating UserGroups <br><br>";
        
        $this->loadRows('select * from groups');
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
        
        $this->loadRows('select * from users');
        while(true)
        {
            $r = $this->nextRow();
            if($r === false)
                break;
           echo "$r->code <br>";  
           $title = trim($r->title);  
           $sql = "INSERT INTO users (users_pk,users_last_name,users_first_name,users_job_title,users_salesforce_fid,users_password,users_email,users_ad_user,users_accounts_fid,users_active) 
           VALUES($r->pk,'$r->last_name','$r->first_name','$r->job_title','$r->sf_nav_user_id','$r->password','$r->email','$r->ad_user',$r->,$r->user_active)";
           
           $this->save($sql);
        } 
        $this->finish();
        
        $this->create_tmp_pk_id_table();
        
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
    
    /*
     * create a tmp table in the old nav db,  with the pk and new id of the users
     * when we migrate tables that are linked to users, we join this table to get the new-user-id
     */
    function create_tmp_pk_id_table()
    {
        
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
    }
    
    private function nextRow()
    {
        return mssql_fetch_object($this->result);
    }
   
    
    private function save($sql)
    {
        $batchSize = 10;
        $this->batch[] = $sql;
        if(count($this->batch) >= $batchSize-1)
            $this->finish(); 
         
    }
    
    private function finish()
    {
        dump($this->batch, false);
        $r = Query::sTransaction($this->batch);
        $this->batch = array();
    }
}
