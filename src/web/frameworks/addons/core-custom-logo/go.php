<?php
/**
 * Theme Warlock - Addon_CoreCustomLogo
 * 
 * @title      Custom Logo
 * @desc       Set your own custom logo
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreCustomLogo extends Addon {
    
    const KEY_WIDTH       = 'width';
    const KEY_HEIGHT      = 'height';
    const KEY_FLEX_WIDTH  = 'flexWidth';
    const KEY_FLEX_HEIGHT = 'flexHeight';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_APPLE;
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public static function getOptions() {
        return array(
            (new Model_Project_Config_Item_Integer(self::KEY_WIDTH))
                ->setValue(150)
                ->setMin(50)
                ->setMax(300)
                ->setMetaTitle('Logo width')
                ->setMetaHeader('Settings')
                ->setMetaDescription('Set the width for the logo'),
            
            (new Model_Project_Config_Item_Integer(self::KEY_HEIGHT))
                ->setValue(150)
                ->setMin(50)
                ->setMax(300)
                ->setMetaTitle('Logo height')
                ->setMetaDescription('Set the height for the logo'),
            
            (new Model_Project_Config_Item_Boolean(self::KEY_FLEX_WIDTH))
                ->setValue(false)
                ->setMetaTitle('Flex Width')
                ->setMetaDescription('Enable the flex-width feature'),
            
            (new Model_Project_Config_Item_Boolean(self::KEY_FLEX_HEIGHT))
                ->setValue(false)
                ->setMetaTitle('Flex Height')
                ->setMetaDescription('Enable the flex-height feature'),
        );
    }   
    
    /**
     * Get the corresponding WordPress Core tags
     */
    public function getTags() {
        return array(
            WordPress_Tags::TAG_CORE_CUSTOM_LOGO,
        );
    }
}
    
/* EOF */