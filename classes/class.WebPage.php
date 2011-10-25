<?php
abstract class WebPage
{
          
    // @var array Arguments array, contains values for webpage class to use.
    public $mArguments;
   
    // @var object Website object that is calling webpage class.
    public $mWebsite;

    // @var array Modules to load in left column.
    public $mSideModules = array();

    // @var int the pk of the page record, need to know this to create targets
    protected $mPagePk;

    //==============  for the head
    //@var string Page title
    public $mPageTitle;
    
    // @var array  An associative array of name => content used to add meta tags to the page.
    public $mHtmlMetaTags = array();

    // @var array An array of urls used to pull in css files for the page.
    public $mStylesheets = array();

    // @var array  An array of <script> tags used to pull in javascript files for the page.
    public $mJavaScripts = array();

    // @var array * An array of javascript function calls used to load javascript onbodyload.
    public $mBodyOnloads = array();
    
    
    //======================= TEMPLATING and caching
    const CACHE_LIFETIME_IN_CACHE_FILE = 2;
    
    // The Smarty Object
    public $mSmarty; 

    // @var string  The default template for the page, usually shared across most pages in the site.
    protected $mDefaultTpl = 'default.tpl';
    
    // @var string Main template, this differs from the default template in that this template is main only
    // for the current page.  This template file is read into the smarty {$content} variable and
    // displayed in the default template.
    public $mMainTpl;
     
    // @var boolean Whether to default allowing caching to be on or off.   
    protected $_mAllowCaching = false;
    
    // @var Boolean Whether this webpage allows caching its main template file 
    protected $_mMainTplCaching = false;
   
    // @var boolean Whether this webpage or module has displayed its cache.
    public $mHasDisplayedCache = false;

    // V@var integer Determines how long a page can be cache with smarty.
    protected $_mSmartyCacheLifetime = 86400;

    // @var string The Smarty Main Template Cache Id
    private $_mSmartyMainTplCacheId;

    // @var string The Smarty Cache Id
    private $_mSmartyCacheId;



    /**
     * The page constructor
     * @param Website $website class instance
     * @param Array $args
     */
    protected function __construct(&$website, &$args)
    {
        $this->mWebsite    = $website;
        $this->mArguments  = $args;
        $this->mSideModules['left'] = array_merge((array)$this->mSideModules['left'], (array)$this->mWebsite->mDefaultModules['left']);
     //   $this->_InitIncludes();        // empty for now
        $this->_InitCaching();         // in derived class
        
        // call SetupSmarty with 'false   turn caching off 
        $this->SetupSmarty(($this->_mAllowCaching || $this->_mMainTplCaching));

        // Determines the necessity to call _InitPage(); 
      //  $this->_InitPageCheck();
    }

    
    
    
    
    public function Display($template = null, $cache_id = null)
    { 
        PageTimer::timeIt("start of Display($this->mMainTpl, $template)");
        // dumpcache on all server in the live environment
 //die($this->mMainTpl);
        
        $this->mSmarty->assign('main_tpl' ,$this->mMainTpl);
        
        
        $template = $template ? $template : $this->mDefaultTpl;
        $cache_id = $cache_id ? $cache_id : $this->_mSmartyCacheId;
      
        $this->mSmarty->display($template); //, $cache_id, $cache_id);

        return true;
    }

    
public function SetupSmarty($allow_caching = true, $callback = null)
    {
        global $CONFIG;
        
        if ((bool) $_SERVER['HTTPS'] != (bool) $this->mSecureMode)
        {
            $url = ($_SERVER['HTTPS'] ? 'http://' : 'https://') . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
            header('Location: ' . $url);
            exit();
        }

        //Construct and initialize the smarty object
        $this->mSmarty = new Smarty();
        $dir = $CONFIG->tpl_path; 
        $this->mSmarty->setTemplateDir($dir);
        $this->mSmarty->setCompileDir("$dir/templates_c");
        $this->mSmarty->setCacheDir($dir.'/cache'); 
        $this->mSmarty->setConfigDir($dir.'/configs'); 


        // Lets define the cache ID so
        // we can use the cache instead
        $this->_mSmartyCacheId  = $this->_GetCacheId();

        // to turn this off on the webpage, override it there, not here
        // note: the Config values here are for the called class (which may be 'Gt', 'Pcio', etc)
        if ($allow_caching)
        {
            //$this->mSmarty->caching          = self::CACHE_LIFETIME_IN_CACHE_FILE;      ??? what is dthe difference?
            $this->mSmarty->setCaching(Smarty::CACHING_LIFETIME_SAVED);
            $this->mSmarty->setCompileCheck(false); 
            $this->mSmarty->setCacheLifetime($this->_mSmartyCacheLifetime);
        }
      
        return null;
    }
    
   protected function _GetCacheId()
    {
        global $CONFIG;
        $id_pieces = array();

        /* Module class name */
        $id_pieces[] = get_class($this);

        /* Website SiteCode */
        $id_pieces[] = $CONFIG->site_code;


        /* add secure if the page in secure mode */
        if (empty($_SERVER['HTTPS']) === false)
        {
            $id_pieces[] = 'secure';
        }

        /* url path & query params */
        $url = parse_url($_SERVER['REQUEST_URI']);

        /* replace the path delimiter '/' with '-' */
        $id_pieces[] = preg_replace("/\//", "-", trim($url['path'], "/"));


        if (count($_GET) > 0)
        {
            $get_data = $_GET;

            unset($get_data['dumpcache']);
            unset($get_data['sessid']);

            foreach ($get_data as $key => $value)
            {
                /* adds query string level data to the cache id */
                $id_pieces[] = "$key=$value";
            }
        }

        $id = implode("-", $id_pieces);
//echo("$id <pre>"); echo(print_r(debug_backtrace())); echo('<br>');

        return md5($id);
    }
    
    
protected function _SmartySetVars()
    {
        $this->mSmarty->assign("page_title",    $this->mPageTitle);
        $this->mSmarty->assign("onload",        implode("; ", $this->mBodyOnloads));
        $this->mSmarty->assign("javascripts",   $this->mJavaScripts);
        $this->mSmarty->assign("meta_tags",     $this->mMetaTags);
        $this->mSmarty->assign("stylesheets",   $this->mStylesheets);
        $this->mSmarty->assign("themedir",      $this->mThemeDir);
        $this->mSmarty->assign("main_tpl",      $this->mMainTpl);
        $this->mSmarty->assign("webpage",       $this);
        $this->mSmarty->assign("sideModules",   $this->mSideModules);
        $this->mSmarty->assign("sessid",        session_id());
        return true;
    }

	/* Print out our custom 404 error page
     * @return null         Always returns null
     */
    public function _error_404()
    {
  
    }
    
    //=================================== abstract methods to be implemented by the derived page classes ===================
    /**
     * Initilizes tpl and cache
     */
    protected abstract function _InitCaching();

    /**
     * Initialize the page settings -- additional smarty vars, etc...
     */
    protected abstract function _InitPage();
    
        
}    