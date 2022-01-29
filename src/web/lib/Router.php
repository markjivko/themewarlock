<?php
/**
 * Theme Warlock - Router
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Router {
    /**
     * Request parts
     * 
     * @var string[]
     */
    protected static $_requestParts = array();
    
    /**
     * Controller instance
     * 
     * @var Controller
     */
    protected static $_controller;
    
    /**
     * Start time
     * 
     * @var int
     */
    public static $startTime;
    
    // Error controller
    const ERROR_MISSING_CONTROLLER        = 'Controller_ErrorMissing';
    const ERROR_MISSING_METHOD_CONTROLLER = 'controller';
    const ERROR_MISSING_METHOD_METHOD     = 'method';
    const ERROR_LOGIC_CONTROLLER          = 'Controller_ErrorLogic';
    const ERROR_LOGIC_METHOD              = 'exception';
    const AJAX_CONTROLLER                 = 'Controller_Ajax';
    const LOGIN_CONTROLLER                = 'Controller_Login';
    const DEFAULT_CONTROLLER              = 'admin';
    const DEFAULT_METHOD                  = 'index';
    
    /**
     * Run the router
     */
    public static function run() {
        // Set the start time
        self::$startTime = microtime(true);

        // Get the request parts
        self::$_requestParts = array_values(array_filter(array_map('trim', explode('/', preg_replace('%\?\w+.*$%', '', $_SERVER['REQUEST_URI'])))));
        
        // No value set
        if (!count(self::$_requestParts)) {
            self::$_requestParts[] = self::DEFAULT_CONTROLLER;
        }
        
        // Get the controller class name
        $controllerName = 'Controller_' . ucfirst(strtolower(self::$_requestParts[0]));
        
        // AJAX controller
        if ($controllerName == self::AJAX_CONTROLLER) {
            // Append the method
            $controllerName .= '_' . implode('', array_map(function($item){
                return trim(ucfirst(strtolower($item)));
            }, explode('_', self::$_requestParts[1])));
            
            // Remove the first part
            array_shift(self::$_requestParts);
        }
        
        // Get the method
        $methodName = isset(self::$_requestParts[1]) ? strtolower(self::$_requestParts[1]) : self::DEFAULT_METHOD;
        
        // Remove the suffix
        $methodName = preg_replace('%\.\w+$%', '', $methodName);
        
        // Remove special characters
        $methodName = lcfirst(implode('', array_map('ucfirst', preg_split('%\W+%', $methodName))));

        // Get the arguments
        $methodArguments = array_map('urldecode', array_slice(self::$_requestParts, 2));
        
        // Log this event
        Log::controller($controllerName, $methodName, $methodArguments);
        
        // Verify the controller exists
        do {
            // Class not defined
            if (!class_exists($controllerName)) {
                // Get the new method arguments
                $methodArguments = array($controllerName);

                // Get the new method name
                $methodName = self::ERROR_MISSING_METHOD_CONTROLLER;

                // Get the new controller name
                $controllerName = self::ERROR_MISSING_CONTROLLER;
                
                // Stop here
                break;
            }

            // Get a class reflection
            $reflection = new ReflectionClass($controllerName);
            
            try {
                // Check the method exists
                $reflection->getMethod($methodName);
            } catch (Exception $ex) {
                // Get the new method arguments
                $methodArguments = array($methodName, $controllerName);

                // Get the new method name
                $methodName = self::ERROR_MISSING_METHOD_METHOD;

                // Get the new controller name
                $controllerName = self::ERROR_MISSING_CONTROLLER;

                // Stop here
                break;
            }
        } while (false);
            
        try {
            // Get the controller instance
            self::$_controller = new $controllerName();

            // Web authentication
            if ($controllerName::$auth == Auth::TYPE_WEB) {
                // Get a class reflection
                $metodReflection = new ReflectionMethod(self::$_controller, $methodName);

                // Prepare the required roles
                $requiredRoles = array();

                // Get the comment
                if (false !== $methodComment = $metodReflection->getDocComment()) {
                    if (preg_match('%@allowed\s+(.*?)\n%ms', $methodComment, $matches)) {
                        // Get the roles
                        $roles = array_values(array_filter(array_map('trim', explode(',', $matches[1]))));
                        
                        // Go through the roles
                        foreach ($roles as $role) {
                            // Set the roles one by one
                            if (in_array($role, Session::$roles)) {
                                $requiredRoles[] = $role;
                            } else {
                                if ('all' == $role) {
                                    // All are allowed
                                    $requiredRoles = Session::$roles;
                                    
                                    // Stop here
                                    break;
                                }
                            }
                        }
                    }
                    
                    // Config-dependent method
                    if (preg_match('%config\s+(.*?)\n%ms', $methodComment, $matches)) {
                        // Get the config
                        $configData = Config::get(false);
                        
                        // Get the config key
                        $configKey = trim($matches[1]);

                        // Disabled by configuration
                        if (isset($configData) && isset($configData[$configKey]) && true !== $configData[$configKey]) {
                            throw new Exception(Controller_ErrorLogic::EXC_INSUFFICIENT_PRIVILEGES);
                        }
                    }
                }
                
                // Get the user model
                $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
                // Get the user role
                $userRole = (null != $userModel && property_exists($userModel, 'role')) ? $userModel->role : null;
                
                // Set the menu
                $menu = WebMenu::getInstance($controllerName, $methodName)->getMenu();

                // Validate the role
                if (!in_array($userRole, $requiredRoles)) {
                    // Redirect to the first path
                    if (count($menu) && get_parent_class(self::$_controller) != Router::AJAX_CONTROLLER) {
                        Controller::Redirect($menu[0]['items'][0]['path']);
                    } else {
                        throw new Exception(Controller_ErrorLogic::EXC_INSUFFICIENT_PRIVILEGES);
                    }
                }
            }
            
            // Ajax start
            if (get_parent_class(self::$_controller) == Router::AJAX_CONTROLLER) {
                // Log the event start
                Log::check(Log::LEVEL_DEBUG) && Log::ajax(get_class(self::$_controller), $methodName, $methodArguments);
                
                // Set the header
                header('content-type:text/plain');
                
                // Start the output buffer
                ob_start();
            }

            // Run the method
            $result = call_user_func_array(array(self::$_controller, $methodName), $methodArguments);
            
            // Ajax end
            if (get_parent_class(self::$_controller) == Router::AJAX_CONTROLLER) {
                // Prepare the result
                $jsonResult = array(
                    Controller_Ajax::PIECE_CONTENT => ob_get_clean(),
                    Controller_Ajax::PIECE_RESULT  => $result,
                    Controller_Ajax::PIECE_STATUS  => Controller_Ajax::STATUS_SUCCESS,
                );
                
                // Log the event
                Log::check(Log::LEVEL_DEBUG) && Log::ajax(get_class(self::$_controller), $methodName, $methodArguments, $jsonResult);
                
                // Output it
                echo json_encode($jsonResult);
            }
        } catch (Exception $exc) {
            // Ajax end
            if (preg_match('%^' . preg_quote(Router::AJAX_CONTROLLER) . '_%', $controllerName)) {
                // Method failure
                header('HTTP/1.0 420 Method Failure');
                
                // Prepare the result
                $jsonResult = array(
                    Controller_Ajax::PIECE_CONTENT => ob_get_clean(),
                    Controller_Ajax::PIECE_RESULT  => $exc->getMessage(),
                    Controller_Ajax::PIECE_STATUS  => Controller_Ajax::STATUS_FAILURE,
                );
                
                // Log the event
                if (!AppMode::equals(AppMode::PRODUCTION)) {
                    Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                    Log::check(Log::LEVEL_DEBUG) && Log::debug(array(
                        get_class(self::$_controller), 
                        $methodName, 
                        $methodArguments, 
                        $jsonResult
                    ));
                } else {
                    Log::check(Log::LEVEL_DEBUG) && Log::ajax(get_class(self::$_controller), $methodName, $methodArguments, $jsonResult);
                }
                
                // Output it
                echo json_encode($jsonResult);
                
                // Stop here
                return;
            }
            
            // Get the new controller
            $controllerName = self::ERROR_LOGIC_CONTROLLER;
            
            // Run the method
            call_user_func_array(array(new $controllerName(), self::ERROR_LOGIC_METHOD), array($exc, self::$_controller, $methodName, $methodArguments));
        }
    }
}

/*EOF*/