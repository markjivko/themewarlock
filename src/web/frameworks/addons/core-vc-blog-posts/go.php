<?php
/**
 * Theme Warlock - Addon_CoreVcBlogPosts
 * 
 * @title      WPBakery Page Builder - Blog posts
 * @desc       Display the latest blog posts in a single-row slider
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreVcBlogPosts extends Addon {
    
    const KEY_ICON                 = 'icon';
    const KEY_PREVIEW_BKG          = 'previewBkg';
    const KEY_TITLE                = 'title';
    const KEY_DESCRIPTION          = 'description';
    const KEY_DEF_SECT_TITLE       = 'defSectTitle';
    const KEY_DEF_SECT_SUBTITLE    = 'defSectSubtitle';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_BOOK;
    
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
            
            (new Model_Project_Config_Item_Image(self::KEY_PREVIEW_BKG))
                ->setImageType(Model_Project_Config_Item_Image::TYPE_PNG)
                ->setImageWidth(512)
                ->setImageHeight(256)
                ->setMetaTitle('Default preview')
                ->setMetaDescription('Set the default preview when the feature image is missing'),
            
            (new Model_Project_Config_Item_String(self::KEY_TITLE))
                ->setValue('Blog posts')
                ->setMetaTitle('VC widget title')
                ->setMetaDescription('Set a custom title for this WPBakery Page Builder widget'),
            
            (new Model_Project_Config_Item_String(self::KEY_DESCRIPTION))
                ->setValue('A simple way to display your latest blog posts')
                ->setIsTextarea(true)
                ->setMetaTitle('VC widget description')
                ->setMetaDescription('Describe this WPBakery Page Builder widget to the end user'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_TITLE))
                ->setValue('Blog')
                ->setMetaTitle('Default VC section title')
                ->setMetaDescription('Set the default title in the VC add-on'),
            
            (new Model_Project_Config_Item_String(self::KEY_DEF_SECT_SUBTITLE))
                ->setValue('See what we\'ve been up to')
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
            Plugin::PLUGIN_VC_BLOG_POSTS,
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
            case Plugin::PLUGIN_VC_BLOG_POSTS:
                $pluginCommon->createIcon($this->addonData[self::KEY_ICON])
                    ->replaceImageFile($this->addonData[self::KEY_PREVIEW_BKG], 'img/no-feature.png');
                break;
        }
    }
}
    
/* EOF */

