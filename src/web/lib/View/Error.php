<?php
/**
 * Theme Warlock - View_Error
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class View_Error extends WebView {
    // Placeholders
    const PH_EXC        = 'exc';
    const PH_CONTROLLER = 'controller';
    const PH_METHOD     = 'method';
    const PH_ARGUMENTS  = 'arguments';
    
    // View parts
    const PART_EXCEPTION = 'error/exception';
    const PART_MISSING = 'error/missing';
}

/* EOF */