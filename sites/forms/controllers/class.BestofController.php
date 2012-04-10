<?
// http://www.centerdigitalgov.com/bestof_manage/newview.php?obj=bestof_contests&pk=83
//http://www.centerdigitalgov.com/bestof/?id=83
class BestofController extends Controller
{
    public function __construct($routerObject, $arguments)
    {
        
     
        global $CONFIG;     
        parent::__construct($routerObject, $arguments); 
        
        $site = $CONFIG->site_code;
        $record_type = $arguments[0];
        $id          = 0 + intval($arguments[1]);       
        $isNew       = $arguments[1] == 'new' ? true :false;
        
        
        $this->mSmarty->assign('site_code', $site);
        $this->mSmarty->assign('site_name', getSiteName($site));
        
        
        switch($record_type)
        {
            case 'contest':
                $this->contest();
                break;
            case 'entry':
                if($isNew)
                    $this->newEntry();
                else 
                    $this->entry();
                break;
            case 'judge_home':
                $this->judge_home();
                break;
            case 'contest':
                if($isNew)
                    $this->newContest();
                else 
                    $this->Contest($id);    
                break;
                               
            default:
                home();
                break;    
        }
        
    }

    
   
  
}
