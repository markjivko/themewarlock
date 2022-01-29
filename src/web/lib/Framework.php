<?php

/**
 * Theme Warlock - Framework
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Framework descriptor
 */
class Framework {

    /**
     * Frameworks root folder name
     */
    const FOLDER = 'frameworks';
    
    /**
     * Addons: Configurable from the web UI
     */
    const FOLDER_ADDONS    = 'addons';
    
    /**
     * Plugins: Includable with every theme
     */
    const FOLDER_PLUGINS   = 'plugins';
    
    /**
     * Scaffolds: Templates to generate common addons
     */
    const FOLDER_SCAFFOLDS = 'scaffolds';
    
    /**
     * Scripts: CSS/JS scripts includable with every theme
     */
    const FOLDER_SCRIPTS   = 'scripts';
    
    /**
     * Types: Framework types
     */
    const FOLDER_TYPES     = 'types';
    
    /**
     * UI Sets: Core Bootstrap themes
     */
    const FOLDER_UI_SETS   = 'ui-sets';
    
    /**
     * Target: One-Page template
     */
    const TARGET_ONEPAGE = 'One Page';

    /**
     * Framework l1
     */
    const ID_ONEPAGE = 'onepage';

    /**
     * Directory structure for the selected project
     * 
     * @var Directory_Structure
     */
    public static $structure;

    /**
     * Framework target - framework IDs association
     * 
     * @var array
     */
    public static $targetIds = array(
        self::TARGET_ONEPAGE => array(
            self::ID_ONEPAGE,
        ),
    );
    
    /**
     * Return the list of available framework types and their description
     * 
     * @return string
     */
    public static function getAll($asArray = false) {
        // Prepare the result
        $result = $asArray ? array() : '';

        // Go through all the available framework types
        foreach (self::_getAllIds() as $id) {
            $data = self::getFrameworkInfo($id, $asArray, $asArray);
            if ($asArray) {
                $result[$id] = $data;
            } else {
                $result .= ($data !== false ? '   ' . $data : self::errorNoInfoString($id)) . PHP_EOL;
            }
        }

        // Return the result
        return $result;
    }
    
    /**
     * Return information about a specific framework by ID
     * 
     * @param string  $id       Framework ID
     * @param boolean $detailed Detailed
     * @param boolean $asArray  As array
     * @return string|boolean False on failure
     */
    public static function getFrameworkInfo($id, $detailed = false, $asArray = false) {
        // Get the $info
        if (file_exists($infoFile = ROOT . '/web/' . Framework::FOLDER . '/' . self::FOLDER_TYPES . '/' . $id . '/info.php')) {
            // Get the information file
            require $infoFile;

            // Set the framework ID
            $info[Cli_Run_Integration::FRAMEWORK_ID] = $id;

            // Return the information as array
            if ($asArray) {
                return $info;
            }
        } else {
            return false;
        }

        // Prepare the short description
        $shortDescription = sprintf(
            '%s %s theme', str_pad('[' . $id . ']', 7, ' ', STR_PAD_RIGHT), $info[Cli_Run_Integration::FRAMEWORK_TARGET]
        );

        // Quick view
        if (!$detailed) {
            return $shortDescription;
        }

        // Get the structure
        if (isset($info[Directory_Structure::LAYOUT])) {
            // Get the structure
            self::$structure = $info[Directory_Structure::LAYOUT];

            // Return the description
            return '   ' . $shortDescription
                . PHP_EOL . PHP_EOL
                . '   Directory structure' . PHP_EOL
                . '   ' . str_repeat('=', 19)
                . PHP_EOL . PHP_EOL
                . self::$structure->describe();
        }

        // No structure defined
        return '   ' . $shortDescription
            . PHP_EOL . PHP_EOL
            . '   ! No custom directory structure defined';
    }

    /**
     * Get the corresponding class name for the given framework target
     * 
     * @param string $frameworkTarget Framework target
     * @param string $suffix          (Optional) Sub-class, no spaces
     * @return string Corresponding class name
     */
    public static function getFrameworkClass($frameworkTarget, $suffix = null) {
        // Get the class name
        $className = $frameworkTarget;

        // Class name contains a space
        if (false !== strpos($className, ' ')) {
            $className = implode('', array_map(function($item) {
                return ucfirst(strtolower(trim($item)));
            }, explode(' ', $className)));
        }
        
        // Append the suffix
        if (!empty($suffix)) {
            $className .= '_' . $suffix;
        }
        
        // All done
        return 'Framework_' . $className;
    }
    
    /**
     * Get all available framework IDs
     * 
     * @return array
     */
    protected static function _getAllIds() {
        return array_map('basename', glob(ROOT . '/web/' . Framework::FOLDER . '/' . self::FOLDER_TYPES . '/*', GLOB_ONLYDIR));
    }

    /**
     * Error to display when framework info.php file is not found
     * 
     * @param string $id Framework ID
     * @return string
     */
    public static function errorNoInfoString($id) {
        return ' ! ' . str_pad('[' . $id . ']', 4, ' ', STR_PAD_RIGHT) . ' Framework does not have an info.php file';
    }
}

/*EOF*/