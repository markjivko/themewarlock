<?php

/**
 * Theme Warlock - Autoloader
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Autoloader {

    /**
     * Autoloader instance
     * 
     * @var Autoloader
     */
    protected static $_instance;

    /**
     * Singleton
     * 
     * @return Autoloader
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Private constructor
     */
    protected function __construct() {
        // Increase the memory
        ini_set('memory_limit', '6048M');
        ini_set('pcre.backtrack_limit', '1024M');

        // No time limit
        set_time_limit(0);

        // Time limit
        ini_set('max_execution_time', 9999999999);
        ini_set('max_input_time', 9999999999);
        
        // Set the timezone
        date_default_timezone_set('Europe/Bucharest');

        // Do nothing
        spl_autoload_register(array($this, '_findClass'));
        
        // Set the log level to info
        Log::setLevel(Log::LEVEL_INFO);
    }

    /**
     * Locate and include a class by name
     * 
     * @param string $className Class name
     */
    protected function _findClass($className = '') {
        // Prepare the class path
        $classPath = str_replace(' ', '/', ucwords(str_replace('_', ' ', $className)));
        if (file_exists($classFileName = ROOT . '/web/lib/' . $classPath . '.php')) {
            require_once $classFileName;
        }
    }

}

/*EOF*/