<?php
/**
 * Theme Warlock - WordPress_Customizer_Element_Item_Select
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Customizer_Element_Item_Select extends WordPress_Customizer_Element_Item {

    /**
     * Associative array
     * 
     * @var string[]
     */
    protected $_values = array();
    
    /**
     * Whether to translate the select (dropdown) values
     * 
     * @var boolean
     */
    protected $_translateValues = true;
    
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
    protected $_sanitizeCallback = self::SANITIZE_ESC_ATTR;
    
    /**
     * WP Customizer Select (Dropdown)
     * 
     * @param string   $key         Select key
     * @param string   $name        Select name
     * @param string   $description Select description
     * @param string[] $values      Associative array
     */
    public function __construct($key, $name, $description, Array $values) {
        parent::__construct($key, $name, $description);
        
        // Store the sanitized values
        $this->_values = array_combine(
            array_map(
                'strval', 
                array_keys(
                    $values
                )
            ), 
            array_map(
                'strval', 
                array_values(
                    $values
                )
            )
        );
    }
    
    /**
     * Generate the PHP code for this Select Item
     * 
     * @return string
     */
    public function register() {
        // Export the select item ID
        $itemIdExported = $this->_exportMixed($this->getId());
        
        // Export the select name
        $itemNameExported = $this->_exportMixed(
            $this->getName(), 
            $this->getTranslateName(),
            self::I18N_ESC_HTML
        );
        
        // Export the select description
        $itemDescriptionExported = $this->_exportMixed(
            $this->getDescription(), 
            $this->getTranslateDescription(),
            self::I18N_ESC_HTML
        );
        
        // Prepare the section ID
        $sectionIdExported = $this->_exportMixed(null == $this->getSection() ? null : $this->getSection()->getId());
        
        // Prepare the default value
        $defaultValue = strlen($this->getDefault()) ? $this->getDefault() : current(array_keys($this->getValues()));
        
        // Export the default value
        $defaultValueExported = $this->_exportMixed($defaultValue);

        // Formatted choices
        $formattedChoices = $this->_exportArray(
            $this->getValues(), 
            2, 
            $this->getTranslateValues()
        );

        // Prepare the add_setting arguments
        $addSettingTransportExported = $this->_exportMixed($this->getTransport());
        $addSettingSanitizeCallbackExported = $this->_exportMixed($this->getSanitizeCallback());
        
        // Prepare the result
        return <<<"CODE"
// $this->_name: dropdown setting
\$wp_customize->add_setting($itemIdExported, array(
    'default'           => $defaultValueExported,
    'transport'         => $addSettingTransportExported,
    'sanitize_callback' => $addSettingSanitizeCallbackExported,
));

// $this->_name: dropdown control
\$wp_customize->add_control(new WP_Customize_Control(\$wp_customize, $itemIdExported, array(
    'section'     => $sectionIdExported,
    'type'        => 'select',
    'label'       => $itemNameExported,
    'description' => $itemDescriptionExported,
    'choices'     => $formattedChoices,
)));
CODE;
    }
    
    /**
     * PHP code to enqueue styles
     * 
     * @param string[] $cssRulesByItemValue CSS rules
     * @see WordPress_Customizer
     * @return string
     */
    public function stylize($cssRulesByItemValue) {
        // No definitions found in customizer.css for this item
        if (!is_array($cssRulesByItemValue) || !count($cssRulesByItemValue)) {
            return '';
        }
        
        // Set the final item key
        $itemKeyFinal = $this->_generateId($this->getKey(), true);
        $itemKeyExported = $this->_exportMixed($itemKeyFinal);
        $itemDefaultExported = $this->_exportMixed($this->getDefault());
        
        // A single key was defined
        if (1 === count($cssRulesByItemValue)) {
            // Get the item value
            $itemValue = current(array_keys($cssRulesByItemValue));
            
            // Get the exported item value
            $itemValueExported = $this->_exportMixed($itemValue);
            
            // Get the CSS rule
            $cssRule = current($cssRulesByItemValue);
            
            // Get the exported CSS rule
            $cssRuleExported = preg_replace(array('%[\r\n] *%', '%\s*([\{\}])\s*%', '% {2,}%'), array(' ', '${1}', ' '), $this->_exportMixed($cssRule));
            
            // A single IF statement will suffice
            return <<<"CODE"
// Get the $itemKeyFinal value
\$$itemKeyFinal = get_theme_mod($itemKeyExported, $itemDefaultExported);  

// Set the '$this->_name' CSS rule
if ($itemValueExported == \$$itemKeyFinal) {
    \$result .= $cssRuleExported;
}
CODE;
        }
        
        // Prepare the switch cases
        $switchCases = '';
        foreach ($cssRulesByItemValue as $itemValue => $cssRule) {
            // Not a valid item
            if (!is_string($cssRule)) {
                continue;
            }
            
            // Get the exported CSS rule
            $cssRuleExported = preg_replace(array('%[\r\n] *%', '%\s*([\{\}])\s*%', '% {2,}%'), array(' ', '${1}', ' '), $this->_exportMixed($cssRule));
            
            // Go through the cases
            $switchCases .= 'case ' . $this->_exportMixed($itemValue) . ':' 
                . PHP_EOL . str_repeat(' ', 8) . "\$result .= $cssRuleExported;"
                . PHP_EOL . str_repeat(' ', 4) . 'break;'
                . PHP_EOL . PHP_EOL . str_repeat(' ', 4);
        }
        $switchCases = rtrim($switchCases);
        
        // Append the PHP code
        return <<<"CODE"
// Get the $itemKeyFinal value
\$$itemKeyFinal = get_theme_mod($itemKeyExported, $itemDefaultExported);  

// Set the '$this->_name' CSS rule
switch (\$$itemKeyFinal) {
    $switchCases
}
CODE;
    }
    
    /**
     * Get the select (dropdown) values; associative array
     * 
     * @return string[]
     */
    public function getValues() {
        return $this->_values;
    }
    
    
    /**
     * Get whether to translate the values
     * 
     * @return boolean
     */
    public function getTranslateValues() {
        return $this->_translateValues;
    }
    
    /**
     * Translate the element values (default <b>true</b>)
     * 
     * @param boolean $boolean Whether to translate the element values
     * @return WordPress_Customizer_Element_Item_Select
     */
    public function setTranslateValues($boolean) {
        $this->_translateValues = (boolean) $boolean;
        return $this;
    }

}

/* EOF */
