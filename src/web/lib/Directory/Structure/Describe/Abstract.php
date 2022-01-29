<?php

/**
 * Theme Warlock - Directory Structure Describe Abstract
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Abstract file and folder description class
 */
abstract class Directory_Structure_Describe_Abstract {

    /**
     * Current working directory
     * 
     * @var string
     */
    protected $_currentDir;

    /**
     * Descriptor
     * 
     * @param string $currentDir Current directory
     */
    public function __construct($currentDir) {
        // Set the current directory
        $this->_currentDir = rtrim($currentDir, '/\\');
    }

    /**
     * Get a list item's tagline
     * 
     * @param array $item Item
     * @return string
     */
    protected function _getTagline($item) {
        // Prepare the size
        $size = (isset($item['_sizeMin']) && null !== $item['_sizeMin'] ? $item['_sizeMin'] : '?')
            . ':'
            . (isset($item['_sizeMax']) && null !== $item['_sizeMax'] ? $item['_sizeMax'] : '?')
            . 'kB';

        // Clearer Output
        $size = preg_match('%^[0\?]{1}\:[0\?]{1}kB$%', $size) ? 'any size' : $size;

        // n: means n+
        $size = preg_replace('%^(\d+)\:\?.*%', '$1kB+', $size);

        // Prepare the count
        $count = (isset($item['_countMin']) && null !== $item['_countMin'] ? $item['_countMin'] : '?')
            . ':'
            . (isset($item['_countMax']) && null !== $item['_countMax'] ? $item['_countMax'] : '?')
            . 'x';

        // A precise number of times
        if (isset($item['_countMin']) && isset($item['_countMax']) && $item['_countMin'] == $item['_countMax']) {
            $count = $item['_countMin'] == null ? 'optional' : $item['_countMin'] . 'x';
        }

        // Optional count
        $count = preg_match('%^[0\?]{1}\:[0\?]{1}x%', $count) ? 'optional' : $count;

        // Mandatory (no need to say so)
        $count = '1:?x' == $count ? 'required' : $count;

        // n: means n+
        $count = preg_replace('%^(\d+)\:\?.*%', '$1+', $count);

        // Return the tagline
        return '   ' . str_pad($count . ', ' . $size . (isset($item['_nameRegEx']) && !empty($item['_nameRegEx']) ? ', RegEx' : ''), 30);
    }

    /**
     * Describe
     * 
     * @param array $item Item information
     * @return string Description
     */
    abstract public function run($item = null);
}

/*EOF*/