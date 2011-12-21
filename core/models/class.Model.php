<?
/*
 * All Models inherit from this class
 * It contains utility functions
 */
class Model
{
    
       
    /**
     * creates a string used for updating a record like:  "body='hello', version=12"
     * creates a string used for inserting a record like: "(body,version) VALUES('hello', 12) "
     * typical use:    
     * $str = FormatUpdateString(GetFieldTypes(false), $params);
     * $sql = "UPDATE events SET $str WHERE id = $id";
     * 
     * @param array of field types
     * @param bool  [default =  false] means that we request a string suitable for 
     *                                 the UPDATE command like: "body='hello', version=12"
     */
    protected  function FormatUpdateString($Fieldsarray, $mode = SQL_UPDATE)
    {
        $insertStr = '(';         //  (body,version)
        $newvalues = '';          //  VALUES('hello', 12)  or body='hello', version=12"
                    
        foreach($Fieldsarray as $field => $Description)
        {
            if(! isset($this->mFields->$field) ) 
            {   
                if($mode == SQL_INSERT && $Description['required']  )            
                    logerror("required field $field is missing" );
                continue; 
            }
            
            if($mode == SQL_UPDATE && $Description['insert_only'] )  // these fields cannot be updated
                continue;  
           
                
            $value = $this->mFields->$field;
                
            if(! $Description['do_not_validate'] )
            {
                if($Description['type'] == 'pk')
                    continue;  // this field will never be inserted or updated
                    
                elseif($Description['type'] == 'varchar')
                    $value = "'". Query::Escape($value) ."'";   
                
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