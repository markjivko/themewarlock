<?php
/**
 * Theme Warlock - WebMenu
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WebMenu {
    
    // Controller descriptors
    const CONTROLLER_NAME    = 'name';
    const CONTROLLER_CLASS   = 'class';
    const CONTROLLER_ITEMS   = 'items';
    const CONTROLLER_PATH    = 'path';
    const CONTROLLER_CURRENT = 'current';
    
    // Item descriptors
    const ITEM_HIDDEN  = 'hidden';
    const ITEM_NAME    = 'name';
    const ITEM_METHOD  = 'method';
    const ITEM_PATH    = 'path';
    const ITEM_CURRENT = 'current';
    
    /**
     * Web Menu
     * 
     * @var WebMenu 
     */
    protected static $_instance;

    /**
     * Current controller name
     * 
     * @var string
     */
    protected $_currentController;
    
    /**
     * Current method name
     * 
     * @var string
     */
    protected $_currentMethod;
    
    /**
     * Menu array
     * 
     * @var array
     */
    protected $_menu = array();
    
    /**
     * Web Menu
     * 
     * @param string $controllerName Controller name
     * @param string $methodName     Method name
     */
    protected function __construct($controllerName, $methodName) {
        // Store these values
        $this->_currentController = $controllerName;
        $this->_currentMethod = $methodName;
        
        // Get the session
        $session = Session::getInstance();
        
        // Get the user model
        if (null == $userModel = $session->get(Session::PARAM_WEB_USER_MODEL)) {
            return;
        }
        
        // Get the user role
        $userRoleId = array_search($userModel->role, Session::$roles);
        
        // Get the controllers list
        $controllersList = array_map(function($item) {return 'Controller_' . basename($item, '.php');}, glob(ROOT . '/web/lib/Controller/*.php'));
        
        // Clean-up the list
        $controllersList = array_filter($controllersList, function($item) {
            return !in_array($item, array(
                Router::AJAX_CONTROLLER,
                Router::ERROR_LOGIC_CONTROLLER,
                Router::ERROR_MISSING_CONTROLLER,
                Router::LOGIN_CONTROLLER,
                'Controller_Logout',
                'Controller_Api',
                'Controller_File',
            ));
        });
        
        // Go through the controllers
        foreach ($controllersList as $controller) {
            // Get the reflection
            $controllerReflection = new ReflectionClass($controller);
            
            // Not a web controller
            if (Auth::TYPE_WEB != $controllerReflection->getStaticPropertyValue('auth')) {
                continue;
            }
            
            // Get the public methods
            $methods = $controllerReflection->getMethods(ReflectionMethod::IS_PUBLIC);
            
            // Remove the static and magic methods
            $methods = array_filter($methods, function($item) {
                return !$item->isStatic() && !preg_match('%^_%', $item->name);
            });
            
            // Go through the methods
            $methods = array_filter($methods, function($item) use($userRoleId) {
                // Prepare the required role
                $requiredRoles = array();

                // Get the comment
                if (false !== $methodComment = $item->getDocComment()) {
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
                            return false;
                        }
                    }
                }
                
                // Get the user role
                $userRole = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL)->role;
                
                // Validate the role
                if (!in_array($userRole, $requiredRoles)) {
                    return false;
                }
                
                // All done
                return true;
            });

            // Set the controller
            $controllerData = array();
            
            // Get the controller path
            $controllerPath = strtolower(preg_replace('%^Controller_%', '', $controller));
            
            // Add the items
            foreach ($methods as $methodReflection) {
                // Prepare the item name
                $itemName = ucfirst($methodReflection->name);
                
                // Get the comment
                if (false !== $methodComment = $methodReflection->getDocComment()) {
                    if (preg_match('%@name\s+(.*?)\n%ms', $methodComment, $matches)) {
                        $itemName = trim($matches[1]);
                    }
                }
                
                $controllerData[] = array(
                    self::ITEM_HIDDEN  => preg_match('%@hidden\b%ms', $methodComment),
                    self::ITEM_NAME    => $itemName,
                    self::ITEM_METHOD  => $methodReflection->name,
                    self::ITEM_PATH    => $controllerPath . (Router::DEFAULT_METHOD == $methodReflection->name ? '' : '/' . $methodReflection->name),
                    self::ITEM_CURRENT => strtolower($methodReflection->name) == strtolower($this->_currentMethod) && strtolower($controller) == strtolower($this->_currentController)
                );
            }
            
            // Prepare the item name
            $controllerDescriptiveName = preg_replace('%^Controller_%', '', $controller);

            // Get the comment
            if (false !== $controllerComment = $controllerReflection->getDocComment()) {
                if (preg_match('%@name\s+(.*?)\n%ms', $controllerComment, $matches)) {
                    $controllerDescriptiveName = trim($matches[1]);
                }
            }
            
            // Store the controller
            if (count($controllerData)) {
                $this->_menu[] = array(
                    self::CONTROLLER_NAME    => $controllerDescriptiveName,
                    self::CONTROLLER_CLASS   => $controller,
                    self::CONTROLLER_ITEMS   => $controllerData,
                    self::CONTROLLER_PATH    => $controllerPath,
                    self::CONTROLLER_CURRENT => strtolower($controller) == strtolower($this->_currentController),
                );
            }
        }
    }

    /**
     * Web Menu
     * 
     * @param string $controllerName Controller name
     * @param string $methodName     Method name
     * @return WebMenu
     */
    public static function getInstance($controllerName = null, $methodName = null) {
        if (!isset(self::$_instance)) {
            self::$_instance = new self($controllerName, $methodName);
        }
        return self::$_instance;
    }
    
    /**
     * Get the menu
     * 
     * @return array
     */
    public function getMenu() {
        return $this->_menu;
    }
}

/* EOF */