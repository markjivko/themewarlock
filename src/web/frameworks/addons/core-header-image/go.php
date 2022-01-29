<?php
/**
 * Theme Warlock - Addon_CoreHeaderImage
 * 
 * @title      Header Image
 * @desc       Use your own custom image in the header
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreHeaderImage extends Addon {
    
    const KEY_WIDTH       = 'width';
    const KEY_HEIGHT      = 'height';
    const KEY_TEXT_COLOR  = 'textColor';
    const KEY_FLEX_WIDTH  = 'flexWidth';
    const KEY_FLEX_HEIGHT = 'flexHeight';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_MODAL_WINDOW;
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public static function getOptions() {
        return array(
            (new Model_Project_Config_Item_Integer(self::KEY_WIDTH))
                ->setValue(1920)
                ->setMin(960)
                ->setMax(1920)
                ->setMetaTitle('Header image width')
                ->setMetaHeader('Settings')
                ->setMetaDescription('Set the width for the header image'),
            
            (new Model_Project_Config_Item_Integer(self::KEY_HEIGHT))
                ->setValue(150)
                ->setMin(50)
                ->setMax(300)
                ->setMetaTitle('Header image height')
                ->setMetaDescription('Set the height for the header image'),
            
            (new Model_Project_Config_Item_Color(self::KEY_TEXT_COLOR))
                ->setValue('#ff000000')    
                ->setMetaTitle('Header text color')
                ->setMetaDescription('Set the default text color to use in the header'),
            
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
        // Prepare the tags
        $tags = array(
            WordPress_Tags::TAG_CORE_CUSTOM_HEADER,
        );

        // Flex-width enabled
        if ($this->addonData[self::KEY_FLEX_WIDTH] instanceof Model_Project_Config_Item_Boolean) {
            if ($this->addonData[self::KEY_FLEX_WIDTH]->getValue()) {
                $tags[] = WordPress_Tags::TAG_CORE_FLEXIBLE_HEADER;
            }
        }
        
        // Flex-height enabled
        if ($this->addonData[self::KEY_FLEX_HEIGHT] instanceof Model_Project_Config_Item_Boolean) {
            if ($this->addonData[self::KEY_FLEX_HEIGHT]->getValue()) {
                $tags[] = WordPress_Tags::TAG_CORE_FLEXIBLE_HEADER;
            }
        }

        // All done
        return $tags;
    }
}
    
/* EOF */