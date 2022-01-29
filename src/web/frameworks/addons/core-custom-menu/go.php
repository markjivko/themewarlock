<?php
/**
 * Theme Warlock - Addon_CoreCustomMenu
 * 
 * @title      Custom Menu
 * @desc       Enable the use of a custom menu
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_CoreCustomMenu extends Addon {
    
    const SECTION_MENU_AREA_TITLE       = 'section_menu_area_title';
    const KEY_FLOATING_MENU             = 'floating_menu';
    const KEY_FLOATING_MENU_TITLE       = 'floating_menu_title';
    const KEY_FLOATING_MENU_DESCRIPTION = 'floating_menu_description';
    const KEY_DYNAMIC_MENU              = 'dynamic_menu';
    const KEY_DYNAMIC_MENU_TITLE        = 'dynamic_menu_title';
    const KEY_DYNAMIC_MENU_DESCRIPTION  = 'dynamic_menu_description';
    
    const KEY_MENU_NAME = 'menuName';
    const KEY_MENU_ID   = 'menuId';
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_OPTION_HORIZONTAL;
    
    public function assert($testName) {
        $result = true;
        
        switch($testName) {

            case 'flavorDefault':
                $result = ('default' === $this->addonData[Model_Project_Config_Item::KEY_FLAVOR]->getValue());
                break;
                
            case 'flavorSplit':
                $result = ('split' === $this->addonData[Model_Project_Config_Item::KEY_FLAVOR]->getValue());
                break;

        }
        
        return $result;
    }
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public static function getOptions() {
        return array(
            (new Model_Project_Config_Item_String(self::KEY_MENU_NAME))
                ->setValue('Primary menu')
                ->setMetaTitle('Menu name')
                ->setMetaHeader('Settings')
                ->setMetaDescription('Set primary menu name'),
            
            (new Model_Project_Config_Item_Integer(self::KEY_MENU_ID))
                ->setValue(1)
                ->setMin(1)
                ->setMax(1000)
                ->setMetaTitle('Menu ID')
                ->setMetaDescription('Set primary menu ID'),
            
            (new Model_Project_Config_Item_String(self::SECTION_MENU_AREA_TITLE))
                ->setValue('Menu area')
                ->setMetaTitle('Customizer section title')
                ->setMetaDescription('Set the title for the WP Customizer for this add-on')
                ->setMetaHeader('WP Customizer'),
            
            (new Model_Project_Config_Item_String(self::KEY_FLOATING_MENU_TITLE))
                ->setValue('Floating menu')
                ->setMetaTitle('Floating menu title')
                ->setMetaDescription('Set the title for the floating menu option'),
            
            (new Model_Project_Config_Item_String(self::KEY_FLOATING_MENU_DESCRIPTION))
                ->setValue('Allow the menu to stick to the top side of the screen when scrolling')
                ->setMetaTitle('Floating menu description')
                ->setMetaDescription('Set the description for the floating menu option'),
            
            (new Model_Project_Config_Item_String(self::KEY_DYNAMIC_MENU_TITLE))
                ->setValue('Dynamic menu')
                ->setMetaTitle('Dynamic menu title')
                ->setMetaDescription('Set the title for the dynamic menu option')
                ->setMetaDepends(Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE)
                ->setMetaDependsAddon(Model_Project_Config::CATEGORY_CORE),
            
            (new Model_Project_Config_Item_String(self::KEY_DYNAMIC_MENU_DESCRIPTION))
                ->setValue('Use Storyline.js dynamic menu; generates the menu entries in correlation to the WPBakery Page Builder blocks you have added on the current page.')
                ->setMetaTitle('Dynamic menu description')
                ->setMetaDescription('Set the description for the dynamic menu option')
                ->setMetaDepends(Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE)
                ->setMetaDependsAddon(Model_Project_Config::CATEGORY_CORE),
        );
    }   
    
    /**
     * Get the corresponding WordPress Core tags
     */
    public function getTags() {
        return array(
            WordPress_Tags::TAG_CORE_CUSTOM_MENU,
        );
    }
    
    /**
     * Initialize the WordPress Customizer
     */
    public function initCustomizer() {
        // Add the panel
        $wpCustomizerPanel = Addon_Core::getThemePanel();
        $this->wpCustomizer->addPanel($wpCustomizerPanel);

        // Add the section
        $wpCustomizerSection = (new WordPress_Customizer_Element_Section(
            $this->addonData[self::SECTION_MENU_AREA_TITLE]->getValue()
        ))->setPanel($wpCustomizerPanel);
        $this->wpCustomizer->addSection($wpCustomizerSection);
        
        // Add the dropdown
        $this->wpCustomizer->addItem(
            (new WordPress_Customizer_Element_Item_Select(
                self::KEY_FLOATING_MENU,
                $this->addonData[self::KEY_FLOATING_MENU_TITLE]->getValue(), 
                $this->addonData[self::KEY_FLOATING_MENU_DESCRIPTION]->getValue(),
                array(
                    'on'  => 'Enable',
                    'off' => 'Disable',
                )
            ))
                ->setDefault('off')
                ->setSection($wpCustomizerSection)
                ->setTranslateValues(true)
                ->setStylize(true)
        );
        
        // Is Storyline.js enabled?
        $useStoryline = isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE]) && Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_USE_STORYLINE];
        
        // Using StoryLine
        if ($useStoryline) {
            // Add the dynamic menu dropdown
            $this->wpCustomizer->addItem(
                (new WordPress_Customizer_Element_Item_Select(
                    self::KEY_DYNAMIC_MENU,
                    $this->addonData[self::KEY_DYNAMIC_MENU_TITLE]->getValue(), 
                    $this->addonData[self::KEY_DYNAMIC_MENU_DESCRIPTION]->getValue(),
                    array(
                        'on'  => 'Enable',
                        'off' => 'Disable',
                    )
                ))
                    ->setDefault('on')
                    ->setSection($wpCustomizerSection)
                    ->setTranslateValues(true)
                    ->setStylize(false)
            );
        }
    }
}
    
/* EOF */