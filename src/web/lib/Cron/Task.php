<?php
/**
 * Theme Warlock - Cron_Task
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

abstract class Cron_Task {
    // Time elements
    const MINUTE    = 'minute';
    const HOUR      = 'hour';
    const DAY       = 'day';
    const DAYOFWEEK = 'dayOfWeek';
    const MONTH     = 'month';
    const YEAR      = 'year';
    
    /**
     * Allowed time elements
     * 
     * @var string[]
     */
    public static $timeElements = array(
        self::MINUTE,
        self::HOUR,
        self::DAY,
        self::DAYOFWEEK,
        self::MONTH,
        self::YEAR,
    );
    
    /**
     * Set the schedule
     * 
     * @return array
     */
    abstract function schedule();
    
    /**
     * Execute
     * 
     * @return array
     */
    abstract function execute();
}

/*EOF*/