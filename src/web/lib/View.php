<?php
/**
 * Theme Warlock - View
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class View {
    
    /**
     * Template name
     * 
     * @var string
     */
    protected $_template = 'index';
    
    /**
     * Placeholders
     * 
     * @var array
     */
    protected $_placeholders = array();
    
    /**
     * Output content
     * 
     * @var string
     */
    protected $_content = '';
    
    /**
     * Display the template
     * 
     * @return null
     */
    public function display() {        
        // Load the file
        if (!file_exists($filePath = ROOT . '/web/tpl/' . $this->_template . '.phtml')) {
            throw new Exception('Template "' . $filePath . '" not found');
        }
        
        // Content is something special
        $this->_content = ob_get_clean();
        
        // Load the template
        require $filePath;
    }
    
    /**
     * Get a placeholder by name
     * 
     * @param string $placeHolder Placeholder name
     * @return mixed Null if placeholder is not defined
     */
    public function getPlaceholder($placeHolder) {
        // Get the value
        $placeHolderValue = isset($this->_placeholders[$placeHolder]) ? $this->_placeholders[$placeHolder] : null;

        // All done
        return is_callable($placeHolderValue) ? $placeHolderValue($placeHolder) : $placeHolderValue;
    }
    
    /**
     * Get a template part by name
     * 
     * @param string $partName Part name
     * @return string Template part contents
     * @throws Exception
     */
    public function getPart($partName) {
        // Load the file
        if (!file_exists($filePath = ROOT . '/web/tpl/part/' . $partName . '.phtml')) {
            throw new Exception('Template "' . $filePath . '" not found');
        }
        
        // Start the output buffer
        ob_start();
        
        // Load the template
        require $filePath;
        
        // All done
        return ob_get_clean();
    }
    
    /**
     * Print a placeholder by name
     * 
     * @param string $placeHolder Placeholder name
     */
    public function printPlaceholder($placeHolder) {
        echo $this->getPlaceholder($placeHolder);
    }
    
    /**
     * Print the content
     * 
     * @return null
     */
    public function printContent() {
        echo $this->_content;
    }
    
    /**
     * Set the placeholders
     * 
     * @param string $placeHolderName  Placeholder name
     * @param mixed  $placeHolderValue Placeholder value
     * @return null
     */
    public function setPlaceholder($placeHolderName, $placeHolderValue) {
        // Set the placeholders
        $this->_placeholders[$placeHolderName] = $placeHolderValue;
    }
    
    /**
     * Get the placeholders
     * 
     * @return array Placeholders
     */
    public function getPlaceholders() {
        return $this->_placeholders;
    }
    
    /**
     * Set the template name without the .phtml suffix
     * 
     * @param string $templateName Template name
     * @return null
     */
    public function setTemplate($templateName) {
        $this->_template = $templateName;
    }
    
    /**
     * Get the template name
     * 
     * @return string
     */
    public function getTemplate() {
        return $this->_template;
    }
}

/*EOF*/