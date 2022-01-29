<?php
/**
 * Theme Warlock - WordPress_License
 * 
 * @title      License Generator
 * @desc       Generate the License that goes along with this WordPress theme
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_License {

    /**
     * Singleton instance of WordPress_License
     * 
     * @var WordPress_License
     */
    protected static $_instance = null;
    
    /**
     * Singleton
     */
    protected function __construct() {
        // Nothing to do
    }
    
    /**
     * Singleton instance of WordPress_License
     * 
     * @return WordPress_License
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Create the license files at the required location
     * 
     * @param string $dirPath Path to store the license files
     * @return WordPress_License
     */
    public function save($dirPath) {
        if (!is_dir($dirPath)) {
            Folder::create($dirPath, 0777, true);
        }
        
        // Copy the license files
        foreach (glob(ROOT . '/web/resources/wordpress/license/*.*') as $licenseFilePath) {
            Addons::getInstance()->parse(
                $licenseFilePath, 
                $dirPath . '/' . basename($licenseFilePath), 
                Model_Project_Config::CATEGORY_CORE
            );
        }
        
        return $this;
    }

}

/* EOF */