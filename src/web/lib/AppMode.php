<?php
/**
 * Theme Warlock - AppMode
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class AppMode {

    // App Modes
    const PRODUCTION  = 'PRODUCTION';
    const DEVELOPMENT = 'DEVELOPMENT';

    /**
     * Check the current app mode
     * 
     * @param string $appMode App Mode, one of <ul>
     * <li>AppMode::PRODUCTION</li>
     * <li>AppMode::DEVELOPMENT</li>
     * </u>
     * @return boolean
     */
    public static function equals($appMode) {
        return $appMode == Config::get()->appMode;
    }
}

/* EOF */
