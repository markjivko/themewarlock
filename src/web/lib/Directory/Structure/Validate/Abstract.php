<?php

/**
 * Theme Warlock - Directory Structure Validate Abstract
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Abstract file and folder validation class
 */
abstract class Directory_Structure_Validate_Abstract {

    /**
     * Current working directory
     * 
     * @var string
     */
    protected $_currentDir;

    /**
     * Validator 
     * 
     * @param string $currentDir Current directory
     */
    public function __construct($currentDir) {
        // Set the current directory
        $this->_currentDir = rtrim($currentDir, '/\\');
    }

    /**
     * Validate size
     */
    protected function _validateSize($items, $min = null, $max = null) {
        // Invalid min
        if ($min < 0) {
            $min = null;
        }

        // Invalid max
        if ($max < 0) {
            $max = null;
        }

        // Valid values
        if ($min > $max && $max !== null) {
            throw new Exception('Invalid min=' . var_export($min, true) . ' and max=' . var_export($max, true) . ' values');
        }

        // Go through each file
        foreach ($items as $item) {
            // Get the filesize in kB
            $size = $this->_getSize($this->_currentDir . '\\' . $item);

            // Validate the minimum size
            if (null !== $min && $size < $min) {
                throw new Exception('Size of ' . $this->_currentDir . '\\' . $item . ' is ' . round($size, 2) . 'kB, expected at least ' . $min . 'kB');
            }

            // Validate the maximum size
            if (null !== $max && $size > $max) {
                throw new Exception('Size of ' . $this->_currentDir . '\\' . $item . ' is ' . round($size, 2) . 'kB, expected at most ' . $max . 'kB');
            }
        }
    }

    /**
     * Get file or folder size
     * 
     * @param string $fileOrFolder File or folder path
     * @return int Value in kB
     */
    abstract protected function _getSize($fileOrFolder);

    /**
     * Validate
     * 
     * @param array $item Item information
     * @throws Exception
     * @return array Validated names
     */
    public function run($item = null) {
        // Validate the structure
        if (!is_array($item)) {
            throw new Exception('Validator ' . get_class() . ' did not receive a proper item to inspect.');
        }

        // The name restriction is mandatory
        if (is_null($item['_name']) && is_null($item['_nameRegEx'])) {
            throw new Exception('You must provide either a name or a RegEx for the name of this ' . $item['_type']);
        }

        // Not found, presumably
        $found = array();

        // Search for a particular name (precedence over the regex search)
        if (null !== $item['_name']) {
            if ('file' == $item['_type']) {
                if (is_file($this->_currentDir . '\\' . $item['_name'])) {
                    $found[] = $item['_name'];
                }
            } else {
                if (is_dir($this->_currentDir . '\\' . $item['_name'])) {
                    $found[] = $item['_name'];
                }
            }

            // Could not find, and item was not optional
            if (!count($found) && $item['_countMin']) {
                throw new Exception('Could not find ' . $item['_type'] . ' "' . $this->_currentDir . '\\' . $item['_name'] . '"');
            }
        } else {
            // Go through the current directory
            foreach (glob($this->_currentDir . '\\*', 'folder' == $item['_type'] ? GLOB_ONLYDIR : 0) as $itemName) {
                // Only need the basename
                $itemName = basename($itemName);

                // Match with the regex
                if (preg_match('%' . $item['_nameRegEx'] . '%', $itemName)) {
                    if ('file' == $item['_type']) {
                        if (!is_dir($this->_currentDir . '\\' . $itemName)) {
                            $found[] = $itemName;
                        }
                    } else {
                        $found[] = $itemName;
                    }
                }
            }
        }

        // Min and max must be positive
        if ((null !== $item['_countMin'] && $item['_countMin'] < 0) || (null !== $item['_countMax'] && $item['_countMax'] < 0)) {
            throw new Exception('Neither minimum nor maximum can be lower than 0');
        }

        // Min must be lower than max
        if (null !== $item['_countMin'] && null !== $item['_countMax'] && $item['_countMin'] > $item['_countMax']) {
            throw new Exception('The maximum cannot be lower than the minimum');
        }

        // Min
        if (null !== $item['_countMin'] && $item['_countMin'] > count($found)) {
            throw new Exception(ucfirst($item['_type']) . ' with RegEx "' . $this->_currentDir . '\\' . $item['_nameRegEx'] . '" found less (' . count($found) . ') than ' . $item['_countMin'] . ' time' . ($item['_countMin'] == 1 ? '' : 's'));
        }

        // Max
        if (null !== $item['_countMax'] && $item['_countMax'] < count($found)) {
            throw new Exception(ucfirst($item['_type']) . ' with RegEx "' . $this->_currentDir . '\\' . $item['_nameRegEx'] . '" found more (' . count($found) . ') than ' . $item['_countMax'] . ' time' . ($item['_countMax'] == 1 ? '' : 's'));
        }

        // Not found, but expected contents
        if (!count($found) && 'folder' == $item['_type'] && count($item['_contents']) && $item['_countMin'] != 0) {
            throw new Exception('Folder named "' . $this->_currentDir . '\\' . (null !== $item['_name'] ? $item['_name'] : $item['_nameRegEx']) . '" could not be found, but expected contents');
        }

        // Validate size
        if (null !== $item['_sizeMin'] || null !== $item['_sizeMax']) {
            $this->_validateSize($found, $item['_sizeMin'], $item['_sizeMax']);
        }

        // Validate dimensions (for files)
        if (null !== $item['_width'] || null !== $item['_height'] || null !== $item['_imagetype'] || null !== $item['_minWidth'] || null !== $item['_minHeight']) {
            $this->_validateDimensionsAndType($found, $item['_width'], $item['_height'], $item['_imagetype'], $item['_minWidth'], $item['_minHeight']);
        }

        // All done
        return $found;
    }

}

/*EOF*/