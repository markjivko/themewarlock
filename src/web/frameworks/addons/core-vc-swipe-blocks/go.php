<?php
/**
 * Theme Warlock - Addon_CoreVcSwipeBlocks
 * 
 * @title      WPBakery Page Builder - Swipe blocks
 * @desc       Use a simple swipe blocks widget for testimonials, products etc.
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreVcSwipeBlocks extends Addon {
    
    const KEY_ICON                 = 'icon';
    const KEY_TITLE                = 'title';
    const KEY_DESCRIPTION          = 'description';
    const KEY_DEF_SECT_TITLE       = 'defSectTitle';
    const KEY_DEF_SECT_SUBTITLE    = 'defSectSubtitle';
    const KEY_DESC_BLOCKS          = 'descBlocks';
    const KEY_DESC_BLOCKS_TITLE    = 'descBlocksTitle';
    const KEY_DESC_BLOCKS_CONTENT  = 'descBlocksContent';
    const KEY_DESC_BLOCKS_IMAGE    = 'descBlocksImage';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_TRANSFER;
    
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
                ->setValue('Testimonials')
                ->setMetaTitle('VC widget title')
                ->setMetaDescription('Set a custom title for this WPBakery Page Builder widget'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESCRIPTION))
                ->setValue('A simple testimonials widget')
                ->setIsTextarea(true)
                ->setMetaTitle('VC widget description')
                ->setMetaDescription('Describe this WPBakery Page Builder widget to the end user'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_TITLE))
                ->setValue('Testimonials')
                ->setMetaTitle('Default VC section title')
                ->setMetaDescription('Set the default title in the VC add-on'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_SUBTITLE))
                ->setValue('Our customers love us')
                ->setMetaTitle('Default VC section subtitle')
                ->setMetaDescription('Set the default subtitle in the VC add-on'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESC_BLOCKS))
                ->setValue('Testimonials')
                ->setMetaTitle('Blocks type')
                ->setMetaDescription('Set the type of blocks used in this swipe widget'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESC_BLOCKS_TITLE))
                ->setValue('Enter the customer\'s name')
                ->setMetaTitle('Blocks title')
                ->setMetaDescription('Describe the block title'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESC_BLOCKS_CONTENT))
                ->setValue('Enter the customer\'s review')
                ->setMetaTitle('Blocks content')
                ->setMetaDescription('Describe the block content'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESC_BLOCKS_IMAGE))
                ->setValue('Set the customer\'s avatar')
                ->setMetaTitle('Blocks image')
                ->setMetaDescription('Describe the block image'),
                
        );
    }   
    
    /**
     * Get the available plugins
     * 
     * @return string[]
     */
    public function getPlugins() {
        return array(
            Plugin::PLUGIN_VC_SWIPE_BLOCKS,
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
            case Plugin::PLUGIN_VC_SWIPE_BLOCKS:
                $pluginCommon->createIcon($this->addonData[self::KEY_ICON]);
                break;
        }
    }
}
    
/* EOF */

