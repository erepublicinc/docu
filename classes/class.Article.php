<?
// a model class
class Article extends Content
{
    private static $mFieldDescriptions = array( 'body'    => array('type'=>'varchar')    );
    
    public function __construct($fieldsObject)
    {
        //print_o($fieldsObject); die('here1');
        
        parent::__construct($fieldsObject);
        $this->mExtraTable  = 'articles';
        $this->mContentType = 'ARTICLE';
         
    }

    public function Save()
    {
        if($this->mPk)
            return $this->SaveExisting();
        else 
            return $this->SaveNew();    
    }
    
    protected function SaveNew()
    { 
       
        $ebody = Query::Escape($this->mFields->body);
                
        $this->mSqlStack[]  = "INSERT INTO articles(contents_fk,version,body) 
                 VALUES(LAST_INSERT_ID(),1,'$ebody')";
        
        $this->mFields->main_author_fk = $_SESSION['user_pk'];  
        $this->mFields->author_fk      = $_SESSION['user_pk'];   

       //  print_o( $this->mFields); die('here2'); 
        
        return parent::SaveNew();
    }
    
    protected function SaveExisting()
    {
        //print_r($params); die;    
        $ebody = Query::Escape($this->mFields->body);
        
        $this->mSqlStack[] = "INSERT INTO articles(contents_fk,version,body)          
                     VALUES($this->mPk, @v,'$ebody')";
         
        return parent::SaveExisting();
    }
     
    
    public static function GetFieldDescriptions()
    {
         return $mFieldDescriptions;
    }
    
    
    public static function GetArticles( $site = null, $orderby = null, $limit = 10, $skip = 0, $status = 'LIVE')  
    {
        return parent::GetContentByType('articles', $site, $orderby, $limit, $skip , $status) ; 
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
echo'<pre>'; print_r($params); die;        
        if(! User::Authorize('ADMIN'))
        {
            return('unauthorized');
        }
        Query::SetAdminMode();
        
        $d      = new Documentation($params);
        $result = $d->Save(); 
              
        return YaasMakeErrorResponse($params);
    }
}

