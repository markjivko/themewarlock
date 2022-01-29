<?php

/**
 * Create the new project
 * 
 */
class Tasks_1NewProject {
    
    /**
     * Source directory name
     * 
     * @var string
     */
    public static $sourceDir = '';

    /**
     * Destination directory name
     * 
     * @var string
     */
    public static $destDir = '';

    /**
     * Destination prefix (for function & variable names)
     * 
     * @var string
     */
    public static $prefix = '';

    /**
     * Destination Lowercase author name - for the theme folder name
     * 
     * @var string
     */
    public static $destAuthorName = '';

    /**
     * Destination project name - for the theme folder name
     * 
     * @var string
     */
    public static $destProjectName = '';
    
    /**
     * Actions
     * 
     * @return null
     */
    public function v1() {
        // Copy the folders, get the directory info
        $this->_copyFolders();
    }

    /**
     * Populate the current project information
     * 
     * @param string $specificProject Path to specific project
     * @throws Exception
     */
    public static function getProjectInfo($specificProject = null) {
        // Valid specific project
        if (null !== $specificProject) {
            // No run.csv defined
            if (!file_exists($runPath = $specificProject . '/run.csv')) {
                return null;
            }
            
            // Get the run data
            $runData = Csv::getData($runPath);
            
            // Get the framework id
            $frameworkId = trim(current(explode(',', $runData[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK])));
            
            // Get the project name
            $projectName = $runData[Cli_Run_Integration::OPT_PROJECT_NAME];
            
            // Get the framework info
            $frameworkInfo = Framework::getFrameworkInfo($frameworkId, true, true);
            
            // Get the other information
            $frameworkTarget = $frameworkInfo[Cli_Run_Integration::FRAMEWORK_TARGET];
        } else {
            // Get the defaults
            list($frameworkId, $frameworkTarget, $projectName) = array(
                Tasks::$config[Cli_Run_Integration::FRAMEWORK_ID], 
                Tasks::$config[Cli_Run_Integration::FRAMEWORK_TARGET], 
                Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_NAME],
            );
        }
        
        $dirs = glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_TYPES . '/' . $frameworkId . '/src', GLOB_ONLYDIR);
        if (false == $dirs || !count($dirs)) {
            // TODO Fix this
            throw new Exception('Source directory not found for framework "' . $frameworkId . '"!');
        }
        
        // Source directory
        self::$sourceDir = substr($dirs[0], strrpos($dirs[0], '/') + 1);

        // Set the author name (or a custom one for the APK)
        self::$destAuthorName = strtolower(Config::get()->authorName);

        // Set the application name
        self::$destProjectName = $projectName;
        self::$destDir = self::getDestDir($projectName);
        self::$prefix = str_replace('-', '_', self::$destDir);

        // Log the information collected so far
        Log::check(Log::LEVEL_INFO) && Log::info(array(
            'sourceDir'         => self::$sourceDir,
            'destDir'           => self::$destDir,
            'destAuthorName'    => self::$destAuthorName,
            'destProjectName'   => self::$destProjectName,
        ));
    }
    
    /**
     * Get the destination directory name OR style.css theme name. <br/>
     * Contains the <b>-</b> (dash) character for Directory Mode, space characters otherwise.
     * 
     * @param string  $projectName Framework Name
     * @param boolean $dirMode     True for directory name mode, false for style.css mode
     * @return string
     */
    public static function getDestDir($projectName, $dirMode = true) {
        // Prepare the result
        $result = preg_replace(
            '%' . ($dirMode ? '\-' : ' ') . '{2,}%', 
            $dirMode ? '-' : ' ', 
            preg_replace(
                '%(\W)%', 
                $dirMode ? '-' : ' ', 
                Config::get()->authorName . ' ' . $projectName
            )
        );
        
        // All done
        return $dirMode ? strtolower($result) : $result;
    }
    
    public static function getSynopsys() {
        // Get the synopsis maximum length
        $maxLength = intval(Config::get()->gPlayMaxSynopsisLength);
        
        // Prepare the synopsis format
        $synopsisFormat = Config::get()->synopsisFormat;
        
        // Get a custom, theme-specific format
        if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_SYNOPSIS_FORMAT]) && strlen(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_SYNOPSIS_FORMAT])) {
            $synopsisFormat = Cli_Run_Integration::$options[Cli_Run_Integration::OPT_SYNOPSIS_FORMAT];
        }
        
        do {
            // Get the actual synopsys
            $synopsis = str_replace(array(
                '__NAME__',
                '__FRAMEWORK__',
                '__AUTHOR__',
            ), array(
                Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_NAME],
                Framework::getTargetSeo(Tasks::$config[Cli_Run_Integration::FRAMEWORK_TARGET]),
                Config::get()->authorName,
            ), $synopsisFormat);
            
            // Valid length
            if (strlen($synopsis) <= $maxLength) {
                break;
            }
            
            // Need to revert to the default
            $synopsisFormat = '__NAME__ theme for __FRAMEWORK__';
        } while (true);
        
        // All done
        return $synopsis;
    }
    
    /**
     * Get the title suffix
     * 
     * @return string
     */
    public static function getTitleSuffix() {
        // Custom plugin title suffix
        if (is_string($pluginSuffix = Addons_Listener::filterRun("^plugin\-\w+", Addons_Listener::TASK_PLUGIN_GET_SUFFIX, Addons_Listener::TYPE_ON))) {
            return $pluginSuffix;
        }
        
        // Default title suffix
        return Config::get()->titleSuffix;
    }

    /**
     * Get the path to the current WordPress theme
     * 
     * @return string
     */
    public static function getPath() {
        return Config::getWpThemesPath() . '/' . self::$destDir;
    }
    
    /**
     * Copy the folders from the framework to the final destination
     * 
     * @throws Exception
     */
    protected function _copyFolders() {
        Log::check(Log::LEVEL_INFO) && Log::info('Copying folders...');

        // Populate the project information
        self::getProjectInfo();

        // Create the batch master folder
        if (!is_dir(Config::getProjectsPath())) {
            Folder::create(Config::getProjectsPath(), 0777, true);
        }
        
        // Create the destination directory
        if (!is_dir(self::getPath())) {
            Folder::create(self::getPath(), 0777, true);
        }
        
        // Clean-up the destination directory
        Folder::clean(self::getPath());

        // Recursively copy the folders to their destination; does not support data tags
        if (is_dir($srcPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_TYPES . '/' . Tasks::$config[Cli_Run_Integration::FRAMEWORK_ID] . '/src')) {
            Folder::copyContents($srcPath, self::getPath());
        }
    
        // Display the status bar
        PercentBar::display(100);
    }
    
    /**
     * Get the verbose version name
     *
     * @param int    $version     Actual version
     * @param int    $modulo      Modulo
     * @return string Version in verbose mode
     */
    public static function getVerboseVersion($version = null, $modulo = null) {
        // Set the defaults
        if (null === $version) {
            $version = intval(str_replace('.', '', isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_VERSION]) ? Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_VERSION] : 1), 10);
        }
        
        // Custom version template
        if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_VERSION_TEMPLATE])) {
            // Custom version template
            $versionTemplate = Cli_Run_Integration::$options[Cli_Run_Integration::OPT_VERSION_TEMPLATE];
            
            // Valid template
            if (preg_match('%^([\d\.\s]*x[\d\.\s]*)\s*(?:,\s*([\-\+]?\s*\d+)\s*)?%i', $versionTemplate, $matches)) {
                // Get the template
                $customTemplate = preg_replace('%\s+%', '', $matches[1]);
                
                // Get the increment
                $customIncrement = 0;
                
                // User-defined value
                if (isset($matches[2])) {
                    $customIncrement = intval(preg_replace('%\s+%', '', $matches[2]));
                }
                
                // All done
                return preg_replace('%x%i', $customIncrement + $version, $customTemplate);
            }
        }
        
        if (null === $modulo) {
            $modulo = Config::get()->versionModulo;
        }
        
        // Get the last part
        $lastPart = (($version-1) % (int)$modulo);

        // Larger version modulo
        if (strlen($modulo) > 1) {
           // Last part - cosmetized
           $lastPartPrepared = '';

           // Get as string
           $lastPartStr = strrev((string)$lastPart);

           // Use 0 if no number is available
           for ($i = 0; $i < strlen($modulo); $i++) {
               $lastPartPrepared = (isset($lastPartStr[$i]) ? $lastPartStr[$i] : '0') . ($lastPartPrepared != '' ? '.' . $lastPartPrepared : '');
           }

           // Overwrite the last part
           $lastPart = $lastPartPrepared;
       }

       // Prepare the result
       $result = (1 + intval(($version-1) / (int)$modulo)) . '.' . $lastPart;

       // All done
       return $result;
    }
}

/*EOF*/