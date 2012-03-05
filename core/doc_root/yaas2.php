<?   //Yet Another Ajax Solution  Version 2       this Yaas2 is different from Yaas as used by navigator !   
require_once 'inc.basic.php';

ignore_user_abort(true);


$data = json_decode($_REQUEST['data']);

$responses = Array();

foreach ($data as $request)
{
    // clean the request params to prevent sql injection
    $keys = get_object_vars($request->params);          
    foreach($keys as $field => $value)
    {
        // there are 2 types of data: integer and text , all date/time fields are integers
        if( strpos($field,'_rev')   || strpos($field,'_id') ||  strpos($field,'_time') )
           $request->params->$field = intval($request->params->$field);
        else   
           $request->params->$field = Query::Escape( $request->params->$field);
    }
    
    try
    {
        $action = explode('.', $request->method);
        $class  = $action[0];
        $method = $action[1];

        if(! class_exists($class) )
        {
            throw( new Exception(' Yaas called with invalid class: '.$class));
        }
  
        // we are calling the static method:    class::sYaasMethod
        // NOTE: we prepend sYaas here to make sure that only sYaas functions can be called
        $responses[] =  call_user_func( array($class,'sYaas'.$method), $request->params);
    }
    catch (Exception $e)
    {
        $responses[] = YaasMakeErrorResponse($request->dsid, 'Caught exception: '. $e->getMessage());
    }
}

echo json_encode($responses);
return true;
    
/////////////////////////////////////////////////////////////////////////////////////////////////////////
//           Utility Functions
/////////////////////////////////////////////////////////////////////////////////////////////////////////
/**
 * This returns an array with result=>success  and the input values
 * @param array or single value 
 * @return array  
 */
function YaasMakeResponse($results)
{
    if(is_array($results))
    {
        $retval = $results;
        $retval['result'] = 'success';
    }
    else 
    {
        $retval = array('result' => 'success', 'value' => $results);
    }  
    return $retval;
}


function YaasMakeErrorResponse($msg = '')
{
    if(empty($msg))
        return array('result' => 'error');
    return array('result' => 'error','errormsg' => $msg);
}    