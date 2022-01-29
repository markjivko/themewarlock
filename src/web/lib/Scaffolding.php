<?php
/**
 * Theme Warlock - Scaffolding
 * 
 * @title      Scaffolding
 * @desc       Create a plugin/addon from a template
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Scaffolding {

    const DATA_SLUG = 'slug';
    const DATA_TITLE = 'title';
    const DATA_DESCRIPTION = 'description';
    const DATA_YEAR = 'year';
    const DATA_VAR_NAME = 'varName';
    const DATA_CLASS_NAME = 'className';
    const DATA_PLUGIN_CONSTANT_NAME = 'pluginConstantName';
    
    /**
     * Scaffolding data
     * 
     * @var string[]
     */
    protected static $_data = array();
    
    /**
     * Run the scaffolding tool (meant for CLI)
     * 
     * @param string $templateName Template to deploy
     */
    public static function run($templateName = null) {
        try {
            self::create($templateName);
        } catch (Exception $ex) {
            Console::p($ex->getMessage(), false);
        }
    }
    
    /**
     * Run the internationalization tool (meant for CLI)
     * 
     * @param string $pluginName Plugin name
     */
    public static function i18n($pluginName = null, $pluginVersion = null) {
        Console::h1('i18n tools');
        
        // Prepare the options
        $pluginOptions = array();

        // Go through the scaffolding resources
        foreach(glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS . '/*', GLOB_ONLYDIR) as $pluginPath) {
            // Save the option
            if (!isset($pluginOptions[basename($pluginPath)])) {
                $pluginOptions[basename($pluginPath)] = ucwords(preg_replace('%\W+%', ' ', basename($pluginPath)));
            }
        }
        
        // Plugin not specified or invalid
        if (!is_string($pluginName) || !strlen($pluginName) || !isset($pluginOptions[$pluginName])) {
            do {
                // One result
                if (1 === count($pluginOptions)) {
                    // Get the first result
                    $pluginName = current(array_keys($pluginOptions));
                    
                    // Inform the user
                    break;
                } 

                // Get the desired template
                $pluginName = Console::options($pluginOptions, 'Choose one of the following plugins');
            } while (false);
        }
        
        // Get the plugin version
        do {
            if (preg_match('%^(?:\d+\.)+\d+$%', $pluginVersion)) {
                break;
            }
            
            // Load the file
            if (is_file($pluginGoPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS . '/' . $pluginName . '/go.php')) {
                require_once $pluginGoPath;
                
                // Prepare the plugin class name
                $pluginClassName = 'Plugin_' . implode(
                    '', 
                    array_map(
                        function($item){
                            return ucfirst(strtolower($item));
                        }, 
                        preg_split('%\W+%', $pluginName)
                    )
                );
                
                if (class_exists($pluginClassName)) {
                    /* @var $pluginClassInstance Plugin */
                    $pluginClassInstance = new $pluginClassName($pluginName, null);
                
                    // Get the plugin version
                    $pluginVersion = $pluginClassInstance->getVersion();
                    break;
                }
            }

            // Plugin version not defined
            $pluginVersion = trim(Input::get('Please specify plugin version'));
        } while(true);

        // Log the action
        Console::h2('Internationalization of <' . $pluginOptions[$pluginName] . '>');
        
        // Update the changelog date
        self::_i18nChangelogDate($pluginName, $pluginVersion);

        // Custom pre-extraction tools
        switch ($pluginName) {
            case Plugin::PLUGIN_RPG:
                self::_i18nRpg();
                break;
        }
        
        // Prepare the plugin path
        $pluginPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS . '/' . $pluginName;
        
        // Extract the ".pot" file
        $portableObjectTemplate = (new WordPress_Pot())->extract(
            $pluginPath,
            
            // Set the right text domain to look for
            $pluginName,
            
            // Store strings inside the theme
            $pluginPath . '/languages/' . $pluginName . '.pot',
            
            // Custom version
            $pluginVersion
        );
        
        // Get the languages
        $coreLanguagePaths = glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . Model_Project_Config::CATEGORY_CORE . '/languages/*.po');
        
        // Import the languages
        foreach ($coreLanguagePaths as $coreLanguageKey => $coreLanguagePath) {
            // Create a new Portable Object for this language
            $portableObjectLanguage = new WordPress_Pot_Translations_Po();
            
            // Import from file
            $portableObjectLanguage->importFromFile($coreLanguagePath);
            
            // Clear old entries
            $portableObjectLanguage->entries = array();
            
            // Merge it with the .pot file
            $portableObjectLanguage->mergeOriginalsWith($portableObjectTemplate, true);

            // Load the translations from cache
            $portableObjectLanguage->updateEntriesFromCache();

            // Export the object as ".po"
//            $portableObjectLanguage->exportToFile($pluginPath . '/languages/' . $pluginName . '-' . basename($coreLanguagePath));

            // Export the object as ".mo"
            (new WordPress_Pot_Translations_Mo())
                ->importFromPo($portableObjectLanguage)
                ->exportToFile($pluginPath . '/languages/' . $pluginName . '-' . basename($coreLanguagePath, '.po') . '.mo');
            
            PercentBar::display(100 * ($coreLanguageKey + 1) / count($coreLanguagePaths));
        }
        
        echo PHP_EOL;
    }
    
    /**
     * Update the changelog date for this plugin release
     * 
     * @param string $pluginName    Plugin Slug
     * @param string $pluginVersion Plugin Version
     */
    protected static function _i18nChangelogDate($pluginName, $pluginVersion) {
        // Get the readme file path
        $readmePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS 
            . '/' . $pluginName . '/readme.txt';
        
        // File found
        if (is_file($readmePath)) {
            // Update the version release date
            $readmeContents = preg_replace(
                '%^(\s*\=\s*\[\s*' . preg_quote($pluginVersion) . '\s*\]).*?\=%ims', 
                '$1 ' . date('Y-m-d') . ' =', 
                file_get_contents($readmePath)
            );
            
            // Rewrite the file
            file_put_contents($readmePath, $readmeContents);
        }
    }
    
    /**
     * Create the i18n.php file for RPG
     */
    protected static function _i18nRpg() {
        // Get the default configuration file path
        $configDefaultPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS 
            . '/rpg/themes/default/config.json';
        
        // Get the configuration data
        $configDefault = is_file($configDefaultPath)
            ? json_decode(trim(file_get_contents($configDefaultPath)), true)
            : array();

        // Invalid format
        if (!is_array($configDefault)) {
            return;
        }
        
        // Prepare the strings
        $strings = array();
        foreach ($configDefault as $configSection => $configSectionData) {
            foreach ($configSectionData as $csdKey => $csdValue) {
                if (is_array($csdValue)) {
                    foreach ($csdValue as $itemKey => $itemValue) {
                        if (is_string($itemValue)
                            && strlen($itemValue)
                            && preg_match('%^(?:name|description|story)$%i', $itemKey)) {
                            $strings[$configSection . '.' . $csdKey . '.' . $itemKey] = $itemValue;
                        }
                    }
                } else {
                    if (is_string($csdValue) 
                        && strlen($csdValue) 
                        && preg_match('%(?:\w+name|description)$%i', $csdKey)) {
                        $strings[$configSection . '.' . $csdKey] = $csdValue;
                    }
                }
            }
        }
        
        // Get the max key width
        $maxKeyWidth = max(array_map('strlen', array_keys($strings)));
        
        // Prepare the exported strings
        $stringsExported = '[' . PHP_EOL;
        foreach ($strings as $key => $value) {
            $padding = str_repeat(' ', $maxKeyWidth - strlen($key));
            
            // Sanitize the values
            $escapedValue = preg_replace(
                array('%[\'’]%u', '%[“”]%u', '%[…]%u', '%\r\n%'), 
                array("\'", '"', '...', "\n"), 
                $value
            );
            $stringsExported .= "    '$key' $padding => __('$escapedValue', 'rpg')," . PHP_EOL;
        }
        $stringsExported .= ']';
        
        // Prepare the exported strings
        $phpContents = <<<"CODE"
<?php
/**
 * Configuration Internationalization
 * 
 * @title      Configuration i18n
 * @desc       Exported strings from configuration for i18n
 * @copyright  (c) {config.authorYear}, {config.authorName}
 * @author     Mark Jivko, https://markjivko.com
 * @package    rpg
 * @license    GPL v3+, gnu.org/licenses/gpl-3.0.txt
 */
!defined('RPG_ROOT') && exit();
 
/**
 * Array of configuration translations
 */
\$rpg_i18n = $stringsExported;

/*EOF*/
CODE;
        
        // Store the data
        file_put_contents(dirname($configDefaultPath) . '/i18n.php', $phpContents);
    }
    
    /**
     * Deploy a particular template
     * 
     * @param string $templateName Template name
     * @throws Exception
     */
    public static function create($templateName = null) {
        Console::h1('Scaffolding tools');
        
        // Prepare the options
        $templateOptions = array();

        // Go through the scaffolding resources
        foreach(glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCAFFOLDS . '/*/*', GLOB_ONLYDIR) as $templatePath) {
            // Prepare the template type
            $templateType = basename(dirname($templatePath));

            // Save the option
            if (!isset($templateOptions[basename($templatePath)])) {
                $templateOptions[basename($templatePath)] = ucfirst(basename($templatePath)) . ' - ' . (Framework::FOLDER_ADDONS == $templateType ? 'Theme Warlock Add-On' : 'WordPress Plug-In');
            } else {
                $templateOptions[basename($templatePath)] .= ' & ' . (Framework::FOLDER_ADDONS == $templateType ? 'Theme Warlock Add-On' : 'WordPress Plug-In');
            }
        }

        // Nothing found
        if (!count($templateOptions)) {
            throw new Exception('No templates available!');
        }
        
        // Template not specified or invalid
        if (!is_string($templateName) || !strlen($templateName) || !isset($templateOptions[$templateName])) {
            do {
                // One result
                if (1 === count($templateOptions)) {
                    // Get the first result
                    $templateName = current(array_keys($templateOptions));
                    
                    // Inform the user
                    break;
                } 

                // Get the desired template
                $templateName = Console::options($templateOptions, 'Choose one of the following templates');
            } while (false);
        }
        
        // Inform the user
        Console::h2('Template "' . $templateOptions[$templateName] . '"');

        // Prepare the template paths
        $templatePaths = array();
        
        // Go through the scaffold types
        foreach (array(Framework::FOLDER_ADDONS, Framework::FOLDER_PLUGINS) as $scaffoldType) {
            if (is_dir($templatePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCAFFOLDS . '/' . $scaffoldType . '/' . $templateName)) {
                $templatePaths[$scaffoldType] = $templatePath;
            }
        }
        
        // Nothing found
        if (!count($templatePaths)) {
            throw new Exception('Addon/plugin template "' . $templateName . '" not found');
        }
        
        // Prepare the data
        self::$_data = array();
        
        // Gather data
        foreach (array(
            self::DATA_SLUG, 
            self::DATA_TITLE, 
            self::DATA_DESCRIPTION
        ) as $dataKey) {
            Console::p('Please type the addon/plugin\'s ' . $dataKey . ':');
            do {
                // Store the value
                self::$_data[$dataKey] = trim(Input::get());
                
                // Slug validation
                if ($dataKey == self::DATA_SLUG) {
                    if (preg_match('%^[a-z][a-z0-9\-]*(?<!\-)$%', self::$_data[$dataKey])) {
                        break;
                    }
                }
                
                // Validate the item length
                if (strlen(self::$_data[$dataKey])) {
                    break;
                }
            } while (true);
        }
        
        // Set the slug
        self::$_data[self::DATA_SLUG] = $templateName . '-' . self::$_data[self::DATA_SLUG];
        
        // Prepare the extras
        self::$_data[self::DATA_YEAR] = date('Y');
        self::$_data[self::DATA_VAR_NAME] = preg_replace('%\W+%', '_', self::$_data[self::DATA_SLUG]);
        self::$_data[self::DATA_CLASS_NAME] = preg_replace('% %', '', ucwords(preg_replace('%\W+%', ' ', self::$_data[self::DATA_SLUG])));
        self::$_data[self::DATA_PLUGIN_CONSTANT_NAME] = 'PLUGIN_' . strtoupper(preg_replace('%[\-]+%', '_', self::$_data[self::DATA_SLUG]));
        
        // Transfer the files
        self::_transferFiles($templatePaths);
    }
    
    /**
     * Transfer the files to their final destination
     * 
     * @param string   $templatePaths Template paths
     */
    protected static function _transferFiles($templatePaths) {
        foreach ($templatePaths as $templatePath) {
            // Get the template type, Framework::FOLDER_ADDONS or Framework::FOLDER_PLUGINS
            $templateType = basename(dirname($templatePath));
            
            // Prepare the destination path
            $destinationPath = ROOT . '/web/' . Framework::FOLDER . '/' . $templateType . '/' . (Framework::FOLDER_ADDONS == $templateType ? 'core-' : '') . self::$_data[self::DATA_SLUG];
            
            // Inform the user
            Console::h2('Deploying "frameworks/' . $templateType . '"...');
            
            // Go through the files
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($templatePath, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
                // Prepare the new location
                $newLocation = $destinationPath . '/' .  str_replace(array('/', '\\'), '/', $iterator->getSubPathName());

                // Parse the data keys for file names
                if (preg_match('%\{scaffold\.\w+\}%', $newLocation)) {
                    $newLocation = self::_parseKeys($newLocation);
                }

                // Directory
                if ($item->isDir()) {
                    if (!is_dir($newLocation)) {
                        Folder::create($newLocation, 0777, true);
                    }
                } else {
                    // File (overwrite), Never copy Thumbs.db
                    if ('Thumbs.db' !== basename($item) && false === strpos(basename($item), ' - Copy')) {
                        // Do not copy PSD files over
                        if (preg_match('%\.psd%i', basename(strval($item)))) {
                            continue;
                        }
                        
                        // Parse the file
                        self::_parse(strval($item), $newLocation);
                        
                        // Log the event
                        Console::p('> Parsed "' . $newLocation . '"');
                    } else {
                        @unlink($item);
                    }
                }
            }
            
            // Plugin
            if (Framework::FOLDER_PLUGINS === $templateType) {
                // Prepare the Plugin.php file path
                $pluginFilePath = ROOT . '/web/lib/Plugin.php';
                
                // Append the plugin constant
                file_put_contents(
                    $pluginFilePath, 
                    preg_replace(
                        '%const\s+PLUGIN_THEME_CHECK\b.*?\;%', 
                        '${0}' . PHP_EOL . 
                        '    const ' . self::$_data[self::DATA_PLUGIN_CONSTANT_NAME] . ' = ' . var_export(self::$_data[self::DATA_SLUG], true) . ';', 
                        file_get_contents($pluginFilePath)
                    )
                );
                
                // Log the event
                Console::p('> Added constant ' . self::$_data[self::DATA_PLUGIN_CONSTANT_NAME] . ' to Plugin.php');
            }
        }
        
        // All done
        Console::h1('Done!');
        Console::p('Remember to fix the @' . strtoupper(implode('', array('fix', 'me'))) . ' tags!');
    }
    
    /**
     * Parse the {scaffold.*} tags
     * 
     * @param string $from Source file path
     * @param string $to   Destination file path
     * @return type
     */
    protected static function _parse($from, $to) {
        // Create the destination
        if (!is_dir(dirname($to))) {
            Folder::create(dirname($to), 0777, true);
        }

        // Modify a few files
        if (!preg_match('%\.(php|phtml|html|xhtml|css|js|cfg|txt)$%i', $from)) {
            copy($from, $to);
            return;
        }
        
        // Parse the data keys and the actions
        $result = self::_parseKeys(
            file_get_contents($from), 
            preg_replace('%^.*?\.(\w+)$%', '${1}', basename($from))
        );

        // Save the text
        file_put_contents($to, $result);
        
        // All done
        return $result;
    }
    
    /**
     * Replace the scaffold tags
     * 
     * @param string $text          Text to parse
     * @param string $fileExtension File extension
     * @return string
     */
    protected static function _parseKeys($text, $fileExtension = null) {
        // Replace the {scaffold.*} tags
        return preg_replace_callback('%{(?P<tree>scaffold\.\w+)}%i', function($item) use($fileExtension){
            // Get the extra items
            $tree = array_filter(array_map('trim', explode('.', $item['tree'])));

            // Get the data point
            $dataPoint = array_shift($tree);

            // Set the escaping
            $dataPointForcedUnescape = (strtoupper($dataPoint[0]) == $dataPoint[0]);
            
            // Get the data key
            $dataKey = array_shift($tree);
            
            // Not found
            if (!isset(self::$_data[$dataKey])) {
                Console::p('Scaffolding key "' . $dataKey . '" not found', false);
                return $item[0];
            }
            
            // Forced non-escaping
            if ($dataPointForcedUnescape) {
                return self::$_data[$dataKey];
            }

            switch ($fileExtension) {
                case 'php':
                case 'phtml':
                    return var_export(self::$_data[$dataKey], true);
                    break;

                case Model_Project_Config_Item_Code::EXT_JS:
                case Model_Project_Config_Item_Code::EXT_JSON:
                    return json_encode(self::$_data[$dataKey]);
                    break;
            }
        }, $text);
    }

}

/* EOF */