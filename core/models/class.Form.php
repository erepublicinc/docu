<?
/* Form model class
 
 */    
class Form extends Model
{
    protected $mFields; // the object with all the fields

    protected static $mFieldDescriptions = array(
            'forms_id'          => array('type'=>'int', 'auto_insert'=>true), // autoincrement
            'forms_tpl'         => array('type'=>'varchar'), 
            'forms_title'       => array('type'=>'varchar', 'required'=>true ), 
            'forms_display_title' => array('type'=>'varchar', 'required'=>true ),        
            'forms_url_name'    => array('type'=>'varchar'),
            'forms_https'       => array('type'=>'bit'), 
            'forms_start_date'  => array('type'=>'date'),
            'forms_end_date'    => array('type'=>'date'),
            'forms_css'         => array('type'=>'varchar'),  
            'forms_site_code'     => array('type'=>'varchar', 'required'=>true, 'form_element' =>'select' ),    
            'forms_eloqua_formid' => array('type'=>'varchar'),
            'forms_xml_data'      => array('type'=>'varchar'),
            'forms_php_class'     => array('type'=>'varchar')
    );
     
    
    public function __construct($params)
    {
        if(is_array($params))
            $params = (object) $params;
            
    	$this->mFields = $params;
    }
    
    
    public function GetFieldDescriptions()
    {
        $fields = self::$mFieldDescriptions; 
        return $fields;     
    }
    
    
    public function Save()
    {   //dump($this->mFields);
        $id  = intval($this->mFields->forms_id);	 
    	$sql = array();
    	if($id == 0)
    	{
    	    $values = $this->FormatUpdateString(self::$mFieldDescriptions, SQL_INSERT);   	    
        	$sql[]  = "INSERT INTO forms.forms $values";
        	$sql[]  = "SELECT LAST_INSERT_ID() as id";
    	}
        else 
        {
            $values = $this->FormatUpdateString(self::$mFieldDescriptions, SQL_UPDATE);           
        	$sql[]  = "UPDATE forms.forms SET $values WHERE forms_id = $id";
        	$sql[]  = "SELECT $id as id";
        }
        
        $r = Query::sTransaction($sql);
       
        if($r)
        {
        	return $r->id ;   // success
        }                  
        return false;
    }
    
    
    public static function sYaasCreateForm($parms)
    {   
        if(! User::Authorize("EDITOR"))
           return YaasMakeErrorResponse("You don't have the proper rights");
        
        $form = new Form($parms);              
        $id   = $form->Save();
         
        if($id)
        	return $id  ;   // success
                
        return false;
    }
    
    /**
     * makes a copy of a form
     * @param int  form_id
     * @return int id of a copy , or false
     */
    public static function Copy($id)
    {   
        if($id <= 0)
          return false;
        
        if(! User::Authorize("EDITOR"))
           return YaasMakeErrorResponse("You don't have the proper rights");
        
        $original = self::GetDetails($id);   
        $original = $original->ToArrray();
        $original['id'] = 0;
        $original['title'] .= ' (copy)';
        
        $clone    = new Form($original); 
        return $clone->Save();
    }
    
    
    
    public static function SaveField($parms)
    {
      //  if(! User::Authorize("EDITOR"))
      //     return YaasMakeErrorResponse("You don't have the proper rights");
          
        if(intval($parms->field_masters_id) > 0 )
        { 
           return self::AddField($parms);
        }   
        return self::UpdateField($parms);   
    } 
    
    
    private static function UpdateField($parms)
    { 
       
        $fields_id = intval($parms->fields_id);   
        $title     = Query::Escape($parms->fields_label);
        $order     = intval($parms->fields_order);
        
        if($parms->fields_locked > 0)
        {
            $sql = "UPDATE forms.fields SET  fields_label = '$title', 
                                             fields_order = $order
                    WHERE fields_id = $fields_id";
        }
        else 
        {
             $sql = "UPDATE forms.fields SET  fields_label = '$title', 
                                             fields_order = $order
                    WHERE fields_id = $fields_id";
        }
        
        $r = new Query($sql); 
        if($r)
            return  $fields_id ; 
              
        return false;        
    }
    
    
    private static function AddField($parms)
    { 
                   
        $field_masters_id = intval($parms->field_masters_id);
        $forms_id          = intval($parms->forms_fid);
        $order             = intval($parms->fields_order);
        
        $sql = array();
        $sql[] ="INSERT INTO forms.fields (forms_fid, fields_label, fields_html_name, fields_tpl, fields_type, fields_class, fields_validation,fields_required,fields_eloqua_name, fields_values, fields_locked, fields_order) 
              SELECT '$forms_id' as forms_fid, fields_label, fields_html_name, fields_tpl, fields_type, fields_class, fields_validation,fields_required,fields_eloqua_name, fields_values, fields_locked, '$order' as fields_order
              FROM forms.field_masters fm 
              WHERE fm.field_masters_id = $field_masters_id";
        $sql[]  = "SELECT LAST_INSERT_ID() as id";
      // dump($sql, false); //return;
        $r =  Query::sTransaction($sql);
        
        if($r)
            return  $r->id ; 
       
        return false;
    }
    
    public static function RemoveField($parms)
    { 
        if(! User::Authorize("EDITOR"))
            return YaasMakeErrorResponse("You don't have the proper rights");
           
        $fields_id = intval($parms['fields_id']);
        $r = new Query("DELETE  FROM forms.fields WHERE fields_id = $fields_id");
        
        if($r)
            return true; 
              
        return false;
    }
    
    
    /**
     *  GetDetails
     *  @param int forms_id
     *  @return form record
     */
    public static function GetDetails($id)
    {
        return new Query("SELECT * FROM forms.forms WHERE forms_id = $id");
    }

    /**
     *  GetDetails
     *  @param string forms_url_name
     *  @return form record
     */
    public static function GetDetailsByName($name)
    {
        return new Query("SELECT * FROM forms.forms WHERE forms_url_name = '$name'");
    }
    
    /**
     *  GetFields
     *  @param int forms_id
     *  @return array of field records
     */
    public static function GetFields($forms_id)
    {
        return new Query("SELECT * FROM forms.fields WHERE forms_fid = $forms_id ORDER BY fields_order");
    }
    
    /**
     *  GetDetails
     *  @return array of field records
     */
    public static function GetFieldMasters()
    {
        return new Query("SELECT * FROM forms.field_masters") ; // ORDER BY field_masters_order");
    }
    
    /**
     * GetForms for a particular site, for the form listing page
     * @param string $site_code
	 * @return all forms for site 
     */
    public static function GetForms($site_code = "ALL")
    {
        if($site_code != 'ALL')
            $siteClause = " WHERE forms_site_code = '$site_code'";
        return new Query("SELECT * FROM forms.forms $siteClause");
    }
    
    
    
}
