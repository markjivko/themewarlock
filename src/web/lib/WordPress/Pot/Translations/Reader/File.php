<?php
/**
 * Theme Warlock - WordPress_Pot_Translations_Reader_File
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Pot_Translations_Reader_File extends WordPress_Pot_Translations_Reader {

    /**
     * File handler 
     * 
     * @var resource
     */
    protected $_fileHandler = false;
    
    /**
     * File reader
     */
    public function __construct($filename) {
        $this->_fileHandler = fopen($filename, 'rb');
    }

    /**
     * @param int $bytes
     */
    public function read($bytes) {
        return fread($this->_fileHandler, $bytes);
    }

    /**
     * @param int $pos
     * @return boolean
     */
    public function seekto($pos) {
        if (-1 == fseek($this->_fileHandler, $pos, SEEK_SET)) {
            return false;
        }
        
        $this->_pos = $pos;
        return true;
    }

    /**
     * @return bool
     */
    public function isResource() {
        return is_resource($this->_fileHandler);
    }

    /**
     * @return bool
     */
    public function feof() {
        return feof($this->_fileHandler);
    }

    /**
     * @return bool
     */
    public function close() {
        return fclose($this->_fileHandler);
    }

    /**
     * @return string
     */
    public function readAll() {
        $all = '';
        while (!$this->feof()) {
            $all .= $this->read(4096);
        }
        return $all;
    }

}

/* EOF */