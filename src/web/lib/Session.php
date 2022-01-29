<?php
/**
 * Theme Warlock - Session
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Session {
    
    // Session name prefix
    const SESSION_NAME_PREFIX = 'ThemeWarlock';
    const SESSION_NAME_SEPARATOR = '_';
    
    // Parameters
    const PARAM_WEB_USER_MODEL         = 'web_user_model';
    const PARAM_WEB_PROJECT_EDIT_TOKEN = 'web_project_edit_token';
    
    // Get the roles
    const ROLE_ADMIN       = 'admin';
    const ROLE_MANAGER     = 'manager';
    const ROLE_COPYRIGHTER = 'copyrighter';
    const ROLE_DESIGNER    = 'designer';
    const ROLE_PARTNER     = 'partner';
    
    // Get the roles order
    public static $roles = array(
        self::ROLE_ADMIN,
        self::ROLE_MANAGER,
        self::ROLE_COPYRIGHTER,
        self::ROLE_DESIGNER,
        self::ROLE_PARTNER,
    );
    
    /**
     * Session instance
     * 
     * @var Session
     */
    protected static $_instances;
    
    /**
     * Command line interface mode
     * 
     * @var boolean
     */
    protected $_cliMode = false;
    
    /**
     * Store the allowed session keys
     * 
     * @var string[]
     */
    protected $_allowedSessionKeys = array();
    
    /**
     * Session handler
     * 
     * @return Session
     */
    protected function __construct($cliMode = false) {
        // Get a self-reflection
        $reflection = new ReflectionClass(__CLASS__);
        @ini_set('session.cookie_secure', 'Off');
        
        // Get the constants
        $this->_allowedSessionKeys = $reflection->getConstants();
        
        // Store the cli mode
        $this->_cliMode = (boolean) $cliMode;

        // Not available in CLI mode
        if ($this->_cliMode) {
            return;
        }
        
        // Name this session
        session_name(
            preg_replace(
                '%\W+%', 
                self::SESSION_NAME_SEPARATOR,
                self::SESSION_NAME_PREFIX . '_' . strtolower(Config::get()->myDomain . '_' . Config::get()->authorName)
            )
        );
        
        // Set the session save path
        if (!is_dir($sessionPath = ROOT . '/web/temp/session')) {
            Folder::create($sessionPath, 0777, true);
        }
        session_save_path($sessionPath);

        // Start the session
        session_start(array(
            'cookie_lifetime' => 30758400,
            'read_and_close'  => true,
        ));
    }
 
    /**
     * Session handler
     * 
     * @return Session
     */
    public static function getInstance() {
        // Prepare the instance key
        $instanceKey = php_sapi_name();
        
        // Get the instance
        if (!isset(self::$_instances[$instanceKey])) {
            self::$_instances[$instanceKey] = new self("cli" === $instanceKey);
        }
        
        // Return it
        return self::$_instances[$instanceKey];
    }
    
    /**
     * Validate a parameter name by value
     * 
     * @param string $paramName Session parameter name
     * @return mixed the constant name for <i>paramName</i> if it is declared, <b>false</b> otherwise.
     * </p>
     */
    protected function _validateParameter($paramName) {
        // Found it
        return array_search($paramName, $this->_allowedSessionKeys);
    }
    
    /**
     * Get a session parameter by name
     * 
     * @param string $paramName Session parameter name
     * @return mixed Paramater value or null on error
     */
    public function get($paramName) {
        if (false === $this->_validateParameter($paramName)) {
            return null;
        }

        // Return the parameter
        return isset($_SESSION) && isset($_SESSION[$paramName]) ? $_SESSION[$paramName] : null;
    }
    
    /**
     * Set a session parameter
     * 
     * @param string $paramName  Parameter name
     * @param string $paramValue Parameter value - not null
     * @return boolean True on success, false on failure
     */
    public function set($paramName, $paramValue) {
        // Open the session
        $this->_open();

        // Expect an error
        $result = false;
        
        do {
            // Parameter must be settable
            if (false === $this->_validateParameter($paramName)) {
                break;
            }

            // Value must not be null
            if (null === $paramValue) {
                break;
            }

            // Set the value
            if (isset($_SESSION)) {
                $_SESSION[$paramName] = $paramValue;
            }
            
            // All went well
            $result = true;
        } while (false);
        
        // Close the session
        $this->_close();
        
        // All done
        return $result;
    }
    
    /**
     * Delete a parameter by name
     * 
     * @param string $paramName Parameter name
     * @return boolean True on success, false on failure
     */
    public function del($paramName) {
        $this->_open();
        
        // Expect an error
        $result = false;
        do {
            // Parameter must be settable
            if (false === $this->_validateParameter($paramName)) {
                break;
            }

            // Remove the value
            if (isset($_SESSION)) {
                unset($_SESSION[$paramName]);
            }
            
            // All went well
            $result = true;
        } while (false);
        
        // Close the session
        $this->_close();
        
        // All done
        return $result;
    }
    
    /**
     * Close the session
     */
    protected function _close() {
        // Not available in CLI mode
        if ($this->_cliMode) {
            return;
        }
        
        session_write_close();
    }

    /**
     * (Re-)Open the session for writing
     */
    protected function _open() {
        // Not available in CLI mode
        if ($this->_cliMode) {
            return;
        }

        session_start();
    }
    
    /**
     * Reset the session
     * 
     * @return boolean True on success, false on failure
     */
    public function reset() {
        // Not available in CLI mode
        if ($this->_cliMode) {
            return false;
        }
        
        // Open the session
        $this->_open();
        
        // Destroy it
        return session_destroy();
    }
}

/*EOF*/