<?php
/**
 * Theme Warlock - Addon_CoreSlider
 * 
 * @title      Slider Revolution
 * @desc       Use a custom integration of Slider Revolution
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreSlider extends Addon {
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_BLACKBOARD;
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public static function getOptions() {
        return array(
        );
    }   
    
    /**
     * Get the available plugins
     * 
     * @return string[]
     */
    public function getPlugins() {
        return array(
            Plugin::PLUGIN_REVSLIDER,
        );
    }
    
}
    
/* EOF */

