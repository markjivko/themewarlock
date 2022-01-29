<?php
/**
 * Theme Warlock - Sql
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Sql {
    
    /**
     * Sql instance
     * 
     * @var Sql
     */
    protected static $_instance;
    
    /**
     * Link
     * 
     * @var mysqli 
     */
    protected $_link;
    
    /**
     * Sql
     */
    protected function __construct() {
        // Connect to the databse
        $this->_link = mysqli_connect(Config::get()->dbHost, Config::get()->dbUsername, Config::get()->dbPassword, Config::get()->dbName);
    }
    
    /**
     * Sql helper
     * 
     * @return Sql
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Execute a query
     * 
     * @param string $query Prepared query
     * @param array  $binds Query binds
     * @return mysqli_result
     */
    public function query($query, $binds = array()) {
        // Prepare the statement
        if (false === $statement = mysqli_prepare($this->_link, $query)) {
            print_r($binds);
            print_r($query);
            throw new Exception('Could not prepare statement');
        }
        
        // Binds provided
        if (count($binds)) {
            // Bind types
            $bindTypes = '';

            // Bind references
            $bindReferences = array();

            // Go through the items
            foreach ($binds as $bindKey => $bindValue) {
                // Add to the bind types
                $bindTypes .= is_numeric($bindValue) ? 'i' : 's';

                // Create a dynamic variable
                ${"bind" . $bindKey} = $bindValue;

                // Append to the bind references
                $bindReferences[] = &${"bind" . $bindKey};
            }

            // Get the bind array
            $bindArray = array_merge(array($bindTypes), $bindReferences);
            
            // Bind the items
            call_user_func_array(array($statement, 'bind_param'), $bindArray);
        }
        
        // Execute it
        $statement->execute();

        // Get the result
        $result = $statement->get_result();

        // Get the affected rows
        $affectedRows = $statement->affected_rows;
        
        // Close the statement
        $statement->close();
        
        // Return the result
        return is_object($result) ? $result : ($affectedRows > 0);
    }
    
    /**
     * Get the link
     * 
     * @return mysqli
     */
    public function getLink() {
        return $this->_link;
    }
}

/*EOF*/