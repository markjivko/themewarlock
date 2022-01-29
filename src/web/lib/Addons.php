<?php
/**
 * Theme Warlock - Addons
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addons {
    
    /**
     * Special Add-On configuration folder name
     */
    const GO_FOLDER_NAME = 'go';
    
    /**
     * Special Add-on configuration file name
     */
    const GO_FILE_NAME   = 'go.php';
    
    // Flavor details
    const FLAVOR_NAME_DEFAULT            = 'default';
    const FLAVOR_FILE_INFO               = 'info.md';
    const FLAVOR_FILE_FUNCTIONS          = 'functions.js';
    const FLAVOR_FILE_FUNCTIONS_EXTRA    = '_functions.js';
    const FLAVOR_FILE_STYLE              = 'style.css';
    const FLAVOR_FILE_STYLE_EXTRA        = '_style.css';
    const FLAVOR_FILE_STYLE_INLINE       = 'style-inline.css';
    const FLAVOR_FILE_STYLE_INLINE_EXTRA = '_style-inline.css';
    const FLAVOR_FILE_CUSTOMIZER         = 'customizer.css';
    
    /**
     * Custom addons<br/> 
     * The keys represent the actual addon name<br/>
     * Allowed key tags: <ul>
     * <li>{value} - Config or run.csv value</li>
     * </ul><br/>
     * The values represent the custom addon config parameter AND run.csv option name<br/><br/>
     * If the config parameter is Boolean, {value} should be ignored as it would be replaced with an empty string.<br/>
     * The custom addons will be applied from bottom to top, before all the other addons.
     * @var Array
     */
    public static $custom = array(
        'apply-{value}'      => Cli_Run_Integration::OPT_SCREENSHOTS_EFFECT,
    );
    
    /**
     * Addons metadata
     * 
     * @var array <br/>
     * <code>
     * array(<br/>
     *     "addon name" => array(<br/>
     *         "addon title",<br/>
     *         "addon description",<br/>
     *     ),<br/>
     * )
     * </code>
     */
    protected static $_addonsMeta = array();
    
    /**
     * Addon configuration cache
     * 
     * @var array
     */
    protected static $_configCache = array();
    
    /**
     * Data keys
     */
    const DATA_IF        = 'if';
    const DATA_ELSE      = 'else';
    const DATA_FOREACH   = 'foreach';
    const DATA_LOG       = 'log';
    const DATA_CALL      = 'call';
    const DATA_UTILS     = 'utils';
    const DATA_ADDON     = 'addon';
    const DATA_PLUGIN    = 'plugin';
    const DATA_PROJECT   = 'project';
    const DATA_FRAMEWORK = 'tasks';
    const DATA_OPTIONS   = 'options';
    const DATA_CONFIG    = 'config';
    
    /**
     * Action keys
     */
    const ACTION_ADD    = 'add';
    const ACTION_REMOVE = 'remove';

    /**
     * Action options
     */
    const ACTION_OPT_COMMON_IF   = 'if';
    const ACTION_OPT_ADD_INDENT  = 'indent';
    const ACTION_OPT_ADD_BEFORE  = 'before';
    const ACTION_OPT_ADD_AFTER   = 'after';
    const ACTION_OPT_ADD_REPLACE = 'replace';
    
    /**
     * Addon magic methods
     */
    const METHOD_NAME_ASSERT               = 'assert';
    const METHOD_NAME_GET_PLUGINS          = 'getPlugins';
    const METHOD_NAME_GET_SCRIPTS          = 'getScripts';
    const METHOD_NAME_INIT_DRAWABLES       = 'initDrawables';
    const METHOD_NAME_INIT_CUSTOMIZER      = 'initCustomizer';
    const METHOD_NAME_ON_PLUGIN_DEPLOYMENT = 'onPluginDeployment';
    
    /**
     * Addons
     * 
     * @var Addons
     */
    protected static $_instance;
    
    /**
     * Allowed actions
     * 
     * @var string[]
     */
    public static $actions = array(
        self::ACTION_ADD,
        self::ACTION_REMOVE,
    );
    
    /**
     * Allowed data keys
     * 
     * @var string[]
     */
    protected $_dataKeys = array(
        self::DATA_IF,
        self::DATA_ELSE,
        self::DATA_FOREACH,
        self::DATA_LOG,
        self::DATA_CALL,
        self::DATA_UTILS,
        self::DATA_ADDON,
        self::DATA_PLUGIN,
        self::DATA_PROJECT,
        self::DATA_FRAMEWORK,
        self::DATA_OPTIONS,
        self::DATA_CONFIG,
    );
    
    /**
     * Current file
     *
     * @var string
     */
    protected $_from;
    
    /**
     * Data
     * 
     * @var array
     */
    protected $_data = array();
    
    /**
     * Addon options
     * 
     * @var array
     */
    protected $_addonOptions = array();
    
    /**
     * Store the deployed plugins
     * 
     * @var string[]
     */
    protected $_deployedPlugins = array();
    
    /**
     * Addons
     * 
     * @return Addons
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Class constructor
     */
    protected function __construct() {
        // Set the framework information
        $this->_data[self::DATA_FRAMEWORK] = Tasks::$config;
        
        // Get the configuration data
        $configData = Config::get(false);
        
        // Add the custom getUse argument
        $configData['getUse'] = Config::getUse();
        
        // Set the config
        $this->_data[self::DATA_CONFIG] = $configData;

        // Set the options
        $this->_data[self::DATA_OPTIONS] = Cli_Run_Integration::$options;
        
        // Populate the project information
        if (!isset($this->_data[self::DATA_PROJECT])) {
            // Prepare the NewProject introspection
            $newProjectIntrospection = new ReflectionClass('Tasks_1NewProject');

            // Get the public static properties
            $newProjectProperties = $newProjectIntrospection->getProperties();

            // Prepare the project properties
            $this->_data[self::DATA_PROJECT] = array();

            // Go through each one
            foreach ($newProjectProperties as $reflectionProperty) {
                $this->_data[self::DATA_PROJECT][$reflectionProperty->getName()] = $reflectionProperty->getValue();
            }
            
            // Store the verbose version
            $this->_data[self::DATA_PROJECT]['versionVerbose'] = Tasks_1NewProject::getVerboseVersion();
        }
    }
    
    /**
     * Get the addon paths
     * 
     * @param string $addonName Current addon name
     * @return string Addon path
     */
    public static function getAddonPath($addonName) {
        return ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName;
    }
    
    /**
     * Get the addon's title and description or an empty array on error
     * 
     * @param string $addonName Addon name
     * @return array 
     */
    public static function getMeta($addonName) {
        // Forced string
        $addonName = strval($addonName);
        
        // Get the metadata
        if (!isset(self::$_addonsMeta[$addonName])) {
            // Prepare the metadata
            $addonMeta = array();

            // Get the file
            if (is_file($goPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName . '/' . self::GO_FILE_NAME)) {
                if (preg_match('%<\?php\s*\/\*(.*?)\*\/%ims', file_get_contents($goPath), $firstComment)) {
                    if (preg_match_all('%\@\s*(title|desc)\s+(.*?(?=\*\s*\@|\*\/))%ims', $firstComment[0], $itemMatches, PREG_SET_ORDER)) {
                        foreach ($itemMatches as $itemMatch) {
                            // Get the item type and data
                            list(, $itemType, $itemData) = $itemMatch;

                            // Sanitize
                            $itemData = trim(preg_replace('%\s*[\r\n]\s*\*\s*%', ' ', $itemData));

                            // Store the data
                            switch ($itemType) {
                                case 'title':
                                    $addonMeta[0] = $itemData;
                                    break;

                                case 'desc':
                                    $addonMeta[1] = $itemData;
                                    break;
                            }
                        }
                    }
                }
            }

            // Store the metadata
            self::$_addonsMeta[$addonName] = $addonMeta;
        }

        // All done
        return self::$_addonsMeta[$addonName];
    }
    
    /**
     * Get the add-ons configuration items for the specified details
     * 
     * @param string $addonName   Add-on name
     * @param string $frameworkId Framework ID
     * @param array  $extra       Extra info
     * @param string $projectPath Project path
     * @return array of Model_Project_Config_Item[]
     */
    public static function getConfig($addonName, $extra = null, $projectPath = null) {
        // Prepare the cache key
        $cacheKey = $addonName . '-' . (null === $extra ? '' : 'x-') . $projectPath;
        
        // Cache not set
        if (!isset(self::$_configCache[$cacheKey])) {
            // Prepare the result
            self::$_configCache[$cacheKey] = array();

            do {
                // Get the addon path
                $addonPath = self::getAddonPath($addonName);

                // Not found
                if (!count(glob($addonPath . '/*'))) {
                    Log::check(Log::LEVEL_WARNING) && Log::warning('Addon "' . $addonName .'" not found for "' . $projectPath . '"');
                    break;
                }

                /*@var $configItems Model_Project_Config_Item[]*/
                $configItems = self::_getOptionsHelper($addonName, $extra);

                // Path provided
                if (null !== $projectPath) {
                    foreach ($configItems as $key => /*@var $value Model_Project_Config_Item*/$value) {
                        // Disk value
                        if ($value->isOnDisk()) {
                            $value->load($projectPath, $addonName);
                        }
                    }
                }
                
                // Store the data
                self::$_configCache[$cacheKey][$addonName] = $configItems;
            } while(false);
        }

        // All done
        return self::$_configCache[$cacheKey];
    }
    
    /**
     * Get the options array; needs access to the go file
     * 
     * @param string  $addonFolderName Current Add-On folder name
     * @param array   $extra           Extra info from Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_ADDONS]
     * @param boolean $formatted       If formatted, the result will be a string array; otherwise, a Model_Project_Config_Item object
     * @return Model_Project_Config_Item[]|string[]
     * @throws Exception
     */
    protected static function _getOptionsHelper($addonFolderName, $extra = null, $formatted = false) {
        /*@var $userConfigItems Model_Project_Config_Item[]*/
        $userConfigItems = array();

        // No extra data defined
        if (null === $extra || !is_array($extra)) {
            $extra = array();
        }
        
        // Extra information provided
        try {
            /*@var $userConfigItems Model_Project_Config_Item[]*/
            $userConfigItems = Model_Project_Config_Item::fromKeyValue($addonFolderName, $extra);
        } catch (Exception $exc) {
            Log::check(Log::LEVEL_ERROR) && Log::error($exc->getMessage(), $exc->getFile(), $exc->getLine());
        }

        // Preformatted
        if ($formatted) {
            // Prepare the data
            $extraData = array();
            
            // Prepare the extra data
            foreach ($userConfigItems as $key => /*@var $item Model_Project_Config_Item*/ $item) {
                $extraData[$key] = $item->isList() ? json_encode($item->getValue()) : $item->getValue();
            }
            
            // All done
            return $extraData;
        }

        // All done
        return $userConfigItems;
    }
    
    /**
     * Get all addon's options
     * 
     * @return array
     */
    public function getAddonOptions() {
        return $this->_addonOptions;
    }
    
    /**
     * Activate an addon by name
     * 
     * @param string $addonName Addon Name
     * @param array  $extra     (optional) Addon configuration; default <b>null</b>
     * @return boolean False if addon not found
     */
    public function activate($addonName, $extra = null) {
        // Get the addon path
        $addonPath = self::getAddonPath($addonName);
        
        // Not found
        if (!count(glob($addonPath . '/*'))) {
            Log::check(Log::LEVEL_INFO) && Log::info('Addon "' . $addonName .'" not found');
            return;
        }

        // Log this event
        Log::check(Log::LEVEL_INFO) && Log::info('Activating addon "' . $addonName . '"');
        
        // Go file found
        if (file_exists($goFilePath = $addonPath . '/' . self::GO_FILE_NAME)) {
            // Load the file
            require_once $goFilePath;

            // Compute the class name
            $className = 'Addon_' . implode('', array_map(function($item) {return ucfirst(strtolower($item));}, explode('-', $addonName)));

            // Class exists
            if (class_exists($className)) {
                // Get the class instance
                $addonInstance = new $className($addonName);

                // Register the add-on
                Addons_Listener::register($addonInstance, $addonName);
                    
                // Valid definition
                if ($addonInstance instanceof Addon) {
                    // Defined plugins
                    if (method_exists($addonInstance, self::METHOD_NAME_GET_PLUGINS)) {
                        $pluginsArray = call_user_func(array($addonInstance, self::METHOD_NAME_GET_PLUGINS));
                        if (!is_array($pluginsArray)) {
                            $pluginsArray = array();
                        }

                        // Add the paths
                        foreach ($pluginsArray as $pluginName) {
                            // Plugin-specifics found
                            if (is_file($pluginGoPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS . '/' . $pluginName . '/' . self::GO_FILE_NAME)) {
                                // Include the plugin
                                require_once $pluginGoPath;

                                // Prepare the plugin class name
                                $pluginClassName = 'Plugin_' . preg_replace('% +%', '', ucwords(preg_replace('%\W+%', ' ', $pluginName)));
                                
                                // Class found
                                if (class_exists($pluginClassName)) {
                                    // Store the plugin instance
                                    if (is_array(Addon::$pluginInstances)) {
                                        if (!isset(Addon::$pluginInstances[$pluginName])) {
                                            // Get the new instance
                                            Addon::$pluginInstances[$pluginName] = new $pluginClassName($pluginName, $addonInstance);

                                            // Validate the class inheritance
                                            if (!Addon::$pluginInstances[$pluginName] instanceof Plugin) {
                                                unset(Addon::$pluginInstances[$pluginName]);
                                            }
                                        } else {
                                            Log::check(Log::LEVEL_ERROR) && Log::error('Plugin "' . $pluginName . '" can not be attributed to add-on "' . $addonName . '", already sibling of add-on "' . get_class(Addon::$pluginInstances[$pluginName]->getParentAddon()) . '"');
                                        }
                                    }
                                }
                            }
                        }
                    }
                    
                    if ($addonInstance instanceof Addon_Core) {
                        // Get the options
                        $coreOptions = array();

                        // Ommit the individual project addons configuration
                        foreach ($this->_data[self::DATA_OPTIONS] as $optionKey => $optionValue) {
                            if (!in_array($optionKey, array(Cli_Run_Integration::OPT_PROJECT_ADDONS))) {
                                $coreOptions[$optionKey] = $optionValue;
                            }
                        }

                        // Store the options
                        $this->_addonOptions[$addonName] = $coreOptions;
                    } else {
                        // Prepare the Add-on Data
                        $addonData = self::getConfig($addonName, null, IO::inputPath());

                        // Go through the data
                        foreach ($addonData as $addonDataConfigs) {
                            // Flavor defined
                            if (isset($addonDataConfigs[Model_Project_Config_Item::KEY_FLAVOR])) {
                                if ($addonDataConfigs[Model_Project_Config_Item::KEY_FLAVOR] instanceof Model_Project_Config_Item_String) {
                                    /* @var $configItem Model_Project_Config_Item_String */
                                    $configItem = $addonDataConfigs[Model_Project_Config_Item::KEY_FLAVOR];

                                    // Prepare the flavor path
                                    $addonFlavorRulesCssPath = null;
                                    foreach (array(Addons::FLAVOR_NAME_DEFAULT, $configItem->getValue()) as $customizerFlavor) {
                                        if (is_file($addonPath . '/' . self::GO_FOLDER_NAME . '/' . $customizerFlavor . '/' . self::FLAVOR_FILE_CUSTOMIZER)) {
                                            $addonFlavorRulesCssPath = $addonPath . '/' . self::GO_FOLDER_NAME . '/' . $customizerFlavor . '/' . self::FLAVOR_FILE_CUSTOMIZER;
                                        }
                                    }
                                    
                                    // A flavor was defined
                                    if (null !== $addonFlavorRulesCssPath) {
                                        // Log the event
                                        Log::check(Log::LEVEL_INFO) && Log::info('Activating addon "' . $addonName . '"\'s "' . basename(dirname($addonFlavorRulesCssPath)) . '" customizer flavor');

                                        // Parse the CSS rules
                                        $addonInstance->wpCustomizerCssRules = $this->_parseCssRules(
                                            $this->parseDataKeys(file_get_contents($addonFlavorRulesCssPath), $addonName, null, Model_Project_Config_Item_Code::EXT_CSS)
                                        );
                                    }
                                }
                            }
                        }
                        
                        // Store the addon options
                        $this->_addonOptions[$addonName] = self::_getOptionsHelper($addonName, $extra, true);
                    }
                }
            }
        }
    }
    
    /**
     * Get the list of deployed plugins, accounting for the WPBakery Page Builder Add-Ons Bundle
     * 
     * @return string[]
     */
    public function getDeployedPlugins() {
        // Prepare the WPBakery Page Builder Add-Ons Bundle name
        $bundleName = Plugin_Bundle::getName();
        
        // Unassociative array
        return array_values(
            // Unique values
            array_unique(
                // All VC plugins are now part of the bundle
                array_map(
                    function($item) use ($bundleName) {
                        return preg_match(Plugin_Bundle::getRegEx(), $item) ? $bundleName : $item;
                    }, 
                    $this->_deployedPlugins
                )
            )
        );
    }
    
    /**
     * Deploy files to the final project destination and parse actions and data tags
     * 
     * @param string $source             Path to source addon/plugin/script
     * @param string $relativePath       Relative path in the project destination 
     * @param string $destinationProject (optional) Full path to the project destination
     * @param string $addonName          (optional) Current addon's name; default <b>Model_Project_Config::CATEGORY_CORE</b>
     * @param string $pluginName         (optional) Current plugin's name, only if working on a plugin file; default <b>null</b>
     */
    public function deployFilesHelper($source, $relativePath, $destinationProject = null, $addonName = Model_Project_Config::CATEGORY_CORE, $pluginName = null) {
        // Set the default project destination folder
        if (null === $destinationProject) {
            $destinationProject = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, Tasks_1NewProject::getPath());
        }
        
        foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
            // Prepare the new location
            $newLocation = $destinationProject . '/' . (strlen($relativePath) ? ($relativePath . '/') : '') . str_replace(array('/', '\\'), '/', $iterator->getSubPathName());

            // Dynamic source folder
            if (preg_match('%\{src\}%', $newLocation)) {
                $newLocation = rtrim($destinationProject . '/' . trim(preg_replace('%\{src\}%', '', $iterator->getSubPathName()), '\\/'), '\\/');
            }

            // Ignore the "go" folder
            if (preg_match('%^' . preg_quote($source . '/' . self::GO_FOLDER_NAME) . '\b%', strval($item))) {
                continue;
            }

            // Parse the data keys for file names
            if (preg_match('%\{\w+\.\w+\}%', $newLocation)) {
                $newLocation = $this->parseDataKeys($newLocation, $addonName, $pluginName);
            }

            // Directory
            if ($item->isDir()) {
                if (!is_dir($newLocation)) {
                    Folder::create($newLocation, 0777, true);
                }
            } else {
                // File (overwrite), Never copy Thumbs.db
                if ('Thumbs.db' !== basename($item) && false === strpos(basename($item), ' - Copy')) {
                    // Ignore the go file
                    if (in_array(basename($item), array(self::GO_FILE_NAME)) && dirname($item) === $source) {
                        // Ignore
                        continue;
                    }

                    // Parse the file
                    $this->parse(strval($item), $newLocation, $addonName, $pluginName);
                } else {
                    unlink($item);
                }
            }
        }
    }
    
    /**
     * Deploy an addon by name
     * 
     * @param string $addonName Addon Name
     * @return boolean False if addon not found
     */
    public function deploy($addonName) {
        // Get the addon path
        $addonPath = self::getAddonPath($addonName);
        
        // Not found
        if (!count(glob($addonPath . '/*'))) {
            Log::check(Log::LEVEL_INFO) && Log::info('Addon "' . $addonName .'" not found');
            return;
        }
        
        // Set the addon options
        $this->_data[self::DATA_ADDON] = isset($this->_addonOptions[$addonName]) ? $this->_addonOptions[$addonName] : array();

        // Log this event
        Log::check(Log::LEVEL_INFO) && Log::info('Deploying addon "' . $addonName . '"');

        // Sanitize the source and destination
        $sourceAddon = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $addonPath);
        $destinationProject = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, Tasks_1NewProject::getPath());

        // Perform only if the destination directory does not exist
        if (!is_dir($destinationProject)) {
            Folder::create($destinationProject, 0777, true);
        }

        // Prepare the sources list
        $sourcePaths = array(
            $sourceAddon => '',
        );
        
        // Get the add-on instance
        $addonInstance = Addons_Listener::get($addonName);
        if (null != $addonInstance) {
            // Define plugins
            if (method_exists($addonInstance, self::METHOD_NAME_GET_PLUGINS)) {
                $pluginsArray = call_user_func(array($addonInstance, self::METHOD_NAME_GET_PLUGINS));
                if (!is_array($pluginsArray)) {
                    $pluginsArray = array();
                }

                // Add the paths
                foreach ($pluginsArray as $pluginName) {
                    if (!in_array($pluginName, $this->_deployedPlugins)) {
                        $this->_deployedPlugins[] = $pluginName;
                        
                        // Prepare the plugin slug
                        $pluginSlug = $pluginName;
                        
                        // Custom Scripts - plugin level
                        $scriptsArray = array();
                        
                        // Plugin instance correctly defined
                        if (isset(Addon::$pluginInstances[$pluginName]) && Addon::$pluginInstances[$pluginName] instanceof Plugin) {
                            // Get scripts method defined
                            if (method_exists(Addon::$pluginInstances[$pluginName], self::METHOD_NAME_GET_SCRIPTS)) {
                                // Get the custom scripts for this plugin
                                $scriptsArray = Addon::$pluginInstances[$pluginName]->getScripts();

                                // Validate the developer's input
                                if (!is_array($scriptsArray)) {
                                    $scriptsArray = array();
                                }
                            }
                            
                            // Store the slug
                            $pluginSlug = Addon::$pluginInstances[$pluginName]->getSlug();
                        }
                        
                        // Mark the plugin for transfer
                        $sourcePaths[ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS . '/' . $pluginName] = Framework::FOLDER_PLUGINS . '/' . $pluginSlug; 
                        
                        // Transfer the scripts before any other task
                        foreach ($scriptsArray as $scriptName) {
                            foreach (array(Plugin::FOLDER_JS, Plugin::FOLDER_CSS) as $scriptType) {
                                if (is_file($mainFilePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCRIPTS . '/' . $scriptName . '/' . $scriptType . '/' . Plugin::FILE_MAIN . '.' . $scriptType)) {
                                    // Prevent duplicate deployments
                                    if (!is_dir($destinationProject . '/' . Framework::FOLDER_PLUGINS . '/' . $pluginSlug . '/' . $scriptType . '/' . $scriptName)) {
                                        $this->deployFilesHelper(
                                            dirname($mainFilePath), 
                                            Framework::FOLDER_PLUGINS . '/' . $pluginSlug . '/' . $scriptType . '/' . $scriptName,
                                            $destinationProject,
                                            $addonName,
                                            $pluginName
                                        );
                                    }
                                }
                            }

                            // Copy the Plugin::FOLDER_IMG folder, if available
                            if (is_dir($mainImgPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCRIPTS . '/' . $scriptName . '/' . Plugin::FOLDER_IMG)) {
                                if (!is_dir($destinationProject . '/' . Framework::FOLDER_PLUGINS . '/' . $pluginSlug . '/' . Plugin::FOLDER_IMG . '/' . $scriptName)) {
                                    $this->deployFilesHelper(
                                        $mainImgPath, 
                                        Framework::FOLDER_PLUGINS . '/' . $pluginSlug . '/' . Plugin::FOLDER_IMG . '/' . $scriptName,
                                        $destinationProject,
                                        $addonName,
                                        $pluginName
                                    );
                                }
                            }
                        }
                    }
                }
            }
            
            // Custom Scripts - theme level
            if (method_exists($addonInstance, self::METHOD_NAME_GET_SCRIPTS)) {
                $scriptsArray = call_user_func(array($addonInstance, self::METHOD_NAME_GET_SCRIPTS));
                if (is_array($scriptsArray)) {
                    // Add the paths
                    foreach ($scriptsArray as $scriptName) {
                        foreach (array(Plugin::FOLDER_JS, Plugin::FOLDER_CSS) as $scriptType) {
                            if (is_file($mainFilePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCRIPTS . '/' . $scriptName . '/' . $scriptType . '/' . Plugin::FILE_MAIN . '.' . $scriptType)) {
                                // Prevent duplicate deployments
                                if (!is_dir($destinationProject . '/' . $scriptType . '/' . $scriptName)) {
                                    $sourcePaths[dirname($mainFilePath)] = $scriptType . '/' . $scriptName;
                                }
                            }
                        }
                
                        // Copy the Plugin::FOLDER_IMG folder, if available
                        if (is_dir($mainImgPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCRIPTS . '/' . $scriptName . '/' . Plugin::FOLDER_IMG)) {
                            if (!is_dir($destinationProject . '/' . Plugin::FOLDER_IMG . '/' . $scriptName)) {
                                $sourcePaths[$mainImgPath] = Plugin::FOLDER_IMG . '/' . $scriptName;
                            }
                        }
                    }
                }
            }
            
            // Define WP Customizer functionality once
            if ($addonInstance instanceof Addon && method_exists($addonInstance, self::METHOD_NAME_INIT_CUSTOMIZER) && null == $addonInstance->wpCustomizer) {
                // Set the property
                $addonInstance->wpCustomizer = new WordPress_Customizer($addonInstance);
                
                // Execute the method
                call_user_func(array($addonInstance, self::METHOD_NAME_INIT_CUSTOMIZER));
            }
        }

        // Deploy the add-on and corresponding plugins
        foreach ($sourcePaths as $source => $relativePath) {
            // Prepare the current plugin's name (if working on a plugin)
            $pluginName = null;
            
            // Plugins need to be converted to archives
            if (Framework::FOLDER_PLUGINS === basename(dirname($source))) {
                // Get the plugin's name
                $pluginName = basename($source);
            }
            
            // Deploy the files
            $this->deployFilesHelper(
                $source, 
                $relativePath, 
                $destinationProject,
                $addonName,
                $pluginName
            );
            
            // Worked on a plugin
            if (null !== $pluginName) {
                // Get the plugin instance
                if (isset(Addon::$pluginInstances[$pluginName]) && Addon::$pluginInstances[$pluginName] instanceof Plugin) {
                    // Plugin deployment actions
                    Addon::$pluginInstances[$pluginName]
                        ->getParentAddon()
                        ->onPluginDeployment(
                            Drawables_Plugin_Common::getInstance(
                                Addon::$pluginInstances[$pluginName], 
                                $destinationProject . '/' . $relativePath
                            )
                        );
                }
                
                // Archive the plugin (except for VC plugins, which will be dealt with later)
                if (!preg_match(Plugin_Bundle::getRegEx(), $pluginName)) {
                    Zip::packNative($destinationProject . '/' . $relativePath, 'tar');
                }
            }
        }
        
        // Prepare the Add-on Data
        $addonData = self::getConfig($addonName, true, IO::inputPath());

        // Go through the data
        foreach ($addonData as $addonDataName => $addonDataConfigs) {
            // Prepare the CSS data
            $cssData = array();
            
            // Prepare the JS data
            $jsData = array();
            
            // Core addon
            if (Model_Project_Config::CATEGORY_CORE === $addonDataName) {
                if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_UI_SET])) {
                    // Store the UI Set CSS
                    if (is_file($uiSetItemPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_UI_SET] . '.css')) {
                        $cssData['Core UI'] = file_get_contents($uiSetItemPath);
                    }
                }
            }
            
            // Flavor defined
            if (isset($addonDataConfigs[Model_Project_Config_Item::KEY_FLAVOR]) && $addonDataConfigs[Model_Project_Config_Item::KEY_FLAVOR] instanceof Model_Project_Config_Item_String) {
                /*@var $configItem Model_Project_Config_Item_String*/
                $configItem = $addonDataConfigs[Model_Project_Config_Item::KEY_FLAVOR];

                // Log the event
                Log::check(Log::LEVEL_INFO) && Log::info('Deploying addon "' . $addonName . '"\'s "' . $configItem->getValue() . '" flavor');
                
                // Style.css file found, as a replacement for the default flavor's style.css
                if (is_file($addonFlavorCssPath = $addonPath . '/' . self::GO_FOLDER_NAME . '/' . $configItem->getValue() . '/' . self::FLAVOR_FILE_STYLE)) {
                    // Store the style.css file
                    $cssData[ucfirst($configItem->getValue()) . ' flavor'] = file_get_contents($addonFlavorCssPath);
                } else {
                    if (is_file($addonFlavorDefaultCssPath = $addonPath . '/' . self::GO_FOLDER_NAME . '/' . self::FLAVOR_NAME_DEFAULT . '/' . self::FLAVOR_FILE_STYLE)) {
                        $cssData[ucfirst($configItem->getValue()) . ' flavor'] = file_get_contents($addonFlavorDefaultCssPath);
                    }
                }

                // Extra style.css, appended after the default flavor's style.css
                if (is_file($addonFlavorExtraCssPath = $addonPath . '/' . self::GO_FOLDER_NAME . '/' . self::FLAVOR_NAME_DEFAULT . '/' . self::FLAVOR_FILE_STYLE_EXTRA)) {
                    $cssData[ucfirst($configItem->getValue()) . ' flavor - extra'] = file_get_contents($addonFlavorExtraCssPath);
                }

                // Functions.js file found, as a replacement for the default flavor's functions.js
                if (is_file($addonFlavorJsPath = $addonPath . '/' . self::GO_FOLDER_NAME . '/' . $configItem->getValue() . '/' . self::FLAVOR_FILE_FUNCTIONS)) {
                    // Store the JS data
                    $jsData[] = file_get_contents($addonFlavorJsPath);
                } else {
                    if(is_file($addonFlavorDefaultJsPath = $addonPath . '/' . self::GO_FOLDER_NAME . '/' . self::FLAVOR_NAME_DEFAULT . '/' . self::FLAVOR_FILE_FUNCTIONS)) {
                        $jsData[] = file_get_contents($addonFlavorDefaultJsPath);
                    }
                }

                // Extra functions.js, appended after the default flavor's function.js
                if(is_file($addonFlavorExtraJsPath = $addonPath . '/' . self::GO_FOLDER_NAME . '/' . $configItem->getValue() . '/' . self::FLAVOR_FILE_FUNCTIONS_EXTRA)) {
                    $jsData[] = file_get_contents($addonFlavorExtraJsPath);
                }
            }

            // Get the addon's CSS config items
            foreach ($addonDataConfigs as /*@var $configItem Model_Project_Config_Item*/ $configItem) {
                // Code element, except for "css-inline", which is used exclusively for Core Custom Colors PHP code injection with <1.origina.toRgb> syntax support
                if (Model_Project_Config_Item_Code::ITEM_TYPE == $configItem->getType() && Model_Project_Config_Item::KEY_CSS_INLINE !== $configItem->getKey()) {
                    if (strlen($configItem->getValue())) {
                        // Prepare the key
                        $scriptDataKey = (strlen($configItem->getMetaTitle()) ? $configItem->getMetaTitle() : ucfirst(str_replace(array('-', '_'), ' ', $configItem->getKey()))) . ' tweaks';
                        switch ($configItem->getExtension()) {
                            case Model_Project_Config_Item_Code::EXT_CSS:
                                $cssData[$scriptDataKey] = $configItem->getValue();
                                break;

                            case Model_Project_Config_Item_Code::EXT_JS:
                                $jsData[$scriptDataKey] = $configItem->getValue();
                                break;
                        }
                    }
                }
            }
            
            // CSS Tweaks
            if (count($cssData)) {
                // Set the main header
                WordPress_Style::getInstance()->addExtraRule('', ucfirst(str_replace(array('-', '_'), ' ', $addonDataName)), 1);

                // Add the rules
                foreach ($cssData as $scriptDataKey => $cssDataValue) {
                    WordPress_Style::getInstance()->addExtraRule(
                        $this->parseDataKeys($cssDataValue, $addonName, null, Model_Project_Config_Item_Code::EXT_CSS), 
                        $this->parseDataKeys($scriptDataKey, $addonName, null, Model_Project_Config_Item_Code::EXT_CSS), 
                        2
                    );
                }
            }
            
            // JS Tweaks
            if (count($jsData)) {
                // Found the destination functions.js file
                if (is_file($finalJsPath = Tasks_1NewProject::getPath() . '/js/' . self::FLAVOR_FILE_FUNCTIONS)) {
            
                // Prepare the code to insert
                $jsCodeToInsert = <<<'JS'
    // __ADDON__ worker
    {project.prefix}_instance.addWorker("__ADDON__", function(addonName, _this) {
__CODE__
    });
JS;
                    // Prepare the indented code, fixing Windows-style line endings
                    $indentedCode = $this->parseDataKeys(
                        str_replace(
                            "\r", 
                            "", 
                            preg_replace(
                                '%^%ims', 
                                '${0}' . str_repeat(' ', 4), 
                                implode(
                                    PHP_EOL . PHP_EOL, 
                                    $jsData
                                )
                            )
                        ),
                        $addonName,
                        null,
                        Model_Project_Config_Item_Code::EXT_JS
                    );

                    // Valid code to insert
                    if (strlen(trim($indentedCode))) {
                        // Append the parsed content to the final functions.js file
                        file_put_contents(
                            $finalJsPath, 
                            preg_replace(
                                // Right after // #Workers#
                                '%\/\/\s*\#Workers\#[ \t]*%ms', 
                                '${0}' . PHP_EOL . '    ' . PHP_EOL . '    ' . 
                                $this->parseDataKeys(
                                    str_replace(
                                        array(
                                            '__CODE__',
                                            '__ADDON__',
                                        ), 
                                        array(
                                            $indentedCode,
                                            $addonName,
                                        ), 
                                        trim($jsCodeToInsert)
                                    ), 
                                    $addonName,
                                    null,
                                    Model_Project_Config_Item_Code::EXT_JS
                                ), 
                                file_get_contents($finalJsPath)
                            )
                        );
                    }
                }
            }
        }
        
        // Custom Drawables actions
        if ($addonInstance instanceof Addon && method_exists($addonInstance, self::METHOD_NAME_INIT_DRAWABLES)) {
            // Execute the method
            call_user_func(array($addonInstance, self::METHOD_NAME_INIT_DRAWABLES));
        }
        
        // Append to the docs
        if ($addonInstance instanceof Addon) {
            WordPress_Docs::getInstance()->appendAddon($addonName, $addonData);
        }
    }
    
    /**
     * Parse an addon field
     * 
     * @param string $from       Addon file path
     * @param string $to         Final destionation path
     * @param string $addonName  Addon name
     * @param string $pluginName (optional) Plugin name - only if working on a plugin
     * @return string This function writes to $to but also returns the result as a string
     * @throws Exception
     */
    public function parse($from, $to, $addonName, $pluginName = null) {
        // Create the destination
        if (!is_dir(dirname($to))) {
            Folder::create(dirname($to), 0777, true);
        }

        // Modify a few files
        if (!preg_match('%\.(php|phtml|html|xhtml|css|js|json|cfg|txt)$%i', $from)) {
            copy($from, $to);
            return;
        }
        
        // Store the original file for reference
        $this->_from = $from;
        
        // Parse the data keys and the actions
        $result = $this->_parseActions(
            $this->parseDataKeys(
                file_get_contents($from), 
                $addonName,
                $pluginName,
                preg_replace('%^.*?\.(\w+)$%', '${1}', basename($from))
            ), 
            file_exists($to) ? file_get_contents($to) : null
        );

        // Save the text
        file_put_contents($to, $result);
        
        // All done
        return $result;
    }
    
    /**
     * Parse the actions
     * 
     * @param string $from Actions file contents
     * @param string $to   Destination file contents
     * @return string Modified destination file contents
     * @throws Exception
     */
    protected function _parseActions($from, $to) {
        // Prepare the actions
        $actions = array();
        
        // To file not created
        $destinationMissing = null === $to;
        
        // Replace the contents
        $from = trim(preg_replace_callback(
            '%\{(' . implode('|', self::$actions) . ')([^\}]*?)\}(.*?)\{\s*\/\s*\1\s*\}%ims', 
            function($item) use (&$actions){
                // Prepare the data
                list(, $action, $extraString, $content) = $item;

                // Prepare the action arguments
                $arguments = array(
                    $content,
                );
                
                // Parse the extra string
                $extraStringParts = array_filter(array_map('trim', explode(' ', $extraString)));

                // Valid parts
                if (count($extraStringParts)) {
                    foreach ($extraStringParts as $extraStringPart) {
                        // Key-value situation
                        if (false != $eqPosition = strpos($extraStringPart, '=')) {
                            // Get the first part
                            $firstPart = trim(substr($extraStringPart, 0, $eqPosition));
                            
                            // And the last
                            $lastPart = trim(substr($extraStringPart, $eqPosition + 1));
                            
                            // Something defined
                            if (!empty($firstPart) && !empty($lastPart)) {
                                // Store the argument, binding the rest of the string
                                $arguments[$firstPart] = trim(preg_replace('%(^["\']|["\']$)%', '', $lastPart));
                            }
                        }
                    }
                }
                
                // Append the action as type, args
                $actions[] = array(
                    $action, 
                    $arguments
                );
                
                // Remove the actual tag
                return '';
            }, 
            $from
        ));

        // Actions performed flag
        $actionsPerformed = false;
            
        // Prepare the method name
        foreach ($actions as $actionDetails) {
            // Get the details
            list($actionType, $options) = $actionDetails;
            
            // Get the method name
            $methodName = '_action' . ucfirst(strtolower($actionType));

            // Verify method exists
            if (method_exists($this, $methodName)) {
                // Set the flag
                $actionsPerformed = true;

                // Get the content
                $content = array_shift($options);

                // Asserted action
                if (isset($options[self::ACTION_OPT_COMMON_IF])) {
                    // Get the extra items
                    $tree = array_filter(array_map('trim', explode('.', $options[self::ACTION_OPT_COMMON_IF])));

                    // Valid definition
                    if (count($tree) >= 1) {
                        // Set the addon listener as a worker
                        $workerInstance = Addons_Listener::get(array_shift($tree));
                        
                        // Assert method defined
                        if (null !== $workerInstance) {
                            // Just checking that the Add-On is enabled
                            if (!count($tree)) {
                                // Call the method
                                $this->$methodName($to, $destinationMissing, $content, $options);
                            } else {
                                // Validation succeeded
                                if (method_exists($workerInstance, self::METHOD_NAME_ASSERT)) {
                                    if (call_user_func_array(array($workerInstance, self::METHOD_NAME_ASSERT), $tree)) {
                                        // Call the method
                                        $this->$methodName($to, $destinationMissing, $content, $options);
                                    }
                                }
                            }
                        }
                    }
                } else {
                    // Call the method every time
                    $this->$methodName($to, $destinationMissing, $content, $options);
                }
            }
        }
        
        // If no actions were performed, simply replace the contents
        $to = $actionsPerformed ? ($to . $from) : $from;
        
        // All done
        return $to;
    }
    
    /**
     * Add text in strategic locations
     * 
     * @param string  $destinationText    Destination text
     * @param boolean $destinationMissing Destination file missing
     * @param string  $content            Content to insert
     * @param array   $options            Options. "before", "after" or "replace" keys with full RegEx strings supported; optionally, "indent"
     * @return null
     * @throws Exception
     */
    protected function _actionAdd(&$destinationText, $destinationMissing, $content, $options = array()) {
        // No destination
        if ($destinationMissing) {
            // Simply append the text
            $destinationText .= $content;
            
            // All done
            return;
        }
        
        // No RegEx
        if (!isset($options[self::ACTION_OPT_ADD_BEFORE]) && !isset($options[self::ACTION_OPT_ADD_AFTER]) && !isset($options[self::ACTION_OPT_ADD_REPLACE])) {
            // Simply append the text
            $destinationText .= $content;
            
            // All done
            return;
        }
        
        // Indentation
        $indentString = '';
        if (isset($options[self::ACTION_OPT_ADD_INDENT])) {
            // Prepare the indent option
            $indent = intval($options[self::ACTION_OPT_ADD_INDENT]);
            
            // Indent is allowed between 0 and 100
            $indent = $indent < 0 ? 0 : ($indent > 100 ? 100 : $indent);
            
            // Prepare the indentation string
            if ($indent > 0) {
                $indentString = str_repeat(' ', 4 * $indent);
            }
        }
        
        // Implemented options
        foreach (array(self::ACTION_OPT_ADD_BEFORE, self::ACTION_OPT_ADD_AFTER, self::ACTION_OPT_ADD_REPLACE) as $optionKey) {
            // Value set
            if (isset($options[$optionKey]) && !empty($options[$optionKey])) {
                // Get the number of replacements made
                $replacementsMade = 0;

                // Append or prepend the content
                $destinationText = preg_replace_callback(
                    '%' . $options[$optionKey] . '%ms', 
                    function($item) use ($optionKey, $content, $indentString){
                        // Indent the content
                        if (strlen($indentString)) {
                            $content = $this->_indent($content, $indentString);
                        }
                        
                        // Prepend
                        if (self::ACTION_OPT_ADD_BEFORE == $optionKey) {
                            return $content . $item[0];
                        }
                        
                        // Append
                        if (self::ACTION_OPT_ADD_AFTER == $optionKey) {
                            return $item[0] . $content;
                        }
                        
                        // Replace
                        return $content;
                    }, 
                    $destinationText,
                    -1,
                    $replacementsMade
                );

                // Nothing found
                if (0 == $replacementsMade && !in_array($options[$optionKey], array('\?\?\?'))) {
                    throw new Exception('RegEx failed for ' . $this->_from . ' ADD:' . $optionKey . '="' . $options[$optionKey] . '"');
                }
            }
        }
    }
    
    /**
     * Remove text from strategic locations
     * 
     * @param string  $destinationText    Destination text
     * @param boolean $destinationMissing Destination file missing
     * @param string  $regEx              Full RegEx string
     * @return null
     * @throws Exception
     */
    protected function _actionRemove(&$destinationText, $destinationMissing, $regEx) {
        // No destination file
        if ($destinationMissing) {
            // Nothing to do
            return;
        }
        
        // Get the number of replacements made
        $replacementsMade = 0;
                
        // Change the text
        $destinationText = preg_replace(
            '%' . $regEx . '%ms', 
            '', 
            $destinationText,
            -1,
            $replacementsMade
        );
        
        // Nothing found
        if (0 == $replacementsMade) {
            throw new Exception('RegEx failed for ' . $this->_from . ' REMOVE:' . $optionKey . '="' . $options[$optionKey] . '"');
        }
    }
    
    /**
     * Parse a keyword structure; can traverse arrays, object properties and object methods - with no arguments
     * 
     * @example ("@value.a.b", {"a" => {"b" => "c"}}) = "c"
     * 
     * @param string $tree           Literal tree
     * @param mixed  $structuredData Structured data to traverse
     * @param string $fileExtension  (optional) File extension
     * @return mixed
     */
    protected function _parseDataKeysInternal($tree, $structuredData, $fileExtension = null) {
        // Prepare the tree
        $treeArray = array_filter(array_map('trim', explode('.', $tree)));
        
        // Get the keyword
        $keyword = array_shift($treeArray);
        
        // Not defined
        if (null === $keyword || !strlen($keyword)) {
            return null;
        }
        
        // Remove the @ prefix
        $keyword = preg_replace('%^@+%', '', $keyword);
        
        // Prepare the result
        $result = $structuredData;
        
        // Go through the rules
        foreach ($treeArray as $branch) {
            if (is_array($result) && isset($result[$branch])) {
                // A valid array key
                $result = $result[$branch];
            } elseif(is_object($result) && property_exists($result, $branch)) {
                // A valid property
                $result = $result->$branch;
            } elseif(is_object($result) && method_exists($result, $branch)) {
                // A valid method name
                $result = $result->$branch();
            } else {
                // Invalid tree, ignore the rest of the branches
                break;
            }
        }
        
        // Serialization
        if(strtolower($keyword[0]) == $keyword[0]) {
            // Prepare the escaped (serialized) values
            $resultSerialized = $result; 

            // Escape according to the current file extension
            switch ($fileExtension) {
                case 'php':
                case 'phtml':
                    $resultSerialized = var_export($result, true);
                    break;

                case Model_Project_Config_Item_Code::EXT_JS:
                case Model_Project_Config_Item_Code::EXT_JSON:
                    $resultSerialized = json_encode($result);
                    break;
            }
            
            // Output the serialized result
            return $resultSerialized;
        }

        // All done
        return $result;
    }
    
    /**
     * Parse the WP Customizer files to associative arrays of rule name => CSS value
     * 
     * @see Addons::self::FLAVOR_FILE_CUSTOMIZER
     * @param string $cssRulesString
     * @return array
     */
    protected function _parseCssRules($cssRulesString) {
        // Prepare the result
        $rules = json_decode('{}');
        
        // Go through the rules
        if (preg_match_all('%\/\*(.*?)\*\/(.*?)(?=\/\*|\z)%ims', $cssRulesString, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $matchData) {
                // Clean-up
                $matchData = array_map('trim', $matchData);
                
                // Get the values
                list(, $ruleName, $ruleData) = $matchData;
                
                // Get the actual keys
                $ruleKeys = array_filter(array_map('trim', explode('.', $ruleName)));

                // Go through the keys
                if (count($ruleKeys)) {
                    // Reset the pointer
                    $rulesClone = $rules;
                    
                    // Go down the tree
                    foreach ($ruleKeys as $ruleKeyIndex => $ruleKey) {
                        // Not a valid key
                        if (!preg_match('%^[\w\-]+$%', $ruleKey)) {
                            continue;
                        }
                        
                        // Property not yet defined
                        if (!property_exists($rulesClone, $ruleKey)) {
                            $rulesClone->$ruleKey = json_decode('{}');
                        }
                        
                        // Final step, store the data
                        if ($ruleKeyIndex == count($ruleKeys) - 1) {
                            $rulesClone->$ruleKey = $ruleData;
                        } else {
                            // Move to the next level
                            $rulesClone = $rulesClone->$ruleKey;
                        }
                    }
                }
            }
        }
        
        // Convert the rules object to an associative array
        return json_decode(json_encode($rules), true);
    }
    
    /**
     * Automatically re-indent text based on the current pointer
     * 
     * @param string $text       Text to re-indent
     * @param string $lineIndent Indent spaces (usually multiples of 4)
     * @return string Re-indented text
     */
    protected function _indent($text, $lineIndent = '') {
        // No indentation necessary
        if (!strlen($lineIndent)) {
            return $text;
        }
        
        // Re-indent each line
        return preg_replace('%^%m', '${0}' . $lineIndent, $text);
    }
    
    /**
     * Parse the data keys
     * 
     * @param string  $fileContents  File contents
     * @param string  $addonName     Add-on name
     * @param string  $pluginName    (optional) Current plugin's name; default <b>null</b>
     * @param string  $fileExtension (optional) File extension; default <b>null</b>
     * @param boolean $logTagForced  (optional) Forcefully keep the <b>{log}<b> tags; default <b>false</b>
     * @return string
     */
    public function parseDataKeys($fileContents, $addonName, $pluginName = null, $fileExtension = null, $logTagForced = false) {
        // Get the data keys
        $dataKeys = $this->_dataKeys;
        
        // Get the actual data
        $data = $this->_data;
        
        // Override the addon info
        $data[self::DATA_ADDON] = isset($this->_addonOptions[$addonName]) ? $this->_addonOptions[$addonName] : array();
        
        // Plugin area
        if (is_string($pluginName) && isset(Addon::$pluginInstances[$pluginName])) {
            $data[self::DATA_PLUGIN] = Addon::$pluginInstances[$pluginName];
        }

        // Implement recursion
        do {
            // Replace the contents
            $newFileContents = preg_replace_callback(
                '%(?P<indent> *)\{(?P<tree>[\w\.\-]+)\s*(?P<extra>[\w\.\-\=\"\'\s]+)?\}(?:(?P<content>.*?)\{\s*\/\2\s*\})?%ims', 
                function($item) use ($dataKeys, $data, $fileExtension, $addonName, $pluginName, $logTagForced) {
                    // Get the extra items
                    $tree = array_filter(array_map('trim', explode('.', $item['tree'])));

                    // Get the data point
                    $dataPoint = array_shift($tree);

                    // Set the escaping
                    $dataPointForcedUnescape = (strtoupper($dataPoint[0]) == $dataPoint[0]);
                    
                    // Lowercase
                    $dataPoint = strtolower($dataPoint);
                    
                    // Not a data key
                    if (!in_array($dataPoint, $dataKeys)) {
                        return $item[0];
                    }
                    
                    // Content
                    $dataContent = isset($item['content']) ? $item['content'] : '';

                    // Dynamic result
                    if (in_array($dataPoint, array(self::DATA_CALL, self::DATA_LOG, self::DATA_FOREACH, self::DATA_UTILS, self::DATA_IF, self::DATA_ELSE))) {
                        // Valid definition
                        if (count($tree) >= 1) {
                            // Get the object
                            $workerInstance = null;
                            $methodName = null;
                            switch ($dataPoint) {
                                // log.LEVEL
                                case self::DATA_LOG:
                                    do {
                                        // Non-PHP file or Production mode, remove all logs
                                        if ('php' !== $fileExtension || AppMode::equals(AppMode::PRODUCTION)) {
                                            break;
                                        }

                                        // Get the log level
                                        $logLevel = strtolower(array_shift($tree));

                                        // Set the default log level
                                        if (!in_array($logLevel, Log::$priorities)) {
                                            $logLevel = Log::LEVEL_DEBUG;
                                        }

                                        // Forced logging or in stage mode
                                        if ($logTagForced || Tasks::isStaging()) {
                                            // Prepare the log variables
                                            $logPathExported = var_export(ROOT . '/web/log/log.txt', true);
                                            $logLevelExported = var_export(strtoupper($logLevel), true);
                                            $logUserIdExported = var_export('U' . WordPress_Session::getInstance()->getUserId(), true);
                                            $logProjectIdExported = var_export('P' . WordPress_Session::getInstance()->getProjectId(), true);
                                            $dataContent = trim($dataContent);
                                            
                                            // Prepare the file and line placeholders
                                            $logArgFile = '__FILE__';
                                            $logArgLine = '__LINE__';
                                            
                                            // Parent mode?
                                            switch (array_shift($tree)) {
                                                case 'parent':
                                                    $logArgFile = 'debug_backtrace(false, 3)[2][\'file\']';
                                                    $logArgLine = 'debug_backtrace(false, 3)[2][\'line\']';
                                                    break;
                                                
                                                case 'grandpa':
                                                    $logArgFile = 'debug_backtrace(false, 4)[3][\'file\']';
                                                    $logArgLine = 'debug_backtrace(false, 4)[3][\'line\']';
                                                    break;
                                            }
                                            
                                            // Prepare the code
                                            $logCode = <<<"LOG"
// @TW-LOG
@file_put_contents(
    $logPathExported, 
    preg_replace_callback(
        '%^.*%m', 
        function(\$item) {
            return ' ' . implode(
                ' | ', 
                array(
                    '[WordPress]', 
                    $logUserIdExported, 
                    $logProjectIdExported, 
                    'S-', 
                    str_pad(getmypid(), 6), 
                    date('d.m H:i:s'), 
                    str_pad(basename($logArgFile, '.php'), 16), 
                    str_pad($logArgLine, 5), 
                    str_pad($logLevelExported, 7, ' ', STR_PAD_RIGHT),
                    preg_replace("%(?:^'|'$)%", '', \$item[0])
                )
            );
        }, 
        var_export(
            $dataContent, 
            true
        )
    ) . PHP_EOL, 
    FILE_APPEND
);

LOG;
                                            // Indent the code
                                            return $this->_indent($logCode, $item['indent']);
                                        }
                                        
                                    } while(false);
                                    
                                    // Remove the logs
                                    return $item['indent'];
                                    break;
                                
                                // foreach.ADDON.METHODNAME
                                case self::DATA_FOREACH:
                                    // Get the addon name
                                    $foreachAddonName = array_shift($tree);

                                    // Get the method name
                                    $methodName = array_shift($tree);

                                    // Set the addon listener as a worker
                                    $workerInstance = Addons_Listener::get($foreachAddonName);

                                    // Valid listener
                                    if (null != $workerInstance && null !== $methodName && method_exists($workerInstance, $methodName)) {
                                        try {
                                            // Prepare the result
                                            $callResult = call_user_func_array(
                                                array(
                                                    $workerInstance, 
                                                    $methodName
                                                ), 
                                                $tree
                                            );

                                            // An array
                                            if (is_array($callResult)) {
                                                // Prepare the new content
                                                $newContentArray = array();

                                                // Get the default aliases
                                                $aliasKey = 'key';
                                                $aliasValue = 'value';
                                                
                                                // Custom aliases provided
                                                if (preg_match('%\bas\s*=\s*\"(\w+)\.(\w+)\"%', $item['extra'], $aliasMatches)) {
                                                    list(, $aliasKey, $aliasValue) = array_map('strtolower', $aliasMatches);
                                                }

                                                // Go through the array
                                                foreach ($callResult as $callResultKey => $callResultValue) {
                                                    // Prepare the list of templates
                                                    $newContentArray[] = preg_replace_callback(
                                                        '%\{\@((' . $aliasKey . '|' . $aliasValue . ')[\w\.\-]*?)\}%ims', 
                                                        function($itemForeach) use ($aliasKey, $callResultKey, $callResultValue, $fileExtension) {
                                                            return $this->_parseDataKeysInternal($itemForeach[1], $aliasKey == strtolower($itemForeach[2]) ? $callResultKey : $callResultValue, $fileExtension);
                                                        }, 
                                                        $dataContent
                                                    );
                                                }

                                                // All done
                                                return $this->_indent(implode('', $newContentArray), $item['indent']);
                                            }
                                        } catch (Exception $ex) {
                                            // Nothing to do
                                            Log::check(Log::LEVEL_ERROR) && Log::error($ex->getMessage(), $ex->getFile(), $ex->getLine());
                                        }
                                    }

                                    // ForEach statement failed
                                    return '';
                                    break;

                                // assert.ADDON.TESTNAME
                                case self::DATA_IF:
                                case self::DATA_ELSE:
                                    // Get the addon name
                                    $assertAddonName = array_shift($tree);

                                    // Set the addon listener as a worker
                                    $workerInstance = Addons_Listener::get($assertAddonName);
                                    
                                    // Check that the Add-On was activated
                                    if (!count($tree)) {
                                        if (($dataPoint == self::DATA_IF && null !== $workerInstance) || ($dataPoint == self::DATA_ELSE && null === $workerInstance)) {
                                            return $this->_indent($dataContent, $item['indent']);
                                        }
                                    } else {
                                        // Assert method defined
                                        if (null !== $workerInstance) {
                                            if (method_exists($workerInstance, self::METHOD_NAME_ASSERT)) {
                                                try {
                                                    // Get the result
                                                    $workerInstanceResult = call_user_func_array(array($workerInstance, self::METHOD_NAME_ASSERT), $tree);

                                                    // Validation succeeded
                                                    if (($dataPoint == self::DATA_IF && $workerInstanceResult) || ($dataPoint == self::DATA_ELSE && !$workerInstanceResult)) {
                                                        return $this->_indent($dataContent, $item['indent']);
                                                    }
                                                } catch (Exception $ex) {
                                                    // Nothing to do
                                                    Log::check(Log::LEVEL_ERROR) && Log::error($ex->getMessage(), $ex->getFile(), $ex->getLine());
                                                }
                                            }
                                        }
                                    }

                                    // Validation failed
                                    return '';
                                    break;

                                // call.ADDON.METHOD
                                case self::DATA_CALL:
                                    // Get the addon name
                                    $callAddonName = array_shift($tree);

                                    // Get the method name
                                    $methodName = array_shift($tree);

                                    // Set the addon listener as a worker
                                    $workerInstance = Addons_Listener::get($callAddonName);
                                    break;

                                // utils.METHOD
                                case self::DATA_UTILS:
                                    // Get the method name
                                    $methodName = array_shift($tree);

                                    // Set the utils as a worker
                                    $workerInstance = Addons_Utils::getInstance($addonName, $pluginName, $dataContent);
                                    break;
                            }

                            // Valid listener
                            if (null != $workerInstance && null !== $methodName && method_exists($workerInstance, $methodName)) {
                                try {
                                    // Prepare the result
                                    $callResult = call_user_func_array(
                                        array(
                                            $workerInstance, 
                                            $methodName
                                        ), 
                                        $tree
                                    );

                                    // Forced non-escaping
                                    if ($dataPointForcedUnescape) {
                                        return $this->_indent($callResult, $item['indent']);
                                    }
                                    
                                    // Do not sanitize the output of these methods
                                    if (property_exists($workerInstance, 'safeMethods')) {
                                        if (in_array($methodName, $workerInstance::$safeMethods) || in_array($methodName, Addon::$safeMethods)) {
                                            // No need to escape the result
                                            return $this->_indent($callResult, $item['indent']);
                                        }
                                    }

                                    switch ($fileExtension) {
                                        case 'php':
                                        case 'phtml':
                                            return $this->_indent(var_export($callResult, true), $item['indent']);
                                            break;

                                        case Model_Project_Config_Item_Code::EXT_JS:
                                        case Model_Project_Config_Item_Code::EXT_JSON:
                                            return $this->_indent(json_encode($callResult), $item['indent']);
                                            break;
                                    }

                                    // Get the result
                                    return $this->_indent($callResult, $item['indent']);

                                } catch (Exception $ex) {
                                    // Nothing to do
                                    Log::check(Log::LEVEL_ERROR) && Log::error($ex->getMessage(), $ex->getFile(), $ex->getLine());
                                }
                            }
                        }

                        // Inform the user
                        Console::p('[Addon] Invalid call/utils: ' . $item['tree'], false);

                        // Could not find definition
                        return $item[0];
                    }

                    // Prepare the final data
                    $finalData = isset($data[$dataPoint]) ? $data[$dataPoint] : null;

                    // Go through the rest of the tree; supports traversing object properties and methods
                    if (count($tree)) {
                        foreach ($tree as $branch) {
                            if (is_array($finalData) && isset($finalData[$branch])) {
                                // A valid array key
                                $finalData = $finalData[$branch];
                            } elseif (is_object($finalData) && property_exists($finalData, $branch)) {
                                // A valid property
                                $finalData = $finalData->$branch;
                            } elseif (is_object($finalData) && method_exists($finalData, $branch)) {
                                // A valid method name
                                $finalData = $finalData->$branch();
                            } else {
                                Console::p('[Addon] Key not found: ' . $item['tree'], false);
                                return $item[0];
                            }
                        }
                    }

                    // Return the final data
                    if (is_array($finalData) || is_object($finalData)) {
                        Console::p('[Addon] Key is object/array: ' . $item['tree'], false);
                        Log::check(Log::LEVEL_DEBUG) && Log::debug('[Addon] Available keys for "' . $item['tree'] . '": ' . implode(', ', array_keys((array) $finalData)));
                        return $item[0];
                    }

                    // Prevent code injections
                    if (in_array($dataPoint, array(self::DATA_OPTIONS, self::DATA_ADDON, self::DATA_PLUGIN))) {
                        // Forced non-escaping
                        if ($dataPointForcedUnescape) {
                            return $this->_indent($finalData, $item['indent']);
                        }
                                    
                        switch ($fileExtension) {
                            case 'php':
                            case 'phtml':
                                return $this->_indent(var_export($finalData, true), $item['indent']);
                                break;

                            case Model_Project_Config_Item_Code::EXT_JS:
                            case Model_Project_Config_Item_Code::EXT_JSON:
                                return $this->_indent(json_encode($finalData), $item['indent']);
                                break;
                        }
                    }

                    // Boolean config.ini item
                    if (self::DATA_CONFIG == $dataPoint && is_bool($finalData)) {
                        return $this->_indent($finalData ? Csv::TRUE : Csv::FALSE, $item['indent']);
                    }

                    // All done
                    return $this->_indent($finalData, $item['indent']);
                }, 
                $fileContents
            );
            
            // No changes were made
            if ($newFileContents == $fileContents) {
                break;
            }
            
            // Store the new data
            $fileContents = $newFileContents;
        } while(true);
        
        // All done
        return $fileContents;
    }
}

/* EOF */