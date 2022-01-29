<?php
/**
 * Theme Warlock - Controller_MissingError
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_ErrorMissing extends Controller {
    
    public static $auth = Auth::TYPE_NONE;
    
    /**
     * Missing method
     * 
     * @param string $methodName     Method name
     * @param string $controllerName Controller name
     * @return null
     * @allowed all
     */
    public static function method($methodName, $controllerName) {
        header('HTTP/1.0 403 Forbidden');
        
        // Get the view
        $view = new View_Error();
        $view->addCss('login');
        
        // Set the title
        $view->setTitle('Error - Missing Method');
        
        // Set the placeholders
        $view->setPlaceholder(View_Error::PH_CONTROLLER, $controllerName);
        $view->setPlaceholder(View_Error::PH_METHOD, $methodName);
        
        // Output the part
        echo $view->getPart(View_Error::PART_MISSING);
        
        // Display it
        $view->display();
    }
 
    /**
     * Missing controller
     * 
     * @param string $controllerName Controller name
     * @return null
     * @allowed all
     */
    public static function controller($controllerName) {
        header('HTTP/1.0 403 Forbidden');
        
        // Get the view
        $view = new View_Error();
        $view->addCss('login');
        
        // Set the title
        $view->setTitle('Error - Missing Controller');
        
        // Set the placeholders
        $view->setPlaceholder(View_Error::PH_CONTROLLER, $controllerName);
        
        // Output the part
        echo $view->getPart(View_Error::PART_MISSING);
        
        // Display it
        $view->display();
    }
}

/*EOF*/