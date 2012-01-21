<?
/* Author model class
 * Author is an authors profile,   it can be linked to a single User,    
 * or it can be a "general" profile like "editorial staff"
 * 
 * also users can have different profiles 
 * 
 */    
class Author extends Model
{
    protected $mFields; // the object with all the fields

    protected static $mFieldDescriptions = array(
            'authors_clk_id'          => array('type'=>'int', 'insert_only'=>true),
            'authors_id'              => array('type'=>'int', 'insert_only'=>true), // autoincrement
            'authors_users_id'        => array('type'=>'int', 'required'=>true, 'form_element' =>'select' ),         
            'authors_name'            => array('type'=>'varchar', 'required'=>true),
            'authors_display_name'    => array('type'=>'varchar', 'required'=>true), 
            'authors_role_title'      => array('type'=>'varchar'),
            'authors_summary'         => array('type'=>'varchar'),
            'authors_bio'             => array('type'=>'varchar'),  
            'authors_active'          => array('type'=>'bit'),  
            'authors_public_email'    => array('type'=>'varchar'),
            'authors_twitter_url'     => array('type'=>'varchar'),
            'authors_googleplus_url'  => array('type'=>'varchar')
    );
     
    
    public function __construct($params)
    {
        if(is_array($params))
            $params = (object) $params;
            
    	$this->mFields = $params;
    }
    
    
    public function GetFieldDescriptions($includeUsers = false)
    {
        $fields = self::$mFieldDescriptions; 
        if($includeUsers)
        {
            $users = User::GetUsers();
            $users->SetAlias(array('id' => 'users_id','title' => 'users_last_name' ));  // slect lists template uses id and title           
            $fields['authors_users_id']['options'] = $users;   // set the options for the users select list         
        }   
        return $fields;     
    }
    
    
    public function Save()
    {   
        $id  = intval($this->mFields->authors_id);	 
    	$sql = array();
    	if($id == 0)
    	{
    	    $values = $this->FormatUpdateString(self::$mFieldDescriptions, SQL_INSERT);   	    
        	$sql[]  = "INSERT INTO authors $values";
        	$sql[]  = "SELECT LAST_INSERT_ID() as id";
    	}
        else 
        {
            $values = $this->FormatUpdateString(self::$mFieldDescriptions, SQL_UPDATE);           
        	$sql[]  = "UPDATE authors SET $values WHERE authors_id = $id";
        	$sql[]  = "SELECT $id as id";
        }
        
        $r = Query::sTransaction($sql);
       
        if($r)
        {
        	return $r->id ;   // success
        }                  
        return false;
    }
    
    /**
     *  GetDetails
     *  @param int authors_id
     *  @return author record
     */
    public static function GetDetails($id)
    {
        return new Query("SELECT * FROM authors WHERE authors_id = $id");
    }

    
    /**
     * GetAuthors4User
	 * @param int users_id
	 * @return all author profiles connected to this user
     */
    public static function GetAuthors4User($users_id)
    {
        $id = intval($users_id);
        if($id == 0)
           return array();
        
        return new Query("SELECT * FROM authors WHERE authors_users_id = $id");
    }
    
    
    /**
     * GetAuthors
	 * @return all author profiles 
     */
    public static function GetAuthors()
    {
        return new Query("SELECT * FROM authors ORDER BY authors_name");
    }
    
}
