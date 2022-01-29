<?php
/**
 * Theme Warlock - WordPress_Pot_Translations_Reader_String
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Pot_Translations_Reader_String extends WordPress_Pot_Translations_Reader {

    /**
     * String to read from
     * 
     * @var string
     */
    protected $_str = '';

    /**
     * String reader
     */
    public function __construct($str = '') {
        $this->_str = $str;
        $this->_pos = 0;
    }

    /**
     * @param string $bytes
     * @return string
     */
    public function read($bytes) {
        $data = $this->substr($this->_str, $this->_pos, $bytes);
        $this->_pos += $bytes;
        if ($this->strlen($this->_str) < $this->_pos) {
            $this->_pos = $this->strlen($this->_str);
        }
        return $data;
    }

    /**
     * @param int $pos
     * @return int
     */
    public function seekto($pos) {
        $this->_pos = $pos;
        if ($this->strlen($this->_str) < $this->_pos) {
            $this->_pos = $this->strlen($this->_str);
        }
        return $this->_pos;
    }

    /**
     * @return int
     */
    public function length() {
        return $this->strlen($this->_str);
    }

    /**
     * @return string
     */
    public function readAll() {
        return $this->substr($this->_str, $this->_pos, $this->strlen($this->_str));
    }

}

/* EOF */