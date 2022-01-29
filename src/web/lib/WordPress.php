<?php
/**
 * Theme Warlock - WordPress
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress {

    /**
     * Screenshot file name
     */
    const FILE_SCREENSHOT = 'screenshot.png';
    
    /**
     * Available tools
     */
    const TOOLS_SB = 'sb';
    const TOOLS_DB = 'db';
    const TOOLS_FS = 'fs';
    const TOOLS_TW = 'tw';
    
    /**
     * Available database tools
     */
    const TOOL_DB_DUMP    = 'dmp';
    const TOOL_DB_RESTORE = 'rst';
    
    /**
     * Available filesystem tools
     */
    const TOOL_FS_DIFF    = 'dff';
    const TOOL_FS_I18N    = 'i18n';
    const TOOL_FS_RESTORE = 'rst';
    
    /**
     * Available sandbox tools
     */
    const TOOL_SB_INITIALIZE = 'init';
    const TOOL_SB_DELETE     = 'del';
    
    /**
     * Available API tools
     */
    const TOOL_TW_THEME_ENABLE    = 'theme-enable';
    const TOOL_TW_PLUGIN_INSTALL  = 'plugin-install';
    const TOOL_TW_SNAPSHOT_IMPORT = 'snapshot-import';
    const TOOL_TW_SNAPSHOT_EXPORT = 'snapshot-export';
    
    // Maximum text length when logging in Debug mode
    const MAX_DEBUG_LOG_SIZE = 1024;
    
    /**
     * Tools information
     */
    protected static $_tools = array(
        self::TOOLS_SB => array(
            self::TOOL_SB_INITIALIZE => 'Initialize the SandBox for the current user',
            self::TOOL_SB_DELETE     => 'Delete the SandBox for the current user',
        ),
        self::TOOLS_DB => array(
            self::TOOL_DB_DUMP    => 'Store a snapshot of the current database',
            self::TOOL_DB_RESTORE => 'Restore a particular snapshot',
        ),
        self::TOOLS_FS => array(
            self::TOOL_FS_DIFF    => 'Store newly modified files',
            self::TOOL_FS_I18N    => 'Gather the strings that need to be translated',
            self::TOOL_FS_RESTORE => 'Restore file modifications from a particular snapshot',
        ),
        self::TOOLS_TW => array(
            self::TOOL_TW_THEME_ENABLE    => 'Enable a specific theme',
            self::TOOL_TW_PLUGIN_INSTALL  => 'Install and activate a specific plugin',
            self::TOOL_TW_SNAPSHOT_IMPORT => 'Import a customer-facing "demo content" package',
            self::TOOL_TW_SNAPSHOT_EXPORT => 'Export the current theme\'s settings',
        ),
    );
    
    /**
     * Execute an action for a specific user/project
     * 
     * @param int    $projectId Project ID
     * @param string $tool      Tool, one of <ul>
     * <li>WordPress::TOOLS_SB</li>
     * <li>WordPress::TOOLS_DB</li>
     * <li>WordPress::TOOLS_FS</li>
     * <li>WordPress::TOOLS_TW</li>
     * </ul>
     * @param string $action    Action; each tool has its own set:
     * <ul>
     * <li>WordPress::TOOLS_SB : <ul>
     *         <li>WordPress::TOOL_SB_INITIALIZE</li>
     *         <li>WordPress::TOOL_SB_DELETE</li>
     *     </ul>
     * </li>
     * <li>WordPress::TOOLS_DB : <ul>
     *         <li>WordPress::TOOL_DB_DUMP</li>
     *         <li>WordPress::TOOL_DB_RESTORE</li>
     *     </ul>
     * </li>
     * <li>WordPress::TOOLS_FS : <ul>
     *         <li>WordPress::TOOL_FS_DIFF</li>
     *         <li>WordPress::TOOL_FS_I18N</li>
     *         <li>WordPress::TOOL_FS_RESTORE</li>
     *     </ul></li>
     * <li>WordPress::TOOLS_TW : <ul>
     *         <li>WordPress::TOOL_TW_THEME_ENABLE</li>
     *     </ul>
     * </li>
     * </ul>
     * @param string|string[] $extra Extra argument(s)
     * @throws Exception
     * @return boolean True on success, false on failure
     */
    public static function executeAction($tool, $action = null, $extra = null) {
        // Get the global arguments
        global $argv;
        
        // Tool not defined
        if (!isset(self::$_tools[$tool])) {
            throw new Exception('Tool "' . $tool . '" not defined');
        }
        
        // No action
        if (count(self::$_tools[$tool]) && null === $action) {
            throw new Exception('Action is mandatory for tool "' . $tool . '"');
        }
        
        // Invalid action
        if (count(self::$_tools[$tool]) && !isset(self::$_tools[$tool][$action])) {
            throw new Exception('Action "' . $action . '" not defined for tool "' . $tool . '"');
        }
        
        // Prepare the arguments
        $argv = array($tool, $action);
        
        // The extra argument must be an array
        if (!is_array($extra)) {
            $extra = array($extra);
        }
        
        // Only strings are allowed
        foreach ($extra as $extraItem) {
            if (is_string($extraItem) || is_numeric($extraItem)) {
                $argv[] = $extraItem;
            }
        }
        
        // Force the global arguments
        array_unshift($argv, Cli_Run_Integration::TOOL_WP);
        array_unshift($argv, ROOT . '/index.php');

        // Run the tool locally
        ob_start();
        $result = self::run();
        ob_end_clean();
        
        // Interpret the result
        if (self::TOOLS_TW == $tool) {
            // Exception
            if (is_array($result) && false === $result[0]) {
                throw new Exception($result[1]);
            }
            return $result[1];
        }
        
        // Return the result
        return $result;
    }
    
    /**
     * Run the WordPress sandbox tools
     */
    public static function run() {
        global $argv;
        
        // Get a reflection
        $reflection = new ReflectionClass(self::class);
        
        // Prepare the available tools
        $tools = array_map(function($item){
            return strtolower(preg_replace('%^runTool%', '', $item->name));
        }, array_filter($reflection->getMethods(), function($item) {
            return preg_match('%^runTool%', $item->name);
        }));
        
        // Nothing defined!
        if (!count($tools)) {
            Console::p('No WordPress Sandbox tools defined yet', false);
            return;
        }
        
        // Prepare the options
        $options = array_combine($tools, array_map(function($item){
            return 'runTool' . ucfirst($item);
        }, $tools));
        
        // Create the array of human readable options
        $optionsHumanReadable = array_combine(array_keys($options), array_map(function($item) use ($reflection) {
            // Get the method's comment
            $methodComment = $reflection->getMethod($item)->getDocComment();
            
            // Clean up the comment
            $methodComment = trim(preg_replace('%(^\/\*+|(?<=\n)\s*\*\W*)%', '', $methodComment));
            
            return $methodComment;
        }, $options));
        
        do {
            if (isset($argv[2]) && isset($options[$argv[2]])) {
                $toolId = $argv[2];
                $toolName = $options[$argv[2]];
                break;
            }
            
            // Ask for the tool
            $toolId = Console::options($optionsHumanReadable, 'Please select a WordPress tool');
            
            // Get the tool name
            $toolName = $options[$toolId];
        } while (false);
        
        // Method (somehow) not found
        if (!method_exists(self::class, $toolName)) {
            Console::p('Sandbox tool "' . $toolName . '" not found', false);
            return;
        }

        // Prepare the tool title
        $toolTitle = 'WordPress ' . $optionsHumanReadable[$toolId] . ' ' . (isset($argv[3]) ? $argv[3] : '') . '(' . json_encode(array_slice($argv, 4)) . ')';
        
        // Log the tool (forced in production mode)
        Log::info(Model_Project::LOG_CHAR_START . ' ' . $toolTitle, null, null, true);
        
        // Run the tool
        try {
            $result = call_user_func(array(self::class, $toolName));
        } catch (Exception $exc) {
            $result = false;
            Console::p($exc->getMessage(), false);
        }
        
        // Log the result (forced in production mode)
        Log::info(Model_Project::LOG_CHAR_FINISH . ' ' . $toolTitle, null, null, true);
        
        // Prepare the result to log
        if (Log::check(Log::LEVEL_DEBUG)) {
            $resultToLog = var_export($result, true);
            if (strlen($resultToLog) > self::MAX_DEBUG_LOG_SIZE) {
                $resultToLog = substr($resultToLog, 0, self::MAX_DEBUG_LOG_SIZE - 3) . '...';
            }

            // Log the details
            Log::debug($resultToLog);
        }
        
        // All done
        return $result;
    }
    
    /**
     * ThemeWarlock API
     */
    public static function runToolTw() {
        global $argv;
        
        // Get the WordPress Session
        $session = WordPress_Session::getInstance();
        
        // Prepare the database tools
        $options = self::$_tools[self::TOOLS_TW];
        
        // Get the API tool
        if (isset($argv[3]) && isset($options[$argv[3]])) {
            $tool = $argv[3];
        } else {
            $tool = Console::options($options, 'Please select the API method');
        }
        
        // Move files
        switch ($tool) {
            case self::TOOL_TW_SNAPSHOT_IMPORT:
                if (isset($argv[4]) && is_numeric($argv[4]) && isset($argv[5]) && strlen($argv[5])) {
                    try {
                        // Fix the theme name
                        $themeName = strtolower(trim(preg_replace('%[^\w\-]+%', '', $argv[5])));
                        
                        // Invalid theme name
                        if (!strlen($themeName)) {
                            throw new Exception('Invalid theme name specified');
                        }
                        
                        // Get the snapshots
                        $snapshots = WordPress_Snapshots::getInstance();
                        
                        /* @var $snapshot WordPress_Snapshots_Snapshot */
                        $snapshot = $snapshots->getById($argv[4]);
                        
                        // Copy the patternized snapshot
                        if (!is_dir($destPath = Config::getWpPath() . '/wp-content/uploads/' . $themeName. '/snapshots/' . $snapshot->getId())) {
                            Folder::copyContents($snapshot->getDistPath(), $destPath);
                        }
                    } catch (Exception $exc) {
                        Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                    }
                }
                break;
        }
        
        // Get the API method name
        $methodName = lcfirst(implode('', array_map('ucfirst', explode('-', $tool))));
                
        // Get theAPI method arguments
        $methodArguments = array_slice($argv, 4);
        
        // Prepare the post load
        $postData = array(
            'method'    => $methodName,
            'arguments' => $methodArguments
        );
        
        // Get the API path
        $apiPath = 'wp-admin/wp-api.php';
        
        // Copy the file
        file_put_contents(
            Config::getWpPath() . '/' . $apiPath,
            Addons::getInstance()->parseDataKeys(
                file_get_contents(ROOT . '/web/resources/wordpress/wp-api.php'),
                Model_Project_Config::CATEGORY_CORE,
                null,
                'php',
                true
            )
        );
        
        // Prepare the URL
        $postDomain = (null === $session->getUserId() ? 'wp' : 'wp-u' . $session->getUserId()) . '.' . Config::get()->myDomain;
        
        // User agent
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0';

        // Prepare the headers
        $curlHeaders = array (
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'User-Agent: ' . $userAgent,
            'DNT: 1',
            'Referer: http://' . $postDomain . '/',
            'Host: ' . $postDomain,
            'Referrer: ' . $postDomain,
            'Cache-Control: no-cache',
        );

        // Prepare the options
        $options = array(
            CURLOPT_POST            => true,
            CURLOPT_POSTFIELDS      => http_build_query($postData),
            CURLOPT_USERAGENT       => $userAgent,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_HEADER          => false,
            CURLOPT_HTTPHEADER      => $curlHeaders,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_REDIR_PROTOCOLS => CURLPROTO_ALL,
            CURLOPT_AUTOREFERER     => true,
            CURLOPT_CONNECTTIMEOUT  => 5,
            CURLOPT_TIMEOUT         => 30,
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_SSL_VERIFYPEER  => false,
            CURLOPT_FAILONERROR     => true,
            CURLOPT_URL             => $postDomain . '/' . $apiPath,
        );

        // Prepare the retries counter
        $curlLoggedOutRetries = 0;
        
        do {
            // Initialize the CURL
            $ch = curl_init();

            // Set the options
            curl_setopt_array($ch, $options);

            // Get the result
            $curlResult = curl_exec($ch);

            // Remove the clutter
            $jsonString = preg_replace('%.*?({"status":.*$)%ims', '${1}', $curlResult);

            // Extra content
            $extraContent = preg_replace('%(.*?){"status":.*$%ims', '${1}', $curlResult);

            // Execute the request
            $jsonArray = @json_decode($jsonString, true);

            // Invalid result
            if (!is_array($jsonArray)) {
                // Logged out
                if (preg_match('%<\!doctype%i', $jsonString)) {
                    // Increment the counter
                    $curlLoggedOutRetries++;
                    
                    // Inform the user
                    Console::p('Invalid result (Logged Out): Attempt ' . $curlLoggedOutRetries . '/3', false);
                    
                    // Give up
                    if ($curlLoggedOutRetries >= 3) {
                        Console::p('Gave up on request');
                        return;
                    }
                    
                    // Start over
                    continue;
                } 
                
                // Other type of error, stop here
                if (false === $curlResult) {
                    Console::p('Invalid result (cURL): ' . curl_error($ch), false);
                } else {
                    Console::p('Invalid result: ' . var_export($jsonString, true), false);
                }
                return;
            }
            
            // Close
            curl_close($ch);
            
            // Valid result, no need to retry
            break;
        } while (true);
        
        // Add the extra content
        $jsonArray['content'] .= trim(html_entity_decode(strip_tags(preg_replace('%<br\s*\/?\s*>%i', PHP_EOL, $extraContent))));

        if (!isset($jsonArray['status']) || !isset($jsonArray['content'])) {
            Console::p('Result has invalid format', false);
            return;
        }
        
        // Content
        if (strlen($jsonArray['content'])) {
            Console::p($jsonArray['content']);
        }
        
        // Prepare the result to log
        $loggedResult = is_string($jsonArray['result']) ? $jsonArray['result'] : json_encode($jsonArray['result']);
        if (strlen($loggedResult) > self::MAX_DEBUG_LOG_SIZE) {
            $loggedResult = substr($loggedResult, 0, self::MAX_DEBUG_LOG_SIZE - 3) . '...';
        }

        // Log the result
        Console::p($loggedResult, (boolean)$jsonArray['status']);
        
        // All done
        return array((boolean)$jsonArray['status'], $jsonArray['result']);
    }
    
    /**
     * SandBox utilities
     */
    public static function runToolSb() {
        global $argv;
        
        // Prepare the database tools
        $options = self::$_tools[self::TOOLS_SB];

        if (isset($argv[3]) && isset($options[$argv[3]])) {
            $tool = $argv[3];
        } else {
            $tool = Console::options($options, 'Please select the sandbox utility');
        }
        
        switch ($tool) {
            case self::TOOL_SB_INITIALIZE:
                self::_sbToolInit();
                break;
            
            case self::TOOL_SB_DELETE:
                self::_sbToolDelete();
                break;
        }
    }
    
    /**
     * Initialize the sandbox for the current user
     */
    protected static function _sbToolInit() {
        // Get the WordPress session
        $wordpressSession = WordPress_Session::getInstance();
        
        // Log the sandbox name
        Console::h1('Sandbox "' . $wordpressSession->getName() . '" initialization');
        
        // Revert file changes
        Console::p('Cleaning-up default SandBox changes...');
        ob_start();
        self::_fsToolRestore(true);
        ob_end_clean();
        
        // Re-create the folder
        Console::p('Moving the files...');
        if (is_dir(Config::getWpPath())) {
            Folder::clean(Config::getWpPath(), true);
        }
        Folder::create(Config::getWpPath(), 0777, true);
        
        // Copy the default sandbox
        Folder::copyContents(Config::getWpPath(true), Config::getWpPath());
        
        // Remove the .git folder
        Folder::clean(Config::getWpPath() . '/.git', true);
        
        // Change the DB credentials
        Console::p('Changing the DB credentials...');
        
        // Change the snapshots
        Folder::pregReplace(
            '%\`' . preg_quote(Config::getWpDbName(true)) . '\`%', 
            '`' . Config::getWpDbName() . '`', 
            Config::getWpPath() . '/wp-db-snapshots',
            '%\.sql$%'
        );
        Folder::pregReplace(
            '%wp\.tw\.com%', 
            'wp-' . $wordpressSession->getName() . '.' . Config::get()->myDomain, 
            Config::getWpPath() . '/wp-db-snapshots',
            '%\.sql$%'
        );
        
        // Change the config file
        file_put_contents(
            Config::getWpPath() . '/wp-config.php', 
            preg_replace(
                '%([\'"]DB_NAME[\'"]\s*,\s*[\'"])' . preg_quote(Config::getWpDbName(true)) . '([\'"])%s', 
                '${1}' . Config::getWpDbName() . '${2}', 
                file_get_contents(Config::getWpPath() . '/wp-config.php')
            )
        );
        
        // Initialize the GIT
        Console::p('Initializing file versioning...');
        shell_exec('git -C "' . Config::getWpPath() . '" init');
        shell_exec('git -C "' . Config::getWpPath() . '" config user.name "Sandbox ' . ucfirst($wordpressSession->getName()) . '"');
        shell_exec('git -C "' . Config::getWpPath() . '" config user.email "sandbox' . $wordpressSession->getName() . '@markjivko.com"');
        shell_exec('git -C "' . Config::getWpPath() . '" add --all');
        shell_exec('git -C "' . Config::getWpPath() . '" commit -am "[code] Initialized Sandbox for user ' . $wordpressSession->getName() . '"');
        
        // Restore the database
        Console::p('Restoring the DB...');
        self::_dbToolRestore();
    }
    
    /**
     * Delete the sandbox for the current user
     */
    protected static function _sbToolDelete() {
        // Get the WordPress session
        $wordpressSession = WordPress_Session::getInstance();
        
        // Log the sandbox name
        Console::h1('Sandbox "' . $wordpressSession->getName() . '" removal');
        
        // Remove the files
        Console::p('Removing the files...');
        Folder::clean(Config::getWpPath(), true);
        
        // Remove the database
        Console::p('Removing the database...');
        
        // Prepare the credentials
        $dbUser = Config::get()->dbUsername;
        $dbPass = Config::get()->dbPassword;
        $dbName = Config::getWpDbName();

        // Prepare the command
        $command = "export MYSQL_PWD=$dbPass; mysql -u $dbUser -e 'drop database if exists $dbName;'";

        // Execute the command
        shell_exec($command);
    }
    
    /**
     * DataBase utilities
     */
    public static function runToolDb() {
        global $argv;
        
        // Prepare the database tools
        $options = self::$_tools[self::TOOLS_DB];

        if (isset($argv[3]) && isset($options[$argv[3]])) {
            $tool = $argv[3];
        } else {
            $tool = Console::options($options, 'Please select the database utility');
        }
        
        switch ($tool) {
            case self::TOOL_DB_DUMP:
                return self::_dbToolDump();
                break;
            
            case self::TOOL_DB_RESTORE:
                return self::_dbToolRestore();
                break;
        }
    }
    
    /**
     * File System utilities
     */
    public static function runToolFs() {
        global $argv;
        
        // Prepare the database tools
        $options = self::$_tools[self::TOOLS_FS];

        if (isset($argv[3]) && isset($options[$argv[3]])) {
            $tool = $argv[3];
        } else {
            $tool = Console::options($options, 'Please select the filesystem utility');
        }
        
        switch ($tool) {
            case self::TOOL_FS_DIFF:
                self::_fsToolDiff();
                break;
            
            case self::TOOL_FS_I18N:
                self::_fsToolI18n();
                break;
            
            case self::TOOL_FS_RESTORE:
                self::_fsToolRestore();
                break;
        }
    }
    
    /**
     * Gather the i18n strings
     */
    protected static function _fsToolI18n() {
        global $argv;
        
        // Get the project ID
        if (!isset($argv[4]) || !strlen($argv[4])) {
            throw new Exception('Project text domain not specified');
        }
        
        // Extract the strings
        $portableObjectTemplate = (new WordPress_Pot())->extract(
            rtrim(Config::getWpPath(), '\\/') . '/wp-content',
            $argv[4]
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
        }
    }
    
    /**
     * Dump the file changes to a particular path
     */
    protected static function _fsToolDiff() {
        global $argv;
        
        // Get the WordPress installation path
        $wpPath = Config::getWpPath();
        
        // Validate it
        if (null == $wpPath || strlen($wpPath) < 1 || !is_dir($wpPath)) {
            Console::p('Invalid WordPress path defined', false);
            return;
        }
        
        // Revert the listing
        passthru('git -C "' . $wpPath . '" reset -- .');
        
        // Stage some of the changes
        passthru('git -C "' . $wpPath . '" add .');
        
        // Unstage other domains
        passthru('git -C "' . $wpPath . '" reset -- wp-admin/wp-api.php');
        passthru('git -C "' . $wpPath . '" reset -- wp-content/themes');
        passthru('git -C "' . $wpPath . '" reset -- wp-content/plugins');
        passthru('git -C "' . $wpPath . '" reset -- wp-content/upgrade');
        
        // Get our folders
        $themeFolders = glob($wpPath . '/wp-content/uploads/' . strtolower(preg_replace('%\W+%', '-', Config::get()->authorName)) . '-*', GLOB_ONLYDIR);

        // Valid result
        if (is_array($themeFolders) && count($themeFolders)) {
            // Go through the list
            foreach ($themeFolders as $themeFolder) {
                // Reset the changes
                passthru('git -C "' . $wpPath . '" reset -- "wp-content/uploads/' . basename($themeFolder) . '"');
            }
        }
        
        // Get the status
        $statusOutput = preg_replace('%(^.*?Changes.*?:|Untracked.*?:.*$)%ims', '', shell_exec('git -C "' . $wpPath . '" status'));
        
        // Get the project ID
        if (!isset($argv[4])) {
            throw new Exception('Project ID not specified');
        }

        // Get the project path
        if (!is_dir($projectPath = Config::getProjectsPath() . '/' . $argv[4])) {
            throw new Exception('Invalid project ID specified');
        }
        
        // Get the snapshot ID
        $snapshotId = 1;
        if (isset($argv[5])) {
            $snapshotId = intval($argv[5]);
        }
        if ($snapshotId < 1 || $snapshotId > 9999) {
            throw new Exception('Invalid snapshot ID');
        }
        
        // Get the snapshot path
        if (!is_dir($snapshotPath = $projectPath . '/' . WordPress_Snapshots::FOLDER_NAME . '/' . $snapshotId)) {
            Folder::create($snapshotPath, 0777, true);
        }
        
        // Clean-up all except the data
        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($snapshotPath, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST) as $path) {
            if ($path->isFile()) {
                // Keep the data file
                if (!in_array(basename($path->getPathname()), array(WordPress_Snapshots_Snapshot::DATA_FILE_NAME, WordPress_Snapshots_Snapshot::PREVIEW_FILE_NAME))) {
                    @unlink($path->getPathname());
                }
            } else {
                // Force PHP to let go of the directory
                closedir(opendir($path->getPathname()));

                // Remove the directory
                @rmdir($path->getPathname());
            }
        }
        
        // Prepare the increment
        $fileIncrement = 1;
        
        // Prepare the translation
        $newPaths = array();
        
        // Go through the lines
        foreach (preg_split('%[\r\n]+%', $statusOutput) as $line) {
            if (preg_match('%^\s+[\w\s]+:\s*(.*?)$%', $line, $matches)) {
                // Get the relative path
                $relativePath = $matches[1];
                
                // Get the new file name
                $newName = 'f' . $fileIncrement . preg_replace('%^.*?(\.\w+)$%', '$1', basename($relativePath));
                
                // Store the translation
                $newPaths[$newName] = $relativePath;
                
                // Copy the file
                copy($wpPath . '/' . $relativePath, $snapshotPath . '/' . $newName);
                
                // Next
                $fileIncrement++;
            }
        }
        
        // Update the data
        $data = array();
        if (file_exists($dataPath = $snapshotPath . '/' . WordPress_Snapshots_Snapshot::DATA_FILE_NAME)) {
            $data = @json_decode(file_get_contents($dataPath), true);
            if (!is_array($data)) {
                $data = array();
            }
        }
        
        // Store the paths
        $data[WordPress_Snapshots_Snapshot::KEY_PATHS] = $newPaths;

        // All done
        file_put_contents($dataPath, json_encode($data));
    }
    
    /**
     * Restore
     */
    protected static function _fsToolRestore($getDefault = false) {
        global $argv;
        
        // Get the WordPress installation path
        $wpPath = Config::getWpPath($getDefault);
        
        // Validate it
        if (null == $wpPath || strlen($wpPath) < 1 || !is_dir($wpPath)) {
            Console::p('Invalid WordPress path defined', false);
            return;
        }
        
        // Revert the modifications
        passthru('git -C "' . $wpPath . '" reset --hard');
        
        // Remove uncommited files and folders
        passthru('git -C "' . $wpPath . '" clean -fd');

        // Project snapshot restore mode
        if (isset($argv[4])) {
            // Get the project path
            if (!is_dir($projectPath = Config::getProjectsPath() . '/' . $argv[4])) {
                throw new Exception('Invalid project ID specified');
            }

            // Get the snapshot ID
            $snapshotId = 1;
            if (isset($argv[5])) {
                $snapshotId = intval($argv[5]);
            }
            if ($snapshotId < 1 || $snapshotId > 9999) {
                throw new Exception('Invalid snapshot ID');
            }
            
            // Get the snapshot path
            if (!is_dir($snapshotPath = $projectPath . '/' . WordPress_Snapshots::FOLDER_NAME . '/' . $snapshotId)) {
                throw new Exception('Snapshot with ID ' . $snapshotId . ' does not exist');
            }
            
            // Get the JSON
            if (!file_exists($jsonPath = $snapshotPath . '/_data.json')) {
                throw new Exception('Data file missing');
            }
            
            // Get the contents
            $jsonData = @json_decode(file_get_contents($jsonPath), true);

            // Validate the JSON data
            WordPress_Snapshots_Snapshot::validateData($jsonData, $snapshotPath, $snapshotId);
            
            // Perform the replacement
            foreach ($jsonData[WordPress_Snapshots_Snapshot::KEY_PATHS] as $localFileName => $remotePath) {
                // Get the full path
                $fullPath = $wpPath . '/' . $remotePath;
                
                // Create directory if necessary
                if (!is_dir($fullPathDir = dirname($fullPath))) {
                    Folder::create($fullPathDir, 0777, true);
                }

                // Copy the file
                copy($snapshotPath . '/' . $localFileName, $fullPath);
            }
        }
    }

    /**
     * Get the snapshot name
     * 
     * @return string Snapshot name without the trailing '.sql'
     */
    protected static function _dbToolGetSnapshotName() {
       global $argv;
       
       // Get the available options
       $options = array_map(function($item){
           return preg_replace('%^snapshot\-%', '', basename($item, '.sql'));
       }, glob(self::_dbToolGetPath() . '/*.sql'));
       
       // Get the version name
       $versionName = isset($argv[4]) ? strtolower(trim($argv[4])) : '';
       
       // Invalid version name
       if (!preg_match('%^[a-z0-9]{2,}$%', $versionName)) {
           // User tried an invalid snapshot name
           if (isset($argv[4]) && '?' != trim($argv[4])) {
               Console::p('The snapshot name failed regex: [a-z0-9]{2,}', false);
           }
           
           // Revert to the original
           $versionName = 'original';
       }

       // Empty input, show the options
       if (count($options) && isset($argv[4]) && '?' == trim($argv[4])) {
           // Get the version ID
           $versionId = Console::options($options, 'Choose a snapshot', 'Snapshot "%s"');
           
           // Get the new version name
           $versionName = $options[$versionId];
       }

       // All done
       return "snapshot-$versionName";
    }
    
    /**
     * Get the dump path
     * 
     * @return string
     */
    protected static function _dbToolGetPath() {
        // Get the WordPress installation path
        $wpPath = Config::getWpPath();

        // Validate it
        if (null == $wpPath || strlen($wpPath) < 1 || !is_dir($wpPath)) {
            Console::p('Invalid WordPress path defined', false);
            exit();
        }
        
        // Create the directory
        if (!is_dir($wpDumpPath = $wpPath . '/wp-db-snapshots')) {
            Folder::create($wpDumpPath, 0777, true);
        }
        
        // All done
        return $wpDumpPath;
    }   
    
    protected static function _dbToolRestore() {
        // Get the path
        $wpPath = self::_dbToolGetPath();

        // Prepare the credentials
        $dbHost = Config::get()->dbHost;
        $dbUser = Config::get()->dbUsername;
        $dbPass = Config::get()->dbPassword;
        $dbName = Config::getWpDbName();

        // Get the snapshot name
        $snapshotName = self::_dbToolGetSnapshotName();
        
        // Prepare the backup file
        $backupFilePath = $wpPath . '/' . $snapshotName . '.sql';

        // File not found
        if (!file_exists($backupFilePath)) {
            Console::p('Database snapshot "' . $snapshotName . '" not found', false);
            return;
        }
        
        // Prepare the old WordPress session
        $oldWpSession = null;
        
        // Try to establish a connection
        if (false != $mysqlConn = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName)) {
            // Prepare the query
            $query = 'SELECT * from `wp_usermeta` WHERE `meta_key` = "session_tokens" AND `user_id` = "1"';
            
            /*@var $result mysqli_result*/
            $result = mysqli_query($mysqlConn, $query);
            if (is_object($result)) {
                if (is_array($rows = $result->fetch_assoc())) {
                    if (isset($rows['meta_value'])) {
                        $oldWpSession = $rows['meta_value'];
                    }
                }
            }
            
            // DataBase pruning, remove all tables
            mysqli_query($mysqlConn, 'SET foreign_key_checks = 0');
            if ($result = mysqli_query($mysqlConn, "SHOW TABLES")) {
                while($row = $result->fetch_array(MYSQLI_NUM)) {
                    mysqli_query($mysqlConn, 'DROP TABLE IF EXISTS ' . $row[0]);
                }
            }
            mysqli_query($mysqlConn, 'SET foreign_key_checks = 1');
        }
        
        // Prepare the command
        $command = "export MYSQL_PWD=$dbPass; mysql -u $dbUser < $backupFilePath";

        // Execute the command
        passthru($command, $returnValue);
        if (0 !== $returnValue) {
            Console::p('Could not execute database restore "' . $snapshotName . '"', false);
            return;
        }
        
        // Restore the session
        if (null != $oldWpSession) {
            // Prepare the query
            $query = 'UPDATE `wp_usermeta` SET `meta_value`="' . mysqli_real_escape_string($mysqlConn, $oldWpSession) . '" WHERE `meta_key` = "session_tokens" AND `user_id` = "1"';
            
            /*@var $result mysqli_result*/
            $result = mysqli_query($mysqlConn, $query);
        }

        // Inform the user
        Console::p('WordPress database snapshot "' . $snapshotName . '" restored successfully!');
        return true;
    }
    
    /**
     * Dump the current database to a local file
     */
    protected static function _dbToolDump() {
        // Get the path
        $wpPath = self::_dbToolGetPath();

        // Prepare the credentials
        $dbHost = Config::get()->dbHost;
        $dbUser = Config::get()->dbUsername;
        $dbPass = Config::get()->dbPassword;
        $dbName = Config::getWpDbName();

        // Get the snapshot name
        $snapshotName = self::_dbToolGetSnapshotName();
        
        // Prepare the backup file
        $backupFilePath = $wpPath . '/' . $snapshotName . '.sql';

        // Prepare the command
        $command = "export MYSQL_PWD=$dbPass; mysqldump --skip-dump-date --opt -h $dbHost -u $dbUser --databases $dbName --add-drop-database > $backupFilePath";

        // Execute the command
        system($command, $returnValue);
        if (0 !== $returnValue) {
            Console::p('Could not execute database snapshot "' . $snapshotName . '"', false);
            return;
        }

        // Inform the user
        Console::p('WordPress database snapshot "' . $snapshotName . '" created successfully!');
        return true;
    }

}

/* EOF */