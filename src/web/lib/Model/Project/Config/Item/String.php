<?php
/**
 * Theme Warlock - Model_Project_Config_Item_String
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config_Item_String extends Model_Project_Config_Item {
    
    // Item type
    const ITEM_TYPE    = "string";
    const ITEM_DEFAULT = "";
    
    // JSON keys
    const JSON_IS_TEXTAREA     = "st";
    const JSON_IS_MARKDOWN     = "sm";
    const JSON_STRING_REGEX    = 'sr';
    const JSON_STRING_OPTIONAL = 'so';
    
    /**
     * Is this string best represented in a textarea?
     * 
     * @var boolean
     */
    protected $_isTextArea = false;
    
    /**
     * Is this string best represented in a markdown-enhanced textarea?
     * 
     * @var boolean
     */
    protected $_isMarkdown = false;
    
    /**
     * Is this string optional?
     * 
     * @var boolean
     */
    protected $_isOptional = false;
    
    /**
     * Regular expression to match the string
     * 
     * @var string
     */
    protected $_regularExpression = '';
    
    /**
     * Validate String
     * 
     * @param string $value
     * @throws Exception
     */
    protected function _validateValue($value) {
        // Optional string; empty strings do not require validation
        if ($this->isOptional() && !strlen($value)) {
            return true;
        }
        
        // Validate the regular expression
        if (strlen($this->getRegEx()) && @!preg_match($this->getRegEx(), $value)) {
            throw new Exception('RegEx ' . $this->getRegEx() . ' failed for field "' . $this->getTitleOrKey() . '"');
        }
        
        // All done
        return is_string($value);
    }
    
    /**
     * Set the textarea preference
     * 
     * @param boolean $isTextArea This string is best displayed in a textarea (default <b>true</b>)
     * @return Model_Project_Config_Item_String
     */
    public function setIsTextarea($isTextArea = true) {
        $this->_isTextArea = (boolean) $isTextArea;
        return $this;
    }
    
    /**
     * Is this string best represented in a textarea?
     * 
     * @return boolean
     */
    public function isTextArea() {
        return $this->_isTextArea;
    }
    
    /**
     * Set the markdown preference
     * 
     * @param boolean $isMarkdown This string is best displayed in a markdown-enhanced textarea (default <b>true</b>)
     * @return Model_Project_Config_Item_String
     */
    public function setIsMarkdown($isMarkdown = true) {
        $this->_isMarkdown = (boolean) $isMarkdown;
        return $this;
    }
    
    /**
     * Is this string best represented in a markdown-enhanced textarea?
     * 
     * @return boolean
     */
    public function isMarkdown() {
        return $this->_isMarkdown;
    }
    
    /**
     * Set whether this string is optional
     * 
     * @param boolean $isOptional This string can be empty (default <b>true</b>)
     * @return Model_Project_Config_Item_String
     */
    public function setIsOptional($isOptional = true) {
        $this->_isOptional = (boolean) $isOptional;
        return $this;
    }
    
    /**
     * Get whether this string is optional
     * 
     * @return boolean
     */
    public function isOptional() {
        return $this->_isOptional;
    }
    
    /**
     * Set a regular expression to match the string against
     * 
     * @param string $regularExpression Full regular expression (including delimiters and flags)
     * @return Model_Project_Config_Item_String
     */
    public function setRegEx($regularExpression) {
        $this->_regularExpression = trim($regularExpression);
        return $this;
    }
    
    /**
     * Get the regular expression to match the string against
     * 
     * @return string Full regular expression (including delimiters and flags)
     */
    public function getRegEx() {
        return $this->_regularExpression;
    }

    /**
     * Custom serialization
     * 
     * @return array
     */
    public function toArray() {
        // Get the data
        $data = parent::toArray();
        
        // Append our values
        $data[self::JSON_IS_TEXTAREA]     = $this->isTextArea();
        $data[self::JSON_IS_MARKDOWN]     = $this->isMarkdown();
        $data[self::JSON_STRING_REGEX]    = $this->getRegEx();
        $data[self::JSON_STRING_OPTIONAL] = $this->isOptional();
        
        // All done
        return $data;
    }
    
    /**
     * File extensions not available for <b>string</b>
     * 
     * @param string $extension
     * @return Model_Project_Config_Item_String
     * @deprecated
     */
    public function setExtension($extension) {
        // All done
        return $this;
    }
    
}

/* EOF */