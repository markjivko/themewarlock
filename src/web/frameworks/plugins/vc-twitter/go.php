<?php
/**
 * Theme Warlock - Plugin_VcTwitter
 * @title      Twitter slider
 * @desc       A simple slider to feature your latest Twitter posts.
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Plugin_VcTwitter extends Plugin {

    /**
     * Get the needed script packages (CSS, JS and IMG) that are bundled with this WordPress plugin
     * Must be defined in frameworks/scripts or a URL
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