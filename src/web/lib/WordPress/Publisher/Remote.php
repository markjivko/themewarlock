<?php
/**
 * Theme Warlock - WordPress_Publisher_Remote
 * 
 * @title      WordPress Publisher - Remote calls handler
 * @desc       Handles the communication with the Live Website
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Publisher_Remote {

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
     * WordPress Publisher
     * 
     * @var WordPress_Publisher
     */
    protected $_parent;
    
    /**
     * Current Snapshot ID
     * 
     * @var int
     */
    protected $_snapshotId = 0;
    
    /**
     * Current Site ID
     * 
     * @var int
     */
    protected $_siteId = 0;
    
    /**
     * Current Site Plugins
     * 
     * @var string[]
     */
    protected $_sitePlugins = array();
    
    /**
     * Current Site Slug Suffix
     * 
     * @var string
     */
    protected $_siteSlugSuffix = '';
    
    /**
     * WordPress Publisher - Remote calls handler
     * 
     * @param WordPress_Publisher $parent
     */
    public function __construct(WordPress_Publisher $parent) {
        $this->_parent = $parent;
    }
    
    /**
     * Set the current snapshot ID to use for the remote requests
     * 
     * @param int $snapshotId Snapshot ID
     * @return WordPress_Publisher_Remote
     */
    protected function _setSnapshotId($snapshotId) {
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Site Snapshot ID set to "' . $snapshotId . '"');
        $this->_snapshotId = intval($snapshotId);
        return $this;
    }
    
    /**
     * Get the current snapshot ID
     * 
     * @return int
     */
    public function getSnapshotId() {
        return $this->_snapshotId;
    }
    
    /**
     * Set the current Site ID
     * 
     * @param int $siteId Site ID
     * @return WordPress_Publisher_Remote
     */
    protected function _setSiteId($siteId) {
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Site ID set to "' . $siteId . '"');
        $this->_siteId = intval($siteId);
        return $this;
    }
    
    /**
     * Set the current Site plugins
     * 
     * @param string[] $plugins Available site plugins
     * @return WordPress_Publisher_Remote
     */
    protected function _setSitePlugins($plugins) {
        if (!is_array($plugins)) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('Remote: Site plugins not defined correctly');
            return $this;
        }
        
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Theme bundled with ' . count($plugins) . ' plugin' . (1 === count($plugins) ? '' : 's'));
        $this->_sitePlugins = $plugins;
        return $this;
    }
    
    /**
     * Available site plugins
     * 
     * @return string[]
     */
    public function getSitePlugins() {
        return $this->_sitePlugins;
    }
    
    /**
     * Get the current WPMU Site ID
     * 
     * @return int
     */
    public function getSiteId() {
        return $this->_siteId;
    }
    
    /**
     * Set the site slug suffix
     * 
     * @param string $siteSlugSuffix Site Slug suffix
     * @return WordPress_Publisher_Remote
     */
    protected function _setSiteSlugSuffix($siteSlugSuffix) {
        $this->_siteSlugSuffix = trim($siteSlugSuffix);
        return $this;
    }
    
    /**
     * Get the site slug suffix
     * 
     * @return string
     */
    public function getSiteSlugSuffix() {
        return $this->_siteSlugSuffix;
    }
    
    /**
     * Upload a file
     * 
     * @param string $fileType  Upload type; default <b>WordPress_Publisher_Remote::FILE_TYPE_THEME</b>
     * @return boolean True on success, false on failure
     */
    public function siteUpload($fileType = self::FILE_TYPE_THEME) {
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Upload "' . $fileType . '"');
        
        // Prepare the directory
        $distPath = $this->_parent->getProject()->getConfig()->getProjectPath() . '/' . IO::FOLDER_NAME;
        
        // Get the file path
        $filePath = null;
        switch ($fileType) {
            // Theme
            case self::FILE_TYPE_THEME:
                $filePath = $distPath . '/' . $this->_parent->getProject()->getConfig()->getDestDir() . '.zip';
                break;
            
            // Documentation
            case self::FILE_TYPE_DOCS:
                $filePath = $distPath . '/' . Dist::FOLDER_DOCS . '/' . $this->_parent->getProject()->getConfig()->getDestDir() . '.zip';
                break;
            
            // Snapshot
            case self::FILE_TYPE_SNAPSHOT:
                $filePath = $distPath . '/' . Dist::FOLDER_SNAPSHOTS . '/' . $this->_snapshotId . '.zip';
                break;
        }
        
        // Validate the path
        if (null === $filePath) {
            throw new Exception('Invalid file type');
        }
        if (!is_file($filePath)) {
            throw new Exception('File "' . $filePath . '" not found');
        }

        // Perform the request
        $result = $this->_request(__FUNCTION__, array($fileType), $filePath);
        
        // Log the results
        Log::check(Log::LEVEL_DEBUG) && Log::debug($result);
        
        // All done
        return $this;
    }
    
    /**
     * Compute the site slug suffix
     * 
     * @param WordPress_Snapshots_Snapshot $snapshot
     * @return string Site Slug Suffix, a number or an empty string
     * @throws Exception
     */
    public static function computeSiteSlugSuffix($snapshot) {
        if (!$snapshot instanceof WordPress_Snapshots_Snapshot) {
            throw new Exception('Invalid argument, expected an instance of WordPress_Snapshots_Snapshot');
        }
        
        // Get the site slug suffix
        return 1 == $snapshot->getId() ? '' : $snapshot->getId();
    }
    
    /**
     * Initialize the site, setting the snapshot ID, site ID and site slug suffix for future requests
     * 
     * @param WordPress_Snapshots_Snapshot $snapshot Snapshot object
     * @throws Exception
     * @return WordPress_Publisher_Remote
     */
    public function initialize($snapshot) {
        // Invalid argument
        if (!$snapshot instanceof WordPress_Snapshots_Snapshot) {
            throw new Exception('Invalid argument, expected an instance of WordPress_Snapshots_Snapshot');
        }
        
        // Store the snapshot ID
        $this->_setSnapshotId($snapshot->getId());
        
        // Reset the plugins
        $this->_setSitePlugins(array());
        
        // Prepare the title
        $siteTitle = $snapshot->getTitle();
        
        // Set the default title
        if (!strlen($siteTitle)) {
            $siteTitle = 'Snapshot #' . $this->getSnapshotId();
        }
        
        // Log the event
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Initializing site "' . $siteTitle . '"');
        
        // Prepare the suffix (a number of an empty string)
        $siteSlugSuffix = self::computeSiteSlugSuffix($snapshot);
        
        // Initialization must be performed from the Root site
        $this->_setSiteSlugSuffix('');
        
        // Perform the request
        list($siteId, $plugins) = $this->_request(__FUNCTION__, array($siteTitle, $snapshot->getDescription(), $siteSlugSuffix));

        // Store the site ID for future requests
        $this->_setSiteId($siteId);
        
        // Store the slug for future request
        $this->_setSiteSlugSuffix($siteSlugSuffix);
        
        // Store the available plugins
        $this->_setSitePlugins($plugins);
        
        // Log the slug suffix
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Site Slug Suffix set to "' . $siteSlugSuffix . '"');
        
        // All done
        return $this;
    }
    
    /**
     * Activate the theme
     * 
     * @throws Exception
     * @return WordPress_Publisher_Remote
     */
    public function siteActivateTheme() {
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Activate Theme');
        
        // Perform the request
        $result = $this->_request(__FUNCTION__, func_get_args());
        
        // Log the details
        Log::check(Log::LEVEL_DEBUG) && Log::debug($result);
        
        // Function chaining
        return $this;
    }
    
    /**
     * Install all current theme's WordPress plugins
     * 
     * @throws Exception
     * @return WordPress_Publisher_Remote
     */
    public function siteInstallPlugin($pluginName) {
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Install Plugin "' . $pluginName . '"');
        
        // Perform the request
        $result = $this->_request(__FUNCTION__, func_get_args());
        
        // Log the details
        Log::check(Log::LEVEL_DEBUG) && Log::debug($result);
        
        // Function chaining
        return $this;
    }
    
    /**
     * Install the current snapshot
     * 
     * @throws Exception
     * @return WordPress_Publisher_Remote
     */
    public function siteInstallSnapshot() {
        Log::check(Log::LEVEL_INFO) && Log::info('Remote: Install Snapshot');
        
        // Perform the request
        $result = $this->_request(__FUNCTION__, func_get_args());
        
        // Log the details
        Log::check(Log::LEVEL_DEBUG) && Log::debug($result);
        
        // Function chaining
        return $this;
    }
    
    /**
     * Prepare the request key
     * 
     * @param string $salt Salt
     * @return string
     */
    protected function _prepareKey($salt) {
        return sha1($salt . Config::get()->authorThemesApiKey);
    }
    
    /**
     * Perform a request
     * 
     * @param string $methodName      Remote method name
     * @param array  $methodArguments Remote method arguments
     * @param string $filePath        (optional) File to upload; default <b>null</b>
     * @throws Exception
     * @return mixed Remote method result
     */
    protected function _request($methodName, Array $methodArguments = array(), $filePath = null) {
        // Prepare the post load
        $postData = array(
            self::POST_KEY_METHOD    => $methodName,
            self::POST_KEY_ARGUMENTS => json_encode($methodArguments),
        );
        
        // Prepare the file pointer
        if (null !== $filePath && is_file($filePath)) {
            // Add the file pointer
            $postData[self::POST_KEY_FILE] = curl_file_create($filePath);
        }
        
        // Prepare the URL
        $postUrl = Config::get()->authorThemesUrl . '/wp-admin/wp-redirect.php';
        
        // User agent
        $userAgent = 'TW:' . $this->_prepareKey($methodName);

        // Prepare the headers
        $curlHeaders = array (
            'User-Agent: ' . $userAgent,
            self::REQUEST_HEADER_SNAPSHOT_ID . ': ' . $this->_snapshotId,
            self::REQUEST_HEADER_SITE_ID . ': ' . $this->_siteId,
            self::REQUEST_HEADER_SITE_SLUG_SUFFIX . ': ' . $this->_siteSlugSuffix,
            self::REQUEST_HEADER_THEME . ': ' . $this->_parent->getProject()->getConfig()->getDestDir(),
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'DNT: 1',
            'Cache-Control: no-cache',
        );

        // Prepare the options
        $options = array(
            CURLOPT_REFERER         => Config::get()->authorThemesUrl,
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => $postData,
            CURLOPT_USERAGENT       => $userAgent,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => false,
            CURLOPT_HTTPHEADER      => $curlHeaders,
            CURLOPT_FOLLOWLOCATION  => false,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_ALL,
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_CONNECTTIMEOUT  => 10,
            CURLOPT_TIMEOUT         => 300,
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_FAILONERROR     => true,
            CURLOPT_URL             => $postUrl,
        );

        // Prepare the retries counter
        $curlLoggedOutRetries = 0;
        
        do {
            // Initialize the CURL
            $ch = curl_init();

            // Set the options
            curl_setopt_array($ch, $options);

            // Get the result
            $curlResult = curl_exec($ch);

            // Remove the clutter
            $jsonString = preg_replace('%.*?({"status":.*$)%ims', '${1}', $curlResult);

            // Extra content
            $extraContent = preg_replace('%(.*?){"status":.*$%ims', '${1}', $curlResult);

            // Execute the request
            $jsonArray = @json_decode($jsonString, true);

            // Invalid result
            if (!is_array($jsonArray)) {
                // Logged out
                if (preg_match('%<\!doctype%i', $jsonString)) {
                    // Increment the counter
                    $curlLoggedOutRetries++;
                    
                    // Inform the user
                    Log::check(Log::LEVEL_WARNING) && Log::warning('Invalid result (Logged Out): Attempt ' . $curlLoggedOutRetries . '/3');
                    
                    // Give up
                    if ($curlLoggedOutRetries >= 3) {
                        throw new Exception('Gave up on request');
                    }
                    
                    // Start over
                    continue;
                } 
                
                // Other type of error, stop here
                if (false === $curlResult) {
                    throw new Exception('Invalid result (cURL): ' . curl_error($ch));
                } else {
                    throw new Exception('Invalid result: ' . var_export($jsonString, true));
                }
            }
            
            // Close
            curl_close($ch);
            
            // Valid result, no need to retry
            break;
        } while (true);
        
        // Add the extra content
        $jsonArray[self::REQUEST_CONTENT] .= trim(html_entity_decode(strip_tags(preg_replace('%<br\s*\/?\s*>%i', PHP_EOL, $extraContent))));

        // Invalid format
        if (!isset($jsonArray[self::REQUEST_STATUS]) || !isset($jsonArray[self::REQUEST_RESULT])) {
            throw new Exception('Result has invalid format');
        }
        
        // Convert to boolean
        $jsonArray[self::REQUEST_STATUS] = (boolean)$jsonArray[self::REQUEST_STATUS];
        
        // Remote exception
        if (!$jsonArray[self::REQUEST_STATUS]) {
            throw new Exception('[Remote Exception]: ' . $jsonArray[self::REQUEST_RESULT]);
        }
        
        // All done
        return $jsonArray[self::REQUEST_RESULT];
    }
    
}

/*EOF*/