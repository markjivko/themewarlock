<?php
/**
 * Theme Warlock - Model_Settings
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Settings {

    /**
     * SQL
     * 
     * @var Sql
     */
    protected $_sql;
    
    /**
     * Associative array of settings
     * 
     * @var string[]
     */
    protected $_settigs = array();
    
    /**
     * Settings model instance
     * 
     * @var Model_Settings
     */
    protected static $_instance;
    
    /**
     * Settings model
     */
    protected function __construct() {
        // Get the SQL instance
        $this->_sql = Sql::getInstance();
        
        // Prepare the query
        $query = 'SELECT * FROM `settings`';
        
        // Run the result
        $result = $this->_sql->query($query);
        
        // Go through the users list
        while($row = $result->fetch_assoc()) {
            $this->_settigs[$row['key']] = null != $row['value'] ? @json_decode($row['value'], true) : null;
        }
    }
    
    /**
     * Settings model
     * 
     * @return Model_Settings
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Get a setting by name
     * 
     * @param string $keyName Setting name
     * @return mixed Value or null if not found
     */
    public function get($keyName) {
        // Get the value
        return isset($this->_settigs[$keyName]) ? $this->_settigs[$keyName] : null;
    }
    
    /**
     * Set a setting by name
     * 
     * @param string $keyName  Setting name
     * @param string $keyValue Setting value
     * @return boolean True on success, false on failure
     */
    public function set($keyName, $keyValue) {
        // Invalid key
        if (empty($keyName)) {
            return false;
        }
        
        // Insert
        if (!isset($this->_settigs[$keyName])) {
            // Prepare the query
            $query = 'INSERT INTO `settings` set `value` = ?, `key` = ?';
        } else {
            // Prepare the query
            $query = 'UPDATE `settings` set `value` = ? WHERE `key` = ?';
        }
        
        // Update the local array
        $this->_settigs[$keyName] = $keyValue;
        
        // Run the result
        return $this->_sql->query($query, array(json_encode($keyValue), $keyName));
    }

}

/* EOF */