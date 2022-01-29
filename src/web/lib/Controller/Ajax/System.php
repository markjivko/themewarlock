<?php
/**
 * Theme Warlock - Controller_Ajax_Projects
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax_System extends Controller_Ajax {
    
    /**
     * Alter a configuration value
     * 
     * @allowed admin
     * @throws Exception
     */
    public function config() {
        // Get the name
        $configName = trim(Input::getInstance()->postRequest('name'));
        
        // Get the value
        $configValue = trim(Input::getInstance()->postRequest('value'));
        
        // Get the configuration descriptor
        $descriptor = Config_Items_Descriptor::getInstance()->describe();
        
        // Validate the configuration name
        if (!isset($descriptor[$configName])) {
            throw new Exception('Invalid configuration key');
        }
        
        // Attempt to set the value
        return $descriptor[$configName]->setValue($configValue);
    }
    
}