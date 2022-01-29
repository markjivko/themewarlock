<?php

/**
 * Theme Warlock - Screenshot
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Screenshot {
    
    /**
     * Image instance
     * 
     * @var Image
     */
    protected static $_image = null;
    
    /**
     * Grab a screenshot of an URL and save it locally
     * 
     * @param string      $url   URL
     * @param string|null $path  (optional) Local path for the screen grab; default <b>null</b><ul>
     * <li>If <b>null</b>, a new temporary path will be created. <b>Make sure you delete the file when no longer needed!</b></li>
     * <li>If <b>string</b>, must be a valid path to a PNG image; the parent directory must be present.</li>
     * <li>If <b>true</b>, a new resource containing the image will be created. The temporary image is removed form the temporary directory</li>
     * </ul>
     * @param int         $width (optional) Web page width in px; default <b>1920</b>
     * @return array <ul>
     * <li>(<b>boolean</b>) Screenshot grab result</li>
     * <ul>
     * <li>(<b>string</b>) Final path to the generated PNG image</li>
     * <li>(<b>resource GD</b>) If $path is "true", a resource is created</li>
     * </ul>
     * </ul>
     */
    public function grab($url, $path = null, $width = WordPress_Snapshots_Snapshot::PREVIEW_WIDTH) {
        // Sanitize width
        $width = intval($width);
        $width = $width < 100 ? 100 : ($width > 3000 ? 3000 : $width);
        
        Log::check(Log::LEVEL_INFO) && Log::info('Screenshot Grabbing for "' . $url . '" @' . $width);
        
        // Expecting a resource
        $returnResource = true === $path;
        
        // Image instance not declared yet
        if(is_null(self::$_image)) {
            self::$_image = new Image();
        }

        // Sanitize the path
        if (!is_null($path)) {
            // Must be a string
            $path = strval($path);
            
            // Must be valid png path
            if (!preg_match('%\w+\.png$%i', $path)) {
                $path = null;
            }
            
            // Parent must be a valid directory
            if (!is_dir(dirname($path))) {
                $path = null;
            }
        }

        // Reverted to the default
        if(is_null($path)) {
            // If the temp directory does not exist
            if(!is_dir($pathParent = ROOT . '/web/' . IO::tempFolder() . '/screenshots')) {
                // Create it
                mkdir($pathParent, 0777, true);
            }
            
            // Prepare screenshot name
            $screenshotName = md5($url) . getmypid() . '.png';

            // The path is temp direstory / screenshot name
            $path = $pathParent . '/' . $screenshotName;   
        }
        
        // Get the options
        $options = array(
            '--app-name'          => 'ThemeWarlock-Screenshot',
            '--app-version'       => '1.0',
            '--url'               => $url,
            '--delay'             => 3500,    
            '--max-wait'          => 10000,    
            '--min-width'         => $width,
            '--user-style-string' => 'html{width:' . $width . 'px;margin:0 auto;}',
            '--zoom-factor'       => 1,
            '--smooth',
            '--insecure',
        );
        $optionsString = '';

        // Add options to optionsString
        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $optionsString .= $value . ' ';
            } else {
                $optionsString .= $key . '=' . (is_numeric($value) ? $value : escapeshellarg($value)) . ' ';
            }
        }
        
        // Prepare the command
        $command = 'xvfb-run -a cutycapt ' . $optionsString . ' --out=' . escapeshellarg($path) . ' 2>&1';

        // Execute command
        exec($command, $output, $return);
        
        // Crop the file
        if (0 == $return) {
            // Prepare the crop command
            $commandCrop = 'convert' . 
                ' ' . escapeshellarg($path) .
                ' -gravity Center' .
                ' -crop ' . $width . 'x0+0+0' . 
                ' ' . escapeshellarg($path);
            
            // Try to convert the image
            shell_exec($commandCrop);
        }
        
        // If $path is true, return a resource and delete physical image
        if($returnResource) {
            $imageResource = self::$_image->load($path);
            @unlink($path);
            return array(0 == $return, $imageResource);
        }
        
        // $return will return non-zero upon an error
        return array(0 == $return, $path);
    }
}

/*EOF*/