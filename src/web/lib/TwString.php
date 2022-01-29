<?php

/**
 * Theme Warlock - TwString
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class TwString {

    /**
     * Multi-byte Upper Case First character, ignoring the quotes
     * 
     * @param string $string   String
     * @param string $encoding Encoding; default UTF-8
     * @return string
     */
    public static function mbUcfirst($string, $encoding = 'UTF-8') {
        // Sprint the string into words
        $words = preg_split('% +%', $string);
        
        // Uppercase the first word
        foreach (array_keys($words) as $key) {
            // Not just a simple quote
            if (!preg_match('%^[\'\"]$%', trim($words[$key]))) {
                // Capitalize
                $words[$key] = preg_replace_callback('%(^[\'\"]?)(.*?$)%u', function($item) use ($encoding) {
                    return $item[1] . mb_convert_case($item[2], MB_CASE_TITLE, $encoding);
                }, $words[$key]);
                
                // All done
                break;
            }
        }
        
        // All done
        return implode(' ', $words);
    }
}

/*EOF*/