<?php
/**
 * Theme Warlock - Addon_Onepage
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_Onepage extends Addon {

    /**
     * Sections
     */
    const SECTION_CORE         = 'core';
    const SECTION_CORE_TITLE   = 'section_core_title';
    const SECTION_SOCIAL       = 'social';
    const SECTION_SOCIAL_TITLE = 'section_social_title';
    
    // Page layout
    const KEY_LAYOUT_TOGGLE             = 'layout_toggle';
    const KEY_LAYOUT_TOGGLE_STATUS      = 'layout_toggle_status';
    const KEY_LAYOUT_TOGGLE_TITLE       = 'layout_toggle_title';
    const KEY_LAYOUT_TOGGLE_DESCRIPTION = 'layout_toggle_description';
    
    // Page gutters
    const KEY_GUTTERS_PAGE_TOGGLE             = 'gutters_page_toggle';
    const KEY_GUTTERS_PAGE_TOGGLE_STATUS      = 'gutters_page_toggle_status';
    const KEY_GUTTERS_PAGE_TOGGLE_TITLE       = 'gutters_page_toggle_title';
    const KEY_GUTTERS_PAGE_TOGGLE_DESCRIPTION = 'gutters_page_toggle_description';
    
    // Rows gutters
    const KEY_GUTTERS_ROWS_TOGGLE             = 'gutters_rows_toggle';
    const KEY_GUTTERS_ROWS_TOGGLE_STATUS      = 'gutters_rows_toggle_status';
    const KEY_GUTTERS_ROWS_TOGGLE_TITLE       = 'gutters_rows_toggle_title';
    const KEY_GUTTERS_ROWS_TOGGLE_DESCRIPTION = 'gutters_rows_toggle_description';
    
    // Copyright in footer
    const KEY_FOOTER_COPY_TOGGLE             = 'footer_copy_toggle';
    const KEY_FOOTER_COPY_TOGGLE_STATUS      = 'footer_copy_toggle_status';
    const KEY_FOOTER_COPY_TOGGLE_TITLE       = 'footer_copy_toggle_title';
    const KEY_FOOTER_COPY_TOGGLE_DESCRIPTION = 'footer_copy_toggle_description';
    
    const KEY_SOCIAL_ACCOUNTS           = 'social_accounts';
    const KEY_SOCIAL_ACCOUNTS_STATUS    = 'social_accounts_status';
    const KEY_SOCIAL_ACCOUNTS_TITLE     = 'social_accounts_title';
    const KEY_SOCIAL_ACCOUNTS_ACCOUNTS  = 'social_accounts_accounts';
    
    const SOCIAL_MEDIA_ACCOUNTS = array(
        'android', 'app-store', 'atlassian', 'audible', 'bandcamp', 'battle-net', 'behance',
        'bitbucket', 'blogger', 'codepen', 'delicious', 'digg', 'discord', 'dribbble', 'dropbox',
        'ebay', 'evernote', 'facebook', 'flickr', 'foursquare', 'github', 'google-play', 'instagram',
        'itch-io', 'itunes', 'jira', 'kickstarter', 'lastfm', 'linkedin', 'medium', 'microsoft',
        'openid', 'patreon', 'paypal', 'pinterest', 'playstation', 'product-hunt', 'quora', 'reddit',
        'shopify', 'skype', 'slack', 'snapchat', 'soundcloud', 'spotify', 'steam', 'stumbleupon',
        'tiktok', 'tumblr', 'twitch', 'twitter', 'vimeo', 'vk', 'vine', 'wordpress', 'yahoo', 'youtube'
    );
    
    /**
     * Safe methods; the result will not be escaped
     */
    public static $safeMethods = array(
    );

    public static function getOptions(){
        return array(
            // Core section
            (new Model_Project_Config_Item_String(self::SECTION_CORE_TITLE))
                ->setValue('Layout')
                ->setMetaTitle('Core section title')
                ->setMetaHeader('Customizer'),
            
            // Core: Layout toggle
            (new Model_Project_Config_Item_Boolean(self::KEY_LAYOUT_TOGGLE_STATUS))
                ->setValue(false)
                ->setMetaTitle('Core: Layout toggle'),
            (new Model_Project_Config_Item_String(self::KEY_LAYOUT_TOGGLE_TITLE))
                ->setValue('Page layout')
                ->setMetaTitle('Title')
                ->setMetaDepends(self::KEY_LAYOUT_TOGGLE_STATUS),
            (new Model_Project_Config_Item_String(self::KEY_LAYOUT_TOGGLE_DESCRIPTION))
                ->setValue('Change the page layout')
                ->setMetaTitle('Description')
                ->setMetaDepends(self::KEY_LAYOUT_TOGGLE_STATUS),
            
            // Core: Page gutters toggle
            (new Model_Project_Config_Item_Boolean(self::KEY_GUTTERS_PAGE_TOGGLE_STATUS))
                ->setValue(false)
                ->setMetaTitle('Core: Page gutters toggle'),
            (new Model_Project_Config_Item_String(self::KEY_GUTTERS_PAGE_TOGGLE_TITLE))
                ->setValue('Page gutters')
                ->setMetaTitle('Title')
                ->setMetaDepends(self::KEY_GUTTERS_PAGE_TOGGLE_STATUS),
            (new Model_Project_Config_Item_String(self::KEY_GUTTERS_PAGE_TOGGLE_DESCRIPTION))
                ->setValue('Toggle the page gutters')
                ->setMetaTitle('Description')
                ->setMetaDepends(self::KEY_GUTTERS_PAGE_TOGGLE_STATUS),
            
            // Core: Page row gutters toggle
            (new Model_Project_Config_Item_Boolean(self::KEY_GUTTERS_ROWS_TOGGLE_STATUS))
                ->setValue(false)
                ->setMetaTitle('Core: Rows gutters toggle'),
            (new Model_Project_Config_Item_String(self::KEY_GUTTERS_ROWS_TOGGLE_TITLE))
                ->setValue('Rows gutters')
                ->setMetaTitle('Title')
                ->setMetaDepends(self::KEY_GUTTERS_ROWS_TOGGLE_STATUS),
            (new Model_Project_Config_Item_String(self::KEY_GUTTERS_ROWS_TOGGLE_DESCRIPTION))
                ->setValue('Toggle the rows gutters')
                ->setMetaTitle('Description')
                ->setMetaDepends(self::KEY_GUTTERS_ROWS_TOGGLE_STATUS),
            
            // Core: Footer copyright toggle
            (new Model_Project_Config_Item_Boolean(self::KEY_FOOTER_COPY_TOGGLE_STATUS))
                ->setValue(false)
                ->setMetaTitle('Core: Footer copyright toggle'),
            (new Model_Project_Config_Item_String(self::KEY_FOOTER_COPY_TOGGLE_TITLE))
                ->setValue('Footer Copyright Notice')
                ->setMetaTitle('Title')
                ->setMetaDepends(self::KEY_FOOTER_COPY_TOGGLE_STATUS),
            (new Model_Project_Config_Item_String(self::KEY_FOOTER_COPY_TOGGLE_DESCRIPTION))
                ->setValue('Toggle the copyright section in the page footer')
                ->setMetaTitle('Description')
                ->setMetaDepends(self::KEY_FOOTER_COPY_TOGGLE_STATUS),
            
            // Social section
            (new Model_Project_Config_Item_Boolean(self::KEY_SOCIAL_ACCOUNTS_STATUS))
                ->setValue(false)
                ->setMetaTitle('Social section'),
            (new Model_Project_Config_Item_String(self::SECTION_SOCIAL_TITLE))
                ->setValue('Social media')
                ->setMetaTitle('Section title')
                ->setMetaDepends(self::KEY_SOCIAL_ACCOUNTS_STATUS),
            (new Model_Project_Config_Item_String(self::KEY_SOCIAL_ACCOUNTS_ACCOUNTS))
                ->setMetaTitle('Available accounts')
                ->setMetaDepends(self::KEY_SOCIAL_ACCOUNTS_STATUS)
                ->setOptions(self::SOCIAL_MEDIA_ACCOUNTS)
                ->setIsList(),
            
            // Widget areas
            (new Model_Project_Config_Item_Boolean('sidebar_header_1'))
                ->setValue(false)
                ->setMetaTitle('Header 1')
                ->setMetaHeader('Sidebars'),
            (new Model_Project_Config_Item_String('sidebar_header_1_title'))
                ->setValue('Header')
                ->setMetaTitle('Widget area title')
                ->setMetaDepends('sidebar_header_1'),
            (new Model_Project_Config_Item_String('sidebar_header_1_description'))
                ->setValue('Add widgets to the header')
                ->setMetaTitle('Widget area description')
                ->setMetaDepends('sidebar_header_1'),
            
            (new Model_Project_Config_Item_Boolean('sidebar_header_2'))
                ->setValue(false)
                ->setMetaTitle('Header 2'),
            (new Model_Project_Config_Item_String('sidebar_header_2_title'))
                ->setValue('Header - Secondary')
                ->setMetaTitle('Widget area title')
                ->setMetaDepends('sidebar_header_2'),
            (new Model_Project_Config_Item_String('sidebar_header_2_description'))
                ->setValue('Add widgets to the secondary header')
                ->setMetaTitle('Widget area description')
                ->setMetaDepends('sidebar_header_2'),
            
            (new Model_Project_Config_Item_Boolean('sidebar_1'))
                ->setValue(false)
                ->setMetaTitle('Sidebar 1'),
            (new Model_Project_Config_Item_String('sidebar_1_title'))
                ->setValue('Sidebar')
                ->setMetaTitle('Sidebar title')
                ->setMetaDepends('sidebar_1'),
            (new Model_Project_Config_Item_String('sidebar_1_description'))
                ->setValue('Add widgets to the sidebar')
                ->setMetaTitle('Sidebar description')
                ->setMetaDepends('sidebar_1'),
            
            (new Model_Project_Config_Item_Boolean('sidebar_2'))
                ->setValue(false)
                ->setMetaTitle('Sidebar 2'),
            (new Model_Project_Config_Item_String('sidebar_2_title'))
                ->setValue('Sidebar - Secondary')
                ->setMetaTitle('Sidebar title')
                ->setMetaDepends('sidebar_2'),
            (new Model_Project_Config_Item_String('sidebar_2_description'))
                ->setValue('Add widgets to the secondary sidebar')
                ->setMetaTitle('Sidebar description')
                ->setMetaDepends('sidebar_2'),
            
            (new Model_Project_Config_Item_Boolean('sidebar_footer_1'))
                ->setValue(false)
                ->setMetaTitle('Footer 1'),
            (new Model_Project_Config_Item_String('sidebar_footer_1_title'))
                ->setValue('Footer')
                ->setMetaTitle('Widget area title')
                ->setMetaDepends('sidebar_footer_1'),
            (new Model_Project_Config_Item_String('sidebar_footer_1_description'))
                ->setValue('Add widgets to the footer')
                ->setMetaTitle('Widget area description')
                ->setMetaDepends('sidebar_footer_1'),
            
            (new Model_Project_Config_Item_Boolean('sidebar_footer_2'))
                ->setValue(false)
                ->setMetaTitle('Footer 2'),
            (new Model_Project_Config_Item_String('sidebar_footer_2_title'))
                ->setValue('Footer - Secondary')
                ->setMetaTitle('Widget area title')
                ->setMetaDepends('sidebar_footer_2'),
            (new Model_Project_Config_Item_String('sidebar_footer_2_description'))
                ->setValue('Add widgets to the secondary footer')
                ->setMetaTitle('Widget area description')
                ->setMetaDepends('sidebar_footer_2'),
        );
    }

    /**
     * General assertion test used for {if.onepage.testName}
     * 
     * @param string $testName Test name
     * @return boolean
     */
    public function assert($testName) {
        $result = false;
        switch ($testName) {
            case 'showAccounts':
                if (isset($this->addonData[$itemKeyToggle = self::KEY_SOCIAL_ACCOUNTS_STATUS])) {
                    $result = $this->addonData[$itemKeyToggle]->getValue();
                }
                break;
                
            case 'hasSidebars':
                $result = count($this->getWidgetAreas('sidebar')) > 0;
                break;
                
            case 'layoutToggle':
                if (isset($this->addonData[$itemKeyToggle = self::KEY_LAYOUT_TOGGLE_STATUS])) {
                    $result = $this->addonData[$itemKeyToggle]->getValue();
                }
                break;
                
            case 'pageGuttersToggle':
                if (isset($this->addonData[$itemKeyToggle = self::KEY_GUTTERS_PAGE_TOGGLE_STATUS])) {
                    $result = $this->addonData[$itemKeyToggle]->getValue();
                }
                break;
                
            case 'rowGuttersToggle':
                if (isset($this->addonData[$itemKeyToggle = self::KEY_GUTTERS_ROWS_TOGGLE_STATUS])) {
                    $result = $this->addonData[$itemKeyToggle]->getValue();
                }
                break;
                
            case 'footerCopy':
                if (isset($this->addonData[$itemKeyToggle = self::KEY_FOOTER_COPY_TOGGLE_STATUS])) {
                    $result = $this->addonData[$itemKeyToggle]->getValue();
                }
                break;
        }
        
        return $result;
    }

    /**
     * Get the corresponding WordPress Core tags
     */
    public function getTags() {
        return array(
            WordPress_Tags::TAG_CORE_THEME_OPTIONS,
        );
    }
    
    /**
     * Get the needed script packages (CSS, JS and IMG) that are bundled with this add-on
     * 
     * @return string[]
     */
    public function getScripts() {
        // Prepare the scripts
        $result = array(
            Script::SCRIPT_JQUERY_UI,
            Script::SCRIPT_JQUERY_VALIDATE,
        );
        
        // Social accounts enabled
        if ($this->assert('showAccounts')) {
            $result[] = Script::SCRIPT_FONTELLO;
        }
                
        // All done
        return $result;
    }
    
    public function getAccounts() {
        if (isset($this->addonData[$itemKeyAccounts = self::KEY_SOCIAL_ACCOUNTS_ACCOUNTS])) {
            return $this->addonData[$itemKeyAccounts]->getValue();
        }
    }
    
    public function getWidgetAreas($customPrefix = null) {
        // Prepare the result
        $result = array();
        
        // Go through the data
        foreach ($this->addonData as $key => $item) {
            if (preg_match('%^sidebar_\w+%', $key) && $item instanceof Model_Project_Config_Item_Boolean) {
                // Filtering by prefix
                if (null != $customPrefix) {
                    // Header and footer areas
                    if (in_array($customPrefix, array('header', 'footer'))) {
                        if (!preg_match('%^sidebar_' . preg_quote($customPrefix) . '%', $key)) {
                            continue;
                        }
                    } elseif('sidebar' == $customPrefix) {
                        // Sidebar definition
                        if (!preg_match('%^sidebar_\d+$%', $key)) {
                            continue;
                        }
                    }
                }
                
                // Enabled
                if ($item->getValue()) {
                    if (isset($this->addonData[$titleKey = $key . '_title']) && isset($this->addonData[$descriptionKey = $key . '_description'])) {
                        $result[$key] = array(
                            'name'        => $this->addonData[$titleKey]->getValue(),
                            'description' => $this->addonData[$descriptionKey]->getValue(),
                        );
                    }
                }
            }
        }
        
        // All done
        return $result;
    }
    
    /**
     * Initialize the WordPress Customizer
     */
    public function initCustomizer() {
        // Add the panel
        $wpCustomizerPanel = Addon_Core::getThemePanel();
        $this->wpCustomizer->addPanel($wpCustomizerPanel);
        
        // Prepare the sections
        $sections = array(
            self::SECTION_CORE => array(
                self::KEY_LAYOUT_TOGGLE => array(
                    'full-width' => 'Full-width', 
                    'boxed'      => 'Boxed'
                ),
                self::KEY_GUTTERS_PAGE_TOGGLE => array(
                    'on'  => 'Enable',
                    'off' => 'Disable',
                ),
                self::KEY_GUTTERS_ROWS_TOGGLE => array(
                    'on'  => 'Enable',
                    'off' => 'Disable',
                ),
                self::KEY_FOOTER_COPY_TOGGLE => array(
                    'on'  => 'Enable',
                    'off' => 'Disable',
                ),
            ),
            self::SECTION_SOCIAL => array(
                self::KEY_SOCIAL_ACCOUNTS => null,
            ),
        );
        
        // Go through the sections
        foreach ($sections as $sectionName => $sectionData) {
            // Get the section item
            $sectionItem = $this->addonData['section_' . $sectionName . '_title'];
            
            // Add the section
            $wpCustomizerSection = (new WordPress_Customizer_Element_Section(
                $sectionItem->getValue()
            ))->setPanel($wpCustomizerPanel);
            $this->wpCustomizer->addSection($wpCustomizerSection);
            
            // Go through the data
            foreach ($sectionData as $itemKey => $itemData) {
                // Valid addon option
                if (isset($this->addonData[$itemKeyStatus = $itemKey . '_status'])) {
                    /* @var $configItemStatus Model_Project_Config_Item */
                    $configItemStatus = $this->addonData[$itemKeyStatus];
                    
                    // Not a boolean configuration
                    if (!$configItemStatus instanceof Model_Project_Config_Item_Boolean) {
                        continue;
                    }

                    // Not enabled
                    if (!$configItemStatus->getValue()) {
                        continue;
                    }

                    // Get the other values
                    switch ($itemKey) {
                        case self::KEY_LAYOUT_TOGGLE:
                        case self::KEY_GUTTERS_PAGE_TOGGLE:
                        case self::KEY_GUTTERS_ROWS_TOGGLE:
                        case self::KEY_FOOTER_COPY_TOGGLE:
                            if (isset($this->addonData[$itemKeyTitle = $itemKey . '_title']) && isset($this->addonData[$itemKeyDescription = $itemKey . '_description'])) {
                                // Add the dropdown
                                $this->wpCustomizer->addItem(
                                    (new WordPress_Customizer_Element_Item_Select(
                                        $itemKey,
                                        $this->addonData[$itemKeyTitle]->getValue(), 
                                        $this->addonData[$itemKeyDescription]->getValue(), 
                                        $itemData
                                    ))
                                        ->setSection($wpCustomizerSection)
                                        ->setTranslateValues(true)
                                        ->setStylize(true)
                                );
                            }
                            break;

                        case self::KEY_SOCIAL_ACCOUNTS:
                            if (isset($this->addonData[$itemKeyAccounts = $itemKey . '_accounts'])) {
                                // Get the social accounts
                                $socialAccounts = $this->addonData[$itemKeyAccounts]->getValue();
                                
                                // Add the text fields
                                if (is_array($socialAccounts)) {
                                    foreach ($socialAccounts as $socialAccountName) {
                                        $this->wpCustomizer->addItem(
                                            (new WordPress_Customizer_Element_Item_Text(
                                                $itemKey . '_' . $socialAccountName,
                                                ucwords($socialAccountName) . ' URL', 
                                                ''
                                            ))
                                                ->setSection($wpCustomizerSection)
                                                ->setSanitizeCallback(WordPress_Customizer_Element_Item::SANITIZE_ESC_URL)
                                        );
                                    }
                                }
                            }
                            break;
                    }
                }
            }
        }
    }
}

/* EOF */