<?php
/**
 * Theme Warlock - Controller_ErrorLogic
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_ErrorLogic extends Controller {
    
    // User role does not allow action
    const EXC_INSUFFICIENT_PRIVILEGES = 'InsufficientPrivileges';
    
    /**
     * Exception handler
     * 
     * @param Exception  $exc             Exception object
     * @param Controller $controller      Controller instance
     * @param string     $methodName      Method name
     * @param array      $methodArguments Method arguments
     */
    public function exception(Exception $exc, $controller, $methodName, $methodArguments) {
        header('HTTP/1.0 403 Forbidden');
        
        // Get the view
        $view = new View_Error();
        $view->addCss('login');
        
        // Set the title
        $view->setTitle('Exception');
        
        /*@var $userModel Model_User*/
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
        // Store this model
        $view->setPlaceholder(View_Admin::PH_CURRENT_USER, $userModel);
        
        // Set the placeholders
        $view->setPlaceholder(View_Error::PH_EXC, $exc);
        $view->setPlaceholder(View_Error::PH_CONTROLLER, $controller);
        $view->setPlaceholder(View_Error::PH_METHOD, $methodName);
        $view->setPlaceholder(View_Error::PH_ARGUMENTS, $methodArguments);
        
        // Output the part
        echo $view->getPart(View_Error::PART_EXCEPTION);
        
        // Display it
        $view->display();
    }
}

/*EOF*/