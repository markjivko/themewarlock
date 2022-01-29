<?php
/**
 * Theme Warlock - Model_Project_Config_Item_Color
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config_Item_Color extends Model_Project_Config_Item {
    
    // Item type
    const ITEM_TYPE    = 'color';
    const ITEM_DEFAULT = '#ffffffff';
    
    // JSON keys
    const JSON_COLOR_RGBA = 'c4';
    
    /**
     * Android-compatible AARRGGBB color format<br/>
     * Ex.: <b>#ff000000</b>
     */
    const COLOR_FORMAT = '#%02x%02x%02x%02x';
    
    /**
     * Validate String
     * 
     * @param string $value
     * @throws Exception
     */
    protected function _validateValue($value) {
        // Validate the AARRGGBB color
        if (strlen($value) && !preg_match('%^#[\da-f]{8}$%i', $value)) {
            throw new Exception('Not a valid AARRGGBB color in field "' . $this->getTitleOrKey() . '"');
        }
        
        // All done
        return is_string($value);
    }
    
    /**
     * Get the AARRGGBB color
     * 
     * @return string AARRGGBB color (ex.: #ff000000)
     */
    public function getValue() {
        return parent::getValue();
    }
    
    /**
     * Set the AARRGGBB color
     * 
     * @param mixed   $value     Color in <b>AARRGGBB</b> format (ex.: #ff000000)
     * @param boolean $userInput This value is set by the user (default <b>false</b>)
     * @return Model_Project_Config_Item_Color
     * @throws Exception
     */
    public function setValue($value = null, $userInput = false) {
        // Store the colors in lowercase format
        return parent::setValue(strtolower($value), $userInput);
    }
    
    /**
     * Get the <b>Red, Green, Blue, Alpha</b> values
     * 
     * @return array Array of <ul>
     * <li><b>(int)</b>   Red   - <b>[0,255]</b></li>
     * <li><b>(int)</b>   Green - <b>[0,255]</b></li>
     * <li><b>(int)</b>   Blue  - <b>[0,255]</b></li>
     * <li><b>(float)</b> Alpha - <b>[0,1]</b></li>
     * </ul>
     */
    public function getRgba() {
        // Get the channels
        list($a, $r, $g, $b) = sscanf($this->getValue(), self::COLOR_FORMAT);
        
        // All done
        return array($r, $g, $b, $a / 255);
    }
    
    /**
     * Get the alpha channel only
     * 
     * @return int Alpha value from 0 to 255
     */
    public function getAlpha() {
        // Get the channels
        list($a, $r, $g, $b) = sscanf($this->getValue(), self::COLOR_FORMAT);
        
        // All done
        return $a;
    }
    
    /**
     * Get the WordPress-compliant color: <ul>
     * <li><b>#</b> prefix</li>
     * <li>No alpha-channel</li>
     * <li>6 Hex characters: 0-9, a-f</li>
     * </ul>
     * 
     * @example #ff0011
     * @return string WordPress-compliant 6-character color
     */
    public function getWpColor() {
        return preg_replace('%^\#[\da-f]{2}%i', '#', $this->getValue());
    }
    
    /**
     * Set the <b>Red, Green, Blue, Alpha</b> values
     * 
     * @param int     $red       Red channel <b>[0,255]</b>
     * @param int     $green     Green channel <b>[0,255]</b>
     * @param int     $blue      Blue channel <b>[0,255]</b>
     * @param float   $alpha     Alpha channel <b>[0,1]</b>
     * @param boolean $userInput This value is set by the user (default <b>false</b>)
     * @return Model_Project_Config_Item_Color
     * @throws Exception
     */
    public function setRgba($red, $green, $blue, $alpha, $userInput = false) {
        // Fix the red
        $red = intval($red);
        $red = $red < 0 ? 0 : ($red > 255 ? 255 : $red);
        
        // Fix the green
        $green = intval($green);
        $green = $green < 0 ? 0 : ($green > 255 ? 255 : $green);
        
        // Fix the blue
        $blue = intval($blue);
        $blue = $blue < 0 ? 0 : ($blue > 255 ? 255 : $blue);
        
        // Fix the alpha channel
        $alpha = floatval($alpha);
        $alpha = $alpha < 0 ? 0 : ($alpha > 1 ? 1 : $alpha);
        
        // Prepare the HEX value
        $hexValue = sprintf(self::COLOR_FORMAT, intval(255 * $alpha), $red, $green, $blue);
        
        // Set it
        $this->setValue($hexValue, $userInput);
        
        // All done
        return $this;
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
        $data[self::JSON_COLOR_RGBA] = $this->getRgba();
        
        // All done
        return $data;
    }
    
    /**
     * Cannot disable <b>color</b> items
     * 
     * @param boolean $isDisabled Mark item as disabled (default <b>true</b>)
     * @return Model_Project_Config_Item_Color
     * @deprecated
     */
    public function setIsDisabled($isDisabled = true) {
        return $this;
    }
    
    /**
     * File extensions not available for <b>color</b>
     * 
     * @param string $extension
     * @return Model_Project_Config_Item_Color
     * @deprecated
     */
    public function setExtension($extension) {
        // All done
        return $this;
    }
    
    /**
     * Meta refresh action not available for <b>color</b>
     * 
     * @param string $action
     * @return Model_Project_Config_Item_Color
     * @deprecated
     */
    public function setMetaRefresh($action) {
        // All done
        return $this;
    }
    
    /**
     * Drop-down picker not available for <b>color</b>
     * 
     * @param boolean $enabled
     * @return Model_Project_Config_Item_Color
     * @deprecated
     */
    public function setMetaOptionsPicker($enabled = true) {
        return $this;
    }
    
}

/* EOF */