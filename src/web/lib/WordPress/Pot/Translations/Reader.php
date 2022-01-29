<?php

/**
 * Theme Warlock - WordPress_Pot_Translations_Reader
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class WordPress_Pot_Translations_Reader {
    /**
     * Endians
     */
    const ENDIAN_LITTLE = 'little';
    const ENDIAN_BIG    = 'big';
    
    /**
     * Endian letters
     */
    const ENDIAN_LETTER_LITTLE = 'V';
    const ENDIAN_LETTER_BIG    = 'N';
    
    /**
     * Multi-byte Encoding
     */
    const ENCODING = 'ascii';
    
    protected $_endian = self::ENDIAN_LITTLE;
    protected $_pos = 0;
    protected $_isOverloaded = false;
    
    /**
     * PHP5 constructor.
     */
    public function __construct() {
        $this->_isOverloaded = ((ini_get("mbstring.func_overload") & 2) != 0) && function_exists('mb_substr');
        $this->_pos = 0;
    }

    /**
     * Sets the endian-ness of the file.
     *
     * @param $endian string 'big' or 'little'
     */
    public function setEndian($endian) {
        $this->_endian = $endian;
    }

    /**
     * Reads a 32bit Integer from the Stream
     *
     * @return mixed The integer, corresponding to the next 32 bits from
     * 	the stream of false if there are not enough bytes or on error
     */
    public function readint32() {
        $bytes = $this->read(4);
        if (4 != $this->strlen($bytes)) {
            return false;
        }
        
        $endian_letter = (self::ENDIAN_BIG == $this->_endian) ? self::ENDIAN_LETTER_BIG : self::ENDIAN_LETTER_LITTLE;
        $int = unpack($endian_letter, $bytes);
        return reset($int);
    }

    /**
     * Reads an array of 32-bit Integers from the Stream
     *
     * @param integer count How many elements should be read
     * @return mixed Array of integers or false if there isn't
     * 	enough data or on error
     */
    public function readint32array($count) {
        $bytes = $this->read(4 * $count);
        if (4 * $count != $this->strlen($bytes)) {
            return false;
        }
        $endian_letter = (self::ENDIAN_BIG == $this->_endian) ? self::ENDIAN_LETTER_BIG : self::ENDIAN_LETTER_LITTLE;
        return unpack($endian_letter . $count, $bytes);
    }

    /**
     * @param string $string
     * @param int    $start
     * @param int    $length
     * @return string
     */
    public function substr($string, $start, $length) {
        if ($this->_isOverloaded) {
            return mb_substr($string, $start, $length, self::ENCODING);
        }
        
        return substr($string, $start, $length);
    }

    /**
     * @param string $string
     * @return int
     */
    public function strlen($string) {
        if ($this->_isOverloaded) {
            return mb_strlen($string, self::ENCODING);
        } 
        
        return strlen($string);
    }

    /**
     * @param string $string
     * @param int    $chunk_size
     * @return array
     */
    public function strSplit($string, $chunk_size) {
        if (!function_exists('str_split')) {
            $length = $this->strlen($string);
            $out = array();
            for ($i = 0; $i < $length; $i += $chunk_size) {
                $out[] = $this->substr($string, $i, $chunk_size);
            }
            return $out;
        } 
        return str_split($string, $chunk_size);
    }

    /**
     * @return int
     */
    public function pos() {
        return $this->_pos;
    }

    /**
     * @return true
     */
    public function isResource() {
        return true;
    }

    /**
     * @return true
     */
    public function close() {
        return true;
    }

}

/* EOF */