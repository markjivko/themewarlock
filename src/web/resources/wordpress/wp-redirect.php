<?php

/**
 * Theme Warlock-WordPress API - This file should not be used directly
 * It is meant to be injected into the WordPress website, and executed via a POST request
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * No cookie required for our admin
 * 
 * @return boolean
 */
function wp_validate_auth_cookie() {
    return true;
}

class Tw_Wp_Task {

    /**
     * Request parameters
     */
    const REQUEST_CONTENT                 = 'content';
    const REQUEST_STATUS                  = 'status';
    const REQUEST_RESULT                  = 'result';
    const REQUEST_HEADER_SNAPSHOT_ID      = 'TW-Snapshot-Id';
    const REQUEST_HEADER_SITE_ID          = 'TW-Site-Id';
    const REQUEST_HEADER_SITE_SLUG_SUFFIX = 'TW-Site-Slug-Suffix';
    const REQUEST_HEADER_THEME            = 'TW-Theme';
    
    /**
     * Upload file types
     */
    const FILE_TYPE_THEME    = 'theme';
    const FILE_TYPE_DOCS     = 'docs';
    const FILE_TYPE_SNAPSHOT = 'snapshot';
    
    /**
     * Post keys
     */
    const POST_KEY_METHOD    = 'method';
    const POST_KEY_ARGUMENTS = 'arguments';
    const POST_KEY_FILE      = 'file';
    
    /**
     * Initialize the WordPress Customizer settings for these methods
     * 
     * @var string[]
     */
    protected static $_wpCustomizerMethods = array();

    /**
     *
     * @var type 
     */
    protected static $_instance;

    /**
     * Internal flag for the authentication method
     * 
     * @var boolean
     */
    protected $_authFlag = false;
    
    /**
     * Current snapshot ID
     * 
     * @var int
     */
    protected $_requestSnapshotId = 0;
    
    /**
     * Current site ID
     * 
     * @var int
     */
    protected $_requestSiteId = 0;
    
    /**
     * Current site slug suffix
     * 
     * @var string
     */
    protected $_requestSiteSlugSuffix = '';
    
    /**
     * Current theme package name
     * 
     * @var string
     */
    protected $_requestTheme = null;
    
    /**
     * WordPress Temporary API
     * 
     * @global type $current_user
     */
    protected function __construct() {
        // Nothing to do here
    }
    
    /**
     * WordPress Temporary API
     * 
     * @return Tw_Wp_Task
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Prepare the request key
     * 
     * @param string $salt Salt
     * @return string
     */
    protected function _prepareKey($salt) {
        return sha1($salt . '{config.authorThemesApiKey}');
    }
    
    /**
     * Get the request header for the provided key
     * 
     * @param string $requestKey Header key
     * @return string|null
     */
    protected function _getRequestHeader($requestKey) {
        // Prepare the header key
        $headerKey = 'HTTP_' . strtoupper(preg_replace('%\W%', '_', $requestKey));
        
        // Get the header
        return isset($_SERVER[$headerKey]) ? $_SERVER[$headerKey] : null;
    }
    
