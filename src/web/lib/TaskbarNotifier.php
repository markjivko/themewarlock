<?php
/**
 * Theme Warlock - TaskBar Notifier
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class TaskbarNotifier {

    // Notification types
    const TYPE_INFO    = 'Info';
    const TYPE_WARNING = 'Warning';
    const TYPE_ERROR   = 'Error';
    
    /**
     * Allowed notification types
     * 
     * @var array
     */
    protected static $_types = array(
        self::TYPE_INFO,
        self::TYPE_WARNING,
        self::TYPE_ERROR,
    );
    
    /**
     * Send a balloon message to the user
     * 
     * @param string $title   Title - mandatory
     * @param string $message Message - mandatory
     * @param string $type    Notification type, one of: <ul>
     * <li>TaskbarNotifier::TYPE_INFO</li>
     * <li>TaskbarNotifier::TYPE_WARNING</li>
     * <li>TaskbarNotifier::TYPE_ERROR</li>
     * </ul>
     */
    public static function sendMessage($title, $message, $type = self::TYPE_INFO) {
        // Set the fallback type
        if (!in_array($type, self::$_types)) {
            $type = self::TYPE_INFO;
        }

        // Prepare the logo name
        $logoName = 'logo-' . strtolower($type) . '.png';
        
        // Trim the title
        $title = trim($title);

        // Trim the description
        $message = trim($message);

        // Log this information
        Log::controller('TaskbarNotifier', strtolower($type), array(), array(str_replace('\\"', '"', $title), str_replace('\\"', '"', $message)));
        
        // Show the message
        passthru('/usr/bin/notify-send -u low -t 100 -i "' . ROOT . '/img/' . $logoName . '" ' . escapeshellarg($title) . ' ' . escapeshellarg($message) . ' > /dev/null 2>&1 &');
    
        // Prepare the file index
        $fileIndex = 0;
        $tempPath = self::_getTempPath();
        
        // Fetch all files with the ".txt" extension from tempPath
        foreach(glob($tempPath . "/*.txt") as $filePath) {
            // If the file is older than 10 minutes
            if((time() - filemtime($filePath)) > 10 * 60) {
                // Delete the file
                unlink($filePath);
            } else {
                // Get base file name
                $fileNameIndex = intval(basename($filePath,".txt"));
                
                // If the current file name is larger than the file index
                if($fileNameIndex > $fileIndex) {
                    // File index equals current file name
                    $fileIndex = $fileNameIndex;
                } 
            }
        }
        
        // Write a new file in $tempPath, at fileIndex + 1
        $uploadFile = $tempPath . "/" . ($fileIndex + 1) . ".txt";
        file_put_contents($uploadFile, json_encode(array($type, $title, $message)));
    }
    
    /**
     * Get the messages local save path
     * 
     * @return string
     */
    protected static function _getTempPath() {
        // If there is no temp directory at 'ROOT . "/web/' . IO::tempFolder() . '/notifier-taskbar"'
        if (!is_dir($tempPath = ROOT . '/web/' . IO::tempFolder() . '/notifier-taskbar')) {
            // Create it
            mkdir($tempPath, 0777, true);
        }
        
        return $tempPath;
    }
    
    /**
     * Fetch the temporary notifications
     * 
     * @return array Array of messages
     */
    public static function fetchMessages() {
        $result = array();
        $tempPath = self::_getTempPath();
        
        // Fetch all files with the ".txt" extension from tempPath
        foreach(glob($tempPath . "/*.txt") as $filePath) {
            
            // Get file contents
            $contents = file_get_contents($filePath);
            
            // Decode file contents
            $encodedContents = json_decode($contents, true);

            // If the converted value is an array of size 3
            if(is_array($encodedContents) && count($encodedContents) == 3) {
                // If the first element is one of the expected types ("Info", "Warning", "Array")
                if(in_array($encodedContents[0], self::$_types)) {
                    // If the next two elements are strings
                    if(is_string($encodedContents[1]) && is_string($encodedContents[2])) {
                        // Cast the first element to lowercase
                        $encodedContents[0] = strtolower($encodedContents[0]);
                        
                        // Append the converted value to "results"
                        $result[] = $encodedContents;
                    }
                }
            }
            
            // Delete the file
            unlink($filePath);
        }
        
        return $result;
        
    }

}

/* EOF */