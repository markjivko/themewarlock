<?php
/**
 * Theme Warlock - Addon_CoreVcBadgeWall
 * 
 * @title      WPBakery Page Builder - Badge wall
 * @desc       Use a badge wall for features, client list etc.
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreVcBadgeWall extends Addon {
    
    const KEY_ICON                 = 'icon';
    const KEY_TITLE                = 'title';
    const KEY_DESCRIPTION          = 'description';
    const KEY_DEF_SECT_TITLE       = 'defSectTitle';
    const KEY_DEF_SECT_SUBTITLE    = 'defSectSubtitle';
    const KEY_DEF_BACKGROUND       = 'defBackground';
    const KEY_DESC_IMAGE           = 'descImage';
    const KEY_DESC_URL             = 'descUrl';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_PUSHPIN;
    
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
                ->setValue('Badge wall')
                ->setMetaTitle('VC widget title')
                ->setMetaDescription('Set a custom title for this WPBakery Page Builder widget'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESCRIPTION))
                ->setValue('A simple badge wall useful for listing clients')
                ->setIsTextarea(true)
                ->setMetaTitle('VC widget description')
                ->setMetaDescription('Describe this WPBakery Page Builder widget to the end user'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_TITLE))
                ->setValue('Clients')
                ->setMetaTitle('Default VC section title')
                ->setMetaDescription('Set the default title in the VC add-on'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_SUBTITLE))
                ->setValue('The client is always right!')
                ->setMetaTitle('Default VC section subtitle')
                ->setMetaDescription('Set the default subtitle in the VC add-on'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESC_IMAGE))
                ->setValue('Choose an image for this client')
                ->setMetaTitle('Item image description')
                ->setMetaDescription('Describe the item image'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESC_URL))
                ->setValue('Choose an URL for this client')
                ->setMetaTitle('Item URL description')
                ->setMetaDescription('Describe the item URL'),
            
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
            Plugin::PLUGIN_VC_BADGE_WALL,
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
            case Plugin::PLUGIN_VC_BADGE_WALL:
                $pluginCommon->createIcon($this->addonData[self::KEY_ICON])
                    ->replaceImageFile($this->addonData[self::KEY_DEF_BACKGROUND], 'img/background.jpg');
                break;
        }
    }
}
    
/* EOF */

