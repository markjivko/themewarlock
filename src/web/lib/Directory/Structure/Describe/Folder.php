<?php

/**
 * Theme Warlock - Directory Structure Describe Folder
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Folder description class
 */
class Directory_Structure_Describe_Folder extends Directory_Structure_Describe_Abstract {

    /**
     * Describe folder
     * 
     * @param array $item Item information
     * @return string Description
     */
    public function run($item = null) {
        // Prepare the padding
        $padding = $this->_currentDir;

        // Prepare the name
        $name = isset($item['_name']) && !empty($item['_name']) ? $item['_name'] : (isset($item['_nameRegEx']) && !empty($item['_nameRegEx']) ? $item['_nameRegEx'] : '?');

        // Prepare the result
        $children = '';

        // Get the contents
        if (isset($item['_contents']) && count($item['_contents'])) {
            foreach ($item['_contents'] as $innerItem) {
                if ('file' == $innerItem['_type']) {
                    $descriptor = new Directory_Structure_Describe_File($this->_currentDir . '\\' . $name);
                } else {
                    $descriptor = new Directory_Structure_Describe_Folder($this->_currentDir . '\\' . $name);
                }

                // Append to the result
                $children .= $descriptor->run($innerItem);
            }
        }

        // Return the result
        return $this->_getTagline($item) . $padding . '|-- ' . $name . PHP_EOL . $children;
    }

}

/*EOF*/