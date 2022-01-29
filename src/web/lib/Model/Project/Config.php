<?php
/**
 * Theme Warlock - Model_Project_Config
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config {

    /**
     * Project data
     */
    const DATA_USER_ID        = 'userId';
    const DATA_PROJECT_ID     = 'projectId';
    const DATA_SANDBOX_DOMAIN = 'sandboxDomain';
    const DATA_CONFIG         = 'config';
    
    /**
     * Project categories
     */
    const CATEGORY_CORE  = 'core';
    const CATEGORY_ADDON = 'addon';
    const CATEGORY_PLUS  = 'plus';
    
    // Plus key
    const CATEGORY_PLUS_KEY  = 'addon';
    
    /**
     * Destination directory
     * 
     * @var string
     */
    protected $_destDir;
    
    /**
     * Available add-ons
     * 
     * @var string[]
     */
    protected $_availableAddons = array();

    /**
     * User ID
     * 
     * @var int
     */
    protected $_userId;
    
    /**
     * Project ID
     * 
     * @var int
     */
    protected $_projectId;
    
    /**
     * Project path
     * 
     * @var string
     */
    protected $_projectPath;
    
    /**
     * Sandbox domain
     * 
     * @var string
     */
    protected $_sandboxDomain;
    
    /**
     * Configuration array
     * 
     * @var array
     */
    protected $_config;
    
    /**
     * Singleton instances
     * 
     * @var Model_Project_Config[]
     */
    protected static $_instances = array();
    
    /**
     * Project configuration
     * 
     * @param int $userId    User ID
     * @param int $projectId Project ID
     */
    protected function __construct($userId, $projectId) {
        // Store the details
        $this->_userId = $userId;
        $this->_projectId = $projectId;
        $this->_sandboxDomain = (null === $userId ? 'wp' : 'wp-u' . $this->_userId) . '.' . Config::get()->myDomain;
        $this->_projectPath = Model_Projects::getProjectPath($userId, $projectId);
        
        // Parse the run.csv file
        $this->_parseCsv();
    }
    
    /**
     * Project configuration
     * 
     * @param int $userId    User ID
     * @param int $projectId Project ID
     * @return Model_Project_Config
     */
    public static function getInstance($userId, $projectId) {
        // Prepare the cache key
        $cacheKey = sprintf('%s-%s', $userId, $projectId);
        
        // Cache miss
        if (!isset(self::$_instances[$cacheKey])) {
            self::$_instances[$cacheKey] = new static($userId, $projectId);
        }
        
        // All done
        return self::$_instances[$cacheKey];
    }
    
    /**
     * Get the destination directory
     * 
     * @return string
     */
    public function getDestDir() {
        return $this->_destDir;
    }
    
    /**
     * Get the project user ID
     * 
     * @return int
     */
    public function getUserId() {
        return $this->_userId;
    }
    
    /**
     * Get the project ID
     * 
     * @return int
     */
    public function getProjectId() {
        return $this->_projectId;
    }
    
    /**
     * Get the sandbox FQDN
     * 
     * @return string
     */
    public function getSandboxDomain() {
        return $this->_sandboxDomain;
    }
    
    /**
     * Get the project's path on disk
     * 
     * @return string
     */
    public function getProjectPath() {
        return $this->_projectPath;
    }
    
    /**
     * Get the project configuration array
     * 
     * @return array
     */
    public function getProjectData() {
        return $this->_config;
    }
    
    /**
     * Parse the CSV file to get the configuration
     */
    protected function _parseCsv() {
        // Prepare the data
        $csvData = array();
        
        // Found the file
        if (file_exists($runPath = $this->_projectPath . '/run.csv')) {
            // Get the data
            $csvData = Csv::getData($runPath);
        }
        
        // Prepare the UI sets and the corresponding information
        $uiSets = UiSets::getInstance()->getAllArray();
        
        // Prepare the default values
        $defaultValues = array(
            // Identity
            Cli_Run_Integration::OPT_PROJECT_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_NAME))
                ->setValue('Project ' . $this->_projectId)
                ->setRegEx('%^[\w ]+$%i')
                ->setSanitizer(function($value) {
                    // Prevent multiple consecutive spaces
                    return trim(preg_replace('%\s+%s', ' ', $value));
                })
                ->setMetaHeader('Identity'),
                    
            Cli_Run_Integration::OPT_PROJECT_ICON => (new Model_Project_Config_Item_Image(Cli_Run_Integration::OPT_PROJECT_ICON))
                ->setImageType(Model_Project_Config_Item_Image::TYPE_PNG)
                ->setImageWidth(512)
                ->setImageHeight(512),
                    
            Cli_Run_Integration::OPT_PROJECT_DESCRIPTION => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_DESCRIPTION))
                ->setSanitizer('strip_tags')
                ->setIsTextarea(),
                    
            Cli_Run_Integration::OPT_PROJECT_DESCRIPTION_STORE => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_DESCRIPTION_STORE))
                ->setIsMarkDown(),
                    
            Cli_Run_Integration::OPT_PROJECT_QUOTE => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_QUOTE))
                ->setIsTextarea()
                ->setMetaRefresh('getQuote')
                ->setSanitizer(function($value) {
                    // Prevent the quote from breaking the JS and PHP comment section
                    return trim(preg_replace('%(\*\/|\/\*)%ims', '', $value));
                }),
                    
            Cli_Run_Integration::OPT_PROJECT_MARKETPLACE => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_MARKETPLACE))
                ->setValue(Dist::MARKET_THEMEFOREST)
                ->setOptionsStrict()
                ->setOptions(array_keys(Dist::getMarketplacesIds())),
            
            Cli_Run_Integration::OPT_PROJECT_PREVIEW_STORE => (new Model_Project_Config_Item_Image(Cli_Run_Integration::OPT_PROJECT_PREVIEW_STORE))
                ->setImageType(Model_Project_Config_Item_Image::TYPE_PNG)
                ->setImageWidth(WordPress_Snapshots_Snapshot::STORE_PREVIEW_WIDTH)
                ->setImageHeight(WordPress_Snapshots_Snapshot::STORE_PREVIEW_HEIGHT),
                    
            Cli_Run_Integration::OPT_PROJECT_PREVIEW => (new Model_Project_Config_Item_Image(Cli_Run_Integration::OPT_PROJECT_PREVIEW))
                ->setImageType(Model_Project_Config_Item_Image::TYPE_PNG)
                ->setImageWidth(WordPress_Snapshots_Snapshot::PREVIEW_WIDTH)
                ->setImageHeight(WordPress_Snapshots_Snapshot::PREVIEW_HEIGHT),
                    
            // Design
            Cli_Run_Integration::OPT_PROJECT_UI_SET => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_UI_SET))
                ->setOptions(array_keys($uiSets))
                ->setMetaOptionsDetails($uiSets)
                ->setMetaHeader('Design'),
                    
            Cli_Run_Integration::OPT_PROJECT_CONTENT_WIDTH => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_CONTENT_WIDTH))
                ->setOptions(array(12, 10, 8, 6))
                ->setValue(10)
                ->setMetaOptionsDetails(array(
                    12 => array('Full width', 'The total content area has 12 columns, no offset'), 
                    10 => array('10 Columns', 'The total content area has 10 columns, offset 1'), 
                    8 => array('8 Columns', 'The total content area has 8 columns, offset 2'), 
                    6 => array('Half width', 'The total content area has 6 columns, offset 3')
                )),
                    
            Cli_Run_Integration::OPT_PROJECT_SIDEBAR_WIDTH => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_SIDEBAR_WIDTH))
                ->setOptions(array(50, 33, 25, 20))
                ->setValue(25)
                ->setMetaOptionsDetails(array(
                    50 => array('Half', 'All sidebars account for 50% of the content width'), 
                    33 => array('Third', 'All sidebars account for 33% of the content width'), 
                    25 => array('Quarter', 'All sidebars account for 25% of the content width'), 
                    20 => array('Fifth', 'All sidebars account for 20% of the content width')
                )),
                   
            Cli_Run_Integration::OPT_PROJECT_HEADER_LINK_COLOR => (new Model_Project_Config_Item_Color(Cli_Run_Integration::OPT_PROJECT_HEADER_LINK_COLOR))
                ->setValue('#ff00b8ff'),
                    
            Cli_Run_Integration::OPT_PROJECT_HEADER_TEXT_COLOR => (new Model_Project_Config_Item_Color(Cli_Run_Integration::OPT_PROJECT_HEADER_TEXT_COLOR))
                ->setValue('#ff333333'),
                    
            Cli_Run_Integration::OPT_COLOR_1_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_COLOR_1_NAME))
                ->setValue('Main')
                ->setMetaHeader('Color scheme'),
            Cli_Run_Integration::OPT_COLOR_1_DEFAULT => (new Model_Project_Config_Item_Color(Cli_Run_Integration::OPT_COLOR_1_DEFAULT))
                ->setValue('#ff16a4d7'),
                    
            Cli_Run_Integration::OPT_COLOR_2_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_COLOR_2_NAME))
                ->setValue('Secondary'),
            Cli_Run_Integration::OPT_COLOR_2_DEFAULT => (new Model_Project_Config_Item_Color(Cli_Run_Integration::OPT_COLOR_2_DEFAULT))
                ->setValue('#ff1bb973'),
                    
            Cli_Run_Integration::OPT_COLOR_3_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_COLOR_3_NAME))
                ->setValue('Decoration'),
            Cli_Run_Integration::OPT_COLOR_3_DEFAULT => (new Model_Project_Config_Item_Color(Cli_Run_Integration::OPT_COLOR_3_DEFAULT))
                ->setValue('#fff06c07'),
                    
            Cli_Run_Integration::OPT_COLOR_4_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_COLOR_4_NAME))
                ->setValue('Typography'),
            Cli_Run_Integration::OPT_COLOR_4_DEFAULT => (new Model_Project_Config_Item_Color(Cli_Run_Integration::OPT_COLOR_4_DEFAULT))
                ->setValue('#ff000000'),
            
            Cli_Run_Integration::OPT_FONT_H1_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_FONT_H1_NAME))
                ->setMetaHeader('Typography')
                ->setValue('Heading 1'),
            Cli_Run_Integration::OPT_FONT_H1_OPTIONS => (new Model_Project_Config_Item_Font(Cli_Run_Integration::OPT_FONT_H1_OPTIONS))
                ->setOptions(Google_Fonts::getFontFamilies())
                ->setIsList(),

            Cli_Run_Integration::OPT_FONT_H2_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_FONT_H2_NAME))
                ->setValue('Heading 2'),
            Cli_Run_Integration::OPT_FONT_H2_OPTIONS => (new Model_Project_Config_Item_Font(Cli_Run_Integration::OPT_FONT_H2_OPTIONS))
                ->setMetaOptionsClone(Cli_Run_Integration::OPT_FONT_H1_OPTIONS)
                ->setIsList(),

            Cli_Run_Integration::OPT_FONT_H3_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_FONT_H3_NAME))
                ->setValue('Heading 3+'),
            Cli_Run_Integration::OPT_FONT_H3_OPTIONS => (new Model_Project_Config_Item_Font(Cli_Run_Integration::OPT_FONT_H3_OPTIONS))
                ->setMetaOptionsClone(Cli_Run_Integration::OPT_FONT_H1_OPTIONS)
                ->setIsList(),

            Cli_Run_Integration::OPT_FONT_TEXT_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_FONT_TEXT_NAME))
                ->setValue('Paragraphs'),
            Cli_Run_Integration::OPT_FONT_TEXT_OPTIONS => (new Model_Project_Config_Item_Font(Cli_Run_Integration::OPT_FONT_TEXT_OPTIONS))
                ->setMetaOptionsClone(Cli_Run_Integration::OPT_FONT_H1_OPTIONS)
                ->setIsList(),

            Cli_Run_Integration::OPT_FONT_MENU_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_FONT_MENU_NAME))
                ->setValue('Menu'),
            Cli_Run_Integration::OPT_FONT_MENU_OPTIONS => (new Model_Project_Config_Item_Font(Cli_Run_Integration::OPT_FONT_MENU_OPTIONS))
                ->setMetaOptionsClone(Cli_Run_Integration::OPT_FONT_H1_OPTIONS)
                ->setIsList(),

            Cli_Run_Integration::OPT_FONT_BUTTON_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_FONT_BUTTON_NAME))
                ->setValue('Buttons'),
            Cli_Run_Integration::OPT_FONT_BUTTON_OPTIONS => (new Model_Project_Config_Item_Font(Cli_Run_Integration::OPT_FONT_BUTTON_OPTIONS))
                ->setMetaOptionsClone(Cli_Run_Integration::OPT_FONT_H1_OPTIONS)
                ->setIsList(),

            Cli_Run_Integration::OPT_FONT_LINK_NAME => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_FONT_LINK_NAME))
                ->setValue('Links'),
            Cli_Run_Integration::OPT_FONT_LINK_OPTIONS => (new Model_Project_Config_Item_Font(Cli_Run_Integration::OPT_FONT_LINK_OPTIONS))
                ->setMetaOptionsClone(Cli_Run_Integration::OPT_FONT_H1_OPTIONS)
                ->setIsList(),
                    
            // Functionality
            Cli_Run_Integration::OPT_PROJECT_TAGS => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_TAGS))
                ->setOptions(array_keys(WordPress_Tags::getSubjectTags()))
                ->setMetaOptionsDetails(WordPress_Tags::getSubjectTags())
                ->setMetaHeader('Functionality'),
                    
            Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE => (new Model_Project_Config_Item_Boolean(Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE))
                ->setValue(true),
                    
            Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS => (new Model_Project_Config_Item_Boolean(Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS))
                ->setValue(true),
                    
            Cli_Run_Integration::OPT_PROJECT_VERSION => (new Model_Project_Config_Item_Integer(Cli_Run_Integration::OPT_PROJECT_VERSION))
                ->setValue(1)
                ->setMin(1)
                ->setMax(1000),
                  
            Cli_Run_Integration::OPT_VERSION_TEMPLATE => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_VERSION_TEMPLATE))
                ->setIsOptional()
                ->setRegEx('%^(?:\d+\.){1,2}x(?:,\-?\d+)?$%'),
                    
            Cli_Run_Integration::OPT_PROJECT_FRAMEWORK => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_FRAMEWORK))
                ->setValue(Framework::ID_ONEPAGE)
                ->setIsDisabled(),
                    
            Cli_Run_Integration::OPT_PROJECT_PLUGINS => (new Model_Project_Config_Item_String(Cli_Run_Integration::OPT_PROJECT_PLUGINS))
                ->setOptions(Cli_Run_Integration::CORE_PLUGINS),
        );

        // Prepare the reordered CSV data
        $csvDataReordered = array();
        foreach (array_keys($defaultValues) as $defaultKey) {
            // Move the item in the defined order
            $csvDataReordered[$defaultKey] = isset($csvData[$defaultKey]) ? $csvData[$defaultKey] : null;
            unset($csvData[$defaultKey]);
        }
        
        // Reassemble the array
        $csvData = array_merge($csvDataReordered, $csvData);
        
        // Go through the defaults
        foreach ($defaultValues as $key => /*@var $configItem Model_Project_Config_Item*/ $configItem) {
            // Item is an array
            if (in_array($key, Cli_Run_Integration::JSON_OPTIONS)) {
                $configItem->setIsList();
            }
            
            // Item already defined, update value
            if (isset($csvData[$key]) && null !== $csvData[$key]) {
                // Update the value
                $configItem->setValue($csvData[$key]);
            }
            
            // Load disk items
            if ($configItem->isOnDisk()) {
                $configItem->load($this->_projectPath, self::CATEGORY_CORE);
            }
            
            // All done
            $csvData[$key] = $configItem;
        }

        // Get the destination directory
        $this->_destDir = Tasks_1NewProject::getDestDir(
            $csvData[Cli_Run_Integration::OPT_PROJECT_NAME]->getValue()
        );

        // Parse addons separately
        $currentAddons = isset($csvData[Cli_Run_Integration::OPT_PROJECT_ADDONS]) && is_array($csvData[Cli_Run_Integration::OPT_PROJECT_ADDONS]) ? $csvData[Cli_Run_Integration::OPT_PROJECT_ADDONS] : array();
        unset($csvData[Cli_Run_Integration::OPT_PROJECT_ADDONS]);
        
        // The current framework's corresponding addon should be first
        if (!isset($currentAddons[$csvData[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK]->getValue()])) {
            $currentAddons = array_merge(array($csvData[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK]->getValue() => array()), $currentAddons);
        }
            
        // Update the rest of the CSV values
        foreach ($csvData as $key => $value) {
            if (!$value instanceof Model_Project_Config_Item) {
                if (is_numeric($value)) {
                    $csvData[$key] = new Model_Project_Config_Item_Integer($key, $value);
                } else {
                    $csvData[$key] = new Model_Project_Config_Item_String($key, $value);
                }
            }
        }
        
        // Get the metadata
        foreach ($csvData as /*@var $configItem Model_Project_Config_Item*/ $csvDataItem) {
            $csvDataItemKey = $csvDataItem->getKey();
            if (isset(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey]) && is_array(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey]) && count(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey]) >= 2) {
                // Get the title and description
                list($csvDataItemTitle, $csvDataItemDescription) = array_values(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey]);
                
                // Set the title
                if (is_string($csvDataItemTitle)) {
                    $csvDataItem->setMetaTitle($csvDataItemTitle);
                }
                
                // Set the description
                if (is_string($csvDataItemDescription)) {
                    $csvDataItem->setMetaDescription($csvDataItemDescription);
                }
                
                // Set the symbol
                if (isset(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey][2]) && strlen(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey][2])) {
                    $csvDataItem->setMetaSymbol(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey][2]);
                }
                
                // Set the url
                if (isset(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey][3]) && strlen(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey][3])) {
                    $csvDataItem->setMetaUrl(Cli_Run_Integration::OPT_DETAILS[$csvDataItemKey][3]);
                }
            }
        }
        
        // Prepare the result
        $result = array(
            self::CATEGORY_CORE => $csvData,
        );

        // Parse the addons
        foreach ($currentAddons as $addonName => $addonKeys) {
            // Get all the available addons
            foreach (Addons::getConfig($addonName, $addonKeys, $this->_projectPath) as $finalAddonName => $finalAddonKeys) {
                // Store the addon
                $result[self::CATEGORY_ADDON . '-' . $finalAddonName] = $finalAddonKeys;
            }
        }

        // Get the available addons
        $this->_availableAddons = array_filter(
            array_map(
                function($item) {
                    return basename($item);
                }, 
                glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/*', GLOB_ONLYDIR)
            ), 
            function($item) use ($currentAddons, $csvData) {
                return preg_match('%^(' . self::CATEGORY_CORE . '|' . preg_quote($csvData[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK]->getValue()) . ')\-%', $item) && !in_array($item, array_keys($currentAddons));
            }
        );

        // Add the + sign
        if (count($this->_availableAddons)) {
            $extraAddonsItem = new Model_Project_Config_Item_String(
                self::CATEGORY_PLUS_KEY, 
                '', 
                true,
                $this->_availableAddons,
                true
            );
            
            // Set the metadata
            $extraAddonsItem->setMetaTitle('Theme Add-On');
            $extraAddonsItem->setMetaDescription('Each Add-On adds extra functionality to the current theme');
            $extraAddonsItem->setMetaOptionsDetails($this->_getAvailableAddonsMeta());
            
            // Add the element
            $result[self::CATEGORY_PLUS] = array(
                self::CATEGORY_PLUS_KEY => $extraAddonsItem
            );
        }
        
        // Store the configuration
        $this->_config = $result;
    }
    
    /**
     * Get the available addons' meta (title and description)
     * 
     * @return array <br/>
     * <code>
     * array(<br/>
     *     "addon name" => array(<br/>
     *         "addon title",<br/>
     *         "addon description",<br/>
     *     ),<br/>
     * )
     * </code>
     */
    protected function _getAvailableAddonsMeta() {
        // Prepare the result
        $result = array();
        
        // Go through the available addons
        foreach ($this->_availableAddons as $addonName) {
            // Store the result
            $result[$addonName] = Addons::getMeta($addonName);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Save the project configuration
     */
    public function save() {
        // Prepare the data
        $data = array();
        
        // Add the core items
        foreach ($this->_config[self::CATEGORY_CORE] as $key => /*@var $valueItem Model_Project_Config_Item*/ $valueItem) {
            $data[$key] = $valueItem->getValue();
        }
        
        // Add the add-ons
        $addons = array();
        foreach ($this->_config as $itemName => $itemDetails) {
            if (preg_match('%^' . self::CATEGORY_ADDON . '\-%', $itemName)) {
                // Get the addon name
                $addonName = preg_replace('%^' . self::CATEGORY_ADDON . '\-%', '', $itemName);
                
                // Prepare the addon details
                $addonDetails = array();
                foreach ($itemDetails as /*@var $configItem Model_Project_Config_Item*/ $configItem) {
                    // Not a disk element or an internal flag
                    if (!$configItem->isOnDisk() && (!preg_match('%^_%', $configItem->getKey()) || in_array($configItem->getKey(), array(Model_Project_Config_Item::KEY_FLAVOR)))) {
                        $addonDetails[$configItem->getKey()] = $configItem->getValue();
                    }
                }
                $addons[$addonName] = $addonDetails;
            }
        }
        
        // Store the add-ons
        $data[Cli_Run_Integration::OPT_PROJECT_ADDONS] = $addons;
        
        // Save the CSV
        Csv::setData($data, $this->_projectPath . '/run.csv');
    }
    
    /**
     * Set the project data
     * 
     * @param string  $category  Category: <ul>
     * <li>Model_Project_Config::CATEGORY_CORE</li>
     * <li>Model_Project_Config::CATEGORY_ADDON . '_addonName'</li>
     * <li>Model_Project_Config::CATEGORY_PLUS</li>
     * </ul>
     * @param array   $data      Key-value array
     * @param boolean $reThrow   Whether to re-throw exceptions (default <b>false</b>)
     * @param boolean $userInput These values are set by the user (default <b>true</b>)
     * @return Model_Project_Config
     * @throws Exception
     */
    public function setProjectAssoc($category, Array $data, $reThrow = false, $userInput = true) {
        if (isset($this->_config[$category])) {
            switch ($category) {
                // Core updates
                case self::CATEGORY_CORE:
                    // Go through the data
                    foreach ($data as $key => $value) {
                        foreach ($this->_config[$category] as /*@var $configItem Model_Project_Config_Item*/ $configItem) {
                            if ($configItem->getKey() == $key) {
                                // Load disk items
                                if ($configItem->isOnDisk()) {
                                    $configItem->load($this->_projectPath, self::CATEGORY_CORE);
                                }
            
                                // Set the value
                                $configItem->setValue($value, $userInput);
                            }
                        }
                    }
                    break;
                    
                case self::CATEGORY_PLUS:
                    // Create add-on
                    if (isset($data[self::CATEGORY_PLUS_KEY])) {
                        foreach ($data[self::CATEGORY_PLUS_KEY] as $addonName) {
                            // Addon not available
                            if (!in_array($addonName, $this->_availableAddons)) {
                                continue;
                            }
                            
                            // Get all the available addons
                            foreach (Addons::getConfig($addonName, null, $this->_projectPath) as $finalAddonName => $finalAddonKeys) {
                                // Store the addon
                                $this->_config[self::CATEGORY_ADDON . '-' . $finalAddonName] = $finalAddonKeys;
                            }

                            // Remove from available addons list
                            $this->_availableAddons = array_filter($this->_availableAddons, function($item) use($addonName) {
                                return $addonName != $item;
                            });
                        }
                        
                        // Valid list of addons
                        if (count($this->_availableAddons)) {
                            $this->_config[self::CATEGORY_PLUS] = array(
                                (new Model_Project_Config_Item_String(
                                    self::CATEGORY_PLUS_KEY, 
                                    '', 
                                    true,
                                    $this->_availableAddons,
                                    true
                                ))->setMetaOptionsDetails($this->_getAvailableAddonsMeta())
                            );
                        } else {
                            unset($this->_config[self::CATEGORY_PLUS]);
                        }
                    }
                    break;
                
                default:
                    do {
                        if (preg_match('%^' . self::CATEGORY_ADDON . '\-.*?$%', $category)) {
                            // Get the add-on name
                            $addonName = preg_replace('%^' . self::CATEGORY_ADDON . '\-(.*?)$%', '$1', $category);

                            // Delete add-on
                            if (!count($data)) {
                                // Remove the add-on category
                                unset($this->_config[$category]);
                                
                                // Update add-on
                                foreach (Addons::getConfig($addonName, null, $this->_projectPath) as $finalAddonName => $finalAddonKeys) {
                                    // Get the item
                                    foreach ($finalAddonKeys as $key => /*@var $item Model_Project_Config_Item*/ $item) {
                                        try {
                                            $item->setValue(null, $userInput);
                                        } catch (Exception $exc) {
                                            if ($reThrow) {
                                                throw $exc;
                                            }
                                        }
                                    }
                                }
                                
                                // Add to available addons list
                                $this->_availableAddons[] = $addonName;
                                break;
                            }
                            
                            // Update add-on
                            foreach (Addons::getConfig($addonName, null, $this->_projectPath) as $finalAddonName => $finalAddonKeys) {
                                // Get the item
                                foreach ($finalAddonKeys as $key => /*@var $item Model_Project_Config_Item*/ $item) {
                                    if (isset($data[$key])) {
                                        try {
                                            $item->setValue($data[$key], $userInput);
                                        } catch (Exception $exc) {
                                            if ($reThrow) {
                                                throw $exc;
                                            }
                                        }
                                    }
                                }

                                // Store the addon
                                $this->_config[self::CATEGORY_ADDON . '-' . $finalAddonName] = $finalAddonKeys;
                            }
                        }
                    } while (false);
                    break;
            }
        }
        
        // All done
        return $this;
    }

    /**
     * Get the project configuration data as an array
     * 
     * @return array
     */
    public function toArray() {
        // Prepare the configuration array
        $configurationArray = array();
        foreach ($this->_config as $category => /*@var $configItems Model_Project_Config_Item[]*/ $configItems) {
            // Prepare the category
            $configurationArray[$category] = array();
            
            // Add the items
            foreach ($configItems as /*@var $configItem Model_Project_Config_Item*/ $configItem) {
                $configurationArray[$category][$configItem->getKey()] = $configItem->toArray();
            }
        }
        
        // Get the whole data
        return array(
            self::DATA_USER_ID        => $this->_userId,
            self::DATA_PROJECT_ID     => $this->_projectId,
            self::DATA_SANDBOX_DOMAIN => $this->_sandboxDomain,
            self::DATA_CONFIG         => $configurationArray,
        );
    }
    
}

/* EOF */