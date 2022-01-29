<?php
/**
 * Theme Warlock - Config_Validation
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Config_Validation {
    /**
     * Config defaults
     *
     * @var Config_Validation
     */
    protected static $_instance;
    
    /**
     * Config defaults
     * 
     * @var Config_Defaults
     */
    protected $_defaults;
    
    /**
     * Configuration validation
     */
    protected function __construct() {
        // Getthe defaults class
        $this->_defaults = Config_Defaults::getInstance();
    }
    
    /**
     * Config_Validation
     * 
     * @return Config_Validation
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Validate the aprameter
     * 
     * @param string $parameterName   Parameter name
     * @param mixed  &$parameterValue Current parameter value
     * @return null
     */
    public function validate($parameterName, &$parameterValue) {
        // Get the method name
        $methodName = 'validate' . ucfirst($parameterName);

        // Method found
        if (method_exists($this, $methodName)) {
            $parameterValue = call_user_func([$this, $methodName], $parameterValue);
        }
    }
    
    /**
     * Validate the log level
     * 
     * @return string
     */
    public function validateLogLevel($logLevel) {
        if (!in_array(strtolower($logLevel), Log::$priorities)) {
            return strtoupper(Log::LEVEL_INFO);
        }
        
        return strtoupper($logLevel);
    }
    
}

/*EOF*/