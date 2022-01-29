<?php
/**
 * Theme Warlock - WordPress_ChildTheme
 * 
 * @title      Child Theme
 * @desc       Generate the child theme
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_ChildTheme {

    /**
     * Create a child theme at the required location
     * 
     * @param string $dirPath            Child theme path
     * @param string $screenshotFilePath Path to the parent "screenshot.png" file to use when generating the final child theme screenshot file
     */
    public function save($dirPath, $screenshotFilePath) {
        if (!is_dir($dirPath)) {
            Folder::create($dirPath, 0777, true);
        }
        
        // Copy the child theme files
        foreach (glob(ROOT . '/web/resources/wordpress/child-theme/*.*') as $licenseFilePath) {
            Addons::getInstance()->parse(
                $licenseFilePath, 
                $dirPath . '/' . basename($licenseFilePath), 
                Model_Project_Config::CATEGORY_CORE
            );
        }
        
        // Get the file path
        $finalScreenshotPath = $dirPath . '/' . WordPress::FILE_SCREENSHOT;
        
        do {
            // File not found
            if (is_file($screenshotFilePath)) {
                // Overlay prepared
                if (is_file($finalScreenshotPath)) {
                    // Get the project data
                    $projectData = Tasks::$project->getConfig()->getProjectData();

                    /* @var $headerLinkColor Model_Project_Config_Item_Color */
                    $headerLinkColor = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_HEADER_LINK_COLOR];

                    // Prepare the command
                    $command = 'convert' . 
                        ' ' . escapeshellarg($screenshotFilePath) . 
                        ' \( ' . escapeshellarg($finalScreenshotPath) . 
                            ' \( -clone 0 -fill "' . $headerLinkColor->getWpColor() . '" -colorize 100 \)' . 
                            ' -compose hue' .
                            ' -composite' .
                        ' \)' . 
                        ' -compose over -composite' .
                        ' ' . escapeshellarg($finalScreenshotPath);

                    // Log it
                    Log::check(Log::LEVEL_DEBUG) && Log::debug($command);
                    
                    // Execute the command
                    exec($command, $commandOutput, $commandReturn);
                    
                    // Execute the command
                    if (0  == $commandReturn) {
                        // Optimize the image
                        Image::optimize($finalScreenshotPath);
                        
                        // Stop here
                        break;
                    } else {
                        Log::check(Log::LEVEL_WARNING) && Log::warning('Could not generate the child theme screenshot file');
                    }
                } else {
                    Log::check(Log::LEVEL_WARNING) && Log::warning('Resource screenshot file not created');
                }
            } else {
                Log::check(Log::LEVEL_WARNING) && Log::warning('Could not find preview file "' . $screenshotFilePath . '"');
            }
        
            // Broken resources
            Log::check(Log::LEVEL_WARNING) && Log::warning('Using main theme\'s screenshot');
                
            // Store the default
            copy($screenshotFilePath, $finalScreenshotPath);
        } while (false);
    }
    
}

/* EOF */