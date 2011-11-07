<?
// a model class
class Article extends Content
{
    private static $mFieldDescriptions = array( 'articles_body'    => array('type'=>'varchar')    );
    
    /*
     *  creates a new article from an array or object
     */
    public function __construct($fieldsObjectOrArray)
    {
        //print_o($fieldsObject); die('here1');
        
        parent::__construct($fieldsObjectOrArray);
        $this->mExtraTable  = 'articles';
        $this->mContentType = 'ARTICLE';
         
    }

    /**
     * returns the pk
     * @see Content::Save()
     */
    public function Save()
    { //die("pk ".$this->mPk);
        
        $newVersion = true;  // always create a new version
        
        $this->mFields->articles_authors_fk  = $_SESSION['user_pk'];  
        if($this->mPk > 0)
            return $this->SaveExisting($newVersion);
        else 
            return $this->SaveNew();    
    }
    
    protected function SaveNew()
    { 
       
        $ebody  = Query::Escape($this->mFields->articles_body);
        $author = $this->mFields->articles_authors_fk   ;      
//        $this->mSqlStack[]  = "INSERT INTO articles(articles_contents_fk, articles_version, articles_body, articles_authors_fk) 
//                 VALUES(LAST_INSERT_ID(),1,'$ebody', $author)";

        $this->mSqlStack[]  = "INSERT INTO articles(articles_contents_fk, articles_version, articles_body, articles_authors_fk) 
                 VALUES(@pk,1,'$ebody', $author)";
        
        $this->mFields->contents_main_authors_fk = $_SESSION['user_pk'];  
         

       //  print_o( $this->mFields); die('here2'); 
        $result = parent::SaveNew();
        //die("the a pk: ".$result->pk);
        
        return $result;
        
        if($result != false)
            return $result->pk;
        return false;    
    }
    
    protected function SaveExisting($newVersion = TRUE)
    {
        //print_r($params); die;    
        $ebody  = Query::Escape($this->mFields->articles_body);
        $author = $this->mFields->articles_authors_fk   ; 
        $pk     = $this->mPk;
        
        if($newVersion)
        {
           $this->mSqlStack[] = "INSERT INTO articles(articles_contents_fk, articles_version, articles_body, articles_authors_fk, articles_update_date)          
                     VALUES($pk, @v,'$ebody', $author , NOW())";
        }
        else 
        {
           $this->mSqlStack[] = "UPDATE articles set articles_body = $ebody, articles_authors_fk = $author,  articles_update_date = NOW()  where articles_pk = $pk";
        }
        
        return parent::SaveExisting($newVersion);
        
    }
     
    
    public static function GetFieldDescriptions()
    {
         return $mFieldDescriptions;
    }
    
    
    public static function GetArticles( $site = null, $orderby = null, $limit = 10, $skip = 0, $status = 'LIVE')  
    {
        return parent::GetContentByType('ARTICLE', $site, $orderby, $limit, $skip , $status) ; 
    }
    
    public static function GetArticle($pk, $version = 0)  
    {
        return Content::getAllData($pk, "articles", $version);
    }
    
    
    /**
     * only static sYaasfuctions can be called through Yaas2 .  This is a security measure
     * @param array $params
     */
    public static function sYaasGetDetails($params)
    {
        return Content::getAllData($params->pk, "articles", intval($params->version))->ToArray();
    }
    
    public static function sYaasSave($params)
    {
//echo'<pre>'; print_r($params); die;        
        if(! User::Authorize('ADMIN'))
        {
            return('unauthorized');
        }
        Query::SetAdminMode();
        
        $d      = new Article($params);
        $result = $d->Save(); 
              
        return $result; //YaasMakeErrorResponse($params);
    }
}

