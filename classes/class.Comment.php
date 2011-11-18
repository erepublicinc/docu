<?
class Comment
{
    public function __construct()
    {
        
    }
    
    /**
	 * get all comments for a give content pk
	 * @param int $pk  content pk
     */
    public static function getComments($contents_fk)
    {
        $contents_fk = intval($contents_fk);
        $sql = "SELECT * from comments WHERE comments_contents_fk = $contents_fk  ORDER BY comments_fk, comments_pk";
        return new Query($sql);
    }
    
    /**
     * 
     * Enter description here ...
     * @param object or array $p
     */
    public static function addComment($p)
    {
        Query::SetAdminMode();
        
        if(is_array($p))
            $p = (object)$p;
          
        $fk =          intval($p->comments_fk);
        $title =       Query::Escape($p->comments_title);
        $body =        Query::Escape($p->comments_body);
        $commenter =   Query::Escape($p->comments_commenter);
        $email =       Query::Escape($p->comments_email);
        $ranking =     intval($p->comments_ranking);
        $contents_fk = intval($p->comments_contents_fk);
 //die("email = $email");       
        $sql = array();
        $sql[] = "INSERT INTO comments (comments_fk,comments_title,comments_body,comments_commenter,comments_email,comments_ranking,comments_date, comments_contents_fk) 
                 VALUES($fk,'$title','$body','$commenter','$email',$ranking, NOW() , $contents_fk)";
        if($comments_fk == 0)
        {
             $sql[] = "UPDATE comments SET comments_fk = LAST_INSERT_ID() where comments_pk = LAST_INSERT_ID() ";
        }
        $sql[] = "SELECT LAST_INSERT_ID() as pk";
        
   //     dump($sql);
        return Query::sTransaction($sql);
    }
    
    /**
	 *
     */
    public static function rankComment($pk)
    {
        $sql = "UPDATE comments SET comments_ranking = comments_ranking + 1 WHERE comments_pk = $pk";
        new Query($sql);
    }
    
	/**
	 * get the content 
     */
    public static function GetMostCommented($site, $type, $number = 5)
    {
        
         $sql = "SELECT * FROM contents WHERE contents_pk in 
                    (SELECT COUNT(comments_contents_fk) FROM comments  
                     WHERE comments_date > DATE_ADD(NOW() , INTERVAL -5 DAY)
                     
                     GROUP BY comments_contents_fk 
                     ORDER BY  COUNT(comments_contents_fk) DESC 
                    )
                 AND contents_type = '$type';    
                 AND 
                 LIMIT $number";
        return new Query($sql);
    }
    
}