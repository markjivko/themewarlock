<?php
/**
 * Theme Warlock - WordPress_Customizer
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Customizer {

    /**
     * Addon instance
     * 
     * @var Addon
     */
    protected $_addon = null;
    
    /**
     * WP Customizer helper
     */
    public function __construct(Addon $addon) {
        $this->_addon = $addon;
    }
    
    /**
     * Customizer elements
     * 
     * @var WordPress_Customizer_Element[]
     */
    protected $_elements = array();
    
    /**
     * Add a Panel
     * 
     * @param WordPress_Customizer_Element_Panel $panel Panel
     * @return WordPress_Customizer
     */
    public function addPanel(WordPress_Customizer_Element_Panel $panel) {
        $this->_elements[] = $panel;
        return $this;
    }
    
    /**
     * Add a Section
     * 
     * @param WordPress_Customizer_Element_Section $section Section
     * @return WordPress_Customizer
     */
    public function addSection(WordPress_Customizer_Element_Section $section) {
        $this->_elements[] = $section;
        return $this;
    }
    
    /**
     * Add an item
     * 
     * @param WordPress_Customizer_Element_Item $item Item
     * @return WordPress_Customizer
     */
    public function addItem(WordPress_Customizer_Element_Item $item) {
        $this->_elements[] = $item;
        return $this;
    }
    
    /**
     * Get the customizer elements
     * 
     * @return WordPress_Customizer_Element[]
     */
    public function getElements() {
        return $this->_elements;
    }
    
    /**
     * Generate the PHP code to register all the provided elements
     * 
     * @return string
     */
    public function register() {
        // Prepare the result
        $result = '';
        
        // Go through the items
        foreach ($this->_elements as $element) {
            // A valid customizer element
            if ($element instanceof WordPress_Customizer_Element) {
                $result .= $element->register() . PHP_EOL . PHP_EOL;
            }
        }
        
        // All done
        return trim($result);
    }
    
    /**
     * Generate the PHP code to stylize the page using CSS from the Addon's <b>cssRules</b> property
     * 
     * @return string
     */
    public function stylize() {
        // Prepare the result
        $result = '';
        
        // Go through the items
        foreach ($this->_elements as $element) {
            // A valid customizer element
            if ($element instanceof WordPress_Customizer_Element_Item && $element->getStylize() && method_exists($element, 'stylize')) {
                // Prepare the CSS rules
                $cssRules = isset($this->_addon->wpCustomizerCssRules[$element->getKey()]) ? $this->_addon->wpCustomizerCssRules[$element->getKey()] : null;

                // Call the stylizer
                $result .= $element->stylize($cssRules) . PHP_EOL . PHP_EOL;
            }
        }
        
        // All done
        return trim($result);
    }

}

/* EOF */