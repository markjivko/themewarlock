<?php
/**
 * Theme Warlock - Model_Session
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Session extends Model {
    
    /**
     * User Model
     * 
     * @var Model_User 
     */
    protected $_userModel = null;
    
    /**
     * Session library
     * 
     * @var Session
     */
    protected $_session;
    
    /**
     * Session Model
     * 
     * @param Model_User $userModel User Model
     */
    public function __construct() {
        // Get a session instance
        $this->_session = Session::getInstance();
        
        // Get the user model
        if (null !== $userModel = $this->_session->get(Session::PARAM_WEB_USER_MODEL)) {
            $this->_userModel = $userModel;
        }
    }
    
    /**
     * Store the user model in the session
     * 
     * @param Model_User $userModel User Model
     * @return boolean True on success, false on failure
     */
    public function setUserModel(Model_User $userModel) {
        // Store the user model
        $this->_userModel = $userModel;
        
        // Store the user model
        return $this->_session->set(Session::PARAM_WEB_USER_MODEL, $this->_userModel);
    }
    
    /**
     * Get the user model
     * 
     * @return Model_User User Model
     */
    public function getUserModel() {
        return $this->_userModel;
    }
    
    /**
     * Destroy the session
     * 
     * @return boolean True on success, false on failure
     */
    public function destroy() {
        return $this->_session->reset();
    }
}

/*EOF*/