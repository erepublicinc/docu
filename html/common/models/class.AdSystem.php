<?
/*
 * the AdSystem class is used in the adDiv template
 */
class AdSystem
{
    static private $singleton = null;
    
    public $site;
    public $newsletterID;
    public $specs;         // width and heigt data for all the ad positions
    public $ad_source;
    
    /**
     * The only public method.
     * @return the AdSystem object
     */
    static public function GetSpecs()
    {
        if(! self::$singleton)
            self::$singleton =  new AdSystem();     
        return self::$singleton; 
    }
    
    
    // this class is a singleton so the constructor has to be private
    private function __construct()
    {
        global $CONFIG;
        
        // do the site specific stuff
        switch($CONFIG->site_code)
        {
            case 'GT' :
                $this->site = 'd.site285.opus';
                break;
            case 'GOV' :
                $this->site = 'i.site285.opus';
                break;   
            case 'EM' :
                $this->site = 'c.site285.opus' ;
                break;     
            case 'DC' :
                $this->site = 'f.site285.opus';
                break;     
            case 'CV' :
                $this->site = 'b.site285.opus';
                break;     
            case 'CDE' :
                $this->site = 'a.site285.opus';
                break;     
        }
        

        $this->newsletterID = filter_ABC123($_GET['nlid']);  
            
        // AD_SOURCE
        $uri = $_SERVER['REQUEST_URI']; 
        $uri = trim($uri,' /');
        $parts = explode('/',$uri);
        //$uri = str_replace('/','_',$uri);
            
        if($parts[0] == "emergency-blogs")
        {
            $this->ad_source = "site={$this->site}/area=_{$uri}/id=" . $parts[1];
        }
        else 
        {
            if(empty($uri))
                $uri = 'index';
            $this->ad_source  = "site={$this->site}/area=_{$uri}";
        }

       
        $this->specs = array(
            'I1' => array('position' => 'I1', 'width' => 1,   'height'=>1),
            'T1' => array('position' => 'T1', 'width' => 500, 'height'=>500),
            'T2' => array('position' => 'T2', 'width' => 728, 'height'=>90),
            'T3' => array('position' => 'T3', 'width' => 120, 'height'=>90),
            'T4' => array('position' => 'T4', 'width' => 370, 'height'=>60),
            'T5' => array('position' => 'T5', 'width' => 640, 'height'=>480),
            'L1' => array('position' => 'L1', 'width' => 160, 'height'=>600),
            'L2' => array('position' => 'L2', 'width' => 160, 'height'=>120),
            'L3' => array('position' => 'L3', 'width' => 160, 'height'=>90),
            'L4' => array('position' => 'L4', 'width' => 160, 'height'=>600),
            'R2' => array('position' => 'R1', 'width' => 300, 'height'=>180),
            'R2' => array('position' => 'R2', 'width' => 300, 'height'=>250),
            'R3' => array('position' => 'R3', 'width' => 300, 'height'=>180),
            'R4' => array('position' => 'R4', 'width' => 300, 'height'=>250),
            'R5' => array('position' => 'R5', 'width' => 160, 'height'=>600),
            'R6' => array('position' => 'R6', 'width' => 234, 'height'=>60),
            'R7' => array('position' => 'R7', 'width' => 120, 'height'=>60),
            'R8' => array('position' => 'R8', 'width' => 300, 'height'=>250),
            'R9' => array('position' => 'R9', 'width' => 120, 'height'=>600),
            'vr1'=> array('position' =>'vr1', 'width' => 88,  'height'=>31),
            'vr2'=> array('position' =>'vr2', 'width' => 88,  'height'=>31),
            'vr3'=> array('position' =>'vr3', 'width' => 88,  'height'=>31),
            'vr4'=> array('position' =>'vr4', 'width' => 88,  'height'=>31),
            'B1' => array('position' => 'B1', 'width' => 728, 'height'=>90),
            'B2' => array('position' => 'B2', 'width' => 120, 'height'=>90),
            'S1' => array('position' => 'S1', 'width' => 120, 'height'=>90),
            'S2' => array('position' => 'S2', 'width' => 88,  'height'=>31),
            'S3' => array('position' => 'S3', 'width' => 88,  'height'=>31),
            'S4' => array('position' => 'S4', 'width' => 120, 'height'=>90),
            'S5' => array('position' => 'S5', 'width' => 140, 'height'=>60),
            'tv1'=> array('position' => 'tv1','width' => 120, 'height'=>50),
            'tl1'=> array('position' => 'tl1','width' => 1,   'height'=>1),
            'tl2'=> array('position' => 'tl2','width' => 1,   'height'=>1),
            'tl3'=> array('position' => 'tl3','width' => 1,   'height'=>1),
            'tl4'=> array('position' => 'tl4','width' => 1,   'height'=>1),
            'tl5'=> array('position' => 'tl5','width' => 1,   'height'=>1),
            'tlx'=> array('position' => 'tlx','width' => 1,   'height'=>1),
            'M1' => array('position' => 'M1', 'width' => 300, 'height'=>250)          
        );
        
    } // end of __construct()
      
}



