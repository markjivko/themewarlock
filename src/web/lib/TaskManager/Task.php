<?php
/**
 * Theme Warlock - TaskManager_Task
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class TaskManager_Task {

    const STATUS_PENDING = 'pending';
    const STATUS_WORKING = 'working';
    const STATUS_DONE    = 'done';
    const STATUS_STOPPED = 'stopped';
    const STATUS_FAILED  = 'failed';
    
    const JSON_STATUS     = 'status';
    const JSON_LAST_ERR   = 'lastErr';
    const JSON_PID        = 'pid';
    const JSON_PERCENT    = 'percent';
    const JSON_START_TIME = 'startTime';
    const JSON_END_TIME   = 'endTime';
    const JSON_USER_ID    = 'userId';
    const JSON_PROJECT_ID = 'projectId';
    
    protected $_id;
    protected $_path;
    
    protected $_status = self::STATUS_PENDING;
    protected $_lastErr = '';
    protected $_pid = 0;
    protected $_percent = 0;
    protected $_startTime = 0;
    protected $_endTime = 0;
    protected $_userId = 0;
    protected $_projectId = 0;

    /**
     * Task ID
     * 
     * @return int
     */
    public function getId() {
        return $this->_id;
    }
    
    /**
     * Task Status, one of <ul>
     * <li>TaskManager_Task::STATUS_PENDING</li>
     * <li>TaskManager_Task::STATUS_WORKING</li>
     * <li>TaskManager_Task::STATUS_DONE</li>
     * <li>TaskManager_Task::STATUS_STOPPED</li>
     * </ul>
     * @return string
     */
    public function getStatus() {
        return $this->_status;
    }
    
    /**
     * Get the last error
     * 
     * @return string
     */
    public function getLastErr() {
        return $this->_lastErr;
    }
    
    /**
     * Task process ID
     * 
     * @return int
     */
    public function getPid() {
        return $this->_pid;
    }
    
    /**
     * Task execution percent
     * 
     * @return int
     */
    public function getPercent() {
        return $this->_percent;
    }
    
    /**
     * Task start time (Unix timestamp)
     * 
     * @return int
     */
    public function getStartTime() {
        return $this->_startTime;
    }
    
    /**
     * Task end time (Unix timestamp)
     * 
     * @return int
     */
    public function getEndTime() {
        return $this->_endTime;
    }
    
    /**
     * Task execution duration in seconds
     * 
     * @return int
     */
    public function getDuration() {
        return $this->_endTime >= $this->_startTime ? ($this->_endTime - $this->_startTime) : 0;
    }
    
    /**
     * Task author/user ID
     * 
     * @return int
     */
    public function getUserId() {
        return $this->_userId;
    }
    
    /**
     * Task project ID
     * 
     * @return int
     */
    public function getProjectId() {
        return $this->_projectId;
    }
    
    /**
     * Task
     * 
     * @param string $taskPath Path to task JSON path ({id}.json)
     * @param int $userId      (optional) User ID
     * @param int $projectId   (optional) Project ID
     * @throws Exception
     */
    public function __construct($taskPath, $userId = null, $projectId = null) {
        // Validate the path
        if (!preg_match('%^\d+\.json$%', basename($taskPath))) {
            throw new Exception('Invalid task path');
        }
        
        // Parent folder not defined
        if (!is_dir(dirname($taskPath))) {
            throw new Exception('Task parent folder does not exist');
        }
        
        // Store the path
        $this->_path = $taskPath;
        
        // Get the id
        $this->_id = intval(preg_replace('%\.json$%', '', basename($taskPath)));
        
        // Prepare a reflection
        $reflectionClass = new ReflectionClass($this);
        
        // Prepare the data
        $data = array();
        foreach ($reflectionClass->getConstants() as $constantKey => $constantValue) {
            if (preg_match('%^JSON_%', $constantKey)) {
                // Get the property
                $variableName = '_' . $constantValue;
                
                // Store the default
                if (isset($this->$variableName)) {
                    $data[$constantValue] = $this->$variableName;
                }
            }
        }

        // Valid file found
        if (is_file($taskPath)) {
            // Get the data
            $jsonData = @json_decode(file_get_contents($taskPath), true);
            
            // Valid data
            if (is_array($jsonData)) {
                foreach ($jsonData as $key => $value) {
                    if (isset($data[$key])) {
                        $data[$key] = $value;
                    }
                }
            }
        }
        
        // Set a custom user ID
        if (null !== $userId) {
            $userId = intval($userId);
            if ($userId < 0) {
                $userId = 0;
            }
            $data[self::JSON_USER_ID] = $userId;
        }
        
        // Set a custom project ID
        if (null !== $projectId) {
            $projectId = intval($projectId);
            if ($projectId < 0) {
                $projectId = 0;
            }
            $data[self::JSON_PROJECT_ID] = $projectId;
        }
        
        // Replace local values
        foreach ($data as $key => $value) {
            // Prepare the variable name
            $variableName = '_' . $key;
            
            // Store the data
            $this->$variableName = $value;
        }
        
        // Trigger a save
        if (null !== $userId || null !== $projectId || !is_file($taskPath)) {
            $this->_save();
        }
    }
    
    /**
     * Save the task
     */
    protected function _save() {
        // Save the file
        file_put_contents($this->_path, json_encode($this->_getData()));
    }
    
    /**
     * Get the task data
     * 
     * @return array
     */
    protected function _getData() {
        // Prepare the data
        $data = array();
        
        // Prepare a reflection
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getConstants() as $constantKey => $constantValue) {
            if (preg_match('%^JSON_%', $constantKey)) {
                // Get the property
                $variableName = '_' . $constantValue;
                
                // Store the default
                if (isset($this->$variableName)) {
                    $data[$constantValue] = $this->$variableName;
                }
            }
        }
        
        // All done
        return $data;
    }
    
    /**
     * Enqueue the task<br/>
     * This will:<ul>
     * <li>Set the percent to 0</li>
     * <li>Mark the task as TaskManager_Task::STATUS_PENDING</li>
     * <li>Clear the PID</li>
     * <li>Clear the start time</li>
     * <li>Clear the end time</li>
     * </ul>
     * 
     * @return TaskManager_Task
     * @throws Exception
     */
    public function enqueue() {
        // Set status to pending
        if (self::STATUS_WORKING === $this->_status) {
            throw new Exception('Task #' . $this->_id . ' cannot be enqueued, currently running');
        }
        
        // 0%
        $this->_percent = 0;
        
        // Store the pending status
        $this->_status = self::STATUS_PENDING;
        
        // Clear the PID
        $this->_pid = 0;
        
        // Clear the start time
        $this->_startTime = 0;
        
        // Clear the end time
        $this->_endTime = 0;
        
        // Save
        $this->_save();
        
        // All done
        return $this;
    }
    
    /**
     * Dequeue the task<br/>
     * This will:<ul>
     * <li>Set the percent to 100</li>
     * <li>Mark the task as TaskManager_Task::STATUS_DONE</li>
     * <li>Clear the PID</li>
     * <li>Clear the start time</li>
     * <li>Clear the end time</li>
     * </ul>
     * 
     * @return TaskManager_Task
     * @throws Exception
     */
    public function dequeue() {
        // Set status to pending
        if (self::STATUS_WORKING === $this->_status) {
            throw new Exception('Task #' . $this->_id . ' cannot be dequeued, currently running');
        }
        
        // 100%
        $this->_percent = 100;
        
        // Store the pending status
        $this->_status = self::STATUS_DONE;
        
        // Clear the PID
        $this->_pid = 0;
        
        // Clear the start time
        $this->_startTime = 0;
        
        // Clear the end time
        $this->_endTime = 0;
        
        // Save
        $this->_save();
        
        // All done
        return $this;
    }
    
    /**
     * Start the execution of this task<br/>
     * This will:<ul>
     * <li>Store the start time</li>
     * <li>Set the percent to 0</li>
     * <li>Mark the task as TaskManager_Task::STATUS_WORKING</li>
     * <li>Clean-up the last error</li>
     * <li>Assign the PID</li>
     * </ul>
     * 
     * @return TaskManager_Task
     * @throws Exception
     */
    public function start() {
        // Invalid status
        if (self::STATUS_WORKING === $this->_status) {
            throw new Exception('Task #' . $this->_id . ' cannot be started, already running');
        }
        
        // Start
        Log::check(Log::LEVEL_INFO) && Log::info('Task::start #' . $this->_id . ', user #' . $this->_userId, ', project #' . $this->_projectId);
        
        // Store the start time
        $this->_startTime = time();
        
        // 0%
        $this->_percent = 0;
        
        // Set status to working
        $this->_status = self::STATUS_WORKING;
        
        // Clean-up the last error
        $this->_lastErr = '';
        
        // Remove project markers to force re-claiming projects
        (new Model_Project_Marker($this->_userId, $this->_projectId))->unmark();
        
        // Get the project path
        $projectPath = Model_Projects::getProjectPath($this->_userId, $this->_projectId);
        
        // Invalid folder
        if (!is_dir($projectPath)) {
            // Log the error
            Log::check(Log::LEVEL_INFO) && Log::info('Task #' . $this->_id . ' project not found for user #' . $this->_userId, ', project #' . $this->_projectId);
            
            // Stop here
            return $this->stop(false);
        }
        
        // Async task
        Process::startAsync(
            Process::getPhpCommand(
                ROOT . '/index.php', 
                Cli_Run_Integration::TOOL_GENERATE, 
                null, 
                $this->_userId, 
                $this->_projectId
            ), 
            true, 
            $pid
        );
        
        // Store the current process ID
        $this->_pid = $pid;
        
        // Save the data
        $this->_save();
        
        // Inform us about this task
        TaskbarNotifier::sendMessage(
            'Task Manager', 
            'Generating project #' . $this->getProjectId() . ' by user #' . $this->getUserId() . '...'
        );
        
        // All done
        return $this;
    }
    
    /**
     * Mark an exception<br/>
     * This will:<ul>
     * <li>Mark the task as TaskManager_Task::STATUS_FAILED</li>
     * <li>Store the end time</li>
     * </ul>
     * 
     * @return TaskManager_Task
     */
    public function markException($string = '') {
        // Mark as stopped
        $this->_status = self::STATUS_FAILED;
        
        // Store the error
        $this->_lastErr = $string;
        
        // Save
        $this->_save();
        
        // Inform us about this task
        TaskbarNotifier::sendMessage(
            'Task Manager', 
            'Failed project #' . $this->getProjectId() . ' by user #' . $this->getUserId() . ': ' . $string,
            TaskbarNotifier::TYPE_ERROR
        );
        
        // All done
        return $this;
    }
    
    /**
     * Stop the task<br/>
     * This will:<ul>
     * <li>Kill the current process</li>
     * <li>Mark the task as TaskManager_Task::STATUS_STOPPED or TaskManager_Task::STATUS_FAILED</li>
     * <li>Clear the PID</li>
     * <li>Store the end time</li>
     * </ul>
     * 
     * @return TaskManager_Task
     */
    public function stop($isFailure = false) {
        // Kill the current process
        if (0 != $this->_pid) {
            // Actual process to kill
            Process::kill($this->_pid);
            
            // Store the end time
            $this->_endTime = time();
        }
        
        // Mark as stopped
        $this->_status = $isFailure ? self::STATUS_FAILED : self::STATUS_STOPPED;
        
        // Clear the PID
        $this->_pid = 0;
        
        // Store the end time
        $this->_endTime = time();
        
        // Save
        $this->_save();
        
        // Inform us about this task
        TaskbarNotifier::sendMessage(
            'Task Manager', 
            'Stopped project #' . $this->getProjectId() . ' by user #' . $this->getUserId(),
            $isFailure ? TaskbarNotifier::TYPE_ERROR : TaskbarNotifier::TYPE_INFO
        );
        
        // All done
        return $this;
    }
    
    /**
     * Array serialization of this task
     * 
     * @return array
     */
    public function toArray() {
        return $this->_getData();
    }
    
    /**
     * Set the task execution percent (0-100)<br/>
     * Setting 100 will:<ul>
     * <li>Mark the task as TaskManager_Task::STATUS_DONE</li>
     * <li>Store the end time</li>
     * <li>Clear the PID</li>
     * </u>
     * 
     * @return TaskManager_Task
     */
    public function setPercent($percent) {
        // Invalid status
        if (self::STATUS_WORKING !== $this->_status) {
            Log::check(Log::LEVEL_DEBUG) && Log::debug('Task #' . $this->_id . ' cannot set progress to idle task');
            return $this;
        }
        
        // Integer
        $percent = intval($percent);
        
        // Ranges
        $percent = $percent < 0 ? 0 : ($percent > 100 ? 100 : $percent);
        
        // Store the percent
        $this->_percent = $percent;
        
        // Store the end time
        $this->_endTime = time();
        
        // All done
        if (100 == $percent) {
            // Mark as done
            $this->_status = self::STATUS_DONE;
            
            // Remove the PID
            $this->_pid = 0;
            
            // Inform us about this task
            TaskbarNotifier::sendMessage(
                'Task Manager', 
                'Finished project #' . $this->getProjectId() . ' by user #' . $this->getUserId()
            );
        }
        
        // Save the data
        $this->_save();
        
        // Next task
        if (100 == $percent) {
            // More than 5 seconds until the next batch (to avoid overlaps)
            if (intval(date('s')) <= 55) {
                // Get the first pending task from the list
                $task = TaskManager::getInstance()->getNext();

                // Execute it
                if (null != $task) {
                    // Start the task
                    try {
                        $task->start();
                    } catch (Exception $exc) {
                        Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                    }
                }
            }
        }
        
        // All done
        return $this;
    }
    
}

/* EOF */
