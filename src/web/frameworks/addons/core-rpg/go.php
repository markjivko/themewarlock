<?php
/**
 * Theme Warlock - Addon_CoreRpg
 * 
 * @title      RPG
 * @desc       Use a custom integration of RPG
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreRpg extends Addon {
    
    /**
     * Configurable elements
     */
    const KEY_URL_WEBSITE = 'urlWebsite';
    const KEY_URL_DISCORD = 'urlDiscord';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_KNIGHT;
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public static function getOptions() {
        return array(
            (new Model_Project_Config_Item_String((self::KEY_URL_WEBSITE)))
                ->setMetaTitle('Website URL')
                ->setMetaDescription('Set the website URL for this plugin')
                ->setValue(''),
            
            (new Model_Project_Config_Item_String((self::KEY_URL_DISCORD)))
                ->setMetaTitle('Discord URL')
                ->setMetaDescription('Set the Discord URL for this plugin')
                ->setValue('https://discord.gg/'),
        );
    }  
    
    /**
     * General assertion test used for {if.core-rpg.testName}
     * 
     * @param string $testName Test name
     * @return boolean
     */
    public function assert($testName) {
        $result = false;
        
        return $result;
    }
    
    /**
     * Get the available plugins
     * 
     * @return string[]
     */
    public function getPlugins() {
        return array(
            Plugin::PLUGIN_RPG,
        );
    }
    
}
    
/* EOF */

