<?php
/**
 * Theme Warlock - Plugin_{Scaffold.className}
 * @title      {Scaffold.title}
 * @desc       {Scaffold.description}
 * @copyright  (c) {Scaffold.year}, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Plugin_{Scaffold.className} extends Plugin {

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