<?
class Comment
{
    public function __construct()
    {
        
    }
    
    /**
	 * get all comments for a give content id
	 * @param int $id  content id
     */
    public static function getComments($contents_id)
    {
        $contents_id = intval($contents_id);
        $sql = "SELECT * from comments WHERE comments_contents_id = $contents_id  ORDER BY comments_parent_id, comments_id";
        return new Query($sql);
    }
    
    /**
     * 
     * Adding a comment
     * NOTE: parent_id is the id of the parent comment for nested comments, for root level comments(that have no parent) this should be 0
     * @param object or array $p
     */
    public static function addComment($p)
    {
        Query::SetAdminMode();
        
        if(is_array($p))
            $p = (object)$p;
          
        $parent_id =   intval($p->comments_parent_id);
        $title =       Query::Escape($p->comments_title);
        $body =        Query::Escape($p->comments_body);
        $commenter =   Query::Escape($p->comments_commenter);
        $email =       Query::Escape($p->comments_email);
        $ranking =     intval($p->comments_ranking);
        $contents_id = intval($p->comments_contents_id);
 //die("email = $email");       
        $sql = array();
        $sql[] = "INSERT INTO comments (comments_parent_id,comments_title,comments_body,comments_commenter,comments_email,comments_ranking,comments_date, comments_contents_id) 
                 VALUES($parent_id,'$title','$body','$commenter','$email',$ranking, NOW() , $contents_id)";
        if($parent_id == 0)
        {
             $sql[] = "UPDATE comments SET comments_parent_id = LAST_INSERT_ID() where comments_id = LAST_INSERT_ID() ";
        }
        $sql[] = "SELECT LAST_INSERT_ID() as id";
        
   //     dump($sql);
        return Query::sTransaction($sql);
    }
    
    /**
	 *
     */
    public static function rankComment($id)
    {
        $sql = "UPDATE comments SET comments_ranking = comments_ranking + 1 WHERE comments_id = $id";
        new Query($sql);
    }
    
	/**
	 * get the content 
     */
    public static function GetMostCommented($site, $type, $number = 5)
    {
        
         $sql = "SELECT * FROM contents WHERE contents_id in 
                    (SELECT COUNT(comments_contents_id) FROM comments  
                     WHERE comments_date > DATE_ADD(NOW() , INTERVAL -5 DAY)
                     
                     GROUP BY comments_contents_id 
                     ORDER BY  COUNT(comments_contents_id) DESC 
                    )
                 AND contents_type = '$type';    
                 AND 
                 LIMIT $number";
        return new Query($sql);
    }
    
}