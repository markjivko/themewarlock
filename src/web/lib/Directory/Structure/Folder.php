<?php

/**
 * Theme Warlock - Directory Structure Folder
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Defines a folder
 */
class Directory_Structure_Folder extends Directory_Structure_Abstract {

    /**
     * Directory structure item type
     * 
     * @var string
     */
    protected $_type = 'folder';

    /**
     * Folder elements
     * 
     * @var array
     */
    protected $_contents = array();

    /**
     * Open a new Directory_Structure_Folder instance
     * 
     * @return Directory_Structure_Folder
     */
    public static function open() {
        // Get an instance of self
        $instance = new self();

        // Get the method arguments
        $arguments = func_get_args();

        // Store the children
        if (count($arguments)) {
            foreach ($arguments as $arg) {
                // Force casting to string so the __toString method gets invoked
                if (is_a($arg, 'Directory_Structure_Abstract')) {
                    $instance->_contents[] = unserialize((string) $arg);
                }
            }
        }

        // Return self
        return $instance;
    }

}

/*EOF*/