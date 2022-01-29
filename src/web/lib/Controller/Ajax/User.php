<?php
/**
 * Theme Warlock - Controller_Ajax_User
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax_User extends Controller_Ajax {

    /**
     * Update the user
     * 
     * @return boolean
     * @allowed admin
     */
    public function update() {
        // Get the e-mail
        $email = trim(Input::getInstance()->postRequest('email'));
        
        // Empty
        if (empty($email)) {
            throw new Exception('E-mail cannot be empty');
        }
        
        // Create the user model
        $userModel = new Model_User($email);
        
        // User not found
        if (!$userModel->exists()) {
            throw new Exception("User not found");
        }
        
        // Get the password
        $password = trim(Input::getInstance()->postRequest('password'));
        
        // Update some stuff
        return $userModel->update(
            strlen($password) ? $password : null, 
            trim(Input::getInstance()->postRequest('name')), 
            trim(Input::getInstance()->postRequest('role'))
        );
    }
    
    /**
     * Delete the user
     * 
     * @return boolean
     * @allowed admin
     */
    public function delete() {
        // Get the e-mail
        $email = trim(Input::getInstance()->postRequest('email'));
        
        // Empty
        if (empty($email)) {
            throw new Exception('E-mail cannot be empty');
        }
        
        // Create the user model
        $userModel = new Model_User($email);
        
        // User not found
        if (!$userModel->exists()) {
            throw new Exception("User not found");
        }
        
        // Delete the sandbox
        $this->wpDelete($userModel->id);
        
        // Update some stuff
        return $userModel->delete();
    }
    
    /**
     * Create a new user
     * 
     * @return boolean
     * @allowed admin
     * @throws Exception
     */
    public function create() {
        // Get the e-mail
        $email = trim(Input::getInstance()->postRequest('email'));
        
        // Empty
        if (empty($email)) {
            throw new Exception('E-mail cannot be empty');
        }
        
        // Create the user model
        $userModel = new Model_User($email);
        
        // User not found
        if ($userModel->exists()) {
            throw new Exception("User already created");
        }
        
        // Create the user
        $result = $userModel->create(
            trim(Input::getInstance()->postRequest('password')), 
            trim(Input::getInstance()->postRequest('name')), 
            trim(Input::getInstance()->postRequest('role'))
        );
        
        // User created successfully
        if (false !== $result) {
            $this->wpInit($userModel->id);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Initialize the WordPress environment for the specified user
     * 
     * @allowed admin
     * @throws Exception
     */
    public function wpInit($userId = null) {
        // Empty user ID
        if (null == $userId) {
            throw new Exception('User ID not specified');
        }
        
        // Not numeric
        if (!is_numeric($userId) || $userId < 1) {
            throw new Exception('Invalid user ID specified');
        }
        
        // Create the user model
        $userModel = new Model_User($userId);
        
        // Invalid user
        if (!$userModel->exists()) {
            throw new Exception('User does not exist');
        }
        
        // Get the WordPress session
        $session = WordPress_Session::getInstance();
        
        // Set the user ID
        $session->setUserId($userId);

        // Delete the sandbox
        WordPress::executeAction(
            WordPress::TOOLS_SB, 
            WordPress::TOOL_SB_DELETE
        );
        
        // (Re-)Initialize the sandbox
        WordPress::executeAction(
            WordPress::TOOLS_SB, 
            WordPress::TOOL_SB_INITIALIZE
        );
        
        // All done
        echo 'Sandbox initialized successfully!';
    }
    
    /**
     * Delete the WordPress environment for the specified user
     * 
     * @allowed admin
     * @throws Exception
     */
    public function wpDelete($userId = null) {
        // Empty user ID
        if (null == $userId) {
            throw new Exception('User ID not specified');
        }
        
        // Not numeric
        if (!is_numeric($userId) || $userId < 1) {
            throw new Exception('Invalid user ID specified');
        }
        
        // Create the user model
        $userModel = new Model_User($userId);
        
        // Invalid user
        if (!$userModel->exists()) {
            throw new Exception('User does not exist');
        }
        
        // Get the WordPress session
        $session = WordPress_Session::getInstance();
        
        // Set the user ID
        $session->setUserId($userId);
        
        // Initialize the sandbox
        WordPress::executeAction(
            WordPress::TOOLS_SB, 
            WordPress::TOOL_SB_DELETE
        );
        
        // All done
        echo 'Sandbox deleted successfully!';
    }
}

/* EOF */