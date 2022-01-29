<?php
/**
 * Theme Warlock - WordPress_Customizer_Element_Item_Text
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Customizer_Element_Item_Text extends WordPress_Customizer_Element_Item {

    /**
     * Input field value
     * 
     * @var string
     */
    protected $_value = '';
    
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
     * WP Customizer Text
     * 
     * @param string   $key         Input key
     * @param string   $name        Input name
     * @param string   $description Input description
     * @param string   $text        (optional) Input field value; default empty string
     */
    public function __construct($key, $name, $description, $text = '') {
        parent::__construct($key, $name, $description);
        
        // Store the sanitized values
        $this->_value = $text;
    }
    
    /**
     * Generate the PHP code for this Text Item
     * 
     * @return string
     */
    public function register() {
        // Export the text item ID
        $itemIdExported = $this->_exportMixed($this->getId());
        
        // Export the text name
        $itemNameExported = $this->_exportMixed(
            $this->getName(), 
            $this->getTranslateName(),
            self::I18N_ESC_HTML
        );
        
        // Export the text description
        $itemDescriptionExported = $this->_exportMixed(
            $this->getDescription(), 
            $this->getTranslateDescription(),
            self::I18N_ESC_HTML
        );
        
        // Prepare the section ID
        $sectionIdExported = $this->_exportMixed(null == $this->getSection() ? null : $this->getSection()->getId());
        
        // Prepare the default value
        $defaultValueExported = $this->_exportMixed($this->getValue());

        // Prepare the add_setting arguments
        $addSettingTransportExported = $this->_exportMixed($this->getTransport());
        $addSettingSanitizeCallbackExported = $this->_exportMixed($this->getSanitizeCallback());
        
        // Prepare the result
        return <<<"CODE"
// $this->_name: setting
\$wp_customize->add_setting($itemIdExported, array(
    'default'           => $defaultValueExported,
    'transport'         => $addSettingTransportExported,
    'sanitize_callback' => $addSettingSanitizeCallbackExported,
));

// $this->_name: control
\$wp_customize->add_control(new WP_Customize_Control(\$wp_customize, $itemIdExported, array(
    'section'     => $sectionIdExported,
    'type'        => 'text',
    'label'       => $itemNameExported,
    'description' => $itemDescriptionExported,
)));
CODE;
    }
    
    /**
     * Get the input field value
     * 
     * @return string
     */
    public function getValue() {
        return $this->_value;
    }
    
}

/* EOF */
