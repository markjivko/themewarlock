<?php
/**
 * Theme Warlock - Cron_Task_Translations
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cron_Task_Translations extends Cron_Task {
    
    /**
     * Set the schedule
     * 
     * @return array
     */
    public function schedule() {
        // Once every 10 minutes
        return array(
            self::MINUTE => function($item) {
                return $item % 70 == 0;
            },
        );
    }
    
    /**
     * Execute the task
     */
    public function execute() {
        // Go through the languages
        foreach (WordPress_Pot_Translations_Cache::getLanguages() as $language) {
            try {
                // Translate
                WordPress_Pot_Translations_Cache::getInstance($language)->googleTranslate(true);
                
                // Sleep between 1 and 3 seconds
                usleep(mt_rand(1000, 3000) * 1000);
            } catch (Exception $exc) {
                // Log the event
                TaskbarNotifier::sendMessage(
                    'Translations - ' . $language, 
                    $exc->getMessage(), 
                    TaskbarNotifier::TYPE_ERROR
                );

                // Log this for future reference
                Log::check(Log::LEVEL_ERROR) && Log::error($exc->getMessage(), $exc->getFile(), $exc->getLine());
            }
        }
        
        // Re-validate all translations
        if (true !== WordPress_Pot_Translations_Cache::validateAll()) {
            // Log the event
            TaskbarNotifier::sendMessage(
                'Translations - Invalid entries', 
                'Please refer to the Translations page for more information', 
                TaskbarNotifier::TYPE_ERROR
            );
        }
    }

}

/*EOF*/