<?php

/**
 * Theme Warlock - Tasks
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Tasks {

    /**
     * Store information about the current framework (info.php contents)
     *
     * @var array
     */
    public static $config = array();

    /**
     * Project model
     * 
     * @var Model_Project
     */
    public static $project = null;
    
    /**
     * Store tasks that were run once
     * 
     * @var array
     */
    public static $ranOnce = array();

    /**
     * Perform just this one task then stop
     * 
     * @var string
     */
    protected static $_oneTimeTasks = array();
    
    /**
     * Addons list
     * 
     * @var string[]
     */
    public static $definedAddons = array();
    
    /**
     * Check this class ran once
     * 
     * @param string $class Class name
     * @return boolean
     */
    public static function ranOnce($class) {
        return isset(self::$ranOnce[$class]);
    }
    
    /**
     * Check whether or not we are in Staging mode
     * 
     * @return boolean
     */
    public static function isStaging() {
        return isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_STAGING]);
    }
    
    /**
     * Run the tasks
     */
    public static function run() {
        // Make sure the Input and Output directories exist
        IO::initFolders(true);
        
        // Get the projects
        $projects = Model_Projects::getInstance(
            WordPress_Session::getInstance()->getUserId()
        );
        self::$project = $projects->get(
            WordPress_Session::getInstance()->getProjectId(), 
            WordPress_Session::getInstance()->getUserId()
        );
        
        // A custom snapshot is to be deployed
        do {
            if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID])) {
                // Get the snapshot ID
                $snapshotId = Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID];
                
                // Values seem ok
                if (is_numeric($snapshotId)) {
                    try {
                        // Initialize the snapshots
                        $snapshots = WordPress_Snapshots::getInstance();

                        /*@var $snapshot WordPress_Snapshots_Snapshot*/
                        $snapshot = $snapshots->getById($snapshotId);

                        // Activate the snapshot
                        $snapshot->activate();

                        // Avoid re-running the default filesystem cleanup
                        break;
                    } catch (Exception $ex) {
                        Log::check(Log::LEVEL_ERROR) && Log::error($ex->getMessage(), $ex->getFile(), $ex->getLine());
                    }
                }
            }

            // Filesystem restore
            WordPress::executeAction(
                WordPress::TOOLS_FS,
                WordPress::TOOL_FS_RESTORE
            );
        } while (false);
        
        // Staging
        if (self::isStaging()) {
            self::$_oneTimeTasks = array('1NewProject');
        }
        
        // Get the framework ID
        $frameworkId = Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK];
        
        // Prepare the method name
        $method = 'v' . trim($frameworkId);

        // Go on for qualified framework types only
        if (!file_exists($infoFile = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_TYPES . '/' . $frameworkId . '/info.php')) {
            Log::check(Log::LEVEL_ERROR) && Log::error('Info file "' . $infoFile . '" not found');
            return;
        }

        // Get the framework information
        require $infoFile;

        // Set the framework ID
        $info[Cli_Run_Integration::FRAMEWORK_ID] = $frameworkId;

        // Save the framnework information
        self::$config = $info;

        // Spacing
        echo PHP_EOL;

        // Populate the project information
        Tasks_1NewProject::getProjectInfo();
            
        // Run one task only
        if (count(self::$_oneTimeTasks)) {
            // Use only the selected classes
            $taskFiles = array();
            foreach (self::$_oneTimeTasks as $oneTask) {
                $taskFiles[] = __DIR__ . '/Tasks/' . $oneTask . '.php';
            }
        } else {
            // Go through all the tasks
            $taskFiles = glob(__DIR__ . '/Tasks/*.php');
        }

        // Implement the addons
        self::_addons();

        // Run all the tasks
        foreach ($taskFiles as $task) {
            // Compute the class name
            $className = 'Tasks_' . substr(basename($task), 0, strrpos(basename($task), '.'));

            // Go through the listeners
            Addons_Listener::run(preg_replace('%^\d+%', '', basename($task, '.php')), Addons_Listener::TYPE_BEFORE);
            
            // Load the class
            $instance = new $className();
            try {
                // Try to revert to the parent framework, if provided, or to the first framework (1)
                if (!method_exists($instance, $method)) {
                    // Use parent or v1?
                    $revertMethod = 'v1';

                    // Log this information
                    Log::check(Log::LEVEL_DEBUG) && Log::debug('Could not find method ' . $method . ', reverting to ' . $revertMethod . ' for task ' . $className);

                    // Decide upon the method
                    $methodToCall = $revertMethod;
                } else {
                    $methodToCall = $method;
                }

                // Run the task
                if (method_exists($instance, $methodToCall)) {
                    // Log the start
                    Log::check(Log::LEVEL_INFO) && Log::info('* Task/' . self::$config[Cli_Run_Integration::FRAMEWORK_ID] . '/' . $className . '::Start');

                    // Output the title
                    echo '  ' . str_pad(' ' . self::$config[Cli_Run_Integration::FRAMEWORK_ID] . ' | Running task "' . preg_replace('%Tasks_\d+%', '', $className) . '" ', 70, '=', STR_PAD_BOTH) . "\n\n";

                    // Initialize the status bar
                    PercentBar::display(0);

                    // Get the result
                    $result = call_user_func(array($instance, $methodToCall));

                    // Log the result
                    Log::check(Log::LEVEL_INFO) && Log::info('* Task/' . self::$config[Cli_Run_Integration::FRAMEWORK_ID] . '/' . $className . '::End' . (null !== $result ? (' = ' . var_export($result, true)) : ''));

                    // Store that this wal already run
                    self::$ranOnce[$className] = true;

                    // Next line
                    echo PHP_EOL . PHP_EOL;
                } else {
                    Log::check(Log::LEVEL_ERROR) && Log::error('Method "' . $methodToCall . '" not found in "' . $className . '"');
                }

                // Go through the listeners
                Addons_Listener::run(preg_replace('%^\d+%', '', basename($task, '.php')), Addons_Listener::TYPE_AFTER);
            } catch (Exception $exception) {
                // Output the message
                echo "\n  [ERROR] " . $exception->getMessage() . "\n";

                // Log this error
                Log::check(Log::LEVEL_ERROR) && Log::error($exception->getMessage(), $exception->getFile(), $exception->getLine());
                
                // Mark this event in the TaskManager
                TaskManager::getInstance()->tick(
                    '[' . basename($exception->getFile()) . ':' . $exception->getLine() . '] ' . $exception->getMessage(), 
                    false
                );
                
                // Stop the loop
                break;
            }
        }
    }
    
    
    /**
     * Implement the addons for the original framework folder
     * 
     * @return null
     */
    protected static function _addons() {    
        // Create a new addon
        $addonsInstance = Addons::getInstance();
        
        // Get the currently defined addons
        $configAddons = array();
        
        // Get the addons list
        if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_ADDONS])) {
            $configAddons = Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_ADDONS];
            
            // Invalid JSON
            if (!is_array($configAddons)) {
                $configAddons = array();
            }
        }

        // Get the actual addons list - removing any attempts to set custom addons directly
        self::$definedAddons = array_filter(array_map(function($item) {return trim(strtolower($item));}, array_keys($configAddons)), function($item) {
            // Ignore the current framework (avoid duplicate entry)
            if (Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK] == $item) {
                return false;
            }
            
            // Ignore apply addons
            if (preg_match('%^(' . implode('|', array_map(function($item) {return preg_quote(preg_replace('%\-?\{value\}%i', '', $item));}, array_keys(Addons::$custom))) . ')\b%', $item) || empty($item)) {
                return false;
            }
            return true;
        });
        
        // Use the framework as an addon
        array_unshift(self::$definedAddons, Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK]);
        
        // Add the custom addons from the list
        foreach (Addons::$custom as $addonPrefix => $addonConfigName) {
            // Get the custom addon config value
            $customAddonValue = Config::get()->$addonConfigName;

            // Get whether the value is boolean
            $customAddonBoolean = is_bool($customAddonValue);
            
            // Get the run.csv custom addon value
            if (isset(Cli_Run_Integration::$options[$addonConfigName])) {
                if ($customAddonBoolean) {
                    $customAddonValue = (Csv::TRUE == Cli_Run_Integration::$options[$addonConfigName]);
                } else {
                    $customAddonValue = Cli_Run_Integration::$options[$addonConfigName];
                }
            }
            
            // Get the addon name
            $addonName = str_replace('{value}', $customAddonValue, $addonPrefix);

            // Custom addon found
            if (!empty($customAddonValue) && count(glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName, GLOB_ONLYDIR))) {
                array_unshift(self::$definedAddons, $addonName);
            }
        }
        
        // Prepend the base addon
        array_unshift(self::$definedAddons, Model_Project_Config::CATEGORY_CORE);

        // Go through the list
        foreach (self::$definedAddons as $definedAddon) {
            // Activate this addon
            $addonsInstance->activate($definedAddon, isset($configAddons[$definedAddon]) ? $configAddons[$definedAddon] : null);
        }
    }
    
}

/*EOF*/