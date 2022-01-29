<?php
/**
 * Theme Warlock - Auth
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Auth {
    /**
     * Web authentication
     */
    const TYPE_WEB = 'web';
    
    /**
     * No authentication
     */
    const TYPE_NONE = 'none';
    
    /**
     * Check if the user is authenticated
     * 
     * @param string $type Authentication type
     */
    public static function check($type) {
        // Get the authentication type
        switch ($type) {
            case self::TYPE_WEB:
                // No user ID defined
                if(null === Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL)) {
                    return false;
                }
                break;
        }

        // All went well
        return true;
    }
}

/*EOF*/