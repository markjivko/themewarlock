<?php

/**
 * Theme Warlock - Log
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Log {

    /**
     * Debug modes
     */
    const LEVEL_DEBUG   = 'debug';
    const LEVEL_INFO    = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR   = 'error';

    /**
     * Set the current logging level
     */
    protected static $_currentLevel = self::LEVEL_DEBUG;
    
    /**
     * Cached checks; store log level check results
     * 
     * @var array
     */
    protected static $_cachedChecks = array();
    
    /**
     * Flag for log rotation check
     * 
     * @var boolean
     */
    protected static $_checkedLogRotate = false;
    
    /**
     * Flag for whether the file permissions were checked
     * 
     * @var boolean
     */
    protected static $_checkedFilePerms = false;
    
    /**
     * Flag for whether the file permissions were checked for the controller log
     * 
     * @var boolean
     */
    protected static $_checkedFilePermsController = false;

    /**
     * Logging priorities
     * 
     * @var array
     */
    public static $priorities = array(
        self::LEVEL_DEBUG,
        self::LEVEL_INFO,
        self::LEVEL_WARNING,
        self::LEVEL_ERROR,
    );

    /**
     * Extra information to log
     * 
     * @var array
     */
    protected static $_extra = array();

    /**
     * Get the current log level
     * 
     * @return string Log level, one of:<ul>
     * <li>Log::LEVEL_DEBUG</li>
     * <li>Log::LEVEL_INFO</li>
     * <li>Log::LEVEL_WARNING</li>
     * <li>Log::LEVEL_ERROR</li>
     * </u>
     */
    public static function getLevel() {
        return self::$_currentLevel;
    }
    
    /**
     * Set the log level
     * 
     * @param string $level Log level
     */
    public static function setLevel($level) {
        $level = strtolower($level);
        if (in_array($level, self::$priorities)) {
            self::$_currentLevel = $level;
        }
    }
    
    /**
     * Get the extra arguments to log
     * 
     * @return array
     */
    public static function getExtra() {
        return self::$_extra;
    }
    
    /**
     * Set extra values for the log
     * 
     * @param array $values Extra values
     */
    public static function setExtra(Array $values) {
        self::$_extra = $values;
    }

    /**
     * Check if the provided error level will be allowed to write to logs
     * 
     * @param string $errorLevel
     * @return boolean
     */
    public static function check($errorLevel) {
        // Cache check
        if (!isset(self::$_cachedChecks[$errorLevel])) {
            // Get the error level priority
            $priority = array_search(strtolower($errorLevel), self::$priorities);

            // Get the current priority
            $currentPriority = array_search(self::$_currentLevel, self::$priorities);

            // All done
            self::$_cachedChecks[$errorLevel] = (is_int($priority) && $priority >= $currentPriority);
        }
        
        // All done
        return self::$_cachedChecks[$errorLevel];
    }
    
    /**
     * Get a daily report of Errors and Warnings by parsing the log.txt file; analyzes the last 24 hours
     * 
     * @param boolean $asHtml (optional) Get the result as a string - HTML table, instead of an array; default <b>false</b>
     * @return array|string|null An array/string with the recovered errors/warnings or Null if nothing found
     */
    public static function getReport($asHtml = false) {
        // Prepare the events list
        $events = array(
            self::LEVEL_ERROR   => array(),
            self::LEVEL_WARNING => array(),
        );
        
        // Prepare the events regex
        $eventsRegEx = '%^ *(.*?\| \d+) +\| (' . implode('|', array_map('strtoupper', array_keys($events))) . ') +\| +(.*)%';

        // Open the file handler
        $handle = @fopen(ROOT . '/web/log/log.txt', 'rb');
        
        // Valid file found
        if ($handle) {
            // Seek
            while (!@feof($handle)) {
                // Get the line
                $buffer = @fgets($handle);
                
                // Valid result
                if (preg_match($eventsRegEx, $buffer, $matches)) {
                    // Get the data
                    list(, $eventHeader, $eventType, $eventDetails) = $matches;
                    
                    // Cast to lowerstring
                    $eventType = strtolower($eventType);
                    
                    // Remove the timestamp from the event key
                    $eventKey = preg_replace('%\| \d+\.\d+ \d+:\d+:\d+ \|%', '|', $eventHeader);
                    
                    // Get the datetime
                    $dateTime = preg_replace('%.*?\| (\d+\.\d+) (\d+:\d+:\d+) \|.*%', '${1}.' . date('Y') . ' ${2}', $eventHeader);
                    
                    // Convert to timestamp
                    $timeStamp = strtotime($dateTime);
                    
                    // Valid entry for consideration
                    if (time() - $timeStamp > 86400) {
                        continue;
                    }
                    
                    // Capture the first line
                    if (!isset($events[$eventType][$eventDetails])) {
                        $events[$eventType][$eventDetails] = array();
                    }
                    
                    // Unique elements
                    if (!in_array($eventKey, $events[$eventType][$eventDetails])) {
                        $events[$eventType][$eventDetails][] = $eventKey;
                    }
                }
            }
            
            // Close the handler
            @fclose($handle);
        }

        // Nothing to report
        if (!count($events[self::LEVEL_ERROR]) && !count($events[self::LEVEL_WARNING])) {
            return null;
        }
        
        // Array result
        if (!$asHtml) {
            return $events;
        }
        
        // Prepare the html
        $htmlResult = '<table>';
        
        // Go through the event types
        foreach ($events as $eventType => $eventData) {
            // No events
            if (!count($eventData)) {
                continue;
            }
            
            // Prepare the heading color
            $headingColor = 'black';
            switch ($eventType) {
                case self::LEVEL_WARNING;
                    $headingColor = 'orange';
                    break;
                
                case self::LEVEL_ERROR:
                    $headingColor = 'red';
                    break;
            }
            
            // Add the heading
            $htmlResult .= '<tr>' . 
                '<td colspan="2" style="color: ' . $headingColor . ';"><b>' . ucfirst($eventType) . '</b></td>' .
            '</tr>';
            
            // Add the rows
            foreach ($eventData as $eventDetails => $eventRows) {
                // Prepare the list
                $eventList = '<ol>' . 
                    implode(
                        PHP_EOL, 
                        array_map(
                            function($item){
                                return '<li>' . $item . '</li>';
                            }, 
                            $eventRows
                        )
                    ) . '</ol>';
                            
                // Add the row
                $htmlResult .= '<tr>' . 
                    '<td>' . $eventList . '</td>' . 
                    '<td><b>' . $eventDetails . '</b></td>' . 
                '</tr>';
            }
        }
        
        // Close the table
        $htmlResult .= '</table>';
        
        // All done
        return $htmlResult;
    }
    
    /**
     * Log a message
     * 
     * @param string  $message       Message
     * @param string  $errorLevel    Error level
     * @param string  $file          File
     * @param int     $line          Line
     * @param boolean $logErrorLevel (optional) Log the error level; default <b>true</b>
     * @param boolean $forced        (optional) Forced logging; default <b>false</b>
     */
    protected static function _log($message, $errorLevel, $file, $line, $logErrorLevel = true, $forced = false) {
        // Cannot log
        if (!$forced && !self::check($errorLevel)) {
            return;
        }
        
        // Create the LOG folder
        if (!self::$_checkedFilePerms && !is_dir(ROOT . '/web/log')) {
            Folder::create(ROOT . '/web/log', 0777, true);
        }

        // User did not provide a file and a line
        if (empty($file) || empty($line)) {
            // Get the debug backtrace information
            $debugBacktrace = debug_backtrace();

            // Get the file
            $file = $debugBacktrace[1]['file'];

            // Get the line
            $line = $debugBacktrace[1]['line'];
        }

        // Split the message into multiple lines
        if (!is_string($message)) {
            $message = var_export($message, true);
        }

        // Prepare the result
        $contents = '';
        
        // Clean-up \r\n
        $message = preg_replace('%\r\n%', PHP_EOL, $message);
        $message = preg_replace('%\r%', '', $message);
        
        // Get the session data
        $sessionData = array(
            Config::getUse(),
            'U' . WordPress_Session::getInstance()->getUserId(),
            'P' . WordPress_Session::getInstance()->getProjectId()
        );
        
        // Snapshot ID provided
        if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID])) {
            // Add the extra item to the log
            $sessionData[] = 'S' . Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID];
        }
        
        // Prepare the message array
        $messageArrayCommon = array(
            str_pad(getmypid(), 6),
            date('d.m H:i:s'),
            str_pad(basename($file, '.php'), 16),
            str_pad($line, 5),
        );
        
        // Break the lines
        foreach (preg_split("%\n%", $message) as $messageLine) {
            // Start with the common details
            $messageArray = $messageArrayCommon;
            
            // Store the error level
            if ($logErrorLevel) {
                $messageArray[] = str_pad(strtoupper($errorLevel), 7, ' ', STR_PAD_RIGHT);
            }
            
            // The last element is always the message
            $messageArray[] = $messageLine;
            
            // Prepare the information
            $logInfo = array_merge($sessionData, self::$_extra, $messageArray);

            // Prepare the contents
            $contents .= ' ' . implode(' | ', $logInfo) . PHP_EOL;
        }

        // Append to the log file
        @file_put_contents($logPath = ROOT . '/web/log/log.txt', $contents, FILE_APPEND);
        
        // Set the file permissions
        if (!self::$_checkedFilePerms) {
            if (0666 !== (fileperms($logPath) & 0777)) {
                @chmod($logPath, 0666);
            }
            self::$_checkedFilePerms = true;
        }
    }

    /**
     * Store controller events locally; logs are visible online at "/users/logs"
     * 
     * @param string $controllerName  Controller name
     * @param string $methodName      Method name
     * @param array  $methodArguments Method arguments
     * @param array  $customPost      (optional) Override the $_POST data for logging purposes
     */
    public static function controller($controllerName, $methodName = '', $methodArguments = array(), $customPost = null) {
        // Implement "Log Ignore"
        if (class_exists($controllerName) && isset($controllerName::$logIgnore) && in_array(strtolower($methodName), array_map('strtolower', $controllerName::$logIgnore))) {
            return;
        }
        
        // Create the LOG folder
        if (!self::$_checkedFilePermsController && !is_dir(ROOT . '/web/log')) {
            Folder::create(ROOT . '/web/log');
        }
        
        // Get the user model
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
        // Valid id
        $userId = 0;
        if (null != $userModel) {
            $userId = $userModel->id;
        }
        
        // Get the remote IP, if available
        $remoteIp = null;
        if (isset($_SERVER) && isset($_SERVER['REMOTE_ADDR'])) {
            $remoteIp = $_SERVER['REMOTE_ADDR'];
        }
        
        // Ignore local crons
        if (0 == $userId && preg_match('%^(127\.0\.0\.1|192\.168\.\d+\.\d+)$%', $remoteIp)) {
            return;
        }
        
        // Store the data
        @file_put_contents(
            $logPath = ROOT . '/web/log/log_user_' . $userId . '.txt',
            json_encode(
                array(
                    date('j M Y, H:i:s A'),
                    $remoteIp,
                    $controllerName, 
                    $methodName, 
                    $methodArguments,
                    is_array($customPost) ? $customPost : (isset($_POST) ? filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING) : array()),
                )
            ) . PHP_EOL,
            FILE_APPEND
        );
        
        // Set the file permissions
        if (!self::$_checkedFilePermsController) {
            if (0666 !== (fileperms($logPath) & 0777)) {
                @chmod($logPath, 0666);
            }
            self::$_checkedFilePermsController = true;
        }
    }
    
    /**
     * Log an AJAX call
     * 
     * @param string $controllerName Controller name
     * @param string $methodName     Method name
     * @param array  $arguments  Method arguments
     * @param mixed  $result     Result
     */
    public static function ajax($controllerName, $methodName, $arguments, $result = null) {
        // Skip AJAX logs unless in debug mode
        if (!Log::check(Log::LEVEL_DEBUG))  {
            return;
        }
        
        // Implement "Log Ignore"
        if (class_exists($controllerName) && isset($controllerName::$logIgnore) && in_array(strtolower($methodName), array_map('strtolower', $controllerName::$logIgnore))) {
            return;
        }
        
        // Prepare the log type
        $logType = null === $result ? 'Call' : 'Result';
        
        // Prepare the log text
        $logText = sprintf(
            '<%1$s>%2$s%3$s%2$s</%1$s>%2$s',
            $logType,
            PHP_EOL,
            var_export(null === $result ? $arguments : $result, true)
        );
        
        // Prepare the Log File
        $logFile = 'Ajax:' . preg_replace('%^Controller_Ajax_%i', '', $controllerName);
        
        // Prepare the Log Line
        $logLine = ucfirst($methodName);
        
        // Log it
        self::_log($logText, self::LEVEL_DEBUG, $logFile, $logLine, false);
    }

    /**
     * Log at a debug level
     * 
     * @param mixed   $message Message
     * @param string  $file    Current file
     * @param int     $line    Current line
     * @param boolean $forced  (optional) Forced logging; default <b>false</b>
     */
    public static function debug($message, $file = '', $line = '', $forced = false) {
        self::_log($message, self::LEVEL_DEBUG, $file, $line, true, $forced);
    }
    
    /**
     * Log at a info level
     * 
     * @param mixed   $message Message
     * @param string  $file    Current file
     * @param int     $line    Current line
     * @param boolean $forced  (optional) Forced logging; default <b>false</b>
     */
    public static function info($message, $file = '', $line = '', $forced = false) {
        self::_log($message, self::LEVEL_INFO, $file, $line, true, $forced);
    }

    /**
     * Log at a warning level
     * 
     * @param mixed   $message Message
     * @param string  $file    Current file
     * @param int     $line    Current line
     * @param boolean $forced  (optional) Forced logging; default <b>false</b>
     */
    public static function warning($message, $file = '', $line = '', $forced = false) {
        self::_log($message, self::LEVEL_WARNING, $file, $line, true, $forced);
    }

    /**
     * Log at a error level
     * 
     * @param mixed   $message Message
     * @param string  $file    Current file
     * @param int     $line    Current line
     * @param boolean $forced  (optional) Forced logging; default <b>false</b>
     */
    public static function error($message, $file = '', $line = '', $forced = false) {
        self::_log($message, self::LEVEL_ERROR, $file, $line, true, $forced);
    }

}

/*EOF*/