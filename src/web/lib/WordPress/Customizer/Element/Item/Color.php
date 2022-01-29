<?php
/**
 * Theme Warlock - WordPress_Customizer_Element_Item_Color
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Customizer_Element_Item_Color extends WordPress_Customizer_Element_Item {
    
    const PLACEHOLDER = '__COLOR__';
    
    /**
     * Whether or not this item will stylize the theme (generate CSS)
     * @var boolean
     */
    protected $_stylize = true;
    
    /**
     * add_setting transport
     *
     * @var string
     */
    protected $_transport = self::TRANSPORT_REFRESH;
    
    /**
     * add_setting sanitize callback
     *
     * @var string 
     */
    protected $_sanitizeCallback = self::SANITIZE_HEX_COLOR;
    
    /**
     * PHP code to register the color
     * 
     * @return string
     */
    public function register() {
        // Export the select item ID
        $itemIdExported = $this->_exportMixed($this->getId());
        
        // Export the color name
        $itemNameExported = $this->_exportMixed(
            $this->getName(), 
            $this->getTranslateName(),
            self::I18N_ESC_HTML
        );
        
        // Prepare the section ID
        $sectionIdExported = $this->_exportMixed(null == $this->getSection() ? 'colors' : $this->getSection()->getId());
        
        // Prepare the add_setting arguments
        $addSettingTransportExported = $this->_exportMixed($this->getTransport());
        $addSettingSanitizeCallbackExported = $this->_exportMixed($this->getSanitizeCallback());
        
        // Prepare the exported values
        return <<<"CODE"
// $this->_name: color setting
\$wp_customize->add_setting($itemIdExported, array(
    'default'           => '',
    'transport'         => $addSettingTransportExported,
    'sanitize_callback' => $addSettingSanitizeCallbackExported,
));

// $this->_name: color control
\$wp_customize->add_control(new WP_Customize_Color_Control(\$wp_customize, $itemIdExported, array(
    'section' => $sectionIdExported,
    'label'   => $itemNameExported,
)));
CODE;
    }
    
    /**
     * PHP code to enqueue styles
     * 
     * @param string $cssRule CSS rule
     * @see WordPress_Customizer
     * @return string
     */
    public function stylize($cssRule) {
        // Forced type
        if (!is_string($cssRule) || !preg_match('%\b' . preg_quote(self::PLACEHOLDER) . '\b%', $cssRule)) {
            return;
        }
        
        // Get the placeholder-ready CSS rules
        return $this->_getCssRulePlaceholders(
            $this->getName(), 
            $cssRule, 
            array(
                self::PLACEHOLDER => $this->getKey()
            )
        );
    }

}

/* EOF */