<?php
/**
 * Theme Warlock - Model_Project
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project {

    const LOG_CHAR_START  = '>';
    const LOG_CHAR_FINISH = '=';
    
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
     * Project configuration
     * 
     * @var Model_Project_Config
     */
    protected $_projectConfig;
    
    /**
     * Project marker
     * 
     * @var Model_Project_Marker
     */
    protected $_projectMarker;
    
    /**
     * Project
     * 
     * @param int $userId    User ID
     * @param int $projectId Project ID
     */
    public function __construct($userId, $projectId) {
        $this->_userId = $userId;
        $this->_projectId = $projectId;
        $this->_projectConfig = Model_Project_Config::getInstance($userId, $projectId);
        $this->_projectMarker = new Model_Project_Marker($userId, $projectId);
    }
    
    /**
     * Generate the project and store it in the "dist" folder
     * 
     * @return boolean
     */
    public static function generate($userId = null, $projectId = null) {
        Console::h1('Deliverables Generator');
        
        try {
            // Get the User ID
            if (null === $userId) {
                $userId = WordPress_Session::getInstance()->getUserId();
            }

            // User ID must be a number
            if (!is_numeric($userId)) {
                throw new Exception('User ID must be a number');
            }

            // Get the Project ID
            if (null === $projectId) {
                $projectId = WordPress_Session::getInstance()->getProjectId();
            }

            // User ID must be a number
            if (!is_numeric($projectId)) {
                throw new Exception('Project ID must be a number');
            }

            // Get the list of snapshots
            $snapshots = WordPress_Snapshots::getInstance($projectId, $userId)->getAll();

            // Nothing to do
            if (!count($snapshots)) {
                throw new Exception('No snapshots found');
            }

            // Go through the snapshots
            foreach ($snapshots as $snapshotKey => $snapshot) {
                // Prepare the extra data
                $extraData = array(
                    Cli_Run_Integration::IOPT_TASK_PROJECT_ID => $projectId,
                    Cli_Run_Integration::IOPT_TASK_USER_ID    => $userId,
                    Cli_Run_Integration::IOPT_SNAPSHOT_ID     => $snapshot->getId(),
                );

                // Additive task
                if (0 !== $snapshotKey) {
                    $extraData[Cli_Run_Integration::IOPT_TASK_ADDITIVE] = 1;
                }

                // Final task
                if ($snapshotKey === count($snapshots) - 1) {
                    $extraData[Cli_Run_Integration::IOPT_TASK_PACKING] = 1;
                }

                // Run the tasks list for this snapshot
                passthru(
                    Process::getPhpCommand(
                        ROOT . '/index.php', 
                        Cli_Run_Integration::TOOL_EXTRA . ' ' . base64_encode(json_encode($extraData)), 
                        null, 
                        $userId, 
                        $projectId
                    )
                );
            }
        } catch (Exception $exc) {
            Console::p($exc->getMessage(), false);
            return false;
        }
        
        // All went well
        return true;
    }
    
    /**
     * Get the user ID
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
     * Get the project configuration
     * 
     * @return Model_Project_Config
     */
    public function getConfig() {
        return $this->_projectConfig;
    }
    
    
    /**
     * Get the project marker
     * 
     * @return Model_Project_Marker
     */
    public function getMarker() {
        return $this->_projectMarker;
    }
    
    /**
     * Get the last logged entry related to this project
     * 
     * @return string|null
     */
    public function getStatus() {
        // Get the log lines
        if (is_file(ROOT . '/web/log/log.txt')) {
            // Get the log lines
            $logLines = shell_exec('tail -1000 ' . ROOT . '/web/log/log.txt | grep -E "\|\s+U' . $this->_userId . '\s+\|\s+P' . $this->_projectId. '\s+\|.*?\|\s+INFO\s+\|\s+(' . self::LOG_CHAR_START . '|' . self::LOG_CHAR_FINISH . ')"');

            // Valid result
            if (null != $logLines && strlen($logLines)) {
                // Get the last line
                if (preg_match('%(' . self::LOG_CHAR_START . '|' . self::LOG_CHAR_FINISH . ')(.*?)$%', $logLines, $lastLineMatches)) {
                    // Prepare the prefix
                    $prefix = '';
                    switch ($lastLineMatches[1]) {
                        case self::LOG_CHAR_START:
                            $prefix = 'Started';
                            break;
                        
                        case self::LOG_CHAR_FINISH:
                            $prefix = 'Finished';
                            break;
                    }
                    
                    // All done
                    return (strlen($prefix) ? ($prefix . ' ') : '') . trim($lastLineMatches[2]);
                }
            }
        }
        
        // No log entry found
        return null;
    }
    
    /**
     * Stage the current project in the WordPress SandBox
     * 
     * @param string $snapshotId Get a particular snapshot
     */
    public function stage($snapshotId = null) {
        // Get the current user id
        $wpSessionId = WordPress_Session::getInstance()->getUserId();
        
        // Set a forced session
        WordPress_Session::getInstance()->setUserId($this->_userId);
        WordPress_Session::getInstance()->setProjectId($this->_projectId);

        // Clean-up the output
        IO::initFolders();
        
        // Prepare the extra data
        $extraData = array(
            Cli_Run_Integration::IOPT_STAGING => Csv::TRUE,
        );
        
        // Snapshot provided
        if (null !== $snapshotId && is_numeric($snapshotId)) {
            $extraData[Cli_Run_Integration::IOPT_SNAPSHOT_ID] = $snapshotId;
        }
        
        // Generate the theme
        Process::startExtra($extraData);
        
        // Revert the session
        WordPress_Session::getInstance()->setUserId($wpSessionId);
    }
    
}

/* EOF */
