<?php
/**
 * Theme Warlock - Model_User
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_User {
    
    /**
     * SQL
     * 
     * @var Sql
     */
    protected $_sql;
    
    /**
     * ID
     * 
     * @var int
     */
    public $id = null;
    
    /**
     * Email
     * 
     * @var string
     */
    public $email = null;
    
    /**
     * Name
     * 
     * @var string
     */
    public $name = null;
    
    /**
     * Role
     * 
     * @var string
     */
    public $role = null;
    
    /**
     * Password
     * 
     * @var string
     */
    protected $_password = null;
    
    /**
     * Exists
     * 
     * @var boolean
     */
    protected $_exists = false;
    
    /**
     * User model
     * 
     * @param string $emailOrIdOrRow User identifier or complete user info
     */
    public function __construct($emailOrIdOrRow) {
        // Get the SQL instance
        $this->_sql = Sql::getInstance();

        // Row provided
        if (is_array($emailOrIdOrRow)) {
            // Already got the result
            $result = $emailOrIdOrRow;
            
            // Set the e-mail
            $this->email = $result['email'];
        } else {
            // ID provided
            if (is_numeric($emailOrIdOrRow)) {
                // Prepare the query
                $query = 'SELECT * FROM `users` WHERE `id` = ?';

                // Set the ID
                $this->id = $emailOrIdOrRow;
            } else {
                // Prepare the query
                $query = 'SELECT * FROM `users` WHERE `email` = ?';

                // Set the e-mail
                $this->email = $emailOrIdOrRow;
            }
            
            // Get the result
            $result = $this->_sql->query($query, array($emailOrIdOrRow))->fetch_assoc();
        }
        
        // Run the result
        if (null !== $result) {
            // User found
            $this->_exists = true;
            
            // Set the Data
            $this->id = $result['id'];
            $this->name = $result['name'];
            $this->email = $result['email'];
            $this->role = $result['role'];
            $this->_password = $result['password'];
        }
    }
    
    /**
     * Create a user for this e-mail address
     * 
     * @param string $password Password
     * @param string $name     User real name
     * @param string $role     User role, @see Session::ROLE_*
     * @return boolean
     */
    public function create($password, $name, $role) {
        // Cannot re-create this user
        if ($this->_exists) {
            return false;
        }

        // Validate the role
        if (!in_array($role, Session::$roles)) {
            $role = Session::ROLE_COPYRIGHTER;
        }
        
        // Prepare the query
        $query = 'INSERT INTO `users` SET `email` = ?, `password` = ?, `name` = ?, `role` = ?';

        // Get the result
        $result = $this->_sql->query($query, array($this->email, $encryptedPassword = $this->_passwordEncrypt($password), $name, $role));
        
        // User found
        if (false !== $result) {
            $this->_exists = true;

            // Set the Data
            $this->id = mysqli_insert_id($this->_sql->getLink());
            $this->name = $name;
            $this->role = $role;
            $this->_password = $encryptedPassword;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Update a user for this e-mail address
     * 
     * @param string $password Password
     * @param string $name     User real name
     * @param string $role     User role, @see Session::ROLE_*
     * @return boolean
     */
    public function update($password = null, $name = null, $role = null) {
        // Cannot update a non-existing user
        if (!$this->_exists) {
            return false;
        }
        
        // Empty password
        if (null == $password) {
            $encryptedPassword = $this->_password;
        } else {
            $encryptedPassword = $this->_passwordEncrypt($password);
        }

        // Empty name
        if (null == $name) {
            $name = $this->name;
        }
        
        // Empty role
        if (null == $role) {
            $role = $this->role;
        }
        
        // Validate the role
        if (!in_array($role, Session::$roles)) {
            $role = Session::ROLE_COPYRIGHTER;
        }
        
        // Prepare the query
        $query = 'UPDATE `users` SET `password` = ?, `name` = ?, `role` = ? WHERE `email` = ?';

        // Get the result
        $result = $this->_sql->query($query, array($encryptedPassword, $name, $role, $this->email));
        
        // Updated
        if (false !== $result) {
            $this->name = $name;
            $this->role = $role;
            $this->_password = $encryptedPassword;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Verify the user password
     * 
     * @param string $password Password
     * @return boolean True if the password matches
     */
    public function verify($password) {
        return $this->_exists && $this->_password === $this->_passwordEncrypt($password);
    }
    
    /**
     * User exists
     * 
     * @return boolean
     */
    public function exists() {
        return $this->_exists;
    }
    
    /**
     * Delete the current user
     * 
     * @return boolean
     */
    public function delete() {
        // Cannot delete a non-existing user
        if (!$this->_exists) {
            return false;
        }
        
        // Prepare the query
        $query = 'DELETE FROM `users` WHERE `email` = ?';

        // Get the result
        $result = $this->_sql->query($query, array($this->email));
        
        // Deleted
        if (false !== $result) {
            $this->_exists = false;

            // Set the Data
            $this->id = null;
            $this->name = null;
            $this->role = null;
            $this->_password = null;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Encrypt a password
     * 
     * @param string $password Password
     * @return string Md5 format encrypted password
     */
    protected function _passwordEncrypt($password) {
        return md5(Config::get()->dbSalt . '-' . $password);
    }
}

/*EOF*/