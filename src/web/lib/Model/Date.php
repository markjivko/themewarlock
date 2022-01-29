<?php
/**
 * Theme Warlock - Model_Date
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Date {

    /**
     * Spell the time
     * 
     * @param int $numberOfSeconds Positive number
     * @return string
     */
    public static function spellTime($numberOfSeconds = 0) {
        // Get the sign
        $negative = $numberOfSeconds >= 0 ? false : true;
        
        // Get the absolute value
        $numberOfSeconds = abs($numberOfSeconds);
        
        // Split the data
        $items = array_map('intval', explode(':', gmdate("H:i:s", $numberOfSeconds)));
        
        // Prepare the placeholders
        $placeHolders = array('hour', 'minute', 'second');
        
        // Prepare the result
        $texts = array();
        
        // Go through the data
        foreach ($placeHolders as $key => $placeHolder) {
            // Get the item
            $item = $items[$key];
            
            // Valid value
            if ($item > 0) {
                $texts[] = $item . ' ' . $placeHolder . ($item == 1 ? '' : 's');
            }
        }
        
        // Prepare the last text
        $lastText = null;
        if (count($texts) >= 2) {
            // Remove the last element from the list
            $lastText = array_pop($texts);
        }
        
        // Prepare the result
        $result = implode(', ', $texts);
        
        // Need to add the final element
        if (null != $lastText) {
            $result .= ' and ' . $lastText;
        }
        
        // Invalid result
        if (!strlen($result)) {
            $result = '0 seconds';
        }
        
        // All done
        return ($negative ? '-' : '') . $result;
    }
    
}

/*EOF*/
