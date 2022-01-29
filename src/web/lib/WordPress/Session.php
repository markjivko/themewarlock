<?php
/**
 * Theme Warlock - WordPress_Session
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Session {
    /**
     * Default session name
     */
    const DEFAULT_SESSION_NAME = "PHPSESSID";
    
    /**
     * Default user ID
     */
    const DEFAULT_USER_ID = 0;
    
    /**
     * Default project ID
     */
    const DEFAULT_PROJECT_ID = 1;
    
    /**
     * Session format user prefix
     */
    const USER_PREFIX = 'u';
    
    /**
     * Session separator
     */
    const SEPARATOR = ':';
    
    /**
     * WordPress_Session instance
     * 
     * @var WordPress_Session
     */
    protected static $_instance;
    
    /**
     * PHP WordPress_Session name
     * 
     * @var string
     */
    protected $_sessionInfo = self::DEFAULT_SESSION_NAME;
    
    /**
     * User ID
     * 
     * @var int
     */
    protected $_sessionUserId = self::DEFAULT_USER_ID;
    
    /**
     * Project ID
     * 
     * @var int
     */
    protected $_sessionProjectId = self::DEFAULT_PROJECT_ID;
    
    /**
     * Session account
     * 
     * @var string
     */
    protected $_sessionAccount = null;
    
    /**
     * PHP Cli WordPress_Session handler
     * 
     * @return WordPress_Session
     */
    protected function __construct() {
        // Validate the session name
        if (preg_match('%^' . self::USER_PREFIX . '(\d+)' . preg_quote(self::SEPARATOR) . '(\d+)(?:' . preg_quote(self::SEPARATOR) . '(\w+))?$%', $this->_sessionInfo = ini_get('session.name'), $matches)) {
            // Store ths User ID and Project ID
            list($this->_sessionUserId, $this->_sessionProjectId, $this->_sessionAccount) = array(intval($matches[1]), intval($matches[2]), isset($matches[3]) ? trim($matches[3]) : '');
            
            // Store the account as null
            if (!strlen($this->_sessionAccount)) {
                $this->_sessionAccount = null;
            }
        } else {
            // Default session
            $this->_sessionInfo = self::DEFAULT_SESSION_NAME;
        }
    }
    
    /**
     * PHP Cli WordPress_Session handler
     * 
     * @return WordPress_Session
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Get the current session account
     */
    public function getAccount() {
        return $this->_sessionAccount;
    }
    
    /**
     * Get the current PHP Cli WordPress_Session name
     * 
     * @param int $userId (optional) Overriding user ID
     * @return string
     */
    public function getName($userId = null) {
        return self::USER_PREFIX . (null === $userId ? $this->_sessionUserId : $userId);
    }
    
    /**
     * Get the current User ID
     * 
     * @return int|null A custom user ID or null for the default user
     */
    public function getUserId() {
        return $this->_sessionUserId;
    }
    
    /**
     * Set a custom user ID for this session
     * 
     * @param int|null $userId User ID
     */
    public function setUserId($userId = null) {
        // The user ID must be an integer
        $userId = intval($userId);
        
        // Must be a positive number
        $this->_sessionUserId = $userId < self::DEFAULT_USER_ID ? self::DEFAULT_USER_ID : $userId;

        // Update the session info
        $this->_updateSessionInfo();
    }
    
    /**
     * Get the project ID
     * 
     * @return int
     */
    public function getProjectId() {
        return $this->_sessionProjectId;
    }
    
    /**
     * Set the project ID
     * 
     * @param int $projectId Project ID
     */
    public function setProjectId($projectId) {
        // The project ID must be an integer
        $projectId = intval($projectId);
        
        // Must be a positive number
        $this->_sessionProjectId = $projectId < self::DEFAULT_PROJECT_ID ? self::DEFAULT_PROJECT_ID : $projectId;
        
        // Update he session info
        $this->_updateSessionInfo();
    }
    
    /**
     * Is this the default PHP Cli WordPress_Session?
     * 
     * @return boolean
     */
    public function isDefault() {
        return self::DEFAULT_SESSION_NAME == $this->_sessionInfo;
    }
    
    /**
     * Update the session information
     */
    protected function _updateSessionInfo() {
        if ($this->_sessionUserId >= self::DEFAULT_USER_ID && $this->_sessionProjectId >= self::DEFAULT_PROJECT_ID) {
            $this->_sessionInfo = self::USER_PREFIX . $this->_sessionUserId . self::SEPARATOR . $this->_sessionProjectId . (strlen($this->_sessionAccount) ? (self::SEPARATOR . $this->_sessionAccount) : '');
        } else {
            $this->_sessionInfo == self::DEFAULT_SESSION_NAME;
        }
    }
}

/*EOF*/