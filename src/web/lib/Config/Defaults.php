<?php
/**
 * Theme Warlock - Config_Defaults
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Config_Defaults {
    
    /**
     * Config defaults
     *
     * @var Config_Defaults
     */
    protected static $_instance;
    
    /**
     * Configuration defaults
     */
    protected function __construct() {
        // Nothing to do
    }
    
    /**
     * Config_Defaults
     * 
     * @return Config_Defaults
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Get the parameter default
     * 
     * @param string $parameterName   Parameter name
     * @param mixed  &$parameterValue Current parameter value
     * @return null
     */
    public function getDefault($parameterName, &$parameterValue) {
        // Get the method name
        $methodName = 'default' . ucfirst($parameterName);

        // Method found
        if (method_exists($this, $methodName)) {
            $parameterValue = $this->$methodName();
        }
    }
}

/*EOF*/