<?
/**
 * 
 * This controller class gets instatiated with a modules record 
 * it sets the template and variables needed by the template
 * then this object gets passed to the template,  so the template can extract its data 
 * @author michael
 *
 */
class HtmlModule
{
    private $data;
    
    public function __construct($record)
    {
        $this->data = new stdClass();
        //$params = json_decode($record->modules_json_params);
        $this->data->html     = $record->modules_body;
        $this->data->title    = $record->contents_display_title;
        $this->data->template = 'common/htmlModule.tpl';
        $this->data->css_class    = 'the_class';  //test
        
    }
    
    
    /**
     * this is how the tpl gets the data
     */
    function __get($key)
    {
        return $this->data->$key;
    }
    
}
