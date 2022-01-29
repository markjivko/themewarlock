<?php
/**
 * Theme Warlock - Controller_Ajax_Quote
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax_Quote extends Controller_Ajax {
    
    /**
     * No auth
     * 
     * @var string
     */
    public static $auth = Auth::TYPE_NONE;
    
    /**
     * Taskbar publishing helper
     * 
     * @allowed all
     */
    public function get() {
        return Whisper_Inspiration::getQuote();
    }
}

/* EOF */