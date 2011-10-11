<?
// a model class
class Documentation extends Content
{
    private static $mFieldDescriptions = array(
        'user_docu'    => array('type'=>'varchar'),
        'design_docu'  => array('type'=>'varchar'),
        'indexing'     => array('type'=>'varchar') );
    
    public function __construct($fieldsObject)
    {
        //print_o($fieldsObject); die('here1');
        
        parent::__construct($fieldsObject);
        $this->mExtraTable  = 'docu_items';
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
       
        $eindexing    = Query::Escape($this->mFields->indexing); 
        $euser_docu   = Query::Escape($this->mFields->user_docu);
        $edesign_docu = Query::Escape($this->mFields->design_docu);
                
        $this->mSqlStack[]  = "INSERT INTO docu_items(contents_fk,version,indexing,user_docu,design_docu) 
                 VALUES(LAST_INSERT_ID(),1,'$eindexing','$euser_docu','$edesign_docu')";
        
        $this->mFields->main_author_fk = $_SESSION['user_pk'];  
        $this->mFields->author_fk      = $_SESSION['user_pk'];   

       //  print_o( $this->mFields); die('here2'); 
        
        return parent::SaveNew();
    }
    
    protected function SaveExisting()
    {
        //print_r($params); die;    
        $eindexing    = Query::Escape($this->mFields->indexing); 
        $euser_docu   = Query::Escape($this->mFields->user_docu);
        $edesign_docu = Query::Escape($this->mFields->design_docu);
        
        $this->mSqlStack[] = "INSERT INTO docu_items(contents_fk,version,indexing,user_docu,design_docu)              
                     VALUES($this->mPk, @v,'$eindexing','$euser_docu','$edesign_docu')";
         
        return parent::SaveExisting();
    }
     
    
    public static function GetFieldDescriptions()
    {
         return array(
            'title'         => array('title'=>'Title', 'type'=>'varchar', 'required'=>true),
            'indexing'      => array('title'=>'Index','type'=>'varchar', 'required'=>true),
            'user_docu'     => array('title'=>'User Documentation','type'=>'varchar'),
            'design_docu'   => array('title'=>'Design Documentation','type'=>'varchar')        
         );
    }
    
    
    
    /**
     * Returns an array of all Documentation objects
     * @return array of Documentation objects 
     */
    public static function GetDocumentation()
    {    
        $sql = "select * from contents c
			join docu_items di 	on c.pk = di.contents_fk and di.version = c.live_version
			where content_type = 'DOCUMENTATION'   			
			order by indexing ";
      
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
         
        $d = Content::getAllData($params->pk, "docu_items", intval($params->version))->ToArray();
        
        
        if(! User::Authorize('SUPER_ADMIN')) // only super admins get to see the design documentation
        {
              unset($d['design_docu']);    
        }
            
        return $d;
    }
    
    public static function sYaasSave($params)
    {
        
        if(! User::Authorize('SUPER_ADMIN'))
        {
            return('unauthorized');
        }
        Query::SetAdminMode();
                
        $params->status = 'LIVE'; // make it live
        $d      = new Documentation($params);
        $result = $d->Save(); 
              
        return YaasMakeErrorResponse($params);
    }
}
