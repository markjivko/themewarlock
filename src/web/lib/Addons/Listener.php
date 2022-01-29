<?php
/**
 * Theme Warlock - Listener
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addons_Listener {
    
    // Listener types
    const TYPE_ON     = 'on';
    const TYPE_GET    = 'get';
    const TYPE_BEFORE = 'before';
    const TYPE_AFTER  = 'after';
    
    // Listener tasks
    const TASK_UI_GET_THEME              = 'uiGetTheme';
    const TASK_DRAWABLES_APPLY_INTERFACE = 'drawablesApplyInterface';
    const TASK_EFFECT_APPLY_INTERFACE    = 'effectApplyInterface';
    const TASK_DEVICES_APPLY_INTERFACE   = 'devicesApplyInterface';
    const TASK_MEDIA_GET_512             = 'mediaGet512';
    const TASK_MEDIA_GET_DESCRIPTION     = 'mediaGetDescription';
    const TASK_PLUGIN_GET_RIBBON         = 'pluginGetRibbon';
    const TASK_PLUGIN_GET_FRAMEWORK      = 'pluginGetFramework';
    const TASK_PLUGIN_GET_SUFFIX         = 'pluginGetSuffix';
    const TASK_PLUGIN_FEAT_FRAMEWORK     = 'pluginFeatFramework';
    const TASK_PLUGIN_APPLY_INTERFACE    = 'pluginApplyInterface';
    
    /**
     * Listeners
     * 
     * @var array Class instances
     */
    protected static $_listeners = array();
    
    /**
     * Sanitize the addon name
     * 
     * @param string &$addonName
     */
    protected static function _sanitizeAddonName(&$addonName) {
        // The Addon name is always lowercase
        $addonName = trim(strtolower($addonName));
        
        // Shortcode
        if ('framework' == $addonName && isset(Tasks::$config[Cli_Run_Integration::FRAMEWORK_ID])) {
            $addonName = Tasks::$config[Cli_Run_Integration::FRAMEWORK_ID];
        }
    }
    
    /**
     * Register a listener
     * 
     * @param mixed  $classInstance Class instance
     * @param string $addonName     Addon name
     */
    public static function register($classInstance, $addonName) {
        // Sanitize the Addon name
        self::_sanitizeAddonName($addonName);
        
        // Log the registry
        Log::check(Log::LEVEL_INFO) && Log::info('Registering listener ' . get_class($classInstance));

        // Will be using the add-on data
        if (property_exists($classInstance, 'addonData')) {
            // Get the project data
            $projectData = Tasks::$project->getConfig()->getProjectData();

            // Prepare the extra data
            $addonProjectData = array();

            // Prepare the key
            $addonProjectDataKey = (Model_Project_Config::CATEGORY_CORE != $addonName ? 'addon-' : '') . $addonName;

            // Value defined
            if (isset($projectData[$addonProjectDataKey])) {
                $addonProjectData = $projectData[$addonProjectDataKey];
            }
            
            // Set the Add-On data
            $classInstance->addonData = $addonProjectData;
        }
        
        // Will be using the add-on name
        if (property_exists($classInstance, 'addonName')) {
            $classInstance->addonName = $addonName;
        }
        
        // Register a listener
        self::$_listeners[$addonName] = $classInstance;
    }
    
    /**
     * Get the listener attributed to this addon
     * 
     * @param string $addonName Addon name
     * @return object|null Class instance or null if not found
     */
    public static function get($addonName) {
        // Sanitize the Addon name
        self::_sanitizeAddonName($addonName);

        // Get the addon listener by name
        return isset(self::$_listeners[$addonName]) ? self::$_listeners[$addonName] : null;
    }
    
    /**
     * Go through the listeners
     * 
     * @param string $taskName Task name
     * @param string $type     Listener type
     */
    public static function run($taskName, $type = self::TYPE_ON) {
        // After the new project
        if (strtolower($taskName) == 'newproject' && $type == self::TYPE_AFTER) {
            // Create a new addon
            $addonsInstance = Addons::getInstance();

            // Go through the list
            foreach (Tasks::$definedAddons as $definedAddon) {
                // Activate this addon
                $addonsInstance->deploy($definedAddon);
            }
            
            // Pack the WPBakery Page Builder plugins
            Plugin_Bundle::pack();
        }
        
        // Prepare the arguments
        $arguments = array_slice(func_get_args(), 2);
        
        // Prepare the result
        $result = array();
        
        // Go through the listeners
        foreach (self::$_listeners as $addonName => $listenerInstance) {
            // Valid instance and method identified
            if (is_object($listenerInstance) && method_exists($listenerInstance, $listenerMethod = self::_getMethodName($taskName, $type))) {
                // Log this
                Log::check(Log::LEVEL_INFO) && Log::info('Triggered a "' . $type . '" listener on "' . $taskName . '" by "' . get_class($listenerInstance) . '"');
                
                // Launch the listeners
                $result[$addonName] = call_user_func_array(array($listenerInstance, $listenerMethod), $arguments);
            }
        }
        
        // All done
        return $result;
    }
    
    /**
     * Parse just one listener
     * 
     * @param string $addonNameRegex Add-on name regular expression
     * @param string $taskName       Task name
     * @param string $type           Task type
     * @return mixed Method result or null on error
     */
    public static function filterRun($addonNameRegex, $taskName, $type = self::TYPE_ON) {
        // Prepare the arguments
        $arguments = array_slice(func_get_args(), 3);
        
        // Prepare the result
        $result = null;

        // Go through the listeners
        foreach (self::$_listeners as $addonName => $classInstance) {
            if (preg_match('%' . $addonNameRegex . '%i', $addonName)) {
                // Valid instance and method identified
                if (is_object($classInstance) && method_exists($classInstance, $listenerMethod = self::_getMethodName($taskName, $type))) {
                    // Log this
                    Log::check(Log::LEVEL_INFO) && Log::info('Triggered a "' . $type . '" listener on "' . $taskName . '" by "' . get_class($classInstance) . '"');

                    // Launch the listeners
                    if (null === $result = call_user_func_array(array($classInstance, $listenerMethod), $arguments)) {
                        $result = true;
                    }
                }
            }
        }
        
        // All done
        return $result;
    }
        
    /**
     * Get the listener method name
     * 
     * @param string $taskName Task name
     * @param string $type     Listener type
     * @return string Listener method name
     */
    protected static function _getMethodName($taskName, $type) {
        return $type . ucfirst($taskName);
    }
}

/*EOF*/