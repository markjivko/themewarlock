<?php
/**
 * Theme Warlock - WordPress_Customizer_Element
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

abstract class WordPress_Customizer_Element {

    const I18N_DEFAULT  = '__';
    const I18N_ESC_HTML = 'esc_html__';
    
    /**
     * Element ID
     * 
     * @var string
     */
    protected $_id = null;
    
    /**
     * Element name
     * 
     * @var string
     */
    protected $_name = null;
    
    /**
     * Element description
     * 
     * @var string
     */
    protected $_description = null;
    
    /**
     * Whether to translate the element name
     *
     * @var boolean
     */
    protected $_translateName = true;
    
    /**
     * Whether to translate the element description
     * 
     * @var boolean
     */
    protected $_translateDescription = true;
    
    /**
     * Element priority; [1,160]
     * 
     * @var int
     */
    protected $_priority = 160;
    
    /**
     * WP Customizer Element
     * 
     * @param string $name        Element name
     * @param string $description (optional) Element description; default empty string
     */    
    public function __construct($name, $description = '') {
        // Get the element ID
        $this->_id = $this->_generateId($name);
        
        // Store the element name and description
        $this->_name = strval($name);
        $this->_description = strval($description);
    }
    
    /**
     * Generate the PHP code to register this element
     * 
     * @return string
     */
    abstract function register();
    
    /**
     * Get the element ID
     * 
     * @return string
     */
    public function getId() {
        return $this->_id;
    }
    
    /**
     * Get the element name
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Get the element description
     * 
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }
    
    /**
     * Get whether to translate the element name
     * 
     * @return boolean
     */
    public function getTranslateName() {
        return $this->_translateName;
    }
    
    /**
     * Translate the element name (default <b>true</b>)
     * 
     * @param boolean $boolean Whether to translate the element name
     * @return WordPress_Customizer_Element
     */
    public function setTranslateName($boolean) {
        $this->_translateName = (boolean) $boolean;
        return $this;
    }
    
    /**
     * Get whether to translate the element description
     * 
     * @return boolean
     */
    public function getTranslateDescription() {
        return $this->_translateDescription;
    }
    
    /**
     * Translate the element description (default <b>true</b>)
     * 
     * @param type $boolean Whether to translate the element description
     * @return WordPress_Customizer_Element
     */
    public function setTranslateDescription($boolean) {
        $this->_translateDescription = (boolean) $boolean;
        return $this;
    }
    
    /**
     * Get the element's priority
     * 
     * @return int
     */
    public function getPriority() {
        return $this->_priority;
    }
    
    /**
     * Set the element priority
     * 
     * @param integer $priority Priority [1,160]
     * @return WordPress_Customizer_Element
     */
    public function setPriority($priority) {
        // Clamp the priority
        $priority = intval($priority);
        $priority = $priority < 1 ? 1 : ($priority > 160 ? 160 : $priority);
        
        // Store it
        $this->_priority = $priority;
        return $this;
    }
    
    /**
     * Generate a key based on the element name
     * 
     * @param string  $elementName Element name
     * @param boolean $forced      (optional) Use the <b>$elementName</b> as such, instead of creating the key from the current class name; default <b>false</b>
     * @return string
     */
    protected function _generateId($elementName, $forced = false) {
        // Prepare the key
        $key = ($forced ? $elementName : (preg_replace('%^' . preg_quote(__CLASS__) . '\_%', '', get_called_class()) . '_' . $elementName));
        
        // Create the ID
        return 'st_' . strtolower(preg_replace(array('%\W+%', '%_+%'), '_', $key));
    }
    
    /**
     * Export the string/mixed value
     * 
     * @param string  $mixed     Item value
     * @param boolean $i18nReady Use WP translation for this element
     * @return string PHP code (var_export-ed)
     */
    protected function _exportMixed($mixed, $i18nReady = false, $i18nFunction = self::I18N_DEFAULT) {
        // Prepare the text domain
        $textDomain = Tasks_1NewProject::$destDir;
        
        // Get the exported value
        return $i18nReady && is_string($mixed) && strlen($mixed) ? ("$i18nFunction(" . var_export($mixed, true) . ", '$textDomain')") : var_export($mixed, true);
    }
    
    /**
     * Export an array using indentation
     * 
     * @param array   $associativeArray Associative array
     * @param int     $indentLevel      Indentation level
     * @param boolean $i18n             (optional) Internationalize the strings in the $values argument (default <b>true</b>)
     * @return string
     */
    protected function _exportArray($associativeArray, $indentLevel = 1, $i18n = true) {
        // Prepare the text domain
        $textDomain = Tasks_1NewProject::$destDir;
        
        // Prepare the exported array
        $exportedArray = var_export($associativeArray, true);
        
        // Indent all lines
        $exportedArray = preg_replace(array('%\n\s+%s', '%\n(?!\s)%'), array(PHP_EOL . str_repeat(' ', ($indentLevel + 1) * 4), PHP_EOL . str_repeat(' ', $indentLevel * 4)), $exportedArray);
        
        // Use translations for the strings
        if ($i18n) {
            return preg_replace('%\=>\s*([\'"].*?[\'"])\s*,%', '=> __($1, \'' . $textDomain . '\'),', $exportedArray);
        }
        
        // No internationalization needed
        return $exportedArray;
    }
    
    /**
     * Replace placeholders with "variable inserts" within a string. <br/>
     * 
     * @param array $placeholderVariables Associative array of <ul>
     * <li><b>__PLACEHOLDER__</b> => <b>variableName</b>,</li>
     * </ul>
     * @param string $text PHP code to perform the replacements in
     * @return string PHP code 
     * @example <b>exportVariableInsert</b>(<br/>array('__COLOR__' => 'colorName'), <br/>'background: __COLOR__;'<br/>) = <b>"'background: ' . $colorName . ';'"</b>;
     */
    protected function _exportVariableInsert(Array $placeholderVariables, $text) {
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