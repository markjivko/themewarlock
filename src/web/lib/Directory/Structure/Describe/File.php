<?php

/**
 * Theme Warlock - Directory Structure Describe File
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * File description class
 */
class Directory_Structure_Describe_File extends Directory_Structure_Describe_Abstract {

    /**
     * Describe file
     * 
     * @param array $item Item information
     * @return string Description
     */
    public function run($item = null) {
        // Prepare the padding
        $padding = (strlen($this->_currentDir) ? '|' . str_repeat(' ', (substr_count($this->_currentDir, '\\') + 1) * 2) : '') . '|-- ';

        // Prepare the name
        $name = isset($item['_name']) && !empty($item['_name']) ? $item['_name'] : (isset($item['_nameRegEx']) && !empty($item['_nameRegEx']) ? $item['_nameRegEx'] : '?');

        // Prepare the delimiter
        $d = (isset($item['_nameRegEx']) && !empty($item['_nameRegEx']) ? '"' : '');

        // Get the width
        $width = isset($item['_width']) ? $item['_width'] : null;

        // Get the height
        $height = isset($item['_height']) ? $item['_height'] : null;

        // Get the type
        $type = isset($item['_imagetype']) ? $item['_imagetype'] : null;

        // Prepare the "other information" holder
        $other = '';

        // Other data?
        if (null !== $width || null !== $height || null !== $type) {
            $other = sprintf(
                '(%sx%s, %s)', $width ? $width : '?', $height ? $height : '?', $type ? $type : 'any type'
            );
        }

        // Return the result
        return $this->_getTagline($item) . $padding . $d . $name . $d . ' ' . $other . PHP_EOL;
    }

}

/*EOF*/