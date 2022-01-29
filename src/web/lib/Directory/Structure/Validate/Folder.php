<?php

/**
 * Theme Warlock - Directory Structure Validate Folder
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Folder validation class
 */
class Directory_Structure_Validate_Folder extends Directory_Structure_Validate_Abstract {

    /**
     * Get folder size
     * 
     * @param string $folder folder path
     * @return int Value in kB
     */
    protected function _getSize($folder) {
        // Return the size
        return Folder::size($folder) / 1024;
    }

}

/*EOF*/