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
        $this->_InitIncludes();        // empty for now
        $this->_InitCaching();         // in derived class
        
        // call SetupSmarty with 'false   turn caching off 
        $this->SetupSmarty(($this->_mAllowCaching || $this->_mMainTplCaching));

        // Determines the necessity to call _InitPage(); 
        $this->_InitPageCheck();
    }


    /**
     * Initilize site wide js and css and body onloads.*
     *  the idea is that derived pages can override this
     * @return bool always true
     */
    protected function _InitIncludes()
    {
        return true;
    }    

    
    /**
     * Determine if it's necessary to call _InitPage().
     * @return bool always true
     */
    protected function _InitPageCheck()
    {
        /* If this webpage or module is not cached, then proceed with the rest of the processing */
        if (!$this->_mAllowCaching || !$this->_DisplayIfCached($this->mDefaultTpl, $this->_mSmartyCacheId))
        {
        	/* Set system smarty vars */
            $this->_SmartySetVars();

            // Lets define the main template cache ID
            $this->_mSmartyMainTplCacheId = $this->_GetCacheId();

            if ($this->_mMainTplCaching)
            {
                if (!($main_content = $this->_DisplayIfCached($this->mMainTpl, $this->_mSmartyMainTplCacheId, true)))
                {
                    $this->_InitPage();

                    $main_content = $this->mSmarty->fetch($this->mMainTpl, $this->_mSmartyMainTplCacheId, $this->_mSmartyMainTplCacheId);
                }

                $this->mSmarty->assign("main_tpl_content", $main_content);
            }
            else
            {
                $this->_InitPage();
            }
        }
        else
        {
            $this->mHasDisplayedCache = true;
        }

        return true;
    }    

    
    /**
     * Loads modules that should display on every page.
     * @return null   Always returns null
     */

     

    /**
     *
     */
    public function AddSideModule($class_name, $location, $arguments = array())
    {
        $this->mSideModules[$location][] = array("class_name" => $class_name, "arguments" => $arguments);
        return null;
    }

    public function ReplaceSideModule($current_mod_class, $new_mod_class, $location, $arguments = array())
    {
        $index = 0;

        foreach ((array)$this->mSideModules[$location] as $mod)
        {
            if($mod['class_name'] == $current_mod_class)
            {
                break;
            }
            $index++;
        }
        if (strlen($new_mod_class) > 0)
        {
            $this->mSideModules[$location][$index]['class_name'] = $new_mod_class;
            $this->mSideModules[$location][$index]['arguments'] = $arguments;
        }
        else
        {
            unset($this->mSideModules[$location][$index]);
        }

        return null;
    }

    /**



 

    /**
     * Load modules
     */
    public function LoadModule($module_settings)
    {
        $module = new $module_settings['class_name']($this->mWebsite, $module_settings['arguments']);

        if (!$module->mHasDisplayedCache)
        {
            $module->Display();
        }

        return null;
    }

    /**
     * Will replace the default template with the one passed in.
     *
     * @param string $default_tpl       The new default template file
     * @return null                     Always returns null
     *
     * Note: if SetupSmarty() was called previously, and caching is disabled
     * from Config, the page may still cache unless compile_check is also enabled
     */
    public function SetDefaultTpl($default_tpl)
    {
        if ('' != trim($default_tpl))
        {
            $this->mDefaultTpl = $default_tpl;
            $this->_CheckCache($default_tpl, $this->_mSmartyCacheId);
        }

        return null;
    }

    public function SetMainTpl($main_tpl)
    {
        if ('' != trim($main_tpl))
        {
            $this->mMainTpl = $main_tpl;
            $this->_CheckCache($main_tpl, $this->_mSmartyMainTplCacheId);
        }
        return null;
    }

    /**
     * Instantiates smarty for a generic website and pulls in the correct config
     * @return bool Always returns true
     */
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
        else
        {
            // Lets make sure the cache is cleared for the page
   //         $this->mSmarty->clear_cache($this->mDefaultTpl, $this->_mSmartyCacheId, $this->_mSmartyCacheId);

            // Any compiled templates that share this cache id will have their compiled templates cleared.
   //         $this->mSmarty->clear_compiled_tpl(null, $this->_mSmartyCacheId);
        }

        if (is_callable($callback))
        {
            call_user_func($callback);
        }

        return null;
    }

    /**
     * Build this cache id base on
     * 1. module class name
     * 2. website sitecode
     * 3. show cms
     * 4. secure
     * 5. url path & query params
     * @return bool Always returns true
     */
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

        return md5($id);
    }


    /**
     * Dumps the smarty cache and displays the page if request by using $_GET['dumpcache'] = 1
     *
     * @return boolean
     * @author Jude Hansen
     */
    private function _DisplayIfCached($template, $cache_id, $fetch = false)
    {
        $this->_CheckCache($template, $cache_id);

        if ($this->mSmarty->is_cached($template, $cache_id, $cache_id))
        {
            if ($fetch)
            {
                return $this->mSmarty->fetch($template, $cache_id, $cache_id);
            }
            else
            {
                $this->Display($template, $cache_id);
            }
            return true;
        }
        else
        {
            /* if a valid cached template is not available, we need to clear out any templates that match the id, as  */
            /* as subsequent calls to fetch() or display() may still pull in an expired template  */
            $this->mSmarty->clear_cache($template, $cache_id, $cache_id);

            // Any compiled templates that share this cache id will have their compiled templates cleared.
            $this->mSmarty->clear_compiled_tpl(null, $cache_id);
        }

        return false;
    }

    private function _CheckCache($template, $cache_id)
    {
        if ( $_GET['dumpcache'] > 0  && $this->mSmarty)
        {
            $this->mSmarty->clear_cache($template, $cache_id, $cache_id);

            // Any compiled templates that share this cache id will have their compiled templates cleared.
            $this->mSmarty->clear_compiled_tpl(null, $cache_id);
        }
        return null;
    }

    /**
     * This method displays the html/smarty content in mDefaultTpl template file.
     *
     * @return bool Always returns true
     */
    public function Display($template = null, $cache_id = null)
    { 
        PageTimer::timeIt("start of Display($this->mMainTpl, $template)");
        // dumpcache on all server in the live environment
        if ( $_GET['dumpcache'] == 1  && array_shift(explode(".", $_SERVER['SERVER_NAME'])) == 'www' )
        {
            // extracting dumpcache variable and reconstruct the dumpache url
            $path = "http://$_SERVER[SERVER_NAME]$_SERVER[PHP_SELF]?".trim(str_replace("dumpcache=1", "", $_SERVER["QUERY_STRING"]), "&");

            // loop through 3 servers
            foreach(range(1,3) as $serverNum)
            {
                $serverNo = $serverNum > 1 ? $serverNum : "";

                // if server environment do not match current server then dumpcache on other servers
                if ($_ENV["HOSTNAME"] !== "arachne$serverNo.erepublic.com")
                {
                    // debugging echo
                    // echo "arachne$serverNo.erepublic.com ==> $path&server=$serverNum&dumpcache=2<br />";
                  $ch = curl_init();
                  curl_setopt($ch, CURLOPT_URL, "$path&server=$serverNum&dumpcache=2");
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //return the transfer as a string
                  $output = curl_exec($ch);
                  curl_close($ch);
                }
            }
        }
        elseif ( $_GET['dumpcache'] > 1) //if dumpcache = 2 , dont display
           return true;

        $template = $template ? $template : $this->mDefaultTpl;
        $cache_id = $cache_id ? $cache_id : $this->_mSmartyCacheId;

        /* Set system smarty vars */
        $this->_SmartySetVars();

        //This is only necessary when the main template is cached, but the default template is not
        if ($this->mSmarty->caching > 0 && !$this->_mAllowCaching)
        {
            $this->mSmarty->caching         = 0;
            $this->mSmarty->force_compile   = true;
        }

    //    die('template: '.$template);
        //Displays the default template
        $this->mSmarty->display($template, $cache_id, $cache_id);

        return true;
    }

    /**
     * Assigns all global website-related variables to smarty, before displaying.
     *
     * @return bool Always returns true
     */
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
        // Insert bad url into database
        $sql  = "insert into ext_invalid_urls (bad_url, hit_date) values ('";
        $sql .= mssql_escape_string($_SERVER['REDIRECT_URL']) . "', GETDATE())";

        require_once('inc.admin.php');
        @mssql_query($sql);

        header('HTTP/1.1 404 Not Found');
        $this->SetMainTpl("../common/error_404.tpl");
        return null;
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