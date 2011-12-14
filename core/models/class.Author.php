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
    	$bio      = Query::Escape($this->mFields->authors_bio);
    	$active   = empty($this->mFields->authors_active)? 0: 1;
    	$pk       = intval($this->mFields->authors_pk);
    	$users_fk = intval($this->mFields->authors_users_fk);
    	
    	$sql = array();
    	if($pk == 0)
    	{
        	$sql[] = "insert into authors (authors_public_email, authors_name, authors_bio, authors_active,authors_users_fk) 
                 values('$email', '$name', '$bio', $active, $users_fk)";
        	$sql[] = "SELECT LAST_INSERT_ID() as pk";
    	}
        else 
        {
        	$sql[] = "UPDATE authors SET authors_public_email = '$email', authors_name = '$name', authors_bio = '$bio', 
        			authors_users_fk = $users_fk, authors_active = $active WHERE authors_pk = $pk";
        	$sql[] = "SELECT $pk as pk";
        }
        
        $r = Query::sTransaction($sql);
       
        if($r)
        {
        	return $r->pk ;   // success
        }                  
        return false;
    }
    
    /**
     *  GetDetails
     *  @param int authors_pk
     *  @return author record
     */
    public static function GetDetails($pk)
    {
        return new Query("SELECT * FROM authors WHERE authors_pk = $pk");
    }
    
    /**
     * GetAuthors4User
	 * @param int users_pk
	 * @return all author profiles connected to this user
     */
    public static function GetAuthors4User($users_fk)
    {
        $pk = intval($users_fk);
        if($pk == 0)
           return array();
        
        return new Query("SELECT * FROM authors WHERE authors_users_fk = $pk");
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
