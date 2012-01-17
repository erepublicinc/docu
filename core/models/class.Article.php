<?
// a model class
class Article extends Content
{
        
    /**
     *  creates a new article from an array or object
     *  @param object or array with fields
     */
    public function __construct($fieldsObjectOrArray)
    {
        parent::__construct($fieldsObjectOrArray);
        
        // for documentation on these members see the Content class   
        $this->mExtraTable  = 'articles';
        $this->mContentType = 'Article';               
        $this->mExtraFieldDescriptions = array(      
        	'contents_article_body'    => array('type'=>'text', 'label'=>'Body') ,  
            'contents_article_type'    => array('type'=>'varchar')  
        );        
    }

    
    /**
     * returns an array of articles
     * @param string site [default = all sites]
     * @param string $orderby [default no order]
     * @param int    $limit   [default is site default for paging]
     * @param int    $skip    [default 0]
     * @param string $status  [default 'READY']
     */
    public static function GetArticles( $site = null, $orderby = null, $limit = 10, $skip = 0, $status = 'READY')  
    {
        return parent::GetContentByType('Article', $site, $orderby, $limit, $skip , $status) ; 
    }
    
     /**
     * returns the article details
     * @param int $id module id (id of the contents object)
     * @param int $version [default = 0 gets the live version]
     * @param bool $includeAuthor [default false]
     */
    public static function GetDetails($id, $version = LIVE_VERSION, $includeAuthor = false)   
    {
        return Content::getAllData($id, "articles", $version, $includeAuthor);
    }
    
    
    /**
     * only static sYaasfuctions can be called through Yaas2 .  This is a security measure
     * @param array $params
     */
    public static function sYaasGetDetails($params)
    {
        return Content::getAllData($params->id, "articles", intval($params->version))->ToArray();
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

