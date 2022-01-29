<?php
/**
 * Theme Warlock - Plugin_VcBlogPosts
 * @title      Blog posts
 * @desc       A simple way to display your latest blog posts
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Plugin_VcBlogPosts extends Plugin {

    /**
     * Get the needed script packages (CSS, JS and IMG) that are bundled with this WordPress plugin
     * Must be defined in Framework::FOLDER_ROOT/scripts or a URL
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