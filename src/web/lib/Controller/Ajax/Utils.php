<?php
/**
 * Theme Warlock - Controller_Utils
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax_Utils extends Controller_Ajax {
    
    /**
     * Ignore these methods when logging user activity
     * 
     * @var array
     */
    public static $logIgnore = array(
        'getNotifHandlerMessages',
    );
    
    /**
     * Send a new message
     * 
     * @allowed admin
     */
    public function logView($logId = 0) {
        // Get the maximum number of results
        $maxResults = intval(trim(Input::getInstance()->postRequest('max')));
        
        // Set the default
        if ($maxResults < 10) {
            $maxResults = 10;
        }
        
        // Don't go too far
        if ($maxResults > 5000) {
            $maxResults = 5000;
        }
        
        // Get the log path
        if (!file_exists($logPath = ROOT . '/web/log/log_user_' . $logId . '.txt')) {
            throw new Exception ('Log #' . $logId . ' not found');
        }

        // Prepare the result
        $result = array();
        
        // Get the total fline lines
        $totalFileLines = 0;
        
        // Open the file
        if ($handle = fopen($logPath, "r")) {
            // Go through each line
            while (false !== fgets($handle)) {
                $totalFileLines++;
            }
            fclose($handle);
        }
        
        // Open the file again
        if ($handle = fopen($logPath, "r")) {
            $currentFileLine = 0;
            while (($line = fgets($handle)) !== false) {
                // Increment the line counter
                $currentFileLine++;
                
                // Last N lines
                if ($totalFileLines - $currentFileLine < $maxResults) {
                    // Store the JSON data in reverse order
                    if (is_array($json = @json_decode($line, true))) {
                        array_unshift($result, $json);
                    }
                }
            }
            fclose($handle);
        }
        
        // Invalid result
        if (count($result) == 0) {
            throw new Exception('No data yet');
        }
        
        // All done
        return $result;
    }
    
    /**
     * Fetch notification handler messages
     * 
     * @allowed admin
     */
    public function getNotifHandlerMessages() {
        return TaskbarNotifier::fetchMessages();
    }
}

/* EOF */