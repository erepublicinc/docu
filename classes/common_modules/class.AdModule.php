<?
/**
 * 
 * This controller class gets instatiated with a modules record 
 * it sets the template and variables needed by the template
 * then this object gets passed to the template,  so the template can extract its data 
 * @author michael
 *
 */
class AdModule
{
    private $data;
    
    public function __construct($record)
    {
        $this->data = new stdClass();
        $params = json_decode($record->modules_json_params);
        //$data->position = $params['position'];
        $this->data->position = 'T2';
        $this->data->template = 'common/ad_div.tpl';
    }
    
    
    /**
     * this is how the tpl gets the data
     */
    function __get($key)
    {
        return $this->data->$key;
    }
    
}
