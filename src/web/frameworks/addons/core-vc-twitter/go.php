<?php
/**
 * Theme Warlock - Addon_CoreVcTwitter
 * 
 * @title      WPBakery Page Builder - Twitter slider
 * @desc       Add a simple slider to feature Twitter posts from a user
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreVcTwitter extends Addon {
    
    const KEY_ICON           = 'icon';
    const KEY_TITLE          = 'title';
    const KEY_DESCRIPTION    = 'description';
    const KEY_DEF_SECT_TITLE = 'defSectTitle';
    const KEY_DEF_USERNAME   = 'defUsername';
    const KEY_DEF_BACKGROUND = 'defBackground';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_BULLHORN;
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public static function getOptions() {
        return array(
            
            (new Model_Project_Config_Item_Image(self::KEY_ICON))
                ->setImageType(Model_Project_Config_Item_Image::TYPE_PNG)
                ->setImageWidth(256)
                ->setImageHeight(256)
                ->setMetaHeader('WPBakery Page Builder Settings')
                ->setMetaDescription('Set the icon for this VC addon'),
            
            (new Model_Project_Config_Item_String(self::KEY_TITLE))
                ->setValue('Twitter slider')
                ->setMetaTitle('VC widget title')
                ->setMetaDescription('Set a custom title for this WPBakery Page Builder widget'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESCRIPTION))
                ->setValue('A simple slider to feature your latest Twitter posts.')
                ->setIsTextarea(true)
                ->setMetaTitle('VC widget description')
                ->setMetaDescription('Describe this WPBakery Page Builder widget to the end user'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_TITLE))
                ->setValue('Tweets')
                ->setMetaTitle('Default VC section title')
                ->setMetaDescription('Set the default title in the VC add-on'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_USERNAME))
                ->setValue('envato')
                ->setMetaTitle('Default Twitter username')
                ->setMetaDescription('Set the default twitter username in the VC add-on'),
            
            (new Model_Project_Config_Item_Image(self::KEY_DEF_BACKGROUND))
                ->setImageType(Model_Project_Config_Item_Image::TYPE_JPG)
                ->setMetaTitle('Default background')
                ->setMetaDescription('Set the default background for this VC add-on'),
        );
    }   
    
    /**
     * Get the available plugins
     * 
     * @return string[]
     */
    public function getPlugins() {
        return array(
            Plugin::PLUGIN_VC_TWITTER,
        );
    }
    
    /**
     * Called when a child WordPress plugin is deployed, right before being packed as a .tar archive.<br/>
     * Useful for working with drawables and other media inside plugins.
     * 
     * @param Drawables_Plugin_Common $pluginCommon WordPress plugin common instance
     */
    public function onPluginDeployment(Drawables_Plugin_Common $pluginCommon) {
        switch ($pluginCommon->getPlugin()->getName()) {
            case Plugin::PLUGIN_VC_TWITTER:
                $pluginCommon->createIcon($this->addonData[self::KEY_ICON])
                    ->replaceImageFile($this->addonData[self::KEY_DEF_BACKGROUND], 'img/background.jpg');
                break;
        }
    }
}
    
/* EOF */