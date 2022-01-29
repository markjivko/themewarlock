<?php
/**
 * Theme Warlock - TaskManager
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class TaskManager {


    /**
     * TaskManager
     */
    protected static $_instance = null;
    
    /**
     * Path checker - onece per session
     * 
     * @var boolean
     */
    protected $_checkedPath = false;
    
    /**
     * Cached tasks
     * 
     * @var TaskManager_Task[]
     */
    protected $_tasks = null;
    
    /**
     * Tick object
     * 
     * @var TaskManager_Task or NULL
     */
    protected $_tick = false;
    
    /**
     * Singleton instance of TaskManager
     * 
     * @return TaskManager
     */
    public static function getInstance() {
        if (null == self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * TaskManager
     */
    protected function __construct() {
        // Singleton
    }
    
    /**
     * Get all the defined tasks
     * 
     * @return TaskManager_Task[]
     */
    public function getAll($reCheck = true) {
        // Get from cache
        if (null !== $this->_tasks && !$reCheck) {
            return $this->_tasks;
        }
        
        // Prepare the result
        $result = array();
        
        // Get the paths
        $jsonPaths = glob($this->_getTasksPath() . '/*.json');
        natsort($jsonPaths);

        // Go through the data
        foreach ($jsonPaths as $jsonPath) {
            $result[] = new TaskManager_Task($jsonPath);
        }
        
        // Store this
        $this->_tasks = $result;
        
        // All done
        return $result;
    }
    
    /**
     * Get a task by ID
     * 
     * @param int $taskId Task ID
     * @return TaskManager_Task
     */
    public function getById($taskId) {
        // Get all the tasks
        $tasks = $this->getAll();
        
        // Go through the list
        foreach ($tasks as $task) {
            if ($taskId == $task->getId()) {
                return $task;
            }
        }
        
        // Nothing found
        return null;
    }
    
    /**
     * Get the next pending task if idle
     * 
     * @return TaskManager_Task
     */
    public function getNext() {
        // Get all the tasks
        $tasks = $this->getAll();
        
        // No tasks defined
        if (!count($tasks)) {
            return null;
        }
        
        // Idle flag
        $result = null;
        
        foreach ($tasks as $task) {
            // Some task is executing
            if (TaskManager_Task::STATUS_WORKING === $task->getStatus()) {
                return null;
            }
            if (TaskManager_Task::STATUS_PENDING === $task->getStatus() && null === $result) {
                $result = $task;
            }
        }
        
        // All done
        return $result;
    }
    
    /**
     * Set the percent for the current project OR mark an exception
     * 
     * @param int|string $percentOrError Percent or Error message if $error is true
     * @param boolean    $allGood        If false, the tick function will mark an exception
     */
    public function tick($percentOrError, $allGood = true) {
        // Not cached yet
        if (false === $this->_tick) {
            $this->_tick = null;
            if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_USER_ID]) && isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_PROJECT_ID])) {
                // Go through the list
                foreach ($this->getAll(false) as $task) {
                    // Task already added
                    if (Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_USER_ID] == $task->getUserId() && Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_PROJECT_ID] == $task->getProjectId()) {
                        $this->_tick = $task;
                        break;
                    }
                }
            }
        }
        
        // Found a task
        null !== $this->_tick && (!$allGood ? $this->_tick->markException($percentOrError) : $this->_tick->setPercent($percentOrError));
    }
    
    /**
     * Check whether this isuer is locked out of editing projects because an export is in progress
     * 
     * @param int $userId Project User ID
     * @return boolean
     */
    public function isLockedForExport($userId) {
        // Invalid argument
        if (!is_numeric($userId)) {
            return false;
        }
        
        // Go through the list
        foreach ($this->getAll() as $task) {
            if ($userId == $task->getUserId()) {
                // A task is pending/executing
                if (in_array($task->getStatus(), array(TaskManager_Task::STATUS_PENDING, TaskManager_Task::STATUS_WORKING))) {
                    // Remove project markers to force re-claiming projects
                    (new Model_Project_Marker($task->getUserId(), $task->getProjectId()))->unmark();
                    return true;
                }
            }
        }
        
        // Not locked
        return false;
    }
    
    /**
     * Get a task by user ID and project ID or add it if necessary
     * 
     * @param int     $userId    User ID
     * @param int     $projectId Project ID
     * @param boolean $autoSpawn (optional) Automatically create task if not found; default <b>true</b>
     * @return TaskManager_Task|null Null if $autoSpawn = false and no task found.
     */
    public function get($userId, $projectId, $autoSpawn = true) {
        // Prepare the ID
        $taskId = 0;
        
        // Go through the list
        foreach ($this->getAll() as $task) {
            // Task already added
            if ($userId == $task->getUserId() && $projectId == $task->getProjectId()) {
                return $task;
            }
            
            // Get the max
            if ($task->getId() > $taskId) {
                $taskId = $task->getId();
            }
        }
        
        // Not auto-spawning a new task
        if (!$autoSpawn) {
            return null;
        }
        
        // Increment
        $taskId++;
        
        // Create the file
        $task = new TaskManager_Task($this->_getTasksPath() . '/' . $taskId . '.json', $userId, $projectId);
        
        // All done
        return $task;
    }
    
    /**
     * Get the path to the tasks, no trailing slash
     * 
     * @return string
     */
    protected function _getTasksPath() {
        // Prepare the path
        $tasksPath = ROOT . '/web/temp/tasks';
        
        // Check the path
        if (!$this->_checkedPath) {
            if (!is_dir($tasksPath)) {
                Folder::create($tasksPath, 0777, true);
            }
            $this->_checkedPath = true;
        }
        
        // All done
        return $tasksPath;
    }

}

/* EOF */
