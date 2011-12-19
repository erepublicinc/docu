<?
/* Author model class
 * Author is an authors profile,   it can be linked to a single User,    
 * or it can be a "general" profile like "editorial staff"
 * 
 * also users can have different profiles 
 * 
 */    
class Author
{
    protected $mFields; // the object with all the fields
    
    public function __construct($params)
    {
        if(is_array($params))
            $params = (object) $params;
            
    	$this->mFields = $params;
    }
    
    public function Save()
    {   //dump($this->mFields);
        
    	$email    = Query::Escape($this->mFields->authors_public_email);
    	$name     = Query::Escape($this->mFields->authors_name);
    	$dname     = Query::Escape($this->mFields->authors_display_name);
    	$bio      = Query::Escape($this->mFields->authors_bio);
    	$active   = empty($this->mFields->authors_active)? 0: 1;
    	$id       = intval($this->mFields->authors_id);
    	$users_id = intval($this->mFields->authors_users_id);
    	
    	$sql = array();
    	if($id == 0)
    	{
        	$sql[] = "insert into authors (authors_public_email, authors_name, authors_display_name, authors_bio, authors_active,authors_users_id) 
                 values('$email', '$name', '$dname', '$bio', $active, $users_id)";
        	$sql[] = "SELECT LAST_INSERT_ID() as id";
    	}
        else 
        {
        	$sql[] = "UPDATE authors SET authors_public_email = '$email', authors_name = '$name', authors_display_name = '$dname',authors_bio = '$bio', 
        			authors_users_id = $users_id, authors_active = $active WHERE authors_id = $id";
        	$sql[] = "SELECT $id as id";
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
        return new Query("SELECT * FROM authors");
    }
    
    
    
    
    
}
