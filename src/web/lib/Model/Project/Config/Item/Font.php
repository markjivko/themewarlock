<?php
/**
 * Theme Warlock - Model_Project_Config_Item_Font
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config_Item_Font extends Model_Project_Config_Item {
    
    // Item type
    const ITEM_TYPE    = "font";
    const ITEM_DEFAULT = "";

    /**
     * Use a drop-down picker for options
     * 
     * @var boolean
     */
    protected $_metaOptionsPicker = true;
    
    /**
     * Validate String
     * 
     * @param string $value
     * @throws Exception
     */
    protected function _validateValue($value) {
        // All done
        return is_string($value);
    }
    
    /**
     * File extensions not available for <b>font</b>
     * 
     * @param string $extension
     * @return Model_Project_Config_Item_Font
     * @deprecated
     */
    public function setExtension($extension) {
        // All done
        return $this;
    }
    
    /**
     * Options details not available for <b>font</b>, they are automatically
     * added by the end user interface - Google Font preview
     * 
     * @param array $optionsDetails
     * @return Model_Project_Config_Item_Font
     * @deprecated
     */
    public function setMetaOptionsDetails($optionsDetails) {
        // All done
        return $this;
    }
    
}

/* EOF */