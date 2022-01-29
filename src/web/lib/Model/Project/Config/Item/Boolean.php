<?php
/**
 * Theme Warlock - Model_Project_Config_Item_Boolean
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config_Item_Boolean extends Model_Project_Config_Item {
    
    // Item type
    const ITEM_TYPE     = 'bool';
    const ITEM_TYPECAST = 'boolval';
    const ITEM_DEFAULT  = false;
    
    /**
     * Validate Boolean
     * 
     * @param boolean $value
     */
    protected function _validateValue($value) {
        return in_array($value, array(0, 1));
    }
    
    /**
     * Options are not available for <b>boolean</b>
     * 
     * @param array $options Options (default <b>array()</b>)
     * @return Model_Project_Config_Item_Boolean
     * @deprecated
     */
    public function setOptions(Array $options = array()) {
        return $this;
    }
    
    /**
     * Options are not available for <b>boolean</b>
     * 
     * @param boolean $strict Strict mode (default <b>true</b>)
     * @return Model_Project_Config_Item_Boolean
     * @deprecated
     */
    public function setOptionsStrict($strict = true) {
        return $this;
    }
    
    /**
     * Cannot disable <b>boolean</b> items
     * 
     * @param boolean $isDisabled Mark item as disabled (default <b>true</b>)
     * @return Model_Project_Config_Item_Boolean
     * @deprecated
     */
    public function setIsDisabled($isDisabled = true) {
        return $this;
    }
    
    /**
     * Lists not available for <b>boolean</b>
     * 
     * @param boolean $isList List (default <b>true</b>)
     * @return Model_Project_Config_Item_Boolean
     * @deprecated
     */
    public function setIsList($isList = true) {
        return $this;
    }
    
    /**
     * File extensions not available for <b>boolean</b>
     * 
     * @param string $extension
     * @return Model_Project_Config_Item_Boolean
     * @deprecated
     */
    public function setExtension($extension) {
        // All done
        return $this;
    }
    
    /**
     * Meta refresh action not available for <b>boolean</b>
     * 
     * @param string $action
     * @return Model_Project_Config_Item_Boolean
     * @deprecated
     */
    public function setMetaRefresh($action) {
        // All done
        return $this;
    }
    
    /**
     * Get the item value
     * 
     * @return boolean
     */
    public function getValue() {
        return $this->_value;
    }
    
    /**
     * Drop-down picker not available for <b>boolean</b>
     * 
     * @param boolean $enabled
     * @return Model_Project_Config_Item_Boolean
     * @deprecated
     */
    public function setMetaOptionsPicker($enabled = true) {
        return $this;
    }
}

/* EOF */