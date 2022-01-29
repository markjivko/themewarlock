<?php
/**
 * Theme Warlock - WordPress_Snapshots_Snapshot
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Snapshots_Snapshot {

    // Data file name
    const DATA_FILE_NAME    = '_data.json';
    const PREVIEW_FILE_NAME = 'preview.png';
    
    // Flag expiration time in seconds
    const FLAG_EXPIRATION_TIME = 300;
    
    /**
     * Dimensions
     */
    const PREVIEW_WIDTH        = 1200;
    const PREVIEW_HEIGHT       = 900;
    const STORE_PREVIEW_WIDTH  = 1200;
    const STORE_PREVIEW_HEIGHT = 640;
    
    // Keys
    const KEY_PATHS       = 'paths';
    const KEY_TITLE       = 'title';
    const KEY_DESCRIPTION = 'description';
    const KEY_PATTERN     = 'pattern';
    
    // JSON keys
    const JSON_ID          = "i";
    const JSON_TITLE       = "t";
    const JSON_DESCRIPTION = "d";
    const JSON_PATTERN     = "p";

    /**
     * Project model
     * 
     * @var Model_Project
     */
    protected $_projectModel = null;
    
    /**
     * Snapshot ID
     * 
     * @var int
     */
    protected $_snapshotId;
    
    /**
     * Snapshot Path
     * 
     * @var int
     */
    protected $_snapshotPath;
    
    /**
     * Snapshot data
     * 
     * @var array
     */
    protected $_data = array();
    
    /**
     * Path to Snapshot activity flags
     * 
     * @var string
     */
    protected $_flagPath;
    
    /**
     * Associated snapshots exporter
     * 
     * @var WordPress_Snapshots_Exporter
     */
    protected $_exporter;
    
    /**
     * Associated snapshots exporter
     * 
     * @var WordPress_Snapshots_ScreenGrabber
     */
    protected $_screenGrabber;
    
    /**
     * Pattern object
     * 
     * @var Pattern
     */
    protected $_pattern;
    
    /**
     * Snapshot controller
     * 
     * @param int           $snapshotId   Snapshot Path
     * @param Model_Project $projectModel Project Model
     * @throws Exception
     */
    public function __construct($snapshotId, Model_Project $projectModel, $create = false) {
        // Invalid id
        if (!is_numeric($snapshotId)) {
            throw new Exception('Invalid snapshot ID "' . $snapshotId . '"');
        }
        
        // Store the flag path
        if (!is_dir($this->_flagPath = ROOT . '/web/' . IO::tempFolder($projectModel->getUserId()) . '/snapshot-flags')) {
            Folder::create($this->_flagPath, 0777, true);
        }
        
        // Store the snapshot ID
        $this->_snapshotId = $snapshotId;
        
        // Store the model
        $this->_projectModel = $projectModel;
        
        // Store the path
        $this->_snapshotPath = $this->getProject()->getConfig()->getProjectPath() . '/' . WordPress_Snapshots::FOLDER_NAME . '/' . $this->getId();
        
        // Create the path
        if (!is_dir($this->getLocation())) {
            if ($create) {
                Folder::create($this->getLocation(), 0777, true);
            }
        }
        
        // Create the exporter
        $this->_exporter = new WordPress_Snapshots_Exporter($this);
        
        // Create the screen grabber
        $this->_screenGrabber = new WordPress_Snapshots_ScreenGrabber($this);
        
        // Prepare the Pattern object
        $this->_pattern = new Pattern();
        
        // Reload the snapshot information
        $this->_reloadData();
        
        // Set the associated pattern
        if (isset($this->_data[self::KEY_PATTERN]) && strlen($this->_data[self::KEY_PATTERN])) {
            // Load the pattern from the save
            $this->_pattern->setPattern($this->_data[self::KEY_PATTERN]);
        } else {
            // Set the pattern for the first time and save the data
            $this->setPattern(null, true);
        }
        
        // Snapshot preview not defined
        if (is_dir($this->getLocation()) && !is_file($this->getPreviewPath())) {
            $this->generatePeview();
        }
    }
    
    /**
     * Get the path to the "dist" folder for the demo snapshots
     * 
     * @return string
     */
    public function getDistPath() {
        return IO::outputPath() . '/' . Dist::FOLDER_SNAPSHOTS_DEMO . '/' . $this->getId();
    }
    
    /**
     * Validate the data stored in "_data.json"
     * 
     * @param array  $jsonData     JSON data as an associative array
     * @param string $snapshotPath Snapshots path
     * @throws Exception
     */
    public static function validateData($jsonData, $snapshotPath, $snapshotId) {
        // Not a valid data
        if (!is_array($jsonData)) {
            throw new Exception('Data file corrupted for snapshot #' . $snapshotId);
        }

        // Validate the data
        if (isset($jsonData[self::KEY_PATHS])) {
            if (!is_array($jsonData[self::KEY_PATHS])) {
                throw new Exception('Paths are corrupted for snapshot #' . $snapshotId);
            }

            // Go through the paths
            foreach ($jsonData[self::KEY_PATHS] as $localFileName => $remotePath) {
                // Invalid file
                if (preg_match('%^[\/|\.]%', $remotePath)) {
                    throw new Exception('Invalid remote path "' . $remotePath . '" for snapshot #' . $snapshotId);
                }
                
                // File removed
                if (!file_exists($snapshotPath . '/' . $localFileName)) {
                    throw new Exception('File "' . $remotePath . '" missing for snapshot #' . $snapshotId);
                }
            }
        }
    }
    
    /**
     * Get the files stored in this Snapshot
     * 
     * @return array
     */
    public function getPaths() {
        return isset($this->_data[self::KEY_PATHS]) ? $this->_data[self::KEY_PATHS] : array();
    }
    
    /**
     * Get this snapshot's title
     * 
     * @return string
     */
    public function getTitle() {
        return isset($this->_data[self::KEY_TITLE]) ? $this->_data[self::KEY_TITLE] : '';
    }
    
    /**
     * Get the preview file path
     * 
     * @return string
     */
    public function getPreviewPath() {
        return $this->getLocation() . '/' . self::PREVIEW_FILE_NAME;
    }
    
    /**
     * Generate a new preview image
     * 
     * @return WordPress_Snapshots_Snapshot
     */
    public function generatePeview() {
        // Generate the new file
        (new Pattern())->generate(self::PREVIEW_WIDTH, self::PREVIEW_HEIGHT, $this->getPreviewPath());
        
        // All done
        return $this;
    }
    
    /**
     * Set the preview image for this snapshot
     * 
     * @param string $filePath Uploaded file path
     * @return WordPress_Snapshots_Snapshot
     * @throws Exception
     */
    public function setPreview($filePath) {
        // This snapshot's directory structure does not exist
        if (!is_dir($this->getLocation())) {
            throw new Exception('Cannot upload preview image to missing snapshot');
        }
        
        // No file provided
        if (!is_file($filePath)) {
            throw new Exception('No input file found');
        }
        
        // Get the image info
        $imageInfo = @getimagesize($filePath);
        
        // Invalid result
        if (!is_array($imageInfo)) {
            throw new Exception('The input file is not an image');
        }
        
        // Validate the mime type
        if ('image/png' != $imageInfo['mime']) {
            throw new Exception('The snapshot preview must be a PNG file');
        }
        
        // Validate the width
        if ($imageInfo[0] !== self::PREVIEW_WIDTH) {
            throw new Exception('The snapshot preview width must be ' . self::PREVIEW_WIDTH . 'px');
        }
        
        // Validate the height
        if ($imageInfo[1] !== self::PREVIEW_HEIGHT) {
            throw new Exception('The snapshot preview width must be ' . self::PREVIEW_HEIGHT . 'px');
        }
        
        // Save the file
        move_uploaded_file($filePath, $this->getPreviewPath());
        
        // All done
        return $this;
    }
    
    /**
     * Set this snapshot's title
     * 
     * @param string  $title      Snapshot Title
     * @param boolean $updateData (optional) Save the data to the disk; default <b>false</b>
     * @return WordPress_Snapshots_Snapshot
     */
    public function setTitle($title, $updateData = false) {
        // Set the title
        $this->_data[self::KEY_TITLE] = trim($title);
        
        // Update the data
        $updateData && $this->_updateData();
        
        // All done
        return $this;
    }
    
    /**
     * Get this snapshot's description
     * 
     * @return string
     */
    public function getDescription() {
        return isset($this->_data[self::KEY_DESCRIPTION]) ? $this->_data[self::KEY_DESCRIPTION] : '';
    }
    
    /**
     * Set this snapshot's description
     * 
     * @param string  $description Snapshot Description
     * @param boolean $updateData  (optional) Save the data to the disk; default <b>false</b>
     * @return WordPress_Snapshots_Snapshot
     */
    public function setDescription($description, $updateData = false) {
        // Set the description
        $this->_data[self::KEY_DESCRIPTION] = trim($description);
        
        // Update the data
        $updateData && $this->_updateData();
        
        // all done
        return $this;
    }
    
    /**
     * Get this snapshot's pattern
     * 
     * @return Pattern
     */
    public function getPatternObject() {
        return $this->_pattern;
    }
    
    /**
     * Save this snapshot's pattern; if none is provided, a random pattern will be used instead
     * 
     * @param string  $pattern    (optional) Pattern to use by name; default <b>null</b>
     * @param boolean $updateData (optional) Save the data to the disk; default <b>false</b>
     * @return WordPress_Snapshots_Snapshot
     */
    public function setPattern($pattern = null, $updateData = false) {
        // Store the pattern
        $this->_pattern->setPattern($pattern);
        
        // Save it internally
        $this->_data[self::KEY_PATTERN] = $this->_pattern->getPattern();
        
        // Update the data
        $updateData && $this->_updateData();
        
        // all done
        return $this;
    }
    
    /**
     * Get the snapshot ID
     * 
     * @return int
     */
    public function getId() {
        return $this->_snapshotId;
    }
    
    /**
     * Get the Project Model used for this snapshot
     * 
     * @return Model_Project
     */
    public function getProject() {
        return $this->_projectModel;
    }
    
    /**
     * Get the path to this snapshot
     * 
     * @return string
     */
    public function getLocation() {
        return $this->_snapshotPath;
    }
    
    /**
     * Get the snapshot exporter
     * 
     * @return WordPress_Snapshots_Exporter
     */
    public function getExporter() {
        return $this->_exporter;
    }
    
    /**
     * Get the screen grabber
     * 
     * @return WordPress_Snapshots_ScreenGrabber
     */
    public function getScreenGrabber() {
        return $this->_screenGrabber;
    }
    
    /**
     * Remove the current snapshot
     */
    public function delete() {
        // Wait for any other capture action to complete
        if (!$this->_waitForAction()) {
            return;
        }
        
        // Remove this snapshot
        Folder::clean($this->getLocation(), true);
        
        // Clean the data
        $this->_data = array();
        
        // Mark this action as completed
        $this->_waitForAction(false);
    }
    
    /**
     * Make sure no 2 script collide while performing filesystem operations;
     * This method must be called once at the beginning of the action and once before the end.
     * 
     * @param boolean $start (optional) True when waiting for an action, false when finishing an action; default <b>true</b>
     * @return boolean False if the flag release check timed out
     */
    protected function _waitForAction($start = true) {
        // Clear the cache
        clearstatcache();
        
        // Get the calling method
        $callingMethod = debug_backtrace()[1]['function'];
        
        // Prepare the flag name
        $flagFile = $this->_flagPath . '/' . $callingMethod . '-' . $this->getProject()->getUserId() . '-' . $this->getProject()->getProjectId() . '-' . $this->getId();
        
        // Finished work
        if (!$start) {
            // Remove the file
            is_file($flagFile) && unlink($flagFile);
            
            // Log the event
            Log::check(Log::LEVEL_INFO) && Log::info('Flag ' . basename($flagFile) . ': Cleared');
            return true;
        }
        
        // Prepare a retry counter
        $counter = 1;
        do {
            // Flag cleared or old flag
            if (!is_file($flagFile) || time() - filemtime($flagFile) >= self::FLAG_EXPIRATION_TIME) {
                Log::check(Log::LEVEL_INFO) && Log::info('Flag ' . basename($flagFile) . ': Start');
                
                // Time to plant our own
                file_put_contents($flagFile, '');
                
                // The current action can carry on
                return true;
            }
            
            // Store the count
            Log::check(Log::LEVEL_DEBUG) && Log::debug('Flag ' . basename($flagFile) . ': Waiting #' . $counter);
            
            // Sleep for half a second
            usleep(500000);
            
            // Increment
            $counter++;
            
            // Avoid zombies (10s max per thread)
            if ($counter >= 20) {
                break;
            }
        } while (true);
        
        // Timed out
        Log::check(Log::LEVEL_WARNING) && Log::warning('Flag ' . basename($flagFile) . ': Timeout');
        return false;
    }
    
    /**
     * Update the snapshot state (file system and database changes)
     * 
     * @return WordPress_Snapshots_Snapshot
     */
    public function capture() {
        // Wait for any other capture action to complete
        if (!$this->_waitForAction()) {
            return $this;
        }
        
        // Dump the changes over the original file
        WordPress::executeAction(
            WordPress::TOOLS_DB, 
            WordPress::TOOL_DB_DUMP
        );

        // Filesystem diff
        WordPress::executeAction(
            WordPress::TOOLS_FS,
            WordPress::TOOL_FS_DIFF,
            array(
                $this->getProject()->getProjectId(),
                $this->getId()
            )
        );

        // Gather the i18n strings
        WordPress::executeAction(
            WordPress::TOOLS_FS,
            WordPress::TOOL_FS_I18N,
            array(
                $this->getProject()->getConfig()->getDestDir()
            )
        );

        // Refresh
        $this->_reloadData();
        
        // Mark this action as completed
        $this->_waitForAction(false);
        
        // All done
        return $this;
    }
    
    /**
     * Activate this snapshot (file system and database changes)
     * 
     * @return WordPress_Snapshots_Snapshot
     */
    public function activate() {
        // Wait for any other activate action to complete
        if (!$this->_waitForAction()) {
            return $this;
        }
        
        // Filesystem clean-up and restoration of this snapshot
        WordPress::executeAction(
            WordPress::TOOLS_FS,
            WordPress::TOOL_FS_RESTORE,
            array(
                $this->getProject()->getProjectId(),
                $this->getId(),
            )
        );

        // Database restoration
        WordPress::executeAction(
            WordPress::TOOLS_DB, 
            WordPress::TOOL_DB_RESTORE
        );
        
        // Mark this action as completed
        $this->_waitForAction(false);
        
        // All done
        return $this;
    }
    
    /**
     * Convert this object into an array
     * 
     * @return array
     */
    public function toArray() {
        return array(
            self::JSON_ID          => $this->getId(),
            self::JSON_TITLE       => $this->getTitle(),
            self::JSON_DESCRIPTION => $this->getDescription(),
            self::JSON_PATTERN     => $this->getPatternObject()->getPattern(),
        );
    }
    
    /**
     * Re-load the "_data.json"
     * 
     * @throws Exception
     */
    protected function _reloadData() {
        $this->_checkPath();
        
        // Get the description
        if (file_exists($dataFilePath = $this->getLocation() . '/' . self::DATA_FILE_NAME)) {
            // Get the data
            $data = @json_decode(file_get_contents($dataFilePath), true);

            // Valid information
            self::validateData($data, $this->getLocation(), $this->getId());
            
            // Store it
            $this->_data = $data;
        }
    }
    
    /**
     * Update the data stored in the "_data.json" file
     * 
     * @throws Exception
     */
    public function _updateData() {
        $this->_checkPath();
        
        // Update the description
        file_put_contents($this->getLocation() . '/' . self::DATA_FILE_NAME, json_encode($this->_data));
    }
    
    /**
     * Check that this snapshot still exists
     * 
     * @throws Exception
     */
    protected function _checkPath() {
        // Get the snapshot path
        if (!is_dir($this->getLocation())) {
            throw new Exception('Snapshot #' . $this->getId() . ' does not exist in project #' . $this->getProject()->getConfig()->getProjectId() . ' by user #' . $this->getProject()->getConfig()->getUserId());
        }
    }
}

/* EOF */