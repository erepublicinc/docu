<?
/*
 * All Models inherit from this class
 * It contains utility functions
 */
class Model
{
    protected $mFields;
    protected $mId;
    
    protected function __construct($params)
    {
        if(is_array($params))
            $params = (object) $params;
        
        if(is_object($params))
        {
            $this->mFields =  $params; //echo('create contetn'.$row->title);
            $this->mId     = ($params->contents_id >0)? $params->contents_id :0;
        }      
        elseif( is_integer($params))
        {
            $this->mId =$params;
        }               
    }
    
        
    public function __get($field)
    {
        return $this->mFields->$field;
    }
    
    public function getAllFields()
    {
        return $this->mFields;
    }
    
    
       
    /**
     * creates a string used for updating a record like:  "body='hello', rev=12"
     * creates a string used for inserting a record like: "(body,rev) VALUES('hello', 12) "
     * typical use:    
     * $str = FormatUpdateString(GetFieldTypes(false), $params);
     * $sql = "UPDATE events SET $str WHERE id = $id";
     * 
     * @param array of field types
     * @param bool  [default =  false] means that we request a string suitable for 
     *                                 the UPDATE command like: "body='hello', rev=12"
     */
    protected  function FormatUpdateString($Fieldsarray, $mode = SQL_UPDATE)
    {
        $insertStr = '(';         //  (body,rev)
        $newvalues = '';          //  VALUES('hello', 12)  or body='hello', rev=12"
                    
        foreach($Fieldsarray as $field => $Description)
        {
            $value = $this->mFields->$field;
             
            if($Description['auto_insert'])
            {  // for the auto_increment fields
                continue;
            }
            
            if(! isset($this->mFields->$field) ) 
            {   
                if($mode == SQL_INSERT && $Description['required']  )            
                    logerror("required field $field is missing" );
                continue; 
            }
            
            if($mode == SQL_UPDATE && $Description['insert_only'] )  // these fields cannot be updated
                continue;  
                
            if($Description['not_0_only'] && empty($value))  // these fields cannot be updated
                continue;  
                
                
           
                
            if(! $Description['do_not_validate'] )
            {
                if($Description['type'] == 'pk')
                    continue;  // this field will never be inserted or updated
                    
                elseif($Description['type'] == 'varchar' || $Description['type'] == 'text')
                {   //if ($field == 'modules_body') dump($value);
                    $value = "'". Query::Escape($value) ."'";   
                }
                elseif($Description['type'] == 'datetime')
                {
                    if(strtoupper($value) != "NOW()" )    
                        $value = "'". date("Y-m-d G:i:00", strtotime($value)) ."'";
                }   
                elseif($Description['type'] == 'date')
                { 
                    if(strtoupper($value) != "NOW()" )    
                        $value =  "'".date("Y-m-d", strtotime($value)) ."'";   
                }
                elseif($Description['type'] == 'int' )
                    $value =  intval($value);
                
                elseif($Description['type'] == 'bit' )
                    $value =  intval($value) >0 ? 1: 0;    
            }                   
            if($mode == SQL_INSERT)
            {
                $insertStr .= "$field,";
                $newvalues .= "$value,";
            }
            else 
            {
                $newvalues .= "$field = $value,";
            }
        }
        
        if($mode == SQL_INSERT)
        {               
            $insertStr = substr($insertStr, 0, strlen($insertStr)-1); // remove last comma  
            $newvalues =  $insertStr . ') VALUES(' . $newvalues;             // combine the strings
          
            $newvalues  = substr($newvalues, 0, strlen($newvalues)-1); // remove last comma  
            $newvalues .= ')';
        }
        else 
        {   
            $newvalues = substr($newvalues, 0, strlen($newvalues)-1); // remove last comma  
        } 
 //dump($newvalues);       
        return  $newvalues;   
    }
   
     
}