<?php
/**
 * Theme Warlock - Task_3Pack
 * 
 * @title      Packing task
 * @desc       Crunch images, set placeholders, generate docs etc. and pack the final archives
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Tasks_4Pack {

    /**
     * Actions
     * 
     * @return null
     */
    public function v1() {
        // Starting point
        PercentBar::display(0);
        
        // This task is the last one to execute, after all snapshots are parsed
        if (!isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_TASK_PACKING])) {
            // All done
            PercentBar::display(100);
            
            // Stop here
            return;
        }
        
        // Get the marketplace
        $marketPlace = isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_MARKETPLACE]) ? Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_MARKETPLACE] : Dist::MARKET_THEMEFOREST;
        
        // Get the tasks
        foreach (Dist::getInstance($marketPlace)->getTasks() as $taskName => $completePercentage) {
            // Execute the task
            Dist::getInstance($marketPlace)->runTask($taskName);
            
            // Status update
            if (null !== $completePercentage) {
                PercentBar::display($completePercentage);
            }
        }
        
        // All done
        PercentBar::display(100);
    }
    
}

/* EOF */
