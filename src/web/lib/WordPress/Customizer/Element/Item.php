<?php
/**
 * Theme Warlock - WordPress_Customizer_Element_Item
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Customizer_Element_Item extends WordPress_Customizer_Element {

    /**
     * add_setting transport types
     */
    const TRANSPORT_REFRESH = 'refresh';
    
    /**
     * add_setting sanitize callback functions
     * 
     * @see https://divpusher.com/blog/wordpress-customizer-sanitization-examples#text
     */
    const SANITIZE_ESC_ATTR  = 'esc_attr';
    const SANITIZE_ESC_URL   = 'esc_url';
    const SANITIZE_HEX_COLOR = 'sanitize_hex_color';
    const SANITIZE_NO_HTML   = 'wp_filter_nohtml_kses';
    
    /**
     * Original item key
     * 
     * @var string
     */
    protected $_key = null;
    
    /**
     * Item's parent section
     * 
     * @var WordPress_Customizer_Element_Section|null 
     */
    protected $_section = null;
    
    /**
     * Whether or not this item will stylize the theme (generate CSS)
     * @var boolean
     */
    protected $_stylize = false;
    
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
     * Default value
     * 
     * @var mixed
     */
    protected $_default = '';
    
    /**
     * WP Customizer Item
     * 
     * @param string $key         Element key
     * @param string $name        Element name
     * @param string $description (optional) Element description; default empty string
     */    
    public function __construct($key, $name, $description = '') {
        // Store the key
        $this->_key = strval($key);
        
        // Get the element ID
        $this->_id = $this->_generateId($this->_key, true);
        
        // Store the element name and description
        $this->_name = strval($name);
        $this->_description = strval($description);
    }
    
    /**
     * Generate the PHP code to register this item
     * 
     * @return string
     */
    public function register() {
        // Implement in child class
    }
    
    /**
     * Generate the PHP code to use this element to stylize the page
     * 
     * @return string
     */
    public function stylize($cssRules){
        // Implement in child class
    }
    
    /**
     * Get this item's original key
     * 
     * @return string
     */
    public function getKey() {
        return $this->_key;
    }
    
    /**
     * Get this item's parent section
     * 
     * @return WordPress_Customizer_Element_Section|null
     */
    public function getSection() {
        return $this->_section;
    }
    
    /**
     * Set this item's parent section
     * 
     * @param WordPress_Customizer_Element_Section $section Item parent section
     * @return WordPress_Customizer_Element_Item
     */
    public function setSection(WordPress_Customizer_Element_Section $section) {
        $this->_section = $section;
        return $this;
    }
    
    /**
     * Get this item's default value
     * 
     * @return mixed
     */
    public function getDefault() {
        return $this->_default;
    }
    
    /**
     * Set this item's default value
     * 
     * @param mixed $default Item default value
     * @return WordPress_Customizer_Element_Item
     */
    public function setDefault($default) {
        $this->_default = $default;
        return $this;
    }
    
    /**
     * Get this item's add_setting transport
     * 
     * @return string
     */
    public function getTransport() {
        return $this->_transport;
    }
    
    /**
     * Get the item's variable name to use in PHP scripts
     * 
     * @return string
     */
    public function exportVarName() {
        // Set the final item key
        $itemKeyFinal = $this->_generateId($this->getKey(), true);
        
        // Get the PHP code
        return "\$$itemKeyFinal";
    }
    
    /**
     * Get the item's variable name definition to use in PHP scripts
     * 
     * @return string
     */
    public function exportVarInit() {
        // Set the final item key
        $itemKeyFinal = $this->_generateId($this->getKey(), true);
        $itemKeyExported = $this->_exportMixed($itemKeyFinal);
        $itemDefaultExported = $this->_exportMixed($this->getDefault());
        
        // Get the PHP code
        $result =  <<<"CODE"
// Get the $itemKeyFinal value
\$$itemKeyFinal = get_theme_mod($itemKeyExported, $itemDefaultExported);
CODE;
        
        // All done
        return trim($result);
    }
    
    /**
     * Set this item's add_setting transport
     * 
     * @param string $transport Transport
     * @return WordPress_Customizer_Element_Item
     */
    public function setTransport($transport) {
        $this->_transport = $transport;
        return $this;
    }
    
    /**
     * Get this item's add_setting sanitize callback
     * 
     * @return mixed
     */
    public function getSanitizeCallback() {
        return $this->_sanitizeCallback;
    }
    
    /**
     * Set this item's add_setting sanitize callback
     * 
     * @param string $sanitizeCallback Sanitize callback
     * @return WordPress_Customizer_Element_Item
     */
    public function setSanitizeCallback($sanitizeCallback) {
        $this->_sanitizeCallback = $sanitizeCallback;
        return $this;
    }
    
    /**
     * Get whether or not this item will stylize the theme (generate CSS)
     * 
     * @return boolean
     */
    public function getStylize() {
        return $this->_stylize;
    }
    
    /**
     * Set whether or not this item will stylize the theme (generate CSS)
     * 
     * @param boolean $boolean
     * @return WordPress_Customizer_Element_Item
     */
    public function setStylize($boolean) {
        $this->_stylize = (boolean) $boolean;
        return $this;
    }
    
    /**
     * Get the PHP code to define a CSS rule with any number of placeholders
     * 
     * @param string $itemTitle           CSS rules title
     * @param string $cssRule             CSS rules; must use the defined placeholders (@see <b>$cssPlaceholders</b>)
     * @param array  $cssPlaceholders     Associative array of placeholder => array of item key, [item default value] | item key; <br/>
     * Example: <b>array('__COLOR__' => array('firstColor', '#ffffff'))</b> or <br/>
     * <b>array('__COLOR__' => 'firstColor')</b>
     * @param boolean $checkValueNotEmpty Do not use CSS rules if the user-defined value is empty (the CSS placeholder replacement might yield an invalid CSS rule); default <b>true</b>
     * @return string
     */
    protected function _getCssRulePlaceholders($itemTitle, $cssRule, Array $cssPlaceholders, $checkValueNotEmpty = true) {
        // Prepare the result
        $result = '';
        
        // Prepare the exported item keys
        $exportedItemKeys = array();
        
        // Go through the placeholders
        foreach ($cssPlaceholders as $placeholderName => $itemDetails) {
            // Just one value provided, use the default
            if (!is_array($itemDetails)) {
                $itemDetails = array($itemDetails, $this->getDefault());
            }
            
            // Not a valid entry
            if (!is_array($itemDetails) || 2 !== count($itemDetails)) {
                continue;
            }
            
            // Get the item key and the default value
            list($itemKey, $itemDefault) = $itemDetails;
            
            // Set the item type
            $itemType = strtolower(trim(preg_replace(array('%[^a-z0-9]+%ims', '%\s{2,}%'), ' ', $placeholderName)));
                
            // Set the final item key
            $itemKeyFinal = $this->_generateId($itemKey, true);
            $itemKeyExported = $this->_exportMixed($itemKeyFinal);
            $itemDefaultExported = $this->_exportMixed($itemDefault);

            // Store the export
            $exportedItemKeys[$placeholderName] = $itemKeyFinal;
            
            // Append the PHP code
            $result .= <<<"CODE"
// Get the $itemKeyFinal $itemType value
\$$itemKeyFinal = get_theme_mod($itemKeyExported, $itemDefaultExported);  
CODE;
            // Add line endings
            $result .= PHP_EOL . PHP_EOL;
        }

        // No valid values
        if (!count($exportedItemKeys)) {
            return '';
        }
        
        // Replace the color
        $cssRuleFinalExported = $this->_getCssRulePlaceholdersVarInsert(
            $exportedItemKeys, 
            // Clean-up the rules in 1 line
            preg_replace(array('%[\r\n] *%', '%\s*([\{\}])\s*%', '% {2,}%'), array(' ', '${1}', ' '), $cssRule)
        );
        
        // Check that the value is not empty
        if ($checkValueNotEmpty) {
            // Prepare the check statement
            $nonEmptyStatementArray = array();
            foreach ($exportedItemKeys as $itemKeyFinal) {
                $nonEmptyStatementArray[] = "!empty(\$$itemKeyFinal)";
            }
            
            // Convert to a string
            $nonEmptyStatement = implode(' && ', $nonEmptyStatementArray);
            
            // Append the PHP code
            $result .= <<<"CODE"
// $itemTitle: CSS rule
if ($nonEmptyStatement) {
    \$result .= $cssRuleFinalExported;
}
CODE;
        } else {
            // Append the PHP code
            $result .= <<<"CODE"
// $itemTitle: CSS rule
\$result .= $cssRuleFinalExported;
CODE;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Replace placeholders with "variable inserts" within a string. <br/>
     * 
     * @param array $placeholderVariables Associative array of <ul>
     * <li><b>__PLACEHOLDER__</b> => <b>variableName</b>,</li>
     * </ul>
     * @param string $text PHP code to perform the replacements in
     * @return string PHP code 
     * @example <b>_getCssRulePlaceholdersVarInsert</b>(<br/>array('__COLOR__' => 'colorName'), <br/>'background: __COLOR__;'<br/>) = <b>"'background: ' . $colorName . ';'"</b>;
     */
    protected function _getCssRulePlaceholdersVarInsert(Array $placeholderVariables, $text) {
        // Escape the single quotes
        $textSingleQuotesEscaped = preg_replace("%(?<!\\\)\'%", "\'", $text);
        
        // Export keys
        $exportKeys = array_keys($placeholderVariables);
        
        // Export values
        $exportValues = array_map(
            function($variableName){
                return "' . \$$variableName . '";
            }, $placeholderVariables
        );
        
        // Prepare the export
        return "'" . str_replace($exportKeys, $exportValues, $textSingleQuotesEscaped) . "'";
    }

}

/* EOF */