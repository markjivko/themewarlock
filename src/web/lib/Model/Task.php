<?php
/**
 * Theme Warlock - Model_Task
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Single task model
 */
class Model_Task {
    /**
     * Task ID
     * 
     * @var int
     */
    public $id = null;
    
    /**
     * Task MAC address
     * 
     * @var string
     */
    public $mac = null;
    
    /**
     * Task name
     * 
     * @var string
     */
    public $task_method = null;
    
    /**
     * Task session (Config::use)
     * 
     * @var string
     */
    public $task_session = null;
    
    /**
     * Task arguments
     * 
     * @var array
     */
    public $task_arguments = array();
    
    /**
     * Task delegation time
     * 
     * @var int
     */
    public $task_time = null;
    
    /**
     * Task Process Id - after each signal
     * 
     * @var int
     */
    public $pid = null;
    
    /**
     * ACK signal
     * 
     * @var int
     */
    public $sig_ack = null;
    
    /**
     * EXC signal
     * 
     * @var int
     */
    public $sig_exc = null;
    
    /**
     * RES signal
     * 
     * @var int
     */
    public $sig_res = null;
    
    /**
     * Task
     */
    public function __construct($row) {
        // Get a reflection
        $reflection = new ReflectionClass(__CLASS__);
        
        // Get the properties
        foreach ($reflection->getProperties() as $reflectionProperty) {
            // Get the value
            $value = isset($row[$reflectionProperty->name]) ? $row[$reflectionProperty->name] : null;

            // Fix the value type
            switch ($reflectionProperty->name) {
                case 'id':
                case 'task_time':
                case 'pid':
                case 'sig_ack':
                case 'sig_exc':
                case 'sig_res':
                    // Must be an integer
                    $value = intval($value);
                    break;
                
                case 'task_arguments':
                    // Must be an array
                    $value = empty($value) ? array() : (array) @json_decode($value, true);
                    break;
            }
            
            // Store it locally
            $this->{$reflectionProperty->name} = $value;
        }
    }
}

/* EOF */