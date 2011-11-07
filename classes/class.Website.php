<?
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
/**
 * This is the overall class a website subclass (class.Gt.php for example) should extend.
 *
 * <br>
 * <b>Usage with a subclass:</b>
 * <br>
 *
 * <code>
 * //Extends a generic website. This class is used to hold the display properties and methods for the GT Website.
 *
 *  class GT extends Website
 *  {
 * 
 *
 *      // this method can be overriden for special processing
 *      public function Display()
 *      {
 *          //code
 *
 *          return parent::Render();
 *      }
 *  }
 *
 * $gt = new Gt();
 * $gt->Render();
 * </code>
 *
 * @author Kavih Williams
 * @author Joel Barker
 * @package Shared
 */


/**
 * This is the overall inteface a website class should implement.
 */
abstract class Website
{   
    protected $_mClassMapping = array();
    protected $_mClassArguments = array();
    
    public $mUser;         // do we need this ?
    protected $_mClassName;  //name of the page class
    public $mPageId;  
    public $mPagePk;  
    
    public $mDefaultModules = array();     //Array of site wide default modules
    
    public $mDateFormat     = '%D';
    public $mTimeFormat     = '%I:%M %p';
    public $mDateTimeFormat = '%m/%d/%y %I:%M %p';
    
    public $mSYSTEM_ENTRY_TIME;
    public function __construct()
    {
        global $CONFIG;  
        $uri            = parse_url($_SERVER['REQUEST_URI']);
        $path           = rtrim($uri['path'], "/");
        $pathSegments   = explode("/", $path);
        
        
        $mode = $pathSegments[0] == 'preview' ? 'PREVIEW': 'LIVE';
        $CONFIG->SetValue('mode', $mode);
        $CONFIG->SetValue('site_name', getSiteName($CONFIG->site_code));
        
        $this->_InitClassMapping();

        // restore the Query string that was changed by the 404
        $this->_modifyEnvironment($_SERVER['REQUEST_URI']);

        $this->_mClassName = null;

        setlocale(LC_MONETARY, 'en_US.UTF-8');
        //die( __CLASS__ .' '. __FUNCTION__ );
    }

    /**
     *  Initializes and displays page based on URL.
     */
    public function Render()
    { 
        // die( __CLASS__ .' '. __FUNCTION__ );
        //$this->_modifyEnvironment($_SERVER['REQUEST_URI']); moved to the constructor
        $this->_GetPageClassName(); 
        
   
        
        $this->_LoadPageClass(); 
        if (!empty($this->page))
        {  
            $this->page->Display();
        }

        PageTimer::printResults();
        return null;
    }
    /**
     * Each subclass (websites) will have their own way of handling unauthorized access.
     * @param String $error_msg
     */
 //   public abstract function UnauthorizedHandler($error_msg);

    //Initialize the class map, aka, available pages for the site.    
    protected abstract function _InitClassMapping();

    /**
     *  Using the URI Path to get the page class
     *  @return bool Always returns true.
     */
    protected function _GetPageClassName()
    {
        global $CONFIG; 
        
        $uri            = strtolower( $_SERVER['REQUEST_URI']);
        $uri            = parse_url($uri);
        $path           = rtrim($uri['path'], "/");
        $pathSegments   = explode("/", $path);
        
    
        if( $pathSegments[0] == 'preview')
        {
             $array_shift($pathSegments);
        }     
    
                        
 
        /**
         * We need to create key for the _mClassMapping array that maps to a page class name.
         * To get the key, we need to explode the path into segments and
         * test the key validity as we loop through the segments array.
         * If the key is not correct, we know that it is an argument for the page.
         */
        $numOfSegments  = count($pathSegments);
        for ($i = 0; $i < $numOfSegments; $i++)
        {
        	/* Creating the key (aka the path) */
        	$tmp_path = implode("/", $pathSegments);

        	/* Check if the key (path) returns a value from the _mClassMapping array */
        	if (empty($this->_mClassMapping[$tmp_path]) === false)
        	{
        		$this->_mClassName = $this->_mClassMapping[$tmp_path]['class'];
        		$CONFIG->SetValue('current_page_pk', $this->_mClassMapping[$tmp_path]['pages_pk']);
        		$CONFIG->SetValue('current_page_id', $this->_mClassMapping[$tmp_path]['pages_id']);
                break; /* we no longer need to check the rest of the segments since we found our page class */
        	}
        	/* Check if the key (path) along with a trailing path separator returns a value from the _mClassMapping array */
        	elseif (empty($this->_mClassMapping["{$tmp_path}/"]['class']) === false)
        	{
        		$this->_mClassName = $this->_mClassMapping["{$tmp_path}/"]['class'];
        		$CONFIG->SetValue('current_page_pk', $this->_mClassMapping["{$tmp_path}/"]['pages_id']);
        		$CONFIG->SetValue('current_page_id', $this->_mClassMapping["{$tmp_path}/"]['pages_pk']);
        		break;// we no longer need to check the rest of the segments since we found our page class 
        	}
        	// If no page class has found we know its a class arguments 
        	else
        	{
        		/* remove the last segment and add it to the arguments list */
        		array_unshift($this->_mClassArguments, array_pop($pathSegments));
        	}
        }

        return true;
    }

    /**
     *  Instantiates class specified by className then
     *  calls the display method of generated class.
     *  @return bool. True on success, false on error.
     */
    private function _LoadPageClass()
    {
  
        if (empty($this->_mClassName))
        { 
            return false;
        }

        try
        {
            if ((class_exists($this->_mClassName, true)))
            {
                $this->page = new $this->_mClassName($this, $this->_mClassArguments);
            }
        }
        catch(Exception $e)
        {
            echo 'Caught exception: '. $e->getMessage();
            return false;
        }
        return true;
    }

    /**
     * Modifies global variables so the next script thinks it was called directly
     * called by __construct()
     * @param string $script            The script that needs to be fooled (relative to the web root)
     * @return string                   Always returns null
     */
    private function _modifyEnvironment($script)
    {
        
        if (strpos($script, '?') !== false)
        {
            list($script, $_SERVER['QUERY_STRING']) = explode('?', $script, 2);

            // set $_GET
            parse_str($_SERVER['QUERY_STRING'], $fake_get);
            parse_str($_SERVER['REDIRECT_QUERY_STRING'], $real_get);
            $_GET = array_merge($real_get, $fake_get);
           
            // Set QUERY_STRING
            $_SERVER['QUERY_STRING'] = http_build_query($_GET);
        }

        $_SERVER['PHP_SELF']        = $script;
        $_SERVER['SCRIPT_NAME']     = $script;
        $_SERVER['SCRIPT_FILENAME'] = $_SERVER['DOCUMENT_ROOT'] . $script;

        header('HTTP/1.1 200 OK');

        return null;
    }

    

    /*
     *  Pieces together a path based on data from pathSegments
     *  using numSegments to determine how many pieces to include.
     *  @return string (path)
     */
    private function _RenderSegmentedPath($path_segments, $num_segments)
    {
        $path = "";
        for ($i=0; $i < $num_segments; $i++)
        {
            $path .= "{$path_segments[$i]}/";
        }
        return $path;
    }
    
    
}