    /**
     * Authenticate the current request
     * 
     * @global type $current_user
     * @global string $pagenow
     * @global array $_wp_submenu_nopriv
     * @global WP_FileSystem $wp_filesystem
     * @throws Exception
     */
    protected function _auth() {
        // Already authenticated
        if ($this->_authFlag) {
            return;
        }
        
        // Access to the current user
        global $current_user, $pagenow, $_wp_submenu_nopriv;

        // Get the user agent
        $userAgent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
        
        // Pre-validate the header
        if (!preg_match('%^TW\:([\da-f]{40})%', $userAgent, $userAgentMatches)) {
            throw new Exception('Not authorized');
        }
        
        // Validate the payload
        if (!isset($_POST)) {
            throw new Exception('No data provided');
        }
        if (!isset($_POST[self::POST_KEY_METHOD])) {
            throw new Exception('No method provided');
        }

        // Get the method name
        $methodName = trim($_POST[self::POST_KEY_METHOD]);

        // Validate the method
        if ($userAgentMatches[1] !== $this->_prepareKey($methodName)) {
            throw new Exception('Method not allowed');
        }
        
        // Store the custom headers
        $this->_requestSnapshotId     = intval($this->_getRequestHeader(self::REQUEST_HEADER_SNAPSHOT_ID));
        $this->_requestSiteId         = intval($this->_getRequestHeader(self::REQUEST_HEADER_SITE_ID));
        $this->_requestSiteSlugSuffix = trim($this->_getRequestHeader(self::REQUEST_HEADER_SITE_SLUG_SUFFIX));
        $this->_requestTheme          = $this->_getRequestHeader(self::REQUEST_HEADER_THEME);
        
        // Set the environment
        $current_user = json_decode(json_encode(array('ID' => 1)));
        $pagenow = 'admin.php';
        $_wp_submenu_nopriv = array();

        // WP_Customizer must be initialized
        if (in_array($_POST[self::POST_KEY_METHOD], self::$_wpCustomizerMethods)) {
            $_REQUEST['wp_customize'] = 'on';
        }
        
        // Lift the time limit
        set_time_limit(0);
        
        // Trick the admin into serving the right site
        $_SERVER['REQUEST_URI'] = '/' . $this->_requestTheme . (strlen($this->_requestSiteSlugSuffix) ? '-' . $this->_requestSiteSlugSuffix : '') . '/wp-admin/' . basename(__FILE__);
        
        /** WordPress Administration Bootstrap */
        require_once(dirname(__FILE__) . '/admin.php');
        
        // Initialize the filesystem
        WP_Filesystem();
        
        // All done
        $this->_authFlag = true;
    }
    
    /**
     * Switch to the desired site by ID
     * 
     * @param int $siteId Site ID
     * @throws Exception
     */
    protected function _switchToSite($siteId = null) {
        if (null === $siteId || false === WP_Site::get_instance($siteId)) {
            throw new Exception('Invalid Site ID');
        }
        switch_to_blog($siteId);
    }
    
    /**
     * Try to execute the requested method
     * 
     * @return mixed Method result
     */
    protected function _executeMethod() {
        // Run the authentication
        $this->_auth();
        
        // Validate the method
        if (!method_exists($this, $_POST[self::POST_KEY_METHOD])) {
            throw new Exception('Method "' . $_POST[self::POST_KEY_METHOD] . '" not implemented');
        }

        // Get the method arguments
        $methodArguments = isset($_POST[self::POST_KEY_ARGUMENTS]) ? @json_decode(stripslashes($_POST[self::POST_KEY_ARGUMENTS]), true): array();
        if (!is_array($methodArguments)) {
            $methodArguments = array();
        }
        
        // Site ID provided
        if (0 !== $this->_requestSiteId) {
            // Switch to the site
            $this->_switchToSite($this->_requestSiteId);
        }
        
        // Prepare the result
        $result = call_user_func_array(array($this, $_POST[self::POST_KEY_METHOD]), $methodArguments);
        
        // Restore current blog
        if (0 !== $this->_requestSiteId) {
            restore_current_blog();
        }
        
        // All done
        return $result;
    }

    /**
     * Upload a file; account for URLs set in <b>\Addons_Utils</b>
     * 
     * @param string $fileType  File type
     * @return type
     * @throws Exception
     */
    public function siteUpload($fileType = null) {
        global $wp_filesystem;
        
        // No file provided
        if (!isset($_FILES[self::POST_KEY_FILE])) {
            throw new Exception('File not set');
        }
        
        // Store the file info
        $fileInfo = $_FILES[self::POST_KEY_FILE];

        // Prepare the file path
        switch ($fileType) {
            // Upload and replace a theme
            case self::FILE_TYPE_THEME:
                if (!preg_match('%\.zip$%', $fileInfo['name'])) {
                    throw new Exception('Expecting a ZIP archive');
                }
                
                // Not the right archive
                if ($this->_requestTheme !== basename($fileInfo['name'], '.zip')) {
                    throw new Exception('Invalid ZIP archive');
                }
                
                // Remove the old theme, if it exists
                if (is_dir($themePath = ABSPATH . 'wp-content/themes/' . $this->_requestTheme)) {
                    $wp_filesystem->rmdir($themePath, true);
                }
                
                // Unzip the file
                return unzip_file($fileInfo['tmp_name'], ABSPATH . 'wp-content/themes');
                break;
                
            // Upload and replace the docs
            case self::FILE_TYPE_DOCS:
                if (!preg_match('%\.zip$%', $fileInfo['name'])) {
                    throw new Exception('Expecting a ZIP archive');
                }
                
                // Not the right archive
                if ($this->_requestTheme !== basename($fileInfo['name'], '.zip')) {
                    throw new Exception('Invalid ZIP archive');
                }
                
                // Remove the old theme, if it exists
                if (is_dir($docsPath = ABSPATH . 'docs/' . $this->_requestTheme)) {
                    $wp_filesystem->rmdir($docsPath, true);
                }
                
                // Create the index
                if (!$wp_filesystem->is_file(ABSPATH . 'docs/index.php')) {
                    // Redirect home
                    $indexContents = '<' . '?php header("location: /");';
                    
                    // Create the file
                    $wp_filesystem->put_contents(ABSPATH . 'docs/index.php', $indexContents);
                }
                
                // Unzip the file
                return unzip_file($fileInfo['tmp_name'], ABSPATH . 'docs');
                break;
            
            // Upload snapshots
            case self::FILE_TYPE_SNAPSHOT:
                if (!preg_match('%\.zip$%', $fileInfo['name'])) {
                    throw new Exception('Expecting a ZIP archive');
                }
                
                // Not the right archive
                if ($this->_requestSnapshotId !== intval(basename($fileInfo['name'], '.zip'))) {
                    throw new Exception('Invalid ZIP archive');
                }
                
                // Unzip the file
                return unzip_file($fileInfo['tmp_name'], ABSPATH . 'wp-content/uploads/sites/' . $this->_requestSiteId . '/' . $this->_requestTheme . '/snapshots');
                break;
        }
        
        // Invalid file
        return false;
    }
    
    /**
     * Initialize the site
     * 
     * @param string $title       Site title
     * @param string $description (optional) Site description; default <b>null</b>
     * @param string $slugSuffix  (optional) Site slug suffix (after "theme-name"); default <b>empty string</b>
     * @throws Exception
     * @return int Site ID
     */
    public function initialize($title = null, $description = '', $slugSuffix = '') {
        if (null === $slugSuffix) {
            throw new Exception('Slug is mandatory');
        }
        if (null === $title) {
            throw new Exception('Title is mandatory');
        }
        
        // Prepare the domain
        $domain = get_network()->domain;
        
        // Prepare the path
        $path = get_network()->path . $this->_requestTheme . (strlen($slugSuffix) ? '-' . $slugSuffix : '') . '/';
        
        // Get the website ID
        $siteId = get_blog_id_from_url($domain, $path);
        
        // Invalid site ID
        if ($siteId <= 0) {
            // Get the new site ID
            $siteId = wpmu_create_blog(
                $domain, 
                $path, 
                $title, 
                get_current_user_id(), 
                array(
                    'public' => 1,
                ), 
                get_current_network_id()
            );
            
            // Some error occured
            if ($siteId instanceof WP_Error) {
                throw new Exception($siteId->get_error_message());
            }
        }
        
        // Update the title
        update_blog_option($siteId, 'blogname', $title);
        
        // Update the description
        if (null !== $description && strlen($description)) {
            update_blog_option($siteId, 'blogdescription', $description);
        }
        
        // Get the plugin upgrader
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-content/themes/' . $this->_requestTheme . '/inc/core-plugins.php';
        require_once ABSPATH . 'wp-content/themes/' . $this->_requestTheme . '/inc/class.tgm-plugin-activation.php';
        
        // Get the plugin activation class
        $pluginActivation = TGM_Plugin_Activation::get_instance();
        
        // Initialize TGM
        $pluginActivation->init();
        
        // Set to automatic
        $pluginActivation->config(array(
            'is_automatic' => true,
        ));

        // All done
        return array($siteId, array_keys($pluginActivation->plugins));
    }
    
    /**
     * Activate the theme
     * 
     * @return boolean
     * @throws Exception
     */
    public function siteActivateTheme() {
        // Theme name not provided
        if (null == $this->_requestTheme) {
            throw new Exception('Theme name is mandatory');
        }

        /* @var $theme WP_Theme */
        $theme = wp_get_theme($this->_requestTheme);

        // All done
        if (!is_object($theme) || !$theme->exists()) {
            throw new Exception('Theme "' . $this->_requestTheme . '" does not exist');
        }
        
        // Enable the theme on the entire network
        WP_Theme::network_enable_theme($this->_requestTheme);
        
        // Get the current theme
        $currentTheme = wp_get_theme();

        // Switch the theme
        if ($currentTheme->get_stylesheet() != $theme->get_stylesheet()) {
            switch_theme($theme->get_stylesheet());
        }
    }

    /**
     * Install all plugins associated with the current theme
     * 
     * @return boolean
     * @throws Exception
     */
    public function siteInstallPlugin($pluginName = null) {
        // Invalid plugin name
        if (null === $pluginName || !strlen($pluginName)) {
            throw new Exception('Invalid plugin name');
        }
        
        // Get the plugin upgrader
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        require_once ABSPATH . 'wp-content/themes/' . $this->_requestTheme . '/inc/core-plugins.php';
        require_once ABSPATH . 'wp-content/themes/' . $this->_requestTheme . '/inc/class.tgm-plugin-activation.php';
        
        // Get the plugin activation class
        $pluginActivation = TGM_Plugin_Activation::get_instance();
        
        // Initialize TGM
        $pluginActivation->init();
        
        // Set to automatic
        $pluginActivation->config(array(
            'is_automatic' => true,
        ));

        // Go through all the plugins
        if(!in_array($pluginName, array_keys($pluginActivation->plugins))) {
            throw new Exception('Plugin "' . $pluginName . '" not found for this theme!');
        }
        
        // Plugin not installed
        $result = false;

        // Get the download path
        $pluginPath = $pluginActivation->get_download_url($pluginName);

        // Prevent RevSlider's remote checks
        if ('revslider' == $pluginName) {
            // Prevent server requests
            $futureTime = time() + 86400;

            // Update the check ptions
            update_option('revslider_server_refresh', $futureTime);
            update_option('revslider-update-check', $futureTime);
            update_option('revslider-update-check-short', $futureTime);
            update_option('revslider-library-check', $futureTime);
            update_option('revslider-templates-check', $futureTime);
        }

        // Valid plugin archive path found
        if (null !== $pluginPath) {
            // Activate the plugins
            $skin = new Plugin_Installer_Skin(array('type' => 'upload'));

            // Create a new instance of Plugin_Upgrader.
            $upgrader = new Plugin_Upgrader($skin);

            // Install from source
            $upgrader->install($pluginPath);

            // Don't try to activate on upgrade of active plugin as WP will do this already.
            if (!is_plugin_active( $pluginName)) {
                foreach (glob(WP_PLUGIN_DIR . '/' . $pluginName . '/*.php' ) as $phpFile) {
                    // Get the plugin information
                    $pluginInfo = get_plugin_data($phpFile, false, false );

                    // Data found
                    if (isset($pluginInfo['Name']) && strlen($pluginInfo['Name'])) {
                        // Activate the plugin
                        if (null === activate_plugin($phpFile)) {
                            $result = true;
                        }
                        break;
                    }
                }
            }
        }
        
        // Flush the rewrite rules
        flush_rewrite_rules();
        
        // All went well
        return $result;
    }
    
    /**
     * Install the current snapshot
     * 
     * @return boolean
     */
    public function siteInstallSnapshot() {
        // Force install, CLI mode
        St_SnapshotManager::getInstance()->getById($this->_requestSnapshotId)->install(true);
        
        // All done
        return true;
    }

    /**
     * Run the tool
     */
    public function run($local = false) {
        // Prepare the result
        $status = true;
        $result = null;

        // Start the buffer
        ob_start();
        try {
            $result = $this->_executeMethod();
        } catch (Exception $ex) {
            $result = $ex->getMessage();
            $status = false;
        }

        // Positive result
        if (null === $result) {
            $result = true;
        }

        // Get the output
        $content = ob_get_clean();

        // Prepare the data
        $data = array(
            self::REQUEST_STATUS  => $status,
            self::REQUEST_RESULT  => $result,
            self::REQUEST_CONTENT => $content,
        );

        // Local mode
        if ($local) {
            return $data;
        }
        
        // Always redirect for the outside world
        header('location: /');
        
        // All done
        echo json_encode($data);
    }

}

// Execute the request
Tw_Wp_Task::getInstance()->run();

/*EOF*/