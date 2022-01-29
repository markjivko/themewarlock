<?php

/**
 * Theme Warlock - Directory Structure File
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Defines a folder
 */
class Directory_Structure_File extends Directory_Structure_Abstract {

    /**
     * Portable Network Graphics
     */
    const TYPE_PNG = 'image/png';

    /**
     * Joint Photographic Experts Group
     */
    const TYPE_JPEG = 'image/jpeg';

    /**
     * Graphics Interchange Format
     */
    const TYPE_GIF = 'image/gif';

    /**
     * Directory structure item type
     * 
     * @var string
     */
    protected $_type = 'file';

    /**
     * Open a new Directory_Structure_File instance
     * 
     * @return Directory_Structure_File
     */
    public static function set() {
        return new self();
    }

    /**
     * Image width in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function width($size = null) {
        $this->_width = (int) $size;
        return $this;
    }

    /**
     * Image height in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function height($size = null) {
        $this->_height = (int) $size;
        return $this;
    }

    /**
     * Image min width in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function minWidth($size = null) {
        $this->_minWidth = (int) $size;
        return $this;
    }

    /**
     * Image min height in pixels
     * 
     * @param int $size Size
     * @return Directory_Structure_File
     */
    public function minHeight($size = null) {
        $this->_minHeight = (int) $size;
        return $this;
    }

    /**
     * Image mime type
     * 
     * @param string $imageType Mime type
     * @return Directory_Structure_File
     */
    public function imageType($imageType = null) {
        $this->_imagetype = $imageType;
        return $this;
    }

}

/*EOF*/