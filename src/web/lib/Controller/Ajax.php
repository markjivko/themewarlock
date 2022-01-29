<?php
/**
 * Theme Warlock - Controller_Ajax
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax extends Controller {
    // Piecers
    const PIECE_CONTENT = 'content';
    const PIECE_RESULT  = 'result';
    const PIECE_STATUS  = 'status';
    
    // Status types
    const STATUS_SUCCESS = 'success';
    const STATUS_FAILURE = 'failure';
    
    // Error codes
    const ERROR_NO_AUTH = 'noAuth';
    
    /**
     * Not allowed method
     * 
     * @allowed all
     * @return null
     */
    public static function errorNoAuth() {
        // Set the header
        header('Content-type:text/plain');
        
        // Output the error for no authentication
        echo json_encode(array(
            self::PIECE_CONTENT => self::ERROR_NO_AUTH,
            self::PIECE_RESULT  => null,
            self::PIECE_STATUS  => self::STATUS_FAILURE,
        ));
        
        // Stop here
        exit();
    }
}

/*EOF*/