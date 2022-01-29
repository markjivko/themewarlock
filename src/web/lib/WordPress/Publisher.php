<?php
/**
 * Theme Warlock - WordPress_Publisher
 * 
 * @title      WordPress theme publisher
 * @desc       Publish a WordPress theme on the Live Website defined in {config.authorThemesUrl}
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Publisher {

    /**
     * WordPress Project to work on
     * 
     * @var Model_Project
     */
    protected $_project;

    /**
     * WordPress Snapshots for the current project
     * 
     * @var WordPress_Snapshots
     */
    protected $_snapshots;
    
    /**
     * WordPress Publisher - Remote calls handler
     *
     * @var WordPress_Publisher_Remote
     */
    protected $_remote;
    
    /**
     * Test Mode
     * 
     * @var boolean
     */
    protected $_testMode = false;
    
    /**
     * WordPress project publisher
     * 
     * @param int     $userId    User ID
     * @param int     $projectId Project ID
     * @param boolean $testMode  (optional) Test Mode; default <b>false</b>
     * @throws Exception
     */
    public function __construct($userId, $projectId, $testMode = false) {
        // Store the test mode
        $this->setTestMode($testMode);
        
        // Store the session
        WordPress_Session::getInstance()->setUserId($userId);
        WordPress_Session::getInstance()->setProjectId($projectId);
        
        // Get the projects instance
        $projects = Model_Projects::getInstance($userId);
        
        // Store the project
        $this->_project = $projects->get(
            WordPress_Session::getInstance()->getProjectId(), 
            WordPress_Session::getInstance()->getUserId()
        );
        
        // Store the snapshots
        $this->_snapshots = WordPress_Snapshots::getInstance(
            WordPress_Session::getInstance()->getProjectId(), 
            WordPress_Session::getInstance()->getUserId()
        );
        
        // Store the remote
        $this->_remote = new WordPress_Publisher_Remote($this);
    }
    
    /**
     * Get the associated project instance
     * 
     * @return Model_Project
     */
    public function getProject() {
        return $this->_project;
    }
    
    /**
     * Get the associated snapshots instance
     * 
     * @return WordPress_Snapshots
     */
    public function getSnapshots() {
        return $this->_snapshots;
    }
    
    /**
     * Get the test mode
     * 
     * @return boolean
     */
    public function getTestMode() {
        return $this->_testMode;
    }
    
    /**
     * Set the test mode
     * 
     * @param boolean $testMode Test Mode
     * @return WordPress_Publisher
     */
    public function setTestMode($testMode) {
        // Store the test mode
        $this->_testMode = (boolean) $testMode;
        
        // Update the local API file
        if ($this->_testMode) {
            file_put_contents(
                Config::getWpPath(true) . '/wp-admin/wp-redirect.php',
                Addons::getInstance()->parseDataKeys(
                    file_get_contents(ROOT . '/web/resources/wordpress/wp-redirect.php'),
                    Model_Project_Config::CATEGORY_CORE,
                    null,
                    'php',
                    true
                )
            );
        }
        
        // All done
        return $this;
    }
    
    /**
     * CLI tool
     * 
     * @param int     $userId    User ID
     * @param int     $projectId Project ID
     * @param boolean $testMode  (optional) Test Mode; default <b>false</b>
     */
    public static function run($userId, $projectId, $testMode = false) {
        Console::h1('Live publishing');
        
        // Sanitize the input
        $userId = intval($userId);
        $projectId = intval($projectId);
        $testMode = (boolean) $testMode;
        
        // Pre-validate
        try {
            if ($userId <= 0) {
                throw new Exception('Invalid User ID');
            }
            if ($projectId <= 0) {
                throw new Exception('Invalid Project ID');
            }
            
            // Get a new instance
            $instance = new self($userId, $projectId, $testMode);
            
            // Run the publish tool
            $instance->publish();
        } catch (Exception $ex) {
            Console::p($ex->getMessage(), false, $ex->getFile(), $ex->getLine());
        }
    }
    
    /**
     * Publish the current project
     */
    public function publish() {
        // Prepare the snapshots
        $snapshots = $this->_snapshots->getAll();
        
        // Get the total number of snapshots
        $snapshotsCount = count($snapshots);
        
        // No snapshots defined
        if (!$snapshotsCount) {
            throw new Exception('No snapshots defined');
        }
        
        // Start the status bar
        PercentBar::display(0);
        
        // Upload the theme archive and enable it for all websites
        $this->_remote->siteUpload(WordPress_Publisher_Remote::FILE_TYPE_THEME);
        PercentBar::display(10);
        
        // Upload the documentation
        $this->_remote->siteUpload(WordPress_Publisher_Remote::FILE_TYPE_DOCS);
        PercentBar::display(15);
        
        // Go through the snapshots
        foreach ($snapshots as $snapshotKey => $snapshot) {
            // Initialize the remote site
            $this->_remote->initialize($snapshot);
            PercentBar::display($this->_getPercent(10, $snapshotKey, $snapshotsCount));
            
            // Activate the theme for the current site
            $this->_remote->siteActivateTheme();
            PercentBar::display($this->_getPercent(20, $snapshotKey, $snapshotsCount));
            
            // (Re-)Install all of the theme's plugins
            $sitePlugins = $this->_remote->getSitePlugins();
            foreach ($sitePlugins as $pluginKey => $pluginName) {
                $this->_remote->siteInstallPlugin($pluginName);
                
                PercentBar::display(
                    $this->_getPercent(
                        20 + ($pluginKey + 1) / count($sitePlugins) * 60, 
                        $snapshotKey, 
                        $snapshotsCount
                    )
                );
            }

            // Upload the current snapshot archive
            $this->_remote->siteUpload(WordPress_Publisher_Remote::FILE_TYPE_SNAPSHOT);
            PercentBar::display($this->_getPercent(90, $snapshotKey, $snapshotsCount));
            
            // Perform a quick (re-)install of the snapshot
            $this->_remote->siteInstallSnapshot();
            PercentBar::display($this->_getPercent(100, $snapshotKey, $snapshotsCount));
        }
        
        // Done
        'cli' == php_sapi_name() && print PHP_EOL;
        Console::p('Finished publishing ' . count($snapshots) . ' snapshot' . (1 === count($snapshots) ? '' : 's'));
    }
    
    /**
     * Compute the total deployment progress
     * 
     * @param int   $percent Current task percent associated with the current array element; [0-100]
     * @param int   $key     Current array key (starts with 0)
     * @param int   $count   Total elements in array
     * @param float $start   (optional) Total countdown start; default <b>15</b>
     * @param float $finish  (optional) Total countdown end; default <b>100</b>
     * @return float Total deployment progress
     */
    protected function _getPercent($percent, $key, $count, $start = 15, $finish = 100) {
        // Get the total interval length
        $intervalLength = ($finish - $start) / $count;
        
        // Get the interval start
        $intervalStart = $key * $intervalLength;
        $intervalEnd = ($key + 1) * $intervalLength;

        // Get the adjusted percentage
        return ($intervalStart + $percent / 100 * ($intervalEnd - $intervalStart)) + $start;
    }
}

/*EOF*/