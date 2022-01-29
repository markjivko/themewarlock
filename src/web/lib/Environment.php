<?php

/**
 * Theme Warlock - Environment
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Environment class
 */
final class Environment {
    /**
     * Instance
     * 
     * @var Environment
     */
    protected static $_instance;
    
    /**
     * Already initialized
     * 
     * @var boolean
     */
    public static $init = false;
    
    /**
     * File-mode Input
     * 
     * @var boolean
     */
    protected static $_fileMode = false;

    /**
     * Return a Singleton instance
     * 
     * @return Environment
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    /**
     * Running in file mode?
     * 
     * @return boolean
     */
    public static function getFileMode() {
        return self::$_fileMode;
    }
    
    /**
     * Initialize the environment
     * 
     * @return boolean
     */
    public static function init() {
        // Start the output buffer
        if (php_sapi_name() != "cli") {
            ob_start();
        }
        
        // Set the xdebug arguments
        @ini_set('xdebug.var_display_max_depth', 5);
        @ini_set('xdebug.var_display_max_children', 256);
        @ini_set('xdebug.var_display_max_data', 1024);
        
        // Arguments
        global $argv;
        
        // Set the current working directory
        chdir(ROOT);
        
        // Already initialized, nothing to do
        if (self::$init) {
            Log::check(Log::LEVEL_INFO) && Log::info('Environment already initialized');
            return true;
        }

        // Set the actual log level
        Log::setLevel(Config::get()->logLevel);
        
        // Already initialized
        self::$init = true;
        
        // All done
        return true;
    }
    
    /**
     * Get an environment key
     * 
     * @param string $key Key
     * @return string|null
     */
    public static function get($key) {
        return self::getInstance()->_get($key);
    }

    /**
     * Set an environment key
     * 
     * @param string $key   Key
     * @param string $value Value
     * @return boolean
     */
    public static function set($key, $value) {
        return self::getInstance()->_set($key, $value);
    }

    /**
     * Append one or more paths
     * 
     * @param string $path Path(s)
     */
    public static function appendPath($path) {
        // Get the combined paths
        $paths = array_filter(array_unique(array_merge(explode(PATH_SEPARATOR, getenv('Path')), explode(PATH_SEPARATOR, $path))));

        // Save to the environment
        return putenv('Path=' . implode(PATH_SEPARATOR, $paths));
    }

    /**
     * Get an environment key
     * 
     * @param string $key Key
     */
    protected function _get($key) {
        if ($this->_validate($key)) {
            $data = getenv($key);
            return false !== $data ? $data : null;
        }
        return null;
    }

    /**
     * Set an environment key
     * 
     * @param string $key   Key
     * @param string $value Value
     * @return boolean
     */
    protected function _set($key, $value) {
        if ($this->_validate($key, $value)) {
            return putenv($key . '=' . $value);
        }
        return false;
    }

    /**
     * Validate that a key is in the liste of constants
     * 
     * @param string $key   Key
     * @param string $value Value
     * @return boolean
     */
    protected function _validate($key, $value = '') {
        // The value must not contain the = sign
        if (false !== strpos($value, '=')) {
            return false;
        }

        // Get allowed constants to set
        $reflection = new ReflectionClass(self::getInstance());
        return in_array($key, $reflection->getConstants());
    }

}

/*EOF*/