<?php
/**
 * Theme Warlock - Model_Project_Config_Item_Code
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config_Item_Code extends Model_Project_Config_Item {
    
    // Item type
    const ITEM_TYPE    = 'code';
    const ITEM_DEFAULT = '';
    const ITEM_ON_DISK = true;
    
    // Known extensions
    const EXT_CSS = 'css';
    const EXT_JS  = 'js';
    const EXT_JSON  = 'json';
    
    /**
     * Validate code
     * 
     * @param string $value
     */
    protected function _validateValue($value) {
        return is_string($value);
    }
    
    /**
     * Options are not available for <b>code</b> files!
     * 
     * @param array $options Options (default <b>array()</b>)
     * @return Model_Project_Config_Item_Code
     * @deprecated
     */
    public function setOptions(Array $options = array()) {
        return $this;
    }
    
    /**
     * Options are not available for <b>code</b> files!
     * 
     * @param boolean $strict Strict mode (default <b>true</b>)
     * @return Model_Project_Config_Item_Code
     * @deprecated
     */
    public function setOptionsStrict($strict = true) {
        return $this;
    }
    
    /**
     * Lists not available for <b>code</b> files!
     * 
     * @param boolean $isList List (default <b>true</b>)
     * @return Model_Project_Config_Item_Code
     * @deprecated
     */
    public function setIsList($isList = true) {
        return $this;
    } 
    
    /**
     * Cannot disable <b>code</b> files!
     * 
     * @param boolean $isDisabled Mark item as disabled (default <b>true</b>)
     * @return Model_Project_Config_Item_Code
     * @deprecated
     */
    public function setIsDisabled($isDisabled = true) {
        return $this;
    }
    
    /**
     * Meta refresh action not available for <b>code</b>
     * 
     * @param string $action
     * @return Model_Project_Config_Item_Code
     * @deprecated
     */
    public function setMetaRefresh($action) {
        // All done
        return $this;
    }
    
    /**
     * Drop-down picker not available for <b>code</b>
     * 
     * @param boolean $enabled
     * @return Model_Project_Config_Item_Code
     * @deprecated
     */
    public function setMetaOptionsPicker($enabled = true) {
        return $this;
    }
    
}

/* EOF */