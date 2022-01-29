<?php
/**
 * Theme Warlock - Input
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Input {
    
    /**
     * Input
     * 
     * @var Input
     */
    protected static $_instance;
    
    /**
     * Input
     */
    protected function __construct() {
        // XSS prevention
        $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
    }
    
    /**
     * Input
     * 
     * @return Input
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }
    
    /**
     * GET value
     * 
     * @param string $paramName Get parameter name
     * @return mixed Value or null
     */
    public function getRequest($paramName) {
        return isset($_GET[$paramName]) ? $_GET[$paramName] : null;
    }
    
    /**
     * POST value
     * 
     * @param string $paramName Post parameter name
     * @return mixed Value or null
     */
    public function postRequest($paramName) {
        return isset($_POST[$paramName]) ? $_POST[$paramName] : null;
    }
    
    /**
     * Get the user Input (CLI)
     * 
     * @param string  $message  Message
     * @param boolean $password The prompt is for a password
     * @return string
     */
    public static function get($message = '', $password = false) {
        // Output a message
        if ('' !== $message) {
            echo PHP_EOL . '* ' . $message . PHP_EOL . '> ';
            Log::check(Log::LEVEL_DEBUG) && Log::debug('Console question: ' . $message);
        } else {
            echo '> ';
        }
        
        // Get the PHP StdIn handler
        $handle = fopen("php://stdin", "r");

        // Wait for user Input
        $answer = fgets($handle);

        // Close the file handler
        fclose($handle);

        // Don't store passwords
        if (!$password) {
            Log::check(Log::LEVEL_DEBUG) && Log::debug('Console answer: ' . $answer);
        }

        // Return the result
        return $answer;
    }
}

/*EOF*/