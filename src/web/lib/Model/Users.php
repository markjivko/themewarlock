<?php
/**
 * Theme Warlock - Model_Users
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Users {
    
    /**
     * SQL
     * 
     * @var Sql
     */
    protected $_sql;
    
    /**
     * Tasks model
     */
    public function __construct() {
        // Get the SQL instance
        $this->_sql = Sql::getInstance();
    }
    
    /**
     * Get all the current users
     * 
     * @return Model_User[]
     */
    public function getAll() {
        // Prepare the query
        $query = 'SELECT * FROM `users`';
        
        // Run the result
        $result = $this->_sql->query($query);
        
        // Prepare the users
        $users = array();
        
        // Go through the users list
        while($row = $result->fetch_assoc()) {
            $users[] = new Model_User($row);
        }
        
        // All done
        return $users;
    }
}

/*EOF*/