<?php

/**
 * Theme Warlock - Config
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Config {

    /**
     * Full configuration array
     * 
     * @var array
     */
    protected static $_config;

    /**
     * Configuration object
     * 
     * @var stdClass
     */
    protected static $_configObject;
    
    /**
     * Return the configuration array
     * 
     * @param string $object Return as object
     * @return Config_Items
     */
    public static function get($object = true) {
        if (!isset(self::$_config)) {
            // Get the private configuration file
            $privateConfig = file_exists(ROOT . '/web/resources/_private/config.ini') 
                ? file_get_contents(ROOT . '/web/resources/_private/config.ini') 
                : '';

            // No config.ini, copy the sample
            if (!file_exists(ROOT . '/web/config/config.ini')) {
                // Copy the sample over
                copy(ROOT . '/web/config/config.sample.ini', ROOT . '/web/config/config.ini');

                // Configuration clean-up (first time)
                self::_configTidy(true);
            } else {
                // Configuration clean-up
                self::_configTidy();
            }

            // Get the INI string
            $iniString = file_get_contents(ROOT . '/web/config/config.ini') . PHP_EOL . PHP_EOL . $privateConfig;

            // Get the configuration array
            if (false === $config = parse_ini_string($iniString, true, INI_SCANNER_RAW)) {
                throw new Exception('Invalid configuration file');
            }

            // Organize the sections
            $config = self::_organizeSections($config);

            // Get the session account
            if (strlen($sessionAccount = WordPress_Session::getInstance()->getAccount())) {
                $config['use'] = $sessionAccount;
            }

            // Save the configuration
            self::$_config = $config;

            // Save the configuration as object
            self::$_configObject = json_decode(json_encode($config), false);

            // Check if the "use" term is provided
            if (!isset($config['use'])) {
                throw new Exception('Config not formatted correctly. Could not find the "use" keyword.');
            }

            // Check if the "use" account is defined
            if (!isset($config[$config['use']])) {
                throw new Exception('Config not formatted correctly. Could not find the "' . $config['use'] . '" account.');
            }
        }
        
        // Return the information
        return $object ? self::$_configObject->{self::$_configObject->use} : self::$_config[self::$_config['use']];
    }

    /**
     * Compare the configuration tool
     * 
     * @param boolean $firstTime First-time config tidy
     * @return null
     */
    protected static function _configTidy($firstTime = false) {
        // Clear the file cache
        clearstatcache();
        
        // The config.ini is newer than the config.sample.ini
        if(!$firstTime && filemtime(ROOT . '/web/config/config.ini') > filemtime(ROOT . '/web/config/config.sample.ini')) {
            // Nothing to do
            return;
        }
        
        // Inform the user
        Console::p('Config update...');
        
        // Get the original config data
        $configData = parse_ini_file(ROOT . '/web/config/config.ini', true, INI_SCANNER_RAW);
        
        // Get the sample config data
        $configSampleData = parse_ini_file(ROOT . '/web/config/config.sample.ini', true, INI_SCANNER_RAW);
        
        // Get the original contents
        $configContents = file_get_contents(ROOT . '/web/config/config.ini');
        
        // Get the final contents
        $configFinalContents = file_get_contents(ROOT . '/web/config/config.sample.ini');
        
        // Config defaults
        $configDefaultsInstance = Config_Defaults::getInstance();
        
        // Config validation
        $configValidationInstance = Config_Validation::getInstance();
        
        // Prepare the counter
        $counter = 0;
        
        // Prepare the total
        $total = count($configSampleData, true);
        
        // Put back the already defined values
        foreach ($configSampleData as $section => $data) {
            // Proccess sections
            if (is_array($data)) {
                foreach ($data as $param => $value) {
                    // Get the previously defined value
                    $value = isset($configData[$section]) && isset($configData[$section][$param]) ? $configData[$section][$param] : $value;
                    
                    // Did not find the parameter
                    if (!preg_match('%(\[\s*' . $section . '.*?\].*?\n' . $param . ')\s*=\s*.*?\n%ms', $configFinalContents)) {
                        // Get the new parameter value
                        $newParamValue = self::_iniValue($value, $type, $param, $configContents);

                        // Get the new parameter description
                        $newParamDesc = <<<"DESC"
;;
; $param
; 
; @var $type
;;
$param = $newParamValue
DESC;

                        // Append to the section
                        $configFinalContents = preg_replace('%(\[\s*' . $section . '\b.*?\].*?\n)%', '$1' . PHP_EOL . $newParamDesc . PHP_EOL . PHP_EOL, $configFinalContents);
                    } else {
                        // Empty value
                        if (empty($value)) {
                            $configDefaultsInstance->getDefault($param, $value);
                        } else {
                            // Validate the parameter
                            $configValidationInstance->validate($param, $value);
                        }

                        // Get the ini value
                        $iniValue = self::_iniValue($value, $type, $param, $configFinalContents);

                        // Replace the value
                        $configFinalContents = preg_replace('%(\[\s*' . $section . '\b.*?\].*?\n' . $param . ')\s*=\s*.*?\n%ms', '$1 = ' . $iniValue . PHP_EOL, $configFinalContents);
                    }
                }
            } else {
                // Proccess individual values
                $data = isset($configData[$section]) ? $configData[$section] : $data;
                                
                // Empty value
                if (empty($data)) {
                    $configDefaultsInstance->getDefault($section, $data);
                } else {
                    // Validate the parameter
                    $configValidationInstance->validate($section, $data);
                }
                
                // Get the ini value
                $iniValue = self::_iniValue($data, $type, $section, $configFinalContents);

                // Replace the value
                $configFinalContents = preg_replace('%\b' . $section . '\b\s*=.*?\n%', $section . ' = ' . $iniValue . PHP_EOL, $configFinalContents);
            }
        }

        // Save the contents
        Console::p('Tidied up the config.ini');
        file_put_contents(ROOT . '/web/config/config.ini', $configFinalContents);
        
        // Prepare the code
        $phpCode = '';
        
        // Go through the config
        if (preg_match_all('%^\;\;\s*(.*?)^\;\;\s*^(\w+)\s*=\s*.*?\s*$%ims', $configFinalContents, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                list(, $description, $name) = $match;
                if ('use' === $name) {
                    continue;
                }
                
                // Clean-up the description
                $description = preg_replace('%^\s*\;\s*%m', '', $description);
                
                // Get the descriptors
                $descriptors = array();
                
                // Remove the descriptors from the description
                $description = trim(preg_replace_callback('%\s*\@(\w+)\s*(.*?)\s*$%m', function($item) use(&$descriptors) {
                    // Lowercase
                    if ('var' == $item[1]) {
                        $item = array_map('strtolower', $item);
                    }
                    
                    // Save the descriptor
                    $descriptors[$item[1]] = $item[2];
                    
                    // Remove the entry
                    return '';
                }, $description));
                
                // Prepare the new descriptors
                $descriptorsString = '';
                foreach ($descriptors as $descKey => $descValue) {
                    $descriptorsString .= '     * @' . $descKey . ' ' . $descValue . PHP_EOL;
                }
                $descriptorsString = rtrim($descriptorsString);
                
                // Append the code
                $phpCode .= <<<"CODE"
    
    /**
     * $description
     * 
$descriptorsString
     */
    public \$$name;
    
CODE;
                
            }
        }
        
        // Store the PHP code
        $phpContents = file_get_contents(ROOT . '/web/lib/Config/Items.php');
        
        // Replace the contents
        $phpContents = preg_replace(
            '%class\s+Config_Items \s*\{.*?\}%ims', 
            'class Config_Items {' . PHP_EOL . $phpCode . PHP_EOL . '}', 
            $phpContents
        );
        
        // Save the file
        file_put_contents(ROOT . '/web/lib/Config/Items.php', $phpContents);
    }
    
    /**
     * Get a value formatted for the ini file
     * 
     * @param mixed  $data        Ini file parameter value
     * @param string &$type       Parameter type
     * @param string $paramName   Parameter name
     * @param string $iniContents Ini file contents
     * @return string
     */
    protected static function _iniValue($data, &$type, $paramName = null, $iniContents = null) {
        // Get the defined type from the comments
        $definedType = null;
        if (!is_null($paramName) && !is_null($iniContents)) {
            // Defined a type for this parameter
            if (preg_match('%;\s*@var\s+(\w+)(?!.*@var.*?\b' . $paramName . '\b).*?\b' . $paramName . '\b%ms', $iniContents, $matches)) {
                $definedType = trim(strtolower($matches[1]));
            }
        }

        // Get the type
        $type = !is_null($definedType) ? $definedType : gettype($data);
        
        // Get the type
        switch ($type) {
            case 'boolean':
            case 'bool':
            case 'int':
            case 'integer':
            case 'float':
            case 'double':
            case 'size':
                $iniValue = $data;
                break;

            case 'string':
                $iniValue = is_numeric($data) ? $data : '"' . $data . '"';
                break;

            default:
                $iniValue = '"' . strval($data) . '"';
        }
        
        // All done
        return $iniValue;
    }
    
    /**
     * Get the current author; this is also the "use" keyword
     * 
     * @return string Author section (same as folder name in resources/_private)
     */
    public static function getUse() {
        // Get the configuration
        self::get();

        // Return the "use" parameter
        return self::$_config['use'];
    }

    /**
     * Organize the array by sections, implementing inheritance
     * 
     * @param array $config Original configuration array
     * @return array
     */
    protected static function _organizeSections($config) {
        // Prepare the result
        $result = array();

        // Store the parents
        $parents = array();

        // Go through each section
        foreach ($config as $section => $values) {
            // Not a section
            if (is_array($values)) {
                // Get the configuration name and parent
                if (false !== strpos($section, ':')) {
                    // Override the section name, removing the parent particle
                    list($section, $parentSection) = array_map('trim', explode(':', $section));

                    // Store the inheritance
                    $parents[] = array($section, $parentSection);
                }
            }
            $result[$section] = $values;
        }

        // Implement the inheritance
        foreach ($parents as $rule) {
            $result[$rule[0]] = $result[$rule[0]] + $result[$rule[1]];
        }
        
        // Get the sample contents
        $sampleContents = file_get_contents(ROOT . '/web/config/config.sample.ini');
        
        // Prepare the defaults for some elements
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                foreach($value as $k => $v) {
                    // Set the boolean values
                    if(in_array($v, array('yes', 'no'), true)) {
                        // Check that the sample did not define this as a string
                        if (!preg_match('%' . $k . '\s*=\s*[\'"]%', $sampleContents)) {
                            $v = ($v == 'yes');
                        }
                    }
                    
                    // Overwrite the values
                    $result[$key][$k] = $v;
                }
            }
        }

        // Return the result
        return $result;
    }
    
    /**
     * Get the standard INI size string as an integer
     * 
     * @return float Size in bytes
     */
    public static function getSizeToFloat($size) {
        return floatval($size) * pow(1024, strpos(' kmgtpezy', strtolower(substr(preg_replace('%b$%i', '', $size), -1))));
    }
    
    /**
     * Get the WordPress sandbox path for the current user
     * 
     * @param boolean $getDefault Get the default sandbox path (from config.ini)
     * @return string Path without trailing slash
     */
    public static function getWpPath($getDefault = false) {
        // Prepare the result
        $result = rtrim(self::get(true)->wpPath, '\\/');
        
        do {
            // Only the default is needed
            if ($getDefault) {
                break;
            }

            // Suffix
            $result .= '-' . WordPress_Session::getInstance()->getName();
        } while(false);
        
        return $result;
    }
    
    /**
     * Get the WordPress database name
     * 
     * @param boolean $getDefault Get the default database name (from config.ini)
     * @return string
     */
    public static function getWpDbName($getDefault = false) {
        // Prepare the result
        $result = self::get(true)->wpDbName;
        
        // Only the default is needed
        if ($getDefault) {
            return $result;
        }
        
        // Get the session
        $wordpressSession = WordPress_Session::getInstance();
        
        // Suffix
        $result .= '_' . $wordpressSession->getName();
        
        // All done
        return $result;
    }
    
    /**
     * Get the path to "wp-content/themes" in the WordPress website
     * 
     * @return string Path without trailing slash
     */
    public static function getWpThemesPath($getDefault = false) {
        // Get the WordPress path
        $wordpressPath = self::getWpPath($getDefault);
        
        // All done
        return rtrim($wordpressPath, '\\/') . '/wp-content/themes';
    }
    
    /**
     * Get the projects path
     * 
     * @return string Path without trailing slash
     */
    public static function getProjectsPath() {
        // Get the session
        $wordpressSession = WordPress_Session::getInstance();
        
        // All done
        return dirname(ROOT) . '/' . basename(ROOT) . '-projects/' . $wordpressSession->getName();
    }
}

/*EOF*/