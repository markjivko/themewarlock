<?php
/**
 * Theme Warlock - Model_Brute
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Brute {
    
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
     * IP
     * 
     * @var string
     */
    public $ip = null;
    
    /**
     * Time
     * 
     * @var string
     */
    public $time = null;
    
    /**
     * Attempts
     * 
     * @var string
     */
    public $attempts = null;
    
    /**
     * Exists
     * 
     * @var boolean
     */
    protected $_exists = false;
    
    /**
     * User model
     * 
     */
    public function __construct() {
        // Get the SQL instance
        $this->_sql = Sql::getInstance();
        
        // Set the IP
        $this->ip = $_SERVER['REMOTE_ADDR'];
        
        // Prepare the query
        $query = 'SELECT * FROM `brutes` WHERE `ip` = ?';
        
        // Run the result
        if (null !== $result = $this->_sql->query($query, array($this->ip))->fetch_assoc()) {
            // User found
            $this->_exists = true;
            
            // Set the Data
            $this->id = $result['id'];
            $this->time = $result['time'];
            $this->attempts = $result['attempts'];
        }
    }
    
    /**
     * Create a user for this IP address
     * 
     * @return boolean
     */
    public function create() {
        // Cannot re-create this user
        if ($this->_exists) {
            return false;
        }
        
        // Prepare the query
        $query = 'INSERT INTO `brutes` SET `ip` = ?, `attempts` = 1, `time` = ?';

        // Get the result
        $result = $this->_sql->query($query, array($this->ip, $time = time()));
        
        // User found
        if (false !== $result) {
            $this->_exists = true;

            // Set the Data
            $this->id = mysqli_insert_id($this->_sql->getLink());
            $this->time = $time;
            $this->attempts = 1;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Increment the number of attempts
     * 
     * @return boolean
     */
    public function increment() {
        // Cannot update a non-existing user
        if (!$this->_exists) {
            return false;
        }
        
        // Empty attempts
        $time = time();
        
        // Prepare the query
        $query = 'UPDATE `brutes` SET `attempts` = `attempts` + 1, `time` = ? WHERE `ip` = ?';

        // Get the result
        $result = $this->_sql->query($query, array($time, $this->ip));
        
        // Updated
        if (false !== $result) {
            $this->time = $time;
            $this->attempts++;
        }
        
        // All done
        return $result;
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
        $query = 'DELETE FROM `brutes` WHERE `ip` = ?';

        // Get the result
        $result = $this->_sql->query($query, array($this->ip));
        
        // Deleted
        if (false !== $result) {
            $this->_exists = false;

            // Set the Data
            $this->id = null;
            $this->time = null;
            $this->attempts = null;
        }
        
        // All done
        return $result;
    }
}

/*EOF*/