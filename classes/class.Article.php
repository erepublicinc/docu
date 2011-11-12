<?
// a model class
class Article extends Content
{
    private static $mFieldDescriptions = array( 'contents_article_body'    => array('type'=>'varchar')    );
    
    /*
     *  creates a new article from an array or object
     */
    public function __construct($fieldsObjectOrArray)
    {
        
        parent::__construct($fieldsObjectOrArray);
        $this->mExtraTable  = 'articles';
        $this->mContentType = 'ARTICLE';        
    }

    /**
     * returns the pk
     * @see Content::Save()
     */
    public function Save()
    {         
        $newVersion = true;  // always create a new version
        
        $this->mFields->contents_author_fk  = $_SESSION['user_pk'];  
        if($this->mPk > 0)
            return $this->SaveExisting($newVersion);
        else 
            return $this->SaveNew();    
    }
    
    protected function SaveNew()
    {       
        $ebody    = Query::Escape($this->mFields->contents_article_body);
        $ecomment = Query::Escape($this->mFields->contents_version_comment);
        $author   = $this->mFields->contents_author_fk   ;  
        $apk      = $_SESSION['user_pk'];  

        $this->mSqlStack[]  = "INSERT INTO articles(contents_fk, contents_version, contents_article_body, contents_version_users_fk, 
                                                    contents_version_date, contents_version_comment) 
                 VALUES(@pk,1,'$ebody', $author, NOW(), '$ecomment')";
        
        $this->mFields->contents_main_author_fk = $author;  
         
        $result = parent::SaveNew();
        
        return $result;
   /*     
        if($result != false)
            return $result->pk;
        return false;
   */         
    }
    
    protected function SaveExisting($isNewVersion = TRUE)
    {
// dump($this->mFields);
        $ebody      = Query::Escape($this->mFields->contents_article_body);
        $ecomment   = Query::Escape($this->mFields->contents_version_comment);
        $author     = $this->mFields->contents_author_fk   ; 
        $pk         = $this->mPk;
        $version    = intval($this->mFields->contents_version);
        $newVersion = intval($this->mFields->contents_latest_version) + 1;  
        
        if($isNewVersion)
        {
           $this->mSqlStack[] = "INSERT INTO articles(contents_fk, contents_version, contents_article_body, contents_version_users_fk, contents_version_date, contents_version_comment)          
                     VALUES($pk, $newVersion,'$ebody', $author , NOW(), '$ecomment')";
        }
        else 
        {
           $this->mSqlStack[] = "UPDATE articles set contents_article_body = $ebody, contents_author_fk = $author,  contents_version_date = NOW()  where contents_fk = $pk AND contents_version = $version";
        }
        
        return parent::SaveExisting($isNewVersion);    
    }
     
    
    public static function GetFieldDescriptions()
    {
         return $mFieldDescriptions;
    }
    
    
    public static function GetArticles( $site = null, $orderby = null, $limit = 10, $skip = 0, $status = 'LIVE')  
    {
        return parent::GetContentByType('ARTICLE', $site, $orderby, $limit, $skip , $status) ; 
    }
    
    public static function GetArticle($pk, $version = LIVE_VERSION)  
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

