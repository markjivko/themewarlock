<?php
/**
 * Theme Warlock - Plugin_VcSocialArea
 * @title      Social Area
 * @desc       Display a beautiful a list of links to your social media accounts
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Plugin_VcSocialArea extends Plugin {

    /**
     * Get the needed script packages (CSS, JS and IMG) that are bundled with this WordPress plugin
     * Must be defined in Framework::FOLDER_ROOT/scripts or a URL
     * 
     * @return string[]
     */
    public function getScripts() {
        return array(
        );
    }
    
    /**
     * Plugin version
     * 
     * @return string
     */
    public function getVersion() {
        return '1.0.0';
    }

}

/* EOF */