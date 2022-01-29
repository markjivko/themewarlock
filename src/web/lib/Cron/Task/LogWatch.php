<?php
/**
 * Theme Warlock - Cron_Task_LogWatch
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron_Task_LogWatch extends Cron_Task {
    
    /**
     * Set the schedule
     * 
     * @return array
     */
    public function schedule() {
        // At 23:55, daily
        return array(
            self::HOUR   => 23,
            self::MINUTE => 55,
        );
    }
    
    /**
     * Execute the task
     */
    public function execute() {
        // Get the log errors
        if (null !== $errorsHtml = Log::getReport(true)) {
            // Errors/warnings found
            Notifier::getInstance()->html('Log Watch: Error Report', $errorsHtml);
        }
    }
}

/*EOF*/