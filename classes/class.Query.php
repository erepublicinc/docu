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
    // these constants are used as parameters in the Constructor
    const NONE        = 0;
    const TRANSACTION = 1;
     
    private static $mConnection = null;

    private static $mAdminMode = false;
    
    // members    
    private $mRow;             // holds the current row
    private $mResultSet;       // returned by the query
    private $mNumQueries;      // number of queries  default =1
    
    private $mPosition;
    
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
    function __construct($query, $cacheId = '', $maxeAge = 100)
    {
        $this->mNumQueries = 1;
        
        if(self::$mConnection == null)
             self::OpenDb(); 
                   
        if(is_array($query))
        {
            $mq = '';
            $this->mNumQueries = count($query);
            
            // conactenate the queries separated by ;
            foreach ($query as $q)               
                $mq .= $q.';';           
//print_r($mq)   ; die;  
        
            $result = self::$mConnection->multi_query($mq);           
            if($result === false)
                logerror('multi_query error : '. self::$mConnection->error, __FILE__ . __LINE__);
                
            $this->mResultSet = self::$mConnection->store_result();    
                 
        }
        else
        {
            // standard query use 
            $this->mResultSet = self::$mConnection->query($query );         
        }
        
        if($this->mResultSet === false)
        {   
            if( $this->mNumQueries == 1) echo "$query <br>\n";   else echo "$mq <br>\n";      
            logerror(self::$mConnection->error, __FILE__ . __LINE__);
        }
        elseif ($this->mResultSet !== TRUE)
            $this->next(); // to load the first row
    }
    
    /**
     * does a transction on the array of sql queries
     * @param array $queryArray 
     */
    public static function sTransaction($queryArray)
    {
        if(self::$mConnection == null)
             self::OpenDb(); 
                         
        self::$mConnection->autocommit(false);

//print_r($queryArray)   ; die;     
        foreach($queryArray as $sql)
        {
            $result = self::$mConnection->query($sql);
            if($result === false)
            {
                $errorString = self::$mConnection->error;
                self::$mConnection->rollback();
                self::$mConnection->autocommit(true);
                logerror("statement: $sql  $errorString", __FILE__ . __LINE__);
                return false;
            }
            /*
            elseif(is_object($result)) {
                $o = $result->fetch_object();
                if(! empty($o->pk))
                {
                     $pk = $resultSet->pk;
                     echo ('pk: '.$pk);
                }
            } */
        }
             
        self::$mConnection->commit();
        self::$mConnection->autocommit(true);
              
        return true;
    }
    
    function __destruct()
    {
        if($this->mresultSet)
           $this->mresultSet->free();
    }
    
    function __get($var)
    {
        if(! $this->mRow)
           return null;   
            return $this->mRow->$var;    
    }
    
    /**
	 * SetValue is usefull for aliasing / renaming fields
	 * @param String $field
	 * @param $value
     */
    public function SetValue($field, $value)
    {
        if(! $this->mRow)
           return null;   
        $this->mRow->$field = $value;    
    }
       
    public function ToArray()
    {
       return array($this->mRow);
    }
    
    
    /**
     * for multi queries to move to the next result set
     * 
     */
    public function NextResultSet()
    {
        if($this->mNumQueries > 1 && self::$mConnection->more_results())
        {
            if( $this->mResultSet->free() === false)
              logerror('NextResultSet error : '. self::$mConnection->error, __FILE__ . __LINE__);
            if(self::$mConnection->next_result() === false)
              logerror('NextResultSet error : '. self::$mConnection->error, __FILE__ . __LINE__);
            if($this->mResultSet = store_result() === false)
              logerror('NextResultSet error : '. self::$mConnection->error, __FILE__ . __LINE__);
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
        $CONFIG->db_dbname = 'newgt2';
                              
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

    
    // these are the iterator functions
    public function rewind() {
        $this->mPosition = 0;
    }

    public function current() {
        return $this->mRow;
    }

    public function key() {
        return $this->mPosition;
    }

    public function next() {
        $this->mRow = $this->mResultSet->fetch_object();
    }

    public function valid() {
        return  (boolean) $this->mRow ; 
    }
    // end of the iterator functions
    
}

Query::OpenDb(); 


     


