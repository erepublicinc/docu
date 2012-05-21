<?
/**
 * The Query class handles all database requests
 * 
 * Usage: 
 * $users = new Query('select * from users');
 * $foreach($users as $user)
 *    echo " $user->name <br>";
 * 
 *  
 * For admin mode call:  Query::SetAdminMode(); before updateing the database
 *  
 */

class Query  implements Iterator
{    
    private static $mConnection = null;

    private static $mAdminMode = false;
    private static $mIgnoreUniqueErrors = false;
    
    public static $echo_sql = false;    
    
    private $mRows = array();   // a copy of the data rows of the current query
    private $mRowIndex = -1;    // index into array above
    private $eof = false;       // all rows of the resutset have been read into  $mRows

    private $mResultSet;       // current resultset returned by the query
    private $mNumQueries;      // number of queries  default =1

    public $mAliasArray;  // array of alternative fieldnames
    

    
    public static function Escape($str)
    {
        $str = str_replace('\n','',$str);
        $str = str_replace('\t','',$str);
        return self::$mConnection->real_escape_string($str);
    }
    
    /**
     * returns a result object which can be iterated
     * @param string  or array of queries
     * @param cacheid if this is set the 
     * @param int max age for a cached item 
     */
    function __construct($sql, $cacheId = '', $maxeAge = 100)
    {
    	global $CONFIG;
 //       if($CONFIG->show_sql)
 //       	show_sql($sql);
    
        $this->mAliasArray = array();
        
        $this->mNumQueries = 1;
        
        if(self::$mConnection == null)
             self::OpenDb(); 
            
             
        if(is_array($sql))
        {
            $mq = '';
            $this->mNumQueries = count($sql);
            
            // conactenate the queries separated by ;
            foreach ($sql as $q)               
                $mq .= $q.';';       
                    
            //dump($mq)   
        
            $result = self::$mConnection->multi_query($mq);           
            if($result === false)
                $this->SqlError(self::$mConnection->errno, 'multi_query error : '. self::$mConnection->error);
            else    
                $this->mResultSet = self::$mConnection->store_result();    
                 
        }
        else
        {
            // standard query use 
            $this->mResultSet = self::$mConnection->query($sql );         
        }
        
        if($this->mResultSet === false)
        {   
            //if( $this->mNumQueries == 1) echo "$sql <br>\n";   else echo "$mq <br>\n";      
            $this->SqlError(self::$mConnection->errno, "Query error in the following statement: <br> $sql <br> error msg:". self::$mConnection->error );
        }
        elseif ($this->mResultSet !== TRUE)
            $this->next(); // to load the first row
    }


    
    /**
     * does a transction on the array of sql queries
     * @param array $sql 
     * @return    true, false, or the first result row
     */
    public static function sTransaction($sql)
    {
        global $CONFIG;
        
        if(! is_array($sql))
            logerror("Query::sTransaction called with non array param ");
        
        if(count($sql) == 0)
            logerror("Query::sTransaction called with empty array ");
        
        if(self::$mConnection == null)
             self::OpenDb(); 
                         
        self::$mConnection->autocommit(false);

        foreach($sql as $query)
        {
            if(self::$echo_sql)
               echo("$query <br>");
               
            $result = self::$mConnection->query($query);
            if($result === false)
            { 
                $errorString = self::$mConnection->error;
                $errorNumber = self::$mConnection->errno;
                self::$mConnection->rollback();
                self::$mConnection->autocommit(true);
                self::SqlError($errorNumber,"statement: $query <br><br>  $errorString");
                
                return false;
            }
        }
        
        self::$mConnection->commit();
        self::$mConnection->autocommit(true);

        if ($result !== TRUE)
        {
           $r =  $result->fetch_object(); // return the first row
           return $r;
        }
        return true;
    }


    
    private static function SqlError($errno, $str)
    {  
        if( self::$mIgnoreUniqueErrors && $errno == 1062)  // 1169 
        {
            // echo "************ unique error ***************";
            return;
        }      
        logerror("sqlerror: $errno  $str");
    }
    
    
    /*
     * call this when you want to ignore unique errors
     */
    public static function IgnoreUniqueErrors($ignore = true)
    {
        self::$mIgnoreUniqueErrors = $ignore;
    }
    
    /*
     * changes the current database
     */
    public static function changeDB($dbName)
    {
        //return mysqli::select_db($dbName);
        return self::$mConnection->select_db($dbName);
    }
    
    
    public function __destruct()
    {
        if($this->mResultSet && is_object($this->mResultSet))
           $this->mResultSet->free();
    }
    
    public function __get($var)
    { 
        if($this->mRowIndex < 0)
           return null;    

        if(isset($this->mAliasArray[$var]))    // we used an alias for the fieldname  
            $var = $this->mAliasArray[$var];
        
        return isset($this->mRows[$this->mRowIndex]->$var) ?  $this->mRows[$this->mRowIndex]->$var : null; 
    }

    /**
     * returns the current record as an array
     */
    public function ToArray()
    {
       if($this->mRowIndex < 0)
           return null; 
       return (array) $this->mRows[$this->mRowIndex] ;   
    }
    
    /**
     * Create shortcut fieldnames ex: 'id' instead of 'contents_id'
     * Great for use with generic templates
     * @param array $alias_array  array( 'id'=>'contents_id', 'body'=>'articles_body');
     */
    public function SetAlias($alias_array)
    {
        $this->mAliasArray = $alias_array;
       
    }

    /**
	 * Sets the value of a field in the current record
	 * @param String $field
	 * @param $value
     */
    public function SetValue($field, $value)
    {
       if($this->mRowIndex < 0)
           return null;     
        $this->mRows[$this->mRowIndex]->$field= $value;
    }
    
    /**
     * for multi queries to move to the next result set 
     */
    public function NextResultSet()
    {
        if($this->mNumQueries > 1 && self::$mConnection->more_results())
        { 
            $this->mResultSet->free();
              
            if(self::$mConnection->next_result() == false)
              logerror('NextResultSet error : '. self::$mConnection->error);
              
            $this->mResultSet = self::$mConnection->store_result() ;
            if($this->mResultSet== false)
                 logerror('store_result error : '. self::$mConnection->error);
                 
            $this->eof = false;
            $this->mRows = array();
            $this->mRowIndex = -1; 
            $this->next();
            return true;
        }
        return false;
    }
    
    
    /** 
     * Needs to be called before you can make updates to the database 
     * 
     */  
    public static function SetAdminMode()
    {   
        global $CONFIG;
        
        if(self::$mAdminMode == true)
           return true;
            
        if(self::$mConnection)
        {             
            if(self::$mConnection->change_user($CONFIG->db_admin_user , $CONFIG->db_admin_password , $CONFIG->db_dbname) === false)
            {
                logerror( " cannot change user to admin user: ".self::$mConnection->error);
                return false;
            }
            self::$mAdminMode == true;
        }       
        else
        {
            return self::OpenDb(true);
        }  
         
        return true;
    }
    
    public static function OpenDb($admin = false)
    {
        global $CONFIG;
                              
        if($admin)
        {   
            self::$mAdminMode == true;
            $connection = new mysqli($CONFIG->db_host,  $CONFIG->db_admin_user, $CONFIG->db_admin_password, $CONFIG->db_dbname);
        }
        else
        {     
            $connection = new mysqli($CONFIG->db_host, $CONFIG->db_user, $CONFIG->db_password, $CONFIG->db_dbname);
        }
            
        if ($connection->connect_errno) 
        {   
            logerror( " cannot open $CONFIG->db_host :$CONFIG->db_dbname: ".$connection->connect_error );
            return false;
        }  
             
        self::$mConnection = $connection;    
        return true;
    }

    
// ======================== below are the iterator functions ========================
   
    public function rewind() 
    {
        $this->mRowIndex = 0;
    }

    public function current() 
    {            
        if($this->mRowIndex > -1 && $this->mRowIndex < count($this->mRows))
        {
            foreach($this->mAliasArray as $alias => $original) 
                $this->mRows[$this->mRowIndex]->$alias =  $this->mRows[$this->mRowIndex]->$original;
            
            return $this->mRows[$this->mRowIndex];
        }
        return null;    
    }

    public function key() 
    {
        return $this->mRowIndex;
    }

    public function next() 
    {  
        $this->mRowIndex++; 
        if(! $this->eof)
        {
            $o = $this->mResultSet->fetch_object();
            if($o)           
                $this->mRows[] = $o;            
            else
                $this->eof =true;
        }       
    }

    public function valid() 
    {
        return ($this->mRowIndex > -1  &&  $this->mRowIndex < count($this->mRows) );
    }
// ======================== end of the iterator functions ========================
    
}

// automatically open the database on loading of this class
Query::OpenDb(); 


     


