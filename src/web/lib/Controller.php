<?php
/**
 * Theme Warlock - Controller
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller {
    
    /**
     * Authentication type
     * 
     * @see Auth::TYPE_* 
     * @var string
     */
    public static $auth = Auth::TYPE_WEB;

    /**
     * Controller
     * 
     * @return Controller
     */
    public function __construct($internal = false) {
        // Internal command
        if ($internal) {
            return;
        }
        
        // Error controller
        if (in_array(get_called_class(), array(Router::ERROR_LOGIC_CONTROLLER, Router::ERROR_MISSING_CONTROLLER))) {
            return;
        }
        
        // Authenticate
        if (!Auth::check(static::$auth)) {
            self::Login();
        } else {
            // Already authenticated
            if (get_called_class() == Router::LOGIN_CONTROLLER) {
                Controller::Redirect('/');
            }
        }
    }
    
    /**
     * Perform a redirect
     * 
     * @param stirng $url URL
     * @return null
     */
    public static function Redirect($url = null) {
        // Default to the local page
        if (null === $url) {
            $url = $_SERVER['REQUEST_URI'];
        }
        
        // Send a location header
        header('Location: ' . $url);
        
        // Stop here
        exit();
    }
    
    /**
     * Log in time-out
     * 
     * @return null
     */
    public static function Login() {
        do {
            // Not authorized
            header('HTTP/1.0 440 Login Time-out');
            
            // Ajax - no login, just an error
            if (get_called_class() == Router::AJAX_CONTROLLER) {
                Controller_Ajax::errorNoAuth();
            }
            
            // Get the login controller
            $logInController = new Controller_Login(true);

            // Show the view
            if (true == $logInController->index()) {
                // All went well, carry on
                Controller::Redirect(get_called_class() == Router::LOGIN_CONTROLLER ? '/' : null);
            }
        } while (false);
        
        // Stop here
        exit();
    }
}

/*EOF*/