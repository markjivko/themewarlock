<?php
/**
 * Theme Warlock - Cron
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron {

    /**
     * Cron Manager
     * 
     * @param string $taskName Task to execute
     * @return null
     */
    public static function run($taskName = null) {
        // Execute a particular task
        if (null !== $taskName) {
            do {
                // Validate the name
                if (!preg_match('%^Cron_Task_\w+%', $taskName)) {
                    break;
                }
                
                // Class does not exist
                if (!class_exists($taskName)) {
                    break;
                }
                
                // Get the class instance
                $taskInstance = new $taskName();
                
                // Get the method
                if (!method_exists($taskInstance, 'execute')) {
                    break;
                }
                
                // Execute
                $taskInstance->execute();
            } while (false);
            
            // All done
            return;
        }

        // Get cron list
        if (count($cronList = Cron_List::getInstance()->getList())) {
            foreach ($cronList as /* @var $taskReflection ReflectionClass */ $taskReflection) {
                // Execute a parallel PHP process
                Process::startTool(Cli_Run_Integration::TOOL_CRON . ' ' . $taskReflection->name, null, null, null, true, true);
            }
        }
    }

}

/* EOF */