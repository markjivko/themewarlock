<?php
/**
 * Theme Warlock - Process
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
 
class Process {
    
    /**
     * Kill a process by ID
     * 
     * @param int $processId Process ID
     */
    public static function kill($processId) {
        self::startAsync('kill ' . $processId);
    }
    
    /**
     * Run an asynchronous task
     * 
     * @param string $command Command - shell escaping must be already performed
     * @return int Return value
     */
    public static function startAsync($command, $silent = true, &$pid = 0) {
        // Prepare the command
        $runCommand = $command . ' > /dev/null 2>&1 & echo $!; ';

        // Run the shell
        $pid = exec($runCommand, $output, $returnVar);
        
        // Not silent
        !$silent && print(implode(PHP_EOL, $output));
        
        // All done
        return $returnVar;
    }
    
    /**
     * Run the main tasks with extra arguments
     * 
     * @param array   $extraData    Extra data; associative array
     * @param string  $accountName (optional) Account name
     * @param int     $userId      (optional) User ID
     * @param int     $projectId   (optional) Project ID
     * @param boolean $silent      (optional) Hide output
     * @param boolean $async       (optional) Async task
     */
    public static function startExtra(Array $extraData, $accountName = null, $userId = null, $projectId = null, $silent = true, $async = false) {
        // Prepare the data
        $payload = base64_encode(json_encode($extraData));
        
        // Start the tool
        return self::startTool(Cli_Run_Integration::TOOL_EXTRA . ' ' . $payload, $accountName, $userId, $projectId, $silent, $async);
    }
    
    /**
     * Pass-through a local tool
     * 
     * @param string  $toolData    Tool information
     * @param string  $accountName (optional) Account name 
     * @param int     $userId      (optional) User ID
     * @param int     $projectId   (optional) Project ID
     * @param boolean $silent      (optional) Hide output
     * @param boolean $async       (optional) Async task
     */
    public static function startTool($toolData = '', $accountName = null, $userId = null, $projectId = null, $silent = false, $async = false) {
        // Prepare the command
        $command = self::getPhpCommand(ROOT . '/index.php', trim($toolData), $accountName, $userId, $projectId);
        
        // Execute the command
        if (!$async) {
            $silent && ob_start();
            passthru($command, $returnValue);
            $silent && ob_end_clean();

            // All done
            return 0 === $returnValue;
        }
        
        // Async task
        return self::startAsync($command, $silent);
    }
    
    /**
     * Get a PHP -f command, passing on the Session
     * 
     * @param string $filePath    File path
     * @param string $extra       (optional) Extra options - must be already escaped
     * @param string $accountName (optional) Account Name
     * @param int    $userId      (optional) User ID
     * @param int    $projectId   (optional) Project ID
     * @return string Command
     */
    public static function getPhpCommand($filePath, $extra = '', $accountName = null, $userId = null, $projectId = null) {
        // Return the command
        return trim('/usr/bin/php '. self::_getPhpSession($accountName, $userId, $projectId) . '-f ' . escapeshellarg($filePath) . ' ' . $extra);
    }
    
    /**
     * Get the PHP session instruction (either an empty string or a string followed by 1 space)
     * 
     * @param string $accountName Account name
     * @param int    $userId      User ID
     * @param int    $projectId   Project ID
     */
    protected static function _getPhpSession($accountName = null, $userId = null, $projectId = null) {
        // Get the account name
        if (null === $accountName) {
            $accountName = WordPress_Session::getInstance()->getAccount();
        }
        
        // Revert to the default
        if (!strlen($accountName)) {
            $accountName = Config::getUse();
        }
        
        // User ID not provided, fallback to the session
        if (null === $userId) {
            $userId = WordPress_Session::getInstance()->getUserId();
        }
        
        // Project ID not provided, fallback to the session
        if (null === $projectId) {
            $projectId = WordPress_Session::getInstance()->getProjectId();
        }
        
        // Set the session
        return '-d session.name=' . 
            WordPress_Session::USER_PREFIX . $userId . 
            WordPress_Session::SEPARATOR . $projectId . 
            WordPress_Session::SEPARATOR . $accountName . 
            ' ';
    }
}

/*EOF*/