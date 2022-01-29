<?php
/**
 * Theme Warlock - Cron_Task_LogRotation
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron_Task_LogRotation extends Cron_Task {
    
    /**
     * Set the schedule
     * 
     * @return array
     */
    public function schedule() {
        // Once every 5 minutes
        return array(
            self::MINUTE => function($item) {
                return $item % 5 == 0;
            },
        );
    }
    
    /**
     * Execute the task
     */
    public function execute() {
        // Go through the logs
        foreach (glob(ROOT . '/web/log/*.txt') as $logPath) {
            // Get the log name
            $logName = basename($logPath, '.txt');

            // Get the max size
            $logMaxSize = Config::getSizeToFloat(Config::get()->logSize);

            // Prepare the cache name
            $logNameCache = $logName . '-' . date('Y-m-d-H-i');

            // Base log
            if (preg_match('%^(log|log_[^-]*?)$%', $logName)) {
                // Log max size reached
                if (filesize($logPath) >= $logMaxSize) {
                    rename($logPath, dirname($logPath) . '/' . $logNameCache . '.txt');
                    TaskbarNotifier::sendMessage(
                        'Log rotation', 
                        'Switched to a new log...'
                    );
                }
            } else {
                // Very old cache
                if (time() - filemtime($logPath) >= Config::get()->logCacheDays * 86400) {
                    // Remove the cache
                    unlink($logPath);
                    TaskbarNotifier::sendMessage(
                        'Log rotation', 
                        'Removed old log from cache...'
                    );
                }
            }
        }
    }
}

/*EOF*/