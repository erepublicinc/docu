<?
// a model class
class Documentation extends Content
{
    private static $mFieldDescriptions = array(
        'specs_user_docu'    => array('type'=>'varchar'),
        'specs_design_docu'  => array('type'=>'varchar'),
        'specs_indexing'     => array('type'=>'varchar'),
        'specs_authors_id'    => array('type'=>'int'));
    
    public function __construct($fieldsObject)
    {
        //print_o($fieldsObject); die('here1');
        
        parent::__construct($fieldsObject);
        $this->mExtraTable  = 'specs';
        $this->mContentType = 'DOCUMENTATION';
         
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
       
        $eindexing    = Query::Escape($this->mFields->specs_indexing); 
        $euser_docu   = Query::Escape($this->mFields->specs_user_docu);
        $edesign_docu = Query::Escape($this->mFields->specs_design_docu);

        $author = $_SESSION['user_id'];
        $this->mFields->contents_main_authors_id = $author;  
           
        
        $this->mSqlStack[]  = "INSERT INTO specs(specs_contents_id,specs_rev,specs_indexing,specs_user_docu,specs_design_docu, specs_authors_id) 
                 VALUES(@id, 1,'$eindexing','$euser_docu','$edesign_docu', $author)";
        

       //  print_o( $this->mFields); die('here2'); 
        
        return parent::SaveNew();
    }
    
    protected function SaveExisting($newrev = TRUE)
    {
        //print_r($params); die;    
        $eindexing    = Query::Escape($this->mFields->specs_indexing); 
        $euser_docu   = Query::Escape($this->mFields->specs_user_docu);
        $edesign_docu = Query::Escape($this->mFields->specs_design_docu);
        
        $author = $_SESSION['user_id'];
        $this->mFields->specs_authors_id = $author;      
                    
        if($newrev)
        {
            $this->mSqlStack[] = "INSERT INTO specs(specs_contents_id,specs_rev,specs_indexing,specs_user_docu,specs_design_docu, specs_authors_id)            
                     VALUES($this->mPk, @v,'$eindexing','$euser_docu','$edesign_docu', $author)";
        }
        else
        {
            $newvalues = $this->FormatUpdateString(self::$mContentFieldDescriptions, SQL_UPDATE);           
            $this->mSqlStack[] = "UPDATE specs set $newvalues  WHERE  specs_contents_id = $this->mPk AND specs_rev = @v -1  ";                              
        }
        return parent::SaveExisting($newrev);
    }
     
    
    public static function GetFieldDescriptions()
    {
         return $this->mFieldDescriptions;
    }
    
    
    
    /**
     * Returns an array of all Documentation objects
     * @return array of Documentation objects 
     */
    public static function GetDocumentation()
    {    
        $sql = "select * from contents 
			join specs  on contents_id = specs_contents_id and specs_rev = contents_live_rev
			where contents_type = 'DOCUMENTATION'   			
			order by specs_indexing ";
      
        $result = new Query($sql); 

        $contents = array();
        foreach($result as $content)
        {
           $contents[] = new Documentation($content);   
        }
        return $contents;
    }
    
    /**
     * only static sYaasfuctions can be called through Yaas2 .  This is a security measure
     * @param array $params
     */
    public static function sYaasGetDetails($params)
    {
        if(! User::Authorize('LOGGED_IN'))
        {
            return('unauthorized');
        }
         
        $d = Content::getAllData($params->id, "specs", intval($params->contents_rev))->ToArray();
        
        
        if(! User::Authorize('SUPER_ADMIN')) // only super admins get to see the design documentation
        {
              unset($d['specs_design_docu']);    
        }
            
        return $d;
    }
    
    /*
     * $params can be an object or an array
     */
    public static function sYaasSave($params)
    {
        
        if(! User::Authorize('SUPER_ADMIN'))
        {
            return('unauthorized');
        }
        Query::SetAdminMode();
                
        $d = new Documentation($params);
        $d->mFields->contents_rev_status = 'READY'; // make it live
        $d->mFields->contents_rev_status = 'READY'; // make it live
        
        $result = $d->Save(); 
        return result;
              
        //return YaasMakeErrorResponse($params);
    }
}
