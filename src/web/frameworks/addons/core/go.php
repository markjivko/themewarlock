<?php
/**
 * Theme Warlock - Addon_Core
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_Core extends Addon {
    
    const COLOR_PALETTE_METHOD_ORIGINAL                      = 'original';
    const COLOR_PALETTE_METHOD_COMPLEMENT                    = 'complement';
    const COLOR_PALETTE_METHOD_LIGHTER                       = 'lighter';
    const COLOR_PALETTE_METHOD_COMPLEMENT_LIGHTER            = 'complementLighter';
    const COLOR_PALETTE_METHOD_DARKER                        = 'darker';
    const COLOR_PALETTE_METHOD_COMPLEMENT_DARKER             = 'complementDarker';
    const COLOR_PALETTE_METHOD_LIGHTEST                      = 'lightest';
    const COLOR_PALETTE_METHOD_COMPLEMENT_LIGHTEST           = 'complementLightest';
    const COLOR_PALETTE_METHOD_DARKEST                       = 'darkest';
    const COLOR_PALETTE_METHOD_COMPLEMENT_DARKEST            = 'complementDarkest';
    const COLOR_PALETTE_METHOD_CONTRAST                      = 'contrast';
    const COLOR_PALETTE_METHOD_COMPLEMENT_CONTRAST           = 'complementContrast';
    const COLOR_PALETTE_METHOD_CONTRAST_NON_COLOR            = 'contrastNonColor';
    const COLOR_PALETTE_METHOD_COMPLEMENT_CONTRAST_NON_COLOR = 'complementContrastNonColor';
    const COLOR_PALETTE_METHOD_PURE                          = 'pure';
    const COLOR_PALETTE_METHOD_COMPLEMENT_PURE               = 'complementPure';
    const COLOR_PALETTE_METHOD_PURE_CONTRAST                 = 'pureContrast';
    const COLOR_PALETTE_METHOD_COMPLEMENT_PURE_CONTRAST      = 'complementPureContrast';
    const COLOR_PALETTE_METHOD_NON_COLOR                     = 'nonColor';
    const COLOR_PALETTE_METHOD_COMPLEMENT_NON_COLOR          = 'complementNonColor';
    
    const COLOR_PALETTE_VALUE_METHOD_HEX  = 'hex';
    const COLOR_PALETTE_VALUE_METHOD_RGB  = 'rgb';
    const COLOR_PALETTE_VALUE_METHOD_RGBA = 'rgba';
    
    /**
     * Cache for the main Themes Panel
     * 
     * @var 
     */
    protected static $_cacheThemePanel = null;
    
    /**
     * Get the available plugins
     * 
     * @return string[]
     */
    public function getPlugins() {
        // Get the plugins
        $pluginsList = isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_PLUGINS]) && is_array(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_PLUGINS]) ? Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_PLUGINS] : array();
        
        // Remove certain plugins in live mode
        if (!Tasks::isStaging()) {
            $pluginsList = array_values(array_filter($pluginsList, function($item) {
                return !in_array($item, array(
                    Plugin::PLUGIN_THEME_CHECK,
                ));
            }));
        }
        
        // WPBakery Page Builder is included by default with all themes
        $pluginsList[] = Plugin::PLUGIN_JS_COMPOSER;
        
        // Use WPBakery Page Builder blocks in sidebars
        if (isset($this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS]) && $this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS]->getValue()) {
            $pluginsList[] = Plugin::PLUGIN_CONTENT_TYPE_WIDGET_BLOCK;
        }
        
        // All done
        return $pluginsList;
    }
    
    /**
     * Get the needed script packages (CSS, JS and IMG) that are bundled with this add-on
     * 
     * @return string[]
     */
    public function getScripts() {
        // Prepare the result
        $result = array(
            Script::SCRIPT_SHIV,
        );
        
        // Use storyline.js
        if (isset($this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE]) && $this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE]->getValue()) {
            $result[] = Script::SCRIPT_JQUERY_STORYLINE;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the list of Documentation sections (addon-specific docs)
     * 
     * @return array Associative array of <br/>
     * <b>Addon Name</b> => array(<ul>
     *      <li><b>WordPress_Docs::SECTION_TITLE</b>       => Title</li>
     *      <li><b>WordPress_Docs::SECTION_DESCRIPTION</b> => Description</li>
     *      <li><b>WordPress_Docs::SECTION_CONTENT</b>     => Content</li>
     * </ul>)
     */
    public function getDocsSections() {
        return WordPress_Docs::getInstance()->getSections();
    }
    
    /**
     * Get the list of WPBakery Page Builder Add-Ons, excluding support (extra) plugins
     * 
     * @return Plugin[]
     */
    public function getVcBundleAddons() {
        // Exclude support plugins from this list, i.e. only list the Add-Ons
        return Plugin_Bundle::getPlugins(false);
    }
    
    /**
     * Get the WPBakery Page Builder plugins Bundle name
     * 
     * @return string
     */
    public function getVcBundleName() {
        return Plugin_Bundle::getName();
    }
    
    /**
     * Get the WPBakery Page Builder plugins Bundle name - variable mode
     * 
     * @return string
     */
    public function getVcBundleNameVar() {
        return Plugin_Bundle::getNameVar();
    }
    
    /**
     * Get the list of available snapshots (for the docs)
     * 
     * @return array Associative array of <br/>
     * <b>Addon Name</b> => array(<ul>
     *      <li><b>WordPress_Docs::SNAPSHOT_TITLE</b>       => Title</li>
     *      <li><b>WordPress_Docs::SNAPSHOT_DESCRIPTION</b> => Description</li>
     *      <li><b>WordPress_Docs::SNAPSHOT_URL</b>         => URL</li>
     * </ul>)
     */
    public function getDocsSnapshots() {
        return WordPress_Docs::getInstance()->getSnapshots();
    }
    
    /**
     * Get the corresponding WordPress Core tags
     */
    public function getTags() {
        return array(
            WordPress_Tags::TAG_CORE_TRANSLATION_READY,
            WordPress_Tags::TAG_CORE_THEME_OPTIONS,
            WordPress_Tags::TAG_CORE_CUSTOM_BACKGROUND,
            WordPress_Tags::TAG_CORE_EDITOR_STYLE,
            WordPress_Tags::TAG_CORE_FEATURED_IMAGES,
            WordPress_Tags::TAG_CORE_FOOTER_WIDGETS,
            WordPress_Tags::TAG_CORE_THREADED_COMMENTS,
            WordPress_Tags::TAG_CORE_CUSTOM_COLORS,
        );
    }
    
    /**
     * Initialize the drawables<ul>
     * <li></li>
     * </ul>
     */
    public function initDrawables() {
        // Create the icon SVG
        if (isset($this->addonData[Cli_Run_Integration::OPT_PROJECT_ICON])) {
            /*@var $iconConfig Model_Project_Config_Item_Image*/
            $iconConfig = $this->addonData[Cli_Run_Integration::OPT_PROJECT_ICON];
            
            // Get the icon path
            $iconPath = $iconConfig->getPath();

            // Prepare the admin images path
            $adminImgPath = Tasks_1NewProject::getPath() . '/admin/img';
                
            // Valid file
            if (strlen($iconPath) && is_file($iconPath)) {
                // Save the full-size icon
                copy($iconPath, $adminImgPath . '/st_icon_512.png');

                // Get the imagemagick object
                $imageMagick = new ImageMagick();

                // Create the corresponding 20x20 SVG
                try {
                    $imageMagick->pngToSvg(
                        $iconPath, 
                        $adminImgPath . '/st_icon_20.svg',
                        $this->addonData[Cli_Run_Integration::OPT_PROJECT_HEADER_TEXT_COLOR]->getWpColor(), 20, 20
                    );
                } catch (Exception $exc) {
                    Log::check(Log::LEVEL_ERROR) && Log::error($exc->getMessage(), $exc->getFile(), $exc->getLine());
                }
            }

            /*@var $iconHeaderColor Model_Project_Config_Item_Color*/
            $iconHeaderColor = $this->addonData[Cli_Run_Integration::OPT_PROJECT_HEADER_LINK_COLOR];

            // Set the colored SVG
            file_put_contents(
                $adminImgPath . '/st_icon_20_active.svg', 
                preg_replace(
                    '%(<g\b[^<>]*?fill\s*?=\s*?)".*?"%', 
                    '${1}"' . $iconHeaderColor->getWpColor() . '"', 
                    file_get_contents(
                        $adminImgPath . '/st_icon_20.svg'
                    )
                )
            );
            
            // Set the menu SVG
            file_put_contents(
                $adminImgPath . '/st_icon_20_white.svg', 
                preg_replace(
                    array(
                        '%(<g\b[^<>]*?fill\s*?=\s*?)".*?"%',
                        '%(<g\b[^<>]*?stroke\s*?=\s*?)".*?"%'
                    ), 
                    array(
                        '${1}"#ffffff"',
                        '${1}"#000000" stroke-width="150" stroke-location="outside"',
                    ), 
                    file_get_contents(
                        $adminImgPath . '/st_icon_20.svg'
                    )
                )
            );
        }

        // Replace actions
        $copyActions = array(
            Cli_Run_Integration::OPT_PROJECT_PREVIEW => WordPress::FILE_SCREENSHOT,
        );

        // Replace the files
        foreach ($copyActions as $copyKey => $copyFileName) {
            // Core resource defined
            if (isset($this->addonData[$copyKey])) {
                /*@var $previewConfig Model_Project_Config_Item_Image*/
                $previewConfig = $this->addonData[$copyKey];

                // Get the preview path
                $previewPath = $previewConfig->getPath();

                // Valid file
                if (is_string($previewPath) && strlen($previewPath) && is_file($previewPath)) {
                    // Save the full-size preview
                    copy($previewPath, Tasks_1NewProject::getPath() . '/' . $copyFileName);
                }
            }
        }
    }
    
    /**
     * Deploy the theme:<ul>
     * <li>Create style.css</li>
     * <li>Add the translations</li>
     * <li>Activate the theme</li>
     * <li>Activate the plugins</li>
     * </ul>
     */
    public function afterNewProject() {
        // Create the style file
        WordPress_Style::getInstance()->parse();
        
        // Prepare the Admin area
        $this->_projectAdminArea();
        
        // Prepare the fonts
        $this->_projectFonts();
        
        // Restore DB and activate theme & plugins
        $this->_projectActivate();
        
        // Prepare the translations
        $this->_projectTranslations();
    }
    
    /**
     * Prepare the files needed for the Admin Area
     */
    protected function _projectAdminArea() {
        // Prepare the theme location
        $themeLocation = Tasks_1NewProject::getPath();
        
        // UI Set declared
        $uiSet = isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_UI_SET])
            && strlen(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_UI_SET]) ? 
                Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_UI_SET] 
                : 
                'default';
        
        // Valid Set defined
        if (is_file($bootstrapFilePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . $uiSet . '.css')) {
            copy($bootstrapFilePath, $themeLocation . '/admin/css/bootstrap.css');
        }
    }
    
    /**
     * Restore DB and activate theme & plugins
     */
    protected function _projectActivate() {
        // Restore the DataBase
        WordPress::executeAction(
            WordPress::TOOLS_DB, 
            WordPress::TOOL_DB_RESTORE
        );

        // Activate the theme
        WordPress::executeAction(
            WordPress::TOOLS_TW,
            WordPress::TOOL_TW_THEME_ENABLE,
            Tasks_1NewProject::$destDir
        );
        
        // Activate the plugins 
        if (count($pluginsList = Addons::getInstance()->getDeployedPlugins())) {
            // Install one by one
            foreach ($pluginsList as $pluginName) {
                // Prepare the plugin slug
                $pluginSlug = $pluginName;
                
                // Valid Plugin instance detected
                if (isset(Addon::$pluginInstances[$pluginName]) && Addon::$pluginInstances[$pluginName] instanceof Plugin) {
                    $pluginSlug = Addon::$pluginInstances[$pluginName]->getSlug(); 
                }
            
                // Trigger the install event
                try {
                    WordPress::executeAction(
                        WordPress::TOOLS_TW,
                        WordPress::TOOL_TW_PLUGIN_INSTALL,
                        $pluginSlug
                    );
                } catch (Exception $exc) {
                    Log::check(Log::LEVEL_WARNING) && Log::warning('Plugin "' . $pluginSlug . '" could not be installed: ' . $exc->getMessage());
                }
            }
        }
    }
    
    /**
     * Prepare the fonts
     */
    protected function _projectFonts() {
        // Prepare the theme location
        $themeLocation = Tasks_1NewProject::getPath();
        
        // Google Fonts - store FONT_FAMILIES in core-custom-fonts.php
        if (is_file($customFontsPath = $themeLocation . '/inc/core-custom-fonts.php')) {
            // Prepare the font families code
            $fontFamiliesCode = 'array(' . PHP_EOL;
            
            // Go through the fonts
            foreach ($this->getFonts() as $fontClass => $fontDetails) {
                // Open the font class
                $fontFamiliesCode .= '    // ' . $fontDetails['name'] . ': font families and required weights/styles' . PHP_EOL;
                $fontFamiliesCode .= '    self::FONT_CLASS_' . $fontDetails['const'] . ' => array(' . PHP_EOL;
                
                // Go through the options
                foreach ($fontDetails['options'] as $fontFamily) {
                    try {
                        // Get list based on <f-x> values
                        $fontFamiliesCode .= '        ' . 
                            var_export($fontFamily, true) . ' => array(' . 
                            implode(', ', array_map(function($item){return var_export($item, true);}, Google_Fonts::getWeights($fontClass, $fontFamily))) . '),' . PHP_EOL;
                    } catch (Exception $exc) {
                        Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                    }
                }
                
                // Close the font class
                $fontFamiliesCode .= '    ),' . PHP_EOL;
            }
            
            // Close the font families PHP code
            $fontFamiliesCode .= ')';            
            
            // Store the details
            file_put_contents(
                $customFontsPath, 
                // Set the FONT_FAMILIES
                preg_replace(
                    '%(const\s+FONT_FAMILIES\s*=\s*).*?;%is', 
                    'const FONT_FAMILIES = ' . 
                        // Indent
                        trim(
                            preg_replace(
                                '%^%m', '${0}' . str_repeat('    ', 2), 
                                $fontFamiliesCode
                            )
                        ) . ';', 
                    file_get_contents($customFontsPath)
                )
            );
        }
    }
    
    /**
     * Save the ".pot", ".po" and ".mo" files based on available translations 
     * gathered by the <b>WordPress::TOOL_FS_I18N</b> action and manually 
     * validated on "http://{config.myDomain}/admin/translations"<br/>
     * 
     * <b>This occurs in the packing task only, right before generating the theme 
     * archive</b>
     */
    protected function _projectTranslations() {
        // Prepare the languages only in packing mode
        if (!isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_PACKING])) {
            return;
        }
        
        // Extract the ".pot" file
        $portableObjectTemplate = (new WordPress_Pot())->extract(
            // Search through the plugins as well
            rtrim(Config::getWpPath(), '\\/') . '/wp-content',
            
            // Set the right text domain to look for
            Tasks_1NewProject::$destDir,
            
            // Store strings inside the theme
            Tasks_1NewProject::getPath() . '/languages/' . Tasks_1NewProject::$destDir . '.pot'
        );
        
        // Get the languages
        $coreLanguagePaths = glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . Model_Project_Config::CATEGORY_CORE . '/languages/*.po');
        
        // Import the languages
        foreach ($coreLanguagePaths as $coreLanguagePath) {
            // Create a new Portable Object for this language
            $portableObjectLanguage = new WordPress_Pot_Translations_Po();
            
            // Import from the available file
            $portableObjectLanguage->importFromFile($coreLanguagePath);

            // Merge it with the .pot file
            $portableObjectLanguage->mergeOriginalsWith($portableObjectTemplate, true);

            // Load the translations from cache
            $portableObjectLanguage->updateEntriesFromCache();

            // Export the object as ".po"
            $portableObjectLanguage->exportToFile(Tasks_1NewProject::getPath() . '/languages/' . basename($coreLanguagePath));

            // Export the object as ".mo"
            (new WordPress_Pot_Translations_Mo())
                ->importFromPo($portableObjectLanguage)
                ->exportToFile(Tasks_1NewProject::getPath() . '/languages/' . basename($coreLanguagePath, '.po') . '.mo');
        }
    }
    
    /**
     * Core options are declared separately
     * 
     * @return array
     */
    public static function getOptions(){
        return array();
    }
    
    /**
     * Get the theme panel OR the result of a method of the theme panel
     * 
     * @example getThemePanel('getId') is useful in addon calls like {call.core.getThemePanel.getId}
     * @param boolean $functionToCall (optional) If set, this function returns the result of that
     * particular WordPress_Customizer_Element_Panel method
     * @return WordPress_Customizer_Element_Panel|mixed
     */
    public static function getThemePanel($functionToCall = null) {
        // Prepare the cache
        if (null === self::$_cacheThemePanel) {
            // Prepare the result
            self::$_cacheThemePanel = (new WordPress_Customizer_Element_Panel(
                Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_NAME] . ' by ' . Config::get()->authorName,
                Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_DESCRIPTION]
            ))
                ->setTranslateName(false)
                ->setPriority(1);
        }
        
        // Call a specific method
        if (is_string($functionToCall) && strlen($functionToCall) && method_exists(self::$_cacheThemePanel, $functionToCall)) {
            return self::$_cacheThemePanel->$functionToCall();
        }
        
        // Get the panel
        return self::$_cacheThemePanel;
    }
    
    /**
     * Calculate the number of columns to assign to content-area and sidebars
     * 
     * @example 'col-10 offset-1'
     * @param string $type Type, one of <ul>
     * <li>'content-area'</li>
     * <li>'sidebar'</li>
     * </ul>
     * @param string|boolean $sidebarForcedPercent (optional) Forced percent for 
     * the sidebar, starting with the letter "p", ex: "<b>p10</b>" for 10%; <br/> 
     * if set to <b>false</b>, the sidebar percent will be calculated 
     * automatically; <br/>default <b>false</b>
     * @param string         $layoutModifier       (optional) Layout modifier, 
     * ex.: "sm"; default <b>empty string</b>
     * @return type
     */
    public function getContentColumns($type = 'content-area', $sidebarForcedPercent = false, $layoutModifier = '') {
        // Get the forced percent for the sidebar
        if (false !== $sidebarForcedPercent) {
            $sidebarForcedPercent = intval(preg_replace('%^p%', '', $sidebarForcedPercent));
        }
        
        // Set the cache key
        $cacheKey = $type . ',' . (false === $sidebarForcedPercent ? 'false' : $sidebarForcedPercent) . ',' . $layoutModifier;

        // Cache hit
        if (null !== $result = Cache::get($cacheKey)) {
            return $result;
        }
        
        // Total content width
        $totalColumns = $this->addonData[Cli_Run_Integration::OPT_PROJECT_CONTENT_WIDTH]->getValue();
        if (!strlen($totalColumns)) {
            $totalColumns = 10;
        }
        
        // Total sidebars width in %
        $sidebarsWidth = $this->addonData[Cli_Run_Integration::OPT_PROJECT_SIDEBAR_WIDTH]->getValue();
        if (!strlen($sidebarsWidth)) {
            $sidebarsWidth = 25;
        }
        
        // Get the project data
        $projectData = Tasks::$project->getConfig()->getProjectData();

        // Count the enabled sidebars
        $numberOfSidebars = 0;
        
        // Prepare the framework ID
        $frameworkId = $this->addonData[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK]->getValue();
        
        // Go through the sidebars controller
        if (isset($projectData[Model_Project_Config::CATEGORY_ADDON . '-' . $frameworkId])) {
            foreach($projectData[Model_Project_Config::CATEGORY_ADDON . '-' . $frameworkId] as $itemName => $itemDetails) {
                if (preg_match('%^sidebar_\d+$%', $itemName) && $itemDetails instanceof Model_Project_Config_Item_Boolean) {
                    if ($itemDetails->getValue()) {
                        $numberOfSidebars++;
                    }
                }
            }
        }
        
        // No sidebars enabled
        if (!$numberOfSidebars) {
            $sidebarsWidth = 0;
        }
        
        // Front page
        if (is_numeric($sidebarForcedPercent)) {
            $sidebarsWidth = $sidebarForcedPercent;
        }
        
        // Compute the sidebar width in columns
        $sidebarColumns = intval(round($totalColumns * $sidebarsWidth / 100, 0));
        
        // Prepare the layout modifier part
        $layoutModifierPart = (strlen($layoutModifier) ? $layoutModifier . '-' : '');
        
        // Prepare the result
        $result = '';
        
        // If a modifier was not provided, the user did not take care of the "xs" layout
        if (!strlen($layoutModifierPart)) {
            // "xs" layout, full-width
            $result = 'col-12 ';
            
            // The rules apply from this breaking point up
            $layoutModifierPart = 'md-';
        }
        
        // Set the result
        switch ($type) {
            case 'sidebar':
                // The sidebar shoult have no offset
                $result .= 'col-' . $layoutModifierPart . $sidebarColumns;
                break;
            
            case 'content-area':
                // The content area is responsible for the offset as well
                $result .= 'col-' . $layoutModifierPart . ($totalColumns - $sidebarColumns) . ' offset-' . $layoutModifierPart . ((12 - $totalColumns) / 2);
                break;
            
            break;
        }
        
        // Store the result in cache
        Cache::set($result, $cacheKey);
        
        // All done
        return $result;
    }
    
    /**
     * Get the color names and defaults as an associative array
     * 
     * @example {"1":{"name":"Alpha", "default":"#ffffff"}, "2":{"name":"Beta", "default":"#ffffff"}, "3":{"name":"Gamma", "default":"#ffffff"}}
     * @return array 
     */
    public function getColors() {
        // Cache hit
        if (null !== $result = Cache::get()) {
            return $result;
        }
        
        // Prepare the result
        $result = array();
        
        // Go through the details
        foreach ($this->addonData as $colorKey => $configItem) {
            /* @var Model_Project_Config_Item_String $configItem */
            if (!preg_match('%^projectColor\d+Name$%', $colorKey) || !$configItem instanceof Model_Project_Config_Item_String) {
                continue;
            }

            // Prepare the color index
            $colorIndex = preg_replace('%^projectColor|Name$%', '', $colorKey);

            // Get the default value
            if (!isset($this->addonData[$colorDefaultKey = 'projectColor' . $colorIndex . 'Default'])) {
                continue;
            }

            // Get the default value
            $configItemDefault = $this->addonData[$colorDefaultKey];

            // Not a color
            if (!$configItemDefault instanceof Model_Project_Config_Item_Color) {
                continue;
            }

            // Store the value
            $result[$colorIndex] = array(
                'name'    => $configItem->getValue(),
                'default' => $configItemDefault->getWpColor(),
                'alpha'   => $configItemDefault->getAlpha(),
            );
        }

        // Store the result in cache
        Cache::set($result);
        
        // All done
        return $result;
    }
    
    /**
     * Get the font class names and defaults
     * 
     * @example {"h1":{"name":"Heading 1", "default":"Arial", "options":["Arial","Exo"], "const":"H1"}, ...}
     * @return array 
     */
    public function getFonts() {
        // Cache hit
        if (null !== $result = Cache::get()) {
            return $result;
        }
        
        // Prepare the result
        $result = array();

        // Go through the details
        foreach ($this->addonData as $fontKey => $configItem) {
            /* @var Model_Project_Config_Item_String $configItem */
            if (!preg_match('%^projectFont\w+Name$%', $fontKey) || !$configItem instanceof Model_Project_Config_Item_String) {
                continue;
            }
            
            // Prepare the font index
            $fontClass = preg_replace('%^projectFont|Name$%', '', $fontKey);

            // Get the options
            if (!isset($this->addonData[$fontOptionsKey = 'projectFont' . $fontClass . 'Options'])) {
                continue;
            }
            
            // Get the font options
            $configItemOptions = $this->addonData[$fontOptionsKey];

            // Not a font
            if (!$configItemOptions instanceof Model_Project_Config_Item_Font) {
                continue;
            }
            
            // Store the value
            $result[strtolower($fontClass)] = array(
                'name'    => $configItem->getValue(),
                'default' => current($configItemOptions->getValue()),
                'options' => $configItemOptions->getValue(),
                'const'   => strtoupper($fontClass),
            );
        }
        
        // Store the result in cache
        Cache::set($result);
        
        // All done
        return $result;
    }
    
    /**
     * Generate the PHP Code inserts needed to use the Customizer colors/fonts with our addon's custom inline CSS code
     * Supports <1.original.rgba> and <f-h1> syntaxes
     * 
     * @return array
     */
    public function getInlineCssCode() {
        // Prepare the result
        $result = array();
        
        // Get a reflection
        $reflectionClass = new ReflectionClass($this);
        
        // Prepare the allowed color palette methods
        $allowedColorPaletteMethods = array();
        
        // Prepare the allowed color palette value methods
        $allowedColorPaletteValueMethods = array();
        
        // Go through the constants
        foreach ($reflectionClass->getConstants() as $constantName => $constantValue) {
            do {
                // Color Palette method
                if (preg_match('%^COLOR_PALETTE_METHOD_%', $constantName)) {
                    $allowedColorPaletteMethods[] = $constantValue;
                    break;
                }
                
                // Color Palette value method
                if (preg_match('%^COLOR_PALETTE_VALUE_METHOD_%', $constantName)) {
                    $allowedColorPaletteValueMethods[] = $constantValue;
                    break;
                }
            } while(false);
        }
        
        // Prepare the allowed color ids
        $allowedColorIds = array();
        foreach (array_keys($this->addonData) as $coreKeyName) {
            if (preg_match('%^projectColor(\d+)Name$%', $coreKeyName, $coreKeyMatches)) {
                $allowedColorIds[] = intval($coreKeyMatches[1]);
            }
        }
        
        // Prepare the allowed font classes
        $allowedFontClasses = array();
        foreach (array_keys($this->addonData) as $coreKeyName) {
            if (preg_match('%^projectFont(\w+)Name$%', $coreKeyName, $coreKeyMatches)) {
                $allowedFontClasses[] = strtolower($coreKeyMatches[1]);
            }
        }

        // Go through all the addons
        foreach(Tasks::$project->getConfig()->getProjectData() as $addonName => $addonData) {
            // Prepare the CSS data
            $cssData = array();
            
            // Trim the "addon-" prefix
            $addonName = preg_replace('%^addon\-%', '', $addonName);
            
            // Prepare the add-on path
            $addonPath = Addons::getAddonPath($addonName);
            
            // Flavor defined
            if (isset($addonData[Model_Project_Config_Item::KEY_FLAVOR]) && $addonData[Model_Project_Config_Item::KEY_FLAVOR] instanceof Model_Project_Config_Item_String) {
                /*@var $configItem Model_Project_Config_Item_String*/
                $configItem = $addonData[Model_Project_Config_Item::KEY_FLAVOR];
                
                // Style-inline.css file found, as a replacement for the default flavor's style-inline.css
                if (is_file($addonFlavorCssPath = $addonPath . '/' . Addons::GO_FOLDER_NAME . '/' . $configItem->getValue() . '/' . Addons::FLAVOR_FILE_STYLE_INLINE)) {
                    // Store the style-inline.css file
                    $cssData[] = file_get_contents($addonFlavorCssPath);
                } else {
                    if (is_file($addonFlavorDefaultCssPath = $addonPath . '/' . Addons::GO_FOLDER_NAME . '/' . Addons::FLAVOR_NAME_DEFAULT . '/' . Addons::FLAVOR_FILE_STYLE_INLINE)) {
                        $cssData[] = file_get_contents($addonFlavorDefaultCssPath);
                    }
                }

                // Extra _style-inline.css, appended after the default flavor's style-inline.css
                if (is_file($addonFlavorExtraCssPath = $addonPath . '/' . Addons::GO_FOLDER_NAME . '/' . Addons::FLAVOR_NAME_DEFAULT . '/' . Addons::FLAVOR_FILE_STYLE_INLINE_EXTRA)) {
                    $cssData[] = file_get_contents($addonFlavorExtraCssPath);
                }
            }
            
            // Found a CSS inline style item
            if (isset($addonData[Model_Project_Config_Item::KEY_CSS_INLINE]) && $addonData[Model_Project_Config_Item::KEY_CSS_INLINE] instanceof Model_Project_Config_Item_Code) {
                // CSS extension
                if (Model_Project_Config_Item_Code::EXT_CSS === $addonData[Model_Project_Config_Item::KEY_CSS_INLINE]->getExtension()) {
                    $cssData[] = $addonData[Model_Project_Config_Item::KEY_CSS_INLINE]->getValue();
                }
            }
            
            // Prepare the addon's CSS code
            $addonCssCode = implode(PHP_EOL, array_filter(array_map('trim', $cssData)));
            
            // Valid inline code defined
            if (strlen($addonCssCode)) {
                // Parse the data keys
                $addonCssCode = Addons::getInstance()->parseDataKeys($addonCssCode, $addonName, null, Model_Project_Config_Item_Code::EXT_CSS);

                // Parse St_Colors <> keys
                $addonCssCode = preg_replace_callback(
                    '%<(\d+)(?:\.?(\w+))?(?:\.(\w+))?(?:\.(\w+))?>%', 
                    function($item) use ($addonName, $allowedColorPaletteMethods, $allowedColorPaletteValueMethods, $allowedColorIds){
                        // Prepare the color palette ID
                        $colorPaletteId = in_array($item[1], $allowedColorIds) ? $item[1] : 1;

                        // Prepare the color palette method
                        $colorPaletteMethod = isset($item[2]) ? $item[2] : self::COLOR_PALETTE_METHOD_ORIGINAL;
                        if (!in_array($colorPaletteMethod, $allowedColorPaletteMethods)) {
                            $colorPaletteMethod = self::COLOR_PALETTE_METHOD_ORIGINAL;
                        }

                        // Prepare the color palette value method
                        $colorPaletteValueMethod = isset($item[3]) ? $item[3] : self::COLOR_PALETTE_VALUE_METHOD_HEX;
                        if (!in_array($colorPaletteValueMethod, $allowedColorPaletteValueMethods)) {
                            $colorPaletteValueMethod = self::COLOR_PALETTE_VALUE_METHOD_HEX;
                        }

                        // Extra argument
                        $extraArgument = '';

                        // Extra argument provided
                        if (isset($item[4])) {
                            // Get the extra argument
                            $extraArgument = $item[4];

                            // St_Colors_Palette_Value->rgba() method
                            if (self::COLOR_PALETTE_VALUE_METHOD_RGBA === $colorPaletteValueMethod) {
                                // Must be an integer
                                $extraArgument = intval($item[4]);

                                // Force the limits
                                $extraArgument = $extraArgument < 0 ? 0 : ($extraArgument > 255 ? 255 : $extraArgument);
                            }

                            // Store the exported version
                            $extraArgument = (is_numeric($extraArgument) ? $extraArgument : var_export($extraArgument, true));
                        }

                        // Get the PHP string insert
                        return '\' . St_Colors::get()->color(' . $colorPaletteId . ')->' . $colorPaletteMethod . '()->' . $colorPaletteValueMethod . '(' . $extraArgument  .')' . ' . \'';
                    }, 
                    var_export($addonCssCode, true)
                );
                    
                // Parse St_Fonts <> keys
                $addonCssCode = preg_replace_callback(
                    '%<f\-(\w+)((?:\.\w+)*)>%', 
                    function($item) use ($addonName, $allowedFontClasses) {
                        // Get the font class
                        $fontClass = in_array($item[1], $allowedFontClasses) ? $item[1] : 'text';
                        
                        // Store the font weight required for this font class
                        Google_Fonts::storeWeight(
                            $fontClass, 
                            strlen($item[2]) ? 
                                array_filter(
                                    array_map(
                                        'trim', 
                                        explode('.', $item[2])
                                    )
                                ) 
                                : 
                                array()
                        );
                        
                        // Prepare the font class constant
                        $fontClassConstant = 'St_Fonts::FONT_CLASS_' . strtoupper($fontClass);
                        
                        // Get the PHP string insert
                        return '\' . St_Fonts::get()->family(' . $fontClassConstant . ') . \'';
                    },
                    $addonCssCode
                );
            
                // Clean-up the addon name
                $addonNameClean = ucwords(preg_replace('%\-%', ' ', $addonName));

                // Store the result in 1 line
                $result[$addonNameClean] = trim(preg_replace(array('%[\r\n] *%', '%\s*([\{\}])\s*%', '% {2,}%'), array(' ', '${1}', ' '), $addonCssCode));
            }
        }

        // All done
        return $result;
    }
    
    /**
     * General assertion test used for {if.core.testName}
     * 
     * @param string $testName Test name
     * @return boolean
     */
    public function assert($testName, $addonName = null) {
        $result = false;
        
        switch($testName) {
            
            case 'addonEnabled':
                $result = null !== Addons_Listener::get($addonName);
                break;

            case 'useStoryline':
                $result = isset($this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE]) && $this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE]->getValue();
                break;

            case 'useWidgetBlocks':
                $result = isset($this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS]) && $this->addonData[Cli_Run_Integration::OPT_PROJECT_USE_WIDGET_BLOCKS]->getValue();
                break;
            
            case 'staging':
                $result = Tasks::isStaging();
                break;
            
            case 'debugging':
                $result = AppMode::equals(AppMode::DEVELOPMENT) && Tasks::isStaging() && Log::LEVEL_DEBUG === Log::getLevel();
                break;
            
            case 'development':
                $result = AppMode::equals(AppMode::DEVELOPMENT);
                break;
            
            case 'additiveTask':
                $result = isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_ADDITIVE]);
                break;

        }
        
        return $result;
    }
    
    /**
     * Get the WordPress post/get requests timeout in seconds
     * 
     * @param int $default Default delay in live mode
     * @return int
     */
    public function getTimeout($default = 45) {
        // No delay when staging
        return Tasks::isStaging() ? 3 : intval($default);
    }
}
    
/* EOF */