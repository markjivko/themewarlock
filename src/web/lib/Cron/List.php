<?php
/**
 * Theme Warlock - Cron_List
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron_List {
    /**
     * Time reference
     * 
     * @var array
     */
    protected $_reference = array();
    
    /**
     * Tasks list
     * 
     * @var array
     */
    protected $_tasks = array();
    
    /**
     * Cron List
     * 
     * @var Cron_List 
     */
    protected static $_instance;
    
    /**
     * Cron List
     * 
     * @return Cron_List
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new static();
        }
        
        return self::$_instance;
    }
    
    /**
     * Cron List
     */
    protected function __construct() {
        // Store the timeframe
        list(
            $this->_reference[Cron_Task::MINUTE], 
            $this->_reference[Cron_Task::HOUR], 
            $this->_reference[Cron_Task::DAY], 
            $this->_reference[Cron_Task::DAYOFWEEK], 
            $this->_reference[Cron_Task::MONTH], 
            $this->_reference[Cron_Task::YEAR]
        ) = array_map('intval', explode(' ', date('i G j N n Y')));

        // Get all the tasks
        array_map(function($item) {
            // Store the class name
            $taskClassName = 'Cron_Task_' . basename($item, '.php');
            
            // Store the actual task
            $this->_tasks[] = new ReflectionClass($taskClassName);
        }, glob(ROOT . '/web/lib/Cron/Task/*.php'));
    }
    
    /**
     * Get the tasks list
     * 
     * @return ReflectionClass[] Array of ReflectionClass for the tasks that need execution
     */
    public function getList() {
        // Prepare the result
        $result = array();
        
        // Go through the tasks
        foreach ($this->_tasks as /* @var $taskReflection ReflectionClass */ $taskReflection) {
            // Get the task schedule
            $taskSchedule = $taskReflection->getMethod('schedule')->invoke($taskReflection->newInstance());
            
            // Validate the schedule
            if ($this->_onTime($taskSchedule)) {
                $result[] = $taskReflection;
            }
        }

        // All done
        return $result;
    }
    
    /**
     * Check if now is the time to execute a task based on its schedule
     * 
     * @param mixed[] $taskSchedule Task schedule
     * @return boolean True if task should be executed, false otherwise
     */
    protected function _onTime($taskSchedule) {
        // Not defined - never execute
        if (!is_array($taskSchedule)) {
            return false;
        }
        
        // Prepare the real schedule
        $schedule = array();

        // Go through the allowed parameters
        foreach (Cron_Task::$timeElements as $timeElement) {
            // Value not provided
            if (!isset($taskSchedule[$timeElement])) {
                // Assume always true
                $schedule[$timeElement] = function() {return true;};
                
                // Next item
                continue;
            } 
            
            // Value not a callable
            if (!is_callable($taskSchedule[$timeElement])) {
                // Value is numeric
                if (is_numeric($taskSchedule[$timeElement])) {
                    // Prepare the reference
                    $reference = $taskSchedule[$timeElement];
                            
                    // Do a numeric check
                    $schedule[$timeElement] = function($item) use ($reference) {return $item == $reference;};
                } else {
                    // Assume always true
                    $schedule[$timeElement] = function() {return true;};
                }
                
                // Next item
                continue;
            }
            
            // Store the callable
            $schedule[$timeElement] = $taskSchedule[$timeElement];
        }

        // Validate each schedule item individually
        foreach ($schedule as $timeElement => $callable) {
            if (!$callable($this->_reference[$timeElement])) {
                return false;
            }
        }
        
        // Need to execute
        return true;
    }
}

/*EOF*/