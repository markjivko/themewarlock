<?php
/**
 * Theme Warlock - Model_Project_Config_Item_Image
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Config_Item_Image extends Model_Project_Config_Item {
    
    // JSON keys
    const JSON_IMAGE_WIDTH  = 'iw';
    const JSON_IMAGE_HEIGHT = 'ih';
    const JSON_IMAGE_TYPE   = 'it';
    
    // Item type
    const ITEM_TYPE           = 'image';
    const ITEM_DEFAULT        = '';
    const ITEM_ON_DISK        = true;
    const ITEM_ON_DISK_BINARY = true;
    
    // Image types
    const TYPE_PNG = 'image/png';
    const TYPE_GIF = 'image/gif';
    const TYPE_JPG = 'image/jpeg';
    
    // File extensions
    const EXT_PNG = 'png';
    const EXT_GIF = 'gif';
    const EXT_JPG = 'jpg';
    
    // Allowed types
    protected $_allowedTypes = array(
        self::TYPE_PNG,
        self::TYPE_GIF,
        self::TYPE_JPG,
    );
    
    /**
     * Required width
     * 
     * @var int
     */
    protected $_imageWidth = null;
    
    /**
     * Required height
     * 
     * @var int
     */
    protected $_imageHeight = null;
    
    /**
     * Required type
     * 
     * @var string
     */
    protected $_imageType = self::TYPE_PNG;
    
    /**
     * Get the image path(s) if available
     * 
     * @return string|string[]|null
     */
    public function getPath() {
        // Get the actual item
        $parent = dirname($this->_itemPathOnDisk);

        // A list of elements
        if (is_array($this->_value)) {
            $result = array();
            foreach ($this->_value as $value) {
                if (self::ITEM_DEFAULT != $value) {
                    $result[] = $parent . '/' . $value;
                }
            }
            return $result;
        }
        
        // Not the default value
        if (self::ITEM_DEFAULT != $this->_value) {
            return $parent . '/' . $this->_value;
        }

        // Nothing to do
        return null;
    }
    
    /**
     * Get required image width
     * 
     * @return int|null
     */
    public function getImageWidth() {
        return $this->_imageWidth;
    }
    
    /**
     * Set required image width
     * 
     * @param int $width
     * @return Model_Project_Config_Item_Image
     */
    public function setImageWidth($width) {
        $width = intval($width);
        if ($width > 0) {
            $this->_imageWidth = $width;
        }
        
        // All done
        return $this;
    }
    
    /**
     * Get required image height
     * 
     * @return int|null
     */
    public function getImageHeight() {
        return $this->_imageHeight;
    }
    
    /**
     * Set require image height
     * 
     * @param int $height
     * @return Model_Project_Config_Item_Image
     */
    public function setImageHeight($height) {
        $height = intval($height);
        if ($height > 0) {
            $this->_imageHeight = $height;
        }
        
        // All done
        return $this;
    }
    
    /**
     * Get the required image type
     * 
     * @return string|null
     */
    public function getImageType() {
        return $this->_imageType;
    }
    
    /**
     * Set required image type (and the corresponding extension), one of: <ul>
     * <li>Model_Project_Config_Item_Image::TYPE_PNG</li>
     * <li>Model_Project_Config_Item_Image::TYPE_GIF</li>
     * <li>Model_Project_Config_Item_Image::TYPE_JPG</li>
     * </ul>
     * @param string $type
     * @return Model_Project_Config_Item_Image
     */
    public function setImageType($type) {
        $type = trim($type);
        if (in_array($type, $this->_allowedTypes)) {
            $this->_imageType = $type;
            
            // Set the extension as well
            switch ($type) {
                case self::TYPE_PNG:
                    $this->setExtension(self::EXT_PNG);
                    break;
                
                case self::TYPE_GIF:
                    $this->setExtension(self::EXT_GIF);
                    break;
                
                case self::TYPE_JPG:
                    $this->setExtension(self::EXT_JPG);
                    break;
            }
        }
        
        // All done
        return $this;
    }
    
    /**
     * Validate String
     * 
     * @param string $value
     * @throws Exception
     */
    protected function _validateValue($value) {
        // Uploading a file
        if (is_file($value)) {
            // Get the image information
            $imageInfo = @getimagesize($value);
            
            // Bad result
            if (!is_array($imageInfo)) {
                throw new Exception('Not a valid image file');
            }
            
            // Validate the type
            if (null !== $this->getImageType() && $this->getImageType() != $imageInfo['mime']) {
                throw new Exception('Invalid image type (' . $imageInfo['mime'] . '), expecting ' . $this->getImageType() . ' for field "' . $this->getTitleOrKey() . '"');
            }
            
            // Validate the width
            if (null !== $this->getImageWidth() && $this->getImageWidth() != $imageInfo[0]) {
                throw new Exception('Invalid width (' . $imageInfo[0] . 'px), expecting ' . $this->getImageWidth() . 'px for field "' . $this->getTitleOrKey() . '"');
            }
            
            // Validate the height
            if (null !== $this->getImageHeight() && $this->getImageHeight() != $imageInfo[1]) {
                throw new Exception('Invalid height (' . $imageInfo[1] . 'px), expecting ' . $this->getImageHeight() . 'px for field "' . $this->getTitleOrKey() . '"');
            }
        }
        
        return strlen($value);
    }
    
    /**
     * Serialize to Array
     * 
     * @return array
     */
    public function toArray() {
        // Prepare the result
        $result = parent::toArray();
        
        // Append image values
        $result[self::JSON_IMAGE_WIDTH]  = $this->getImageWidth();
        $result[self::JSON_IMAGE_HEIGHT] = $this->getImageHeight();
        $result[self::JSON_IMAGE_TYPE]   = $this->getImageType();
        
        // All done
        return $result;
    }
    
    /**
     * Options are not available for <b>image</b> files!
     * 
     * @param array $options Options (default <b>array()</b>)
     * @return Model_Project_Config_Item_Image
     * @deprecated
     */
    public function setOptions(Array $options = array()) {
        return $this;
    }
    
    /**
     * Options are not available for <b>image</b> files!
     * 
     * @param boolean $strict Strict mode (default <b>true</b>)
     * @return Model_Project_Config_Item_Image
     * @deprecated
     */
    public function setOptionsStrict($strict = true) {
        return $this;
    }
    
    /**
     * Cannot disable <b>image</b> files!
     * 
     * @param boolean $isDisabled Mark item as disabled (default <b>true</b>)
     * @return Model_Project_Config_Item_Image
     * @deprecated
     */
    public function setIsDisabled($isDisabled = true) {
        return $this;
    }
    
    /**
     * Meta refresh action not available for <b>image</b>
     * 
     * @param string $action
     * @return Model_Project_Config_Item_Image
     * @deprecated
     */
    public function setMetaRefresh($action) {
        // All done
        return $this;
    }
    
    /**
     * Drop-down picker not available for <b>image</b>
     * 
     * @param boolean $enabled
     * @return Model_Project_Config_Item_Image
     * @deprecated
     */
    public function setMetaOptionsPicker($enabled = true) {
        return $this;
    }
    
}

/* EOF */