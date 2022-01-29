<?php

/**
 * Theme Warlock - Sqlite
 * 
 * @copyright (c) 2019, Mark Jivko
 * @author    Mark Jivko https://markjivko.com
 * @package   Theme Warlock
 * @since     TW 1.0
 */
class Sqlite {
    
    /**
     * Singleton instance of Sqlite
     * 
     * @var Sqlite
     */
    protected static $_instance = null;
    
    /**
     * Path to the SQLite database
     * 
     * @var string
     */
    protected $_path = null;
    
    /**
     * SQLite3 instance
     * 
     * @var SQLite3
     */
    protected $_db = null;
    
    /**
     * Sqlite Class constructor
     */
    protected function __construct() {
    }
    
    
    /**
     * Get a singleton instance of Sqlite
     * 
     * @return Sqlite
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    } 
    
    /**
     * Set the path to the SQLite database and create a new internal SQLite3 instance
     * 
     * @param string $path Full path to <b>*.db</b> file
     * @throws Exception
     */
    public function setPath($path) {
        $path = strval($path);
        if (!preg_match('%\.db$%', $path)) {
            throw new Exception('Invalid database name "' . $path . '"');
        }

        if (!is_dir(dirname($path))) {
            throw new Exception('Parent is not a valid directory "' . $path . '"');
        }

        // Using a new database
        if($path !== $this->_path) {
            $this->_db = new SQLite3($path);
        }
        
        // Store the path
        $this->_path = $path;
    }

    /**
     * Get the path to the SQLite database
     * 
     * @return string|null Null if the path was not set
     */
    public function getPath() {
        return $this->_path;
    }

    /**
     * Get the database
     * 
     * @return SQLite3
     */
    public function getDb() {
        return $this->_db;
    }
    
    /**
     * Performs a query on the SQLite database
     * 
     * @param string $query Prepared statement. <b>Make sure to specify all keys in the $binds argument!</b>
     * @param array  $binds Associative array of <ul>
     * <li><b>key</b> - SQL prepared statement key with or without preceding semicolon</li>
     * <li><b>value</b> - value to use in the prepared statement</li>
     * </ul>
     * @throws Exception
     * 
     * @return SQLite3Result
     */
    public function query($query, $binds = array()) {
        if (null === $this->getPath()) {
            throw new Exception('No database provided');
        }
        
        // Prepare the statement
        $stmt = $this->_db->prepare($query);
        
        // Statement is an object
        if (false !== $stmt) {
            // Bind each key
            foreach($binds as $key => $value) {
                // Sanitize the key
                $sanitizedKey = ':' . preg_replace('%^\:+%', '', $key);

                // All keys must begin with semicolon (:)
                $stmt->bindValue($sanitizedKey, $value);
            }

            // Execute the query
            return $stmt->execute();
        }
        
        // Invalid statement
        return false;
    }
}

/*EOF*/