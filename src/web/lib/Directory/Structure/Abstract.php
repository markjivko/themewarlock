<?php

/**
 * Theme Warlock - Directory Structure Abstract
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Defines a folder
 */
abstract class Directory_Structure_Abstract {

    /**
     * Directory structure item type
     * 
     * @var string
     */
    protected $_type;

    /**
     * Minimum folder size in bytes
     * 
     * @var int
     */
    protected $_sizeMin;

    /**
     * Maximum folder size in bytes
     * 
     * @var int
     */
    protected $_sizeMax;

    /**
     * Exact folder name
     * 
     * @var string
     */
    protected $_name;

    /**
     * Folder name RegEx
     * 
     * @var string
     */
    protected $_nameRegEx;

    /**
     * Minimum number of appearences
     * 
     * @var int
     */
    protected $_countMin = 1;

    /**
     * Maximum number of appearences
     * 
     * @var int
     */
    protected $_countMax;

    /**
     * Image width
     * 
     * @var int
     */
    protected $_width;

    /**
     * Image height
     * 
     * @var int
     */
    protected $_height;

    /**
     * Image min width
     * 
     * @var int
     */
    protected $_minWidth;

    /**
     * Image min height
     * 
     * @var int
     */
    protected $_minHeight;

    /**
     * Image type
     * 
     * @var string
     */
    protected $_imagetype;

    /**
     * Minimum folder size in bytes
     * 
     * @param int $size Size
     * @return Directory_Structure_Abstract
     */
    public function sizeMin($size) {
        $this->_sizeMin = (int) $size;
        return $this;
    }

    /**
     * Maximum folder size in bytes
     * 
     * @param int $size Size
     * @return Directory_Structure_Abstract
     */
    public function sizeMax($size) {
        $this->_sizeMax = (int) $size;
        return $this;
    }

    /**
     * Folder name exactly
     * 
     * @param string $name Name
     * @return Directory_Structure_Abstract
     */
    public function name($name) {
        $this->_name = (string) $name;
        return $this;
    }

    /**
     * Folder name RegEx
     * 
     * @param string $nameRegEx Name RegEx
     * @return Directory_Structure_Abstract
     */
    public function nameRegex($nameRegEx) {
        $this->_nameRegEx = (string) $nameRegEx;
        return $this;
    }

    /**
     * Minimum number of appearences
     * 
     * @param int $count Count
     * @return Directory_Structure_Abstract
     */
    public function countMin($count) {
        $this->_countMin = (int) $count;
        return $this;
    }

    /**
     * Maximum number of appearences
     * 
     * @param int $count Count
     * @return Directory_Structure_Abstract
     */
    public function countMax($count) {
        $this->_countMax = (int) $count;
        return $this;
    }

    /**
     * Image width in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function width($size = null) {
        return $this;
    }

    /**
     * Image height in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function height($size = null) {
        return $this;
    }

    /**
     * Image min width in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function minWidth($size = null) {
        return $this;
    }

    /**
     * Image min height in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function minHeight($size = null) {
        return $this;
    }

    /**
     * Image mime type
     * 
     * @param string $type Mime type
     * @return Directory_Structure_File
     */
    public function imageType($type = null) {
        return $this;
    }

    /**
     * Return the information
     * 
     * @return string Serialized array of properties
     */
    final public function __toString() {
        // Get a reflection
        $reflection = new ReflectionClass($this);

        // Prepare the data
        $data = array();

        // Get the properties names and values
        foreach ($reflection->getProperties(ReflectionProperty::IS_PROTECTED) as $prop) {
            $data[$prop->getName()] = $this->{$prop->getName()};
        }

        // Return a string
        return serialize($data);
    }

}

/*EOF*/