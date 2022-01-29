<?php
/**
 * Theme Warlock - Controller_Users
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 * @name       Users
 */

class Controller_Users extends Controller {

    /**
     * Users management
     * 
     * @name Management
     * @allowed admin
     */
    public function index() {
        // Get the view
        $view = new View_Users();

        // Add the JavaScript
        $view->addJs('users/index');
        
        // Get the users from the database
        $usersModel = new Model_Users();
        
        // Get the users
        $view->setPlaceholder(View_Users::PH_USERS, $usersModel->getAll());
        
        // Output the part
        echo $view->getPart(View_Users::PART_USERS_INDEX);
        
        // Display it
        $view->display();
    }
    
    /**
     * View the website activity
     * 
     * @name Logs
     * @allowed admin
     */
    public function logs() {
        // Get a view
        $view = new View_Users();
        
        // Set the JS
        $view->addJs('users/logs');
        $view->addCss('users/logs');

        // Prepare the logged users
        $loggedUsers = array_map(
            function($item){
                // Get the user ID
                $userId = intval(preg_replace('%^log_user_%', '', basename($item, '.txt')));
                
                // Prepare the user info
                $userInfo = null;
                if (0 != $userId) {
                    // Get the data
                    $userInfo = new Model_User($userId);
                    
                    // User not found
                    if (!$userInfo->exists()) {
                        $userInfo = null;
                    }
                }
                
                // Store the data
                return array($userId, $userInfo);
            }, 
            glob(ROOT . '/web/log/log_user_*.txt')
        );
        
        // Get the logged entries
        $view->setPlaceholder(View_Users::PH_USERS, $loggedUsers);
        
        // Output the part
        echo $view->getPart(View_Users::PART_USERS_LOGS);
        
        // Display it
        $view->display();
    }

}

/* EOF */