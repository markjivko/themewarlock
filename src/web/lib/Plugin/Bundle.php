<?php
/**
 * Theme Warlock - Plugin_Bundle
 * 
 * @title      Plugins bundle for WPBakery Page Builder Add-Ons
 * @desc       Create a single plugin for all the Add-Ons, improving speed of deployment and overall performance
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Plugin_Bundle {
    
    /**
     * Local cache
     * 
     * @var mixed[]
     */
    protected static $_cache = array();
    
    /**
     * Pack the deployed WPBakery Page Builder Plugins in one package<br/>
     * Used to reduce the number of VC content plugins resulting in improved install performance<br/>
     * <b>To be called AFTER all addons and plugins are deployed!</b>
     */
    public static function pack() {
        // Single use
        if (null !== self::_cacheGet(__FUNCTION__)) {
            Log::warning('Tried to create the WPBakery Page Builder Bundle multiple times');
            return;
        }
        
        // Prepare the available WPBakery Page Builder Plugins list
        $vcPlugins = self::getPlugins();

        // Nothing to do
        if (!count($vcPlugins)) {
            Log::info('No WPBakery Page Builder plugins deployed!');
            return;
        }
        
        /* @var $firstPlugin Plugin */
        $firstPlugin = current($vcPlugins);
        
        // Create the destination directory once
        if (!is_dir($destDir = Tasks_1NewProject::getPath() . '/' . Framework::FOLDER_PLUGINS . '/' . self::getName())) {
            // Deploy the files
            Addons::getInstance()->deployFilesHelper(
                ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS . '/' . Plugin::PLUGIN_VC_BUNDLE, 
                Framework::FOLDER_PLUGINS . '/' . self::getName(),
                null,
                Model_Project_Config::CATEGORY_CORE,
                $firstPlugin->getName()
            );
            
            // Rename the main file
            rename($destDir . '/main.php', $destDir . '/' . self::getName() . '.php');
            
            // Prepare the UI set
            $uiSet = isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_UI_SET]) ? Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_UI_SET] : UiSets::SET_DEFAULT;
            
            // Something changed, revert to the default
            if (!is_file(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . $uiSet . '.css')) {
                $uiSet = UiSets::SET_DEFAULT;
            }
            
            // Prepare the default Bootstrap file
            copy(
                ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . $uiSet . '.css', 
                $destDir . '/css/bootstrap.css'
            );
        }
        
        // Go through the deployed VC plugins
        foreach ($vcPlugins as $plugin) {
            // Prepare the source directory
            $srcDir = Tasks_1NewProject::getPath() . '/' . Framework::FOLDER_PLUGINS . '/' . $plugin->getSlug();
            
            // Invalid directory
            if (!is_dir($srcDir)) {
                Log::warning('Source directory missing for VC plugin "' . $plugin->getName(). '"');
                continue;
            }
            
            // Copy the resources
            Folder::copyContents($srcDir, $destDir . '/' . $plugin->getSlug());
            
            // Remove the original folder
            Folder::clean($srcDir, true);
        }
        
        // Plugins bundle detected
        if (is_dir($destDir)) {
            Zip::packNative($destDir, 'tar');
        }
        
        // Flag the execution of this method
        self::_cacheSet(__FUNCTION__, true);
    }

    /**
     * Get the bundle name
     * 
     * @return string
     */
    public static function getName() {
        // Get the result from cache
        $result = self::_cacheGet(__FUNCTION__);
        
        // Cache miss
        if (null === $result) {
            // Prepare the result
            $result = 'st-vc-bundle-' . Tasks_1NewProject::$destDir;
            
            // Store in cache
            self::_cacheSet(__FUNCTION__, $result);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the bundle variable name
     * 
     * @return type
     */
    public static function getNameVar() {
        // Get the result from cache
        $result = self::_cacheGet(__FUNCTION__);
        
        // Cache miss
        if (null === $result) {
            // Prepare the result
            $result = preg_replace('%\W+%', '_', self::getName());
            
            // Store in cache
            self::_cacheSet(__FUNCTION__, $result);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the Regular Expression for plugins included in the bundle
     * 
     * @param boolean $includeExtra (optional) Include support (extra) plugins; default <b>true</b>
     * @return string
     */
    public static function getRegEx($includeExtra = true) {
        // Get the result from cache
        $result = self::_cacheGet($cacheKey = __FUNCTION__ . '/' . intval($includeExtra));
        
        // Cache miss
        if (null === $result) {
            // Prepare the RegEx
            $result = '%^vc\-%';

            // Include extra support plugins in this bundle
            if ($includeExtra) {
                // Prepare the extra plugins ind this bundle
                $extraPlugins = array();

                // Use WPBakery Page Builder blocks in sidebars
                if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS]) && Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS]) {
                    $extraPlugins[] = Plugin::PLUGIN_CONTENT_TYPE_WIDGET_BLOCK;
                }

                // Valid extra plugins found
                if (count($extraPlugins)) {
                    $result = '%^(?:vc\-|(?:' . implode('|', array_map('preg_quote', $extraPlugins)) . ')$)%';
                }
            }
            
            // Store in cache
            self::_cacheSet($cacheKey, $result);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the deployed WPBakery Page Builder plugins
     * 
     * @param boolean $includeExtra (optional) Include the support plugins when creating the bundle; default <b>true</b>
     * @return Plugin[]
     */
    public static function getPlugins($includeExtra = true) {
        // Get the result from cache
        $result = self::_cacheGet($cacheKey = __FUNCTION__ . '/' . intval($includeExtra));
        
        // Cache miss
        if (null === $result) {
            // Prepare the result
            $result = array();

            // Go through the deployed VC plugins
            foreach (Addon::$pluginInstances as $plugin) {
                // Not a WPBakery Page Builder plugin
                if (!preg_match(self::getRegEx($includeExtra), $plugin->getName())) {
                    continue;
                }

                // Store the plugin information
                $result[$plugin->getName()] = $plugin;
            }
            
            // Store in cache
            self::_cacheSet($cacheKey, $result);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get a value stored in cache
     * 
     * @param string $cacheKey Cache Key
     * @return mixed|null Stored value or NULL if none found
     */
    protected static function _cacheGet($cacheKey) {
        return isset(self::$_cache[$cacheKey]) ? self::$_cache[$cacheKey] : null;
    }
    
    /**
     * Store a value in cache
     * 
     * @param string $cacheKey   Cache Key
     * @param mixed  $cacheValue Cache Value
     */
    protected static function _cacheSet($cacheKey, $cacheValue) {
        self::$_cache[$cacheKey] = $cacheValue;
    }
    
}

/*EOF*/