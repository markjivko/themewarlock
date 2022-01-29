<?php
/**
 * Theme Warlock - IO
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
 
class IO {
    
    /**
     * Project Exported resources folder name
     */
    const FOLDER_NAME = 'dist';
    
    /**
     * WordPress Session (php's session.name handler)
     * 
     * @var WordPress_Session
     */
    protected static $_session = null;
    
    /**
     * The current project path
     * 
     * @var string
     */
    protected static $_root = null;
    
    /**
     * Initialize
     */
    protected static function _init() {
        // Get the session instance
        if (null === self::$_session) {
            self::$_session = WordPress_Session::getInstance();
        }
        
        // Current project path
        if (null === self::$_root) {
            self::$_root = Model_Projects::getProjectPath(
                self::$_session->getInstance()->getUserId(), 
                self::$_session->getInstance()->getProjectId()
            );
        }
    }
    
    /**
     * Get the input folder path
     * 
     * @return string
     */
    public static function inputPath() {
        // Initialize
        self::_init();
        
        // Get the root
        return self::$_root;
    }
    
    /**
     * Get the output folder path
     * 
     * @return string
     */
    public static function outputPath() {
        // Initialize
        self::_init();
        
        // Get the root
        return self::$_root . '/' . self::FOLDER_NAME;
    }
    
    /**
     * Get the temporary folder name (may include /subfolder)
     * 
     * @param int $userId (optional) User ID
     * @return string
     */
    public static function tempFolder($userId = null) {
        // Initialize
        self::_init();
        
        // Other user
        return 'temp/_' . self::$_session->getName($userId);
    }
    
    /**
     * Initialize (and clean) the IO folders
     */
    public static function initFolders($cleanUp = false) {
        // Project exists
        if ($cleanUp && is_dir(self::inputPath())) {
            if (is_dir(self::outputPath())) {
                if (!Tasks::isStaging() && !isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_ADDITIVE])) {
                    Folder::clean(self::outputPath());
                }
            } else {
                Folder::create(self::outputPath(), 0777, true);
            }
        }
    }
}

/*EOF*/