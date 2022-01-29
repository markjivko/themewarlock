<?php
/**
 * Theme Warlock - Cron_Task_GarbageCollector
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron_Task_GarbageCollector extends Cron_Task {
    
    /**
     * Set the schedule
     * 
     * @return array
     */
    public function schedule() {
        // Once every 30 minutes
        return array(
            self::MINUTE => function($item) {
                return $item % 30 == 0;
            },
        );
    }
    
    /**
     * Execute the task
     */
    public function execute() {
        // Get the WordPress respositories
        $repositories = glob(Config::getWpPath(true) . '-*');
        
        // Go through all the WordPress instances
        foreach ($repositories as $wpPath) {
            Console::h1($wpPath);
            
            // Prune
            passthru('git -C "' . $wpPath . '" prune');
            
            // (Re-)enable auto garbage collection
            passthru('git -C "' . $wpPath . '" gc --auto');
        }
        
        // Inform the user
        TaskbarNotifier::sendMessage(
            'Garbage Collection', 
            'Performed GC on ' . count($repositories) . ' WordPress ' . (count($repositories) == 1 ? 'repository' : 'repositories') . '.'
        );
    }
}

/*EOF*/