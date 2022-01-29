<?php
/**
 * Theme Warlock - Controller_Logout
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Logout extends Controller {
    
    /**
     * Log out functionality
     * 
     * @allowed all
     */
    public function index() {
        // Get the session model
        $sessionModel = new Model_Session();
        
        // Successful logout
        if ($sessionModel->getUserModel() != null) {
            Notifier::getInstance()->success(
                'Logged out', 
                array(
                    'IP'       => $_SERVER['REMOTE_ADDR'],
                    'username' => $sessionModel->getUserModel()->email,
                )
            );
            
            // Log this on-screen
            TaskbarNotifier::sendMessage(
                'Logged out', 
                ucfirst($sessionModel->getUserModel()->name) . ' has just signed out!'
            );
        }
        
        // Destroy the session
        $sessionModel->destroy();
        
        // Redirect to the index
        Controller::Redirect('/');
    }
}

/*EOF*/