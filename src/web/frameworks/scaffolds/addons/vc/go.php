<?php
/**
 * Theme Warlock - Addon_Core{Scaffold.className}
 * 
 * @title      WPBakery Page Builder - {Scaffold.title}
 * @desc       {Scaffold.description}
 * @copyright  (c) {Scaffold.year}, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_Core{Scaffold.className} extends Addon {
    
    const KEY_ICON              = 'icon';
    const KEY_TITLE             = 'title';
    const KEY_DESCRIPTION       = 'description';
    const KEY_DEF_SECT_TITLE    = 'defSectTitle';
    const KEY_DEF_SECT_SUBTITLE = 'defSectSubtitle';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_ALERT; // @FIXME
    
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
                ->setValue({scaffold.title})
                ->setMetaTitle('VC widget title')
                ->setMetaDescription('Set a custom title for this WPBakery Page Builder widget'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESCRIPTION))
                ->setValue({scaffold.description})
                ->setIsTextarea(true)
                ->setMetaTitle('VC widget description')
                ->setMetaDescription('Describe this WPBakery Page Builder widget to the end user'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_TITLE))
                ->setValue('@FIXME')
                ->setMetaTitle('Default VC section title')
                ->setMetaDescription('Set the default title in the VC add-on'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_SUBTITLE))
                ->setValue('@FIXME')
                ->setMetaTitle('Default VC section subtitle')
                ->setMetaDescription('Set the default subtitle in the VC add-on'),
                
        );
    }   
    
    /**
     * Get the available plugins
     * 
     * @return string[]
     */
    public function getPlugins() {
        return array(
            Plugin::{Scaffold.pluginConstantName},
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
            case Plugin::{Scaffold.pluginConstantName}:
                $pluginCommon->createIcon($this->addonData[self::KEY_ICON]);
                break;
        }
    }
}
    
/* EOF */

