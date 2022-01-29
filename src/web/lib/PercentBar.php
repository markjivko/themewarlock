<?php

/**
 * Theme Warlock - PercentBar
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class PercentBar {

    protected static $_lastOutput = 0;

    protected static $_totalTasks = 0;
    
    protected static $_totalSnapshots = null;
    
    protected static $_currentSnapshot = null;
    
    protected static $_cliMode;
    
    /**
     * Display a Percent Bar
     * 
     * @param float  $percent Percent (0-100)
     * @param string $extra   Extra data to display
     */
    public static function display($percent, $extra = null) {
        // Get the percentage
        $percentInt = (int) $percent;

        // Slow increment?
        if (self::$_lastOutput < $percentInt) {
            for ($i = self::$_lastOutput; $i <= $percentInt; $i++) {
                self::_display($i);
            }
        }

        // Output the percent
        self::_display($percent, $extra);

        // Save the last Output
        self::$_lastOutput = $percentInt;
        
        // Get the total number of tasks
        if (0 == self::$_totalTasks) {
            // Get the task IDs
            $taskIds = array_map(
                function($item) {
                    return intval(preg_replace('%^(\d+).*%', '$1', basename($item)));
                }, 
                glob(ROOT . '/web/lib/Tasks/*.php')
            );
                
            // Store the maximum
            self::$_totalTasks = max($taskIds);
        }
        
        // Get the total number of snapshots
        if (null === self::$_totalSnapshots) {
            self::$_totalSnapshots = count(WordPress_Snapshots::getInstance()->getAll());
            if (self::$_totalSnapshots <= 0) {
                self::$_totalSnapshots = 1;
            }
        }
        
        // Get the current snapshot
        if (null === self::$_currentSnapshot) {
            self::$_currentSnapshot = isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID]) ? intval(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID]) : 0;
            if (self::$_currentSnapshot <= 0) {
                self::$_currentSnapshot = self::$_totalSnapshots;
            }
        }
        
        // Get the functions
        $backtrace = debug_backtrace(null, 2);
        if ('Tasks' === basename(dirname($backtrace[0]['file']))) {
            // Get the task ID
            $taskId = intval(preg_replace('%^(\d+).*%', '$1', basename($backtrace[0]['file'])));

            // Compute the total percentage
            $totalPercent = 100 * ($taskId - 1) / self::$_totalTasks + $percent / self::$_totalTasks;
            
            // Adjust for all snapshots
            $totalPercent = 100 * (self::$_currentSnapshot - 1) / self::$_totalSnapshots + $totalPercent / self::$_totalSnapshots;
            
            // Tick
            TaskManager::getInstance()->tick($totalPercent);
        }
    }

    /**
     * Percent bar display helper
     * 
     * @param float  $percent Percent
     * @param string $extra   Extra data
     */
    protected static function _display($percent, $extra = null) {
        // Initialize the CLI mode flag
        if (!isset(self::$_cliMode)) {
            self::$_cliMode = ('cli' === php_sapi_name());
        }
        
        // Set the line length
        $lineLength = 58;

        // Command Line interface
        if (self::$_cliMode) {
            // Clear the line
            echo str_repeat(chr(8), $lineLength + 12 + strlen($extra)) . str_repeat(' ', $lineLength + 12 + strlen($extra)) . str_repeat(chr(8), $lineLength + 12 + strlen($extra));

            // Output the bar
            echo '  ' . str_pad(str_repeat('*', (int) $percent / 100 * $lineLength) . ' ' . number_format(round($percent, 2), 2) . '% ' . (!empty($extra) ? $extra . ' ' : '') , $lineLength, '-', STR_PAD_RIGHT);
        }
    }
}

/*EOF*/