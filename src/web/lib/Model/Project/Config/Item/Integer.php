<?php
/**
 * Theme Warlock - Model_Project_Config_Item_Integer
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config_Item_Integer extends Model_Project_Config_Item {
    
    // Item type
    const ITEM_TYPE     = 'int';
    const ITEM_TYPECAST = 'intval';
    const ITEM_DEFAULT  = 0;
    
    // JSON keys
    const JSON_INT_MIN = 'inm';
    const JSON_INT_MAX = 'inx';
    
    /**
     * Minimum allowed value
     * 
     * @var int|null
     */
    protected $_min = null;
    
    /**
     * Maximum allowed value
     * 
     * @var int|null
     */
    protected $_max = null;
    
    /**
     * Validate Integer
     * 
     * @param int $value
     */
    protected function _validateValue($value) {
        // Validate the min
        if (null !== $this->getMin() && $value < $this->getMin()) {
            throw new Exception('Value too small (' . $value . ' < ' . $this->getMin() . ') for field "' . $this->getTitleOrKey() . '"');
        }
        
        // Validate the max
        if (null !== $this->getMax() && $value > $this->getMax()) {
            throw new Exception('Value too large (' . $value . ' > ' . $this->getMax() . ') for field "' . $this->getTitleOrKey() . '"');
        }
        
        // All done
        return is_numeric($value);
    }
    
    /**
     * Set the smallest value allowed
     * 
     * @param int $minValue Minimum value
     * @return Model_Project_Config_Item_Integer
     */
    public function setMin($minValue) {
        $this->_min = intval($minValue);
        return $this;
    }
    
    /**
     * Get the smallest value allowed or <b>null</b> if none was set
     * 
     * @return int|null
     */
    public function getMin() {
        return $this->_min;
    }

        
    /**
     * Set the largest value allowed
     * 
     * @param int $maxValue Maximum value
     * @return Model_Project_Config_Item_Integer
     */
    public function setMax($maxValue) {
        $this->_max = intval($maxValue);
        return $this;
    }
    
    /**
     * Get the largest value allowed or <b>null</b> if none was set
     * 
     * @return int|null
     */
    public function getMax() {
        return $this->_max;
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
        $data[self::JSON_INT_MIN] = $this->getMin();
        $data[self::JSON_INT_MAX] = $this->getMax();
        
        // All done
        return $data;
    }
    
    /**
     * File extensions not available for <b>integer</b>
     * 
     * @param string $extension
     * @return Model_Project_Config_Item_Integer
     * @deprecated
     */
    public function setExtension($extension) {
        // All done
        return $this;
    }
    
}

/* EOF */