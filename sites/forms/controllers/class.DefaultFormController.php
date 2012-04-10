<?
class DefaultFormController extends Controller
{
    protected $theForm;
    public function __construct($routerObject, $arguments)
    {          
        global $CONFIG;     
        parent::__construct($routerObject, $arguments); 
        
        $site = $CONFIG->site_code;
        
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        
        $this->theForm = $routerObject->theForm;
       
        if(! empty($_REQUEST['forms_id']))
            $this->SaveForm();
        else 
            $this->RenderForm($site);    
             
    }
    
    
    
    protected function SaveForm()
    {

        
    }
    
    protected function RenderForm($site)
    {
        //dump($this->theForm);
        $fieldList = array();        
        $fields = Form::GetFields($this->theForm->forms_id);
          
        foreach($fields as $field)
        {
            $fieldList[$field->fields_id] = array('type' => $field->fields_type , 
                                                  'html_name' => $field->fields_html_name , 
                                                  'label' => $field->fields_label,
    											  'required' => $field->fields_required,
    											  'validation' => $field->fields_validation,
                                                  'tpl' => $field->fields_tpl
                                                 ); 
        }
     //dump($fieldList);
        $this->mSmarty->assign('form_data', $fieldList);
        $this->mSmarty->assign('value', array() );
        $this->mDefaultTpl   = 'defaultForm.tpl';
        $this->mPageTitle = $this->theForm->forms_display_title;
    }
    
    
   
    protected function _InitCaching(){}
    protected function _InitPage(){}
}
