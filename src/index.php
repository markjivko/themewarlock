<?php
/**
 * Theme Warlock - EntryPoint
 * 
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0 
 */

class EntryPoint {
    
    /**
     * Initialization
     * 
     * @return null
     */
    protected static function _init() {
        // Set the root
        !defined('ROOT') && define('ROOT', dirname(__FILE__));

        // Get the autoloader
        require_once ROOT . '/web/lib/Autoloader.php';

        // Get the autoloader instance
        Autoloader::getInstance();
        
        // Initialize the environment
        if (!Environment::init()) {
            Console::h3('Please try again...');
            exit();
        }
    }

    /**
     * Runner
     * 
     * @return null
     */
    public static function run() {
        // Initialize
        self::_init();

        // Command line
        if ('cli' == php_sapi_name()) {
            // Prepare the CLI integration
            new Cli_Run_Integration();
        } else {
            // Perform the routing
            Router::run();
        }
    }

}

EntryPoint::run();

/*EOF*/