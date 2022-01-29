<?php
/**
 * Theme Warlock - LogIn
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Login extends Controller {
    
    /**
     * Log In
     */
    public function index() {
        // Get the view
        $view = new View_Login();
        
        // Set the title
        $view->setTitle('Log In');

        // Add the style
        $view->addCss('login');
        $view->addJs('login');
        
        // E-mail provided
        if (null !== $email = Input::getInstance()->postRequest(View_Login::PH_USERNAME)) {
            // Get the user model
            $modelUser = new Model_User($email);
            
            // Get the brute model
            $bruteModel = new Model_Brute();
            
            do {
                // Been here before
                if ($bruteModel->exists()) {
                    // 3+ attempts
                    if ($bruteModel->attempts >= 3) {
                        // Less than an hour
                        if (time() <= $bruteModel->time + 3600) {
                            // Inform the user
                            $view->setPlaceholder(View_Login::PH_NOTIFICATION, 'Too many attempts! Try again later.');
                            Notifier::getInstance()->failure(
                                'Brute force attack', 
                                array(
                                    'IP'       => $_SERVER['REMOTE_ADDR'],
                                    'username' => $email,
                                    'password' => Input::getInstance()->postRequest(View_Login::PH_PASSWORD),
                                )
                            );
                            TaskbarNotifier::sendMessage(
                                'Brute force attack', 
                                'Failed login attempt for "' . $email . '"', 
                                TaskbarNotifier::TYPE_ERROR
                            );
                            break;
                        } else {
                            // Ok, give it another try
                            $bruteModel->delete();
                        }
                    }
                }

                // Valid password
                if ($modelUser->verify(Input::getInstance()->postRequest(View_Login::PH_PASSWORD))) {
                    // Get the session model
                    $sessionModel = new Model_Session();

                    // Save the session
                    $sessionModel->setUserModel($modelUser);

                    // Successful login
                    Notifier::getInstance()->success(
                        'Logged in', 
                        array(
                            'IP'       => $_SERVER['REMOTE_ADDR'],
                            'username' => $email,
                        )
                    );
                    
                    TaskbarNotifier::sendMessage(
                        'Logged in', 
                        ucfirst($modelUser->name) . ' has just signed in!'
                    );
                    
                    // All done
                    return true;
                } else {
                    // Inform the user
                    $view->setPlaceholder(View_Login::PH_NOTIFICATION, 'Invalid username or password.');
                    
                    // Increment the model
                    $bruteModel->exists() ? $bruteModel->increment() : $bruteModel->create();
                }
            } while (false);
        }
        
        // Set other placeholders
        $view->setPlaceholder(View_Login::PH_USERNAME, Input::getInstance()->postRequest(View_Login::PH_USERNAME));
        $view->setPlaceholder(View_Login::PH_PASSWORD, Input::getInstance()->postRequest(View_Login::PH_PASSWORD));
        
        // Output the part
        echo $view->getPart(View_Login::PART_LOGIN);
        
        // Display it
        $view->display();
        
        // Stay on the login page for more
        return false;
    }
}

/*EOF*/