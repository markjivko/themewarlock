<?php
/**
 * Theme Warlock - Cron_Task_TaskManager
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron_Task_TaskManager extends Cron_Task {
    
    /**
     * Set the schedule
     * 
     * @return array
     */
    public function schedule() {
        // Once a minute
        return array();
    }
    
    /**
     * Execute the task
     */
    public function execute() {
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

/*EOF*/