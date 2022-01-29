<?php
/**
 * Theme Warlock - Plugin_VcSwipeBlocks
 * @title      Swipe blocks
 * @desc       A simple swipe blocks widget
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Plugin_VcSwipeBlocks extends Plugin {

    /**
     * Get the needed script packages (CSS, JS and IMG) that are bundled with this WordPress plugin
     * Must be defined in frameworks/scripts or a URL
     * 
     * @return string[]
     */
    public function getScripts() {
        return array(
            Script::SCRIPT_JQUERY_UI,
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