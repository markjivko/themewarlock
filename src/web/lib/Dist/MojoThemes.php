<?php
/**
 * Theme Warlock - Dist_MojoThemes
 * 
 * @title      MOJO Marketplace distributables
 * @desc       Task to execute when packing distributables for MOJO Marketplace
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Dist_MojoThemes extends Dist {

    /**
     * Tasks
     */
    const TASK_PACK_SCREENSHOTS   = 'packScreenshots';
    const TASK_PACK_GRAPHICS      = 'packGraphics';
    const TASK_PACK_DEMO_DATA     = 'packDemoData';
    const TASK_PACK_DOCUMENTATION = 'packDocumentation';
    const TASK_PACK_THEME         = 'packTheme';
        
    /**
     * Folders
     */
    const FOLDER_DOCUMENTATION = 'Documentation';
    const FOLDER_SCREENSHOTS   = 'Screenshots';
    const FOLDER_DEMO_DATA     = 'Demo Data';
    
    /**
     * Files
     */
    const FILE_HERO_IMAGE       = '1. hero-image.jpg';
    const FILE_SQUARE_THUMBNAIL = '2. square-thumbnail.png';
    const FILE_LARGE_THUMBNAIL  = '3. large-thumbnail.png';
    
    /**
     * Patterns
     */
    const PATTERN_PROFILE_URL = 'https://www.mojomarketplace.com/store/__X__';
    const PATTERN_LICENSE_URL = 'https://www.mojomarketplace.com/mojo-license';
    
    /**
     * Get the path to the documentation
     * 
     * @return string
     */
    protected function _getPathDocs() {
        return IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_DOCUMENTATION;
    }

    /**
     * Get the path to the license
     * 
     * @return string
     */
    protected function _getPathLicense() {
        // Do not generate a license folder
        return null;
    }

    /**
     * Get the available Distributable tasks for the current marketplace; un-associative array<br/>
     * Must include <b>Dist::TASK_PACK_SNAPSHOTS</b>!
     * 
     * @return array
     */
    protected function _getTasks() {
        return array(
            self::TASK_PACK_SCREENSHOTS,
            self::TASK_PACK_GRAPHICS,
            
            self::TASK_PACK_SNAPSHOTS,
            
            self::TASK_PACK_DEMO_DATA,
            self::TASK_PACK_DOCUMENTATION,
            self::TASK_PACK_THEME,
        );
    }
    
    /**
     * Create the Screenshots folder
     */
    protected function _packScreenshots() {
        Log::check(Log::LEVEL_INFO) && Log::info('Storing the screenshots...');
        
        // Prepare the folder
        if (!is_dir($screenshotsFolder = IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_SCREENSHOTS)) {
            Folder::create($screenshotsFolder, 0777, true);
        }
        
        // Go through the snapshots
        $increment = 1;
        
        // Go through the snapshots
        foreach (glob(IO::outputPath() . '/' . self::FOLDER_SNAPSHOTS . '/*/' . WordPress_Snapshots_Snapshot::PREVIEW_FILE_NAME) as $snapshotPreviewPath) {
            if (is_file($snapshotPreviewPath)) {
                copy($snapshotPreviewPath, $screenshotsFolder . '/screenshot-' . $increment . '.png');
                
                // Next image
                $increment++;
            }
        }
    }
    
    /**
     * Pack the graphical assets
     */
    protected function _packGraphics() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating graphic assets...');
        
        // Get the project data
        $projectData = Tasks::$project->getConfig()->getProjectData();
        
        /* @var $storePreview Model_Project_Config_Item_Image */
        $storePreview = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_PREVIEW_STORE];
        
        // Create the Hero Image
        if (strlen($storePreview->getPath()) && is_file($storePreview->getPath())) {
            $this->_imageMagick->resizeFile(
                $storePreview->getPath(), 
                IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FILE_HERO_IMAGE, 
                1180, 660
            );
        }
        
        /* @var $projectIcon Model_Project_Config_Item_Image */
        $projectIcon = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_ICON];
        
        // Get the icon path
        $iconPath = $projectIcon->getPath();
        
        // Revert to the default
        if (!strlen($iconPath) || !is_file($iconPath)) {
            $iconPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . Model_Project_Config::CATEGORY_CORE . '/admin/img/st_icon_512.png';
        }
        
        // Create the square thumbnail
        $this->_imageMagick->nudgeFile(
            $iconPath,
            IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FILE_SQUARE_THUMBNAIL,
            0, 0,
            160, 160
        );
        
        // Screenshot.png file defined
        if (is_file($themePreviewPath = IO::outputPath() . '/' . Tasks_1NewProject::$destDir . '/' . WordPress::FILE_SCREENSHOT)) {
            $this->_imageMagick->nudgeFile(
                $themePreviewPath,
                IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FILE_LARGE_THUMBNAIL,
                0, 0,
                260, 156
            );
        }
    }
    
    /**
     * Create the Demo Data archive
     */
    protected function _packDemoData() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . self::FOLDER_DEMO_DATA . '.zip"...');
        
        // Prepare the paths
        $pathSource = IO::outputPath() . '/' . self::FOLDER_SNAPSHOTS_DEMO;
        $pathDestination = IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_DEMO_DATA;
            
        // Move the folder
        Folder::copyContents($pathSource, $pathDestination);
        Folder::clean($pathSource, true);
        
        // Create the archive
        Zip::packNative($pathDestination);
    }
    
    /**
     * Create the Documentaiton archive
     */
    protected function _packDocumentation() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . self::FOLDER_DOCUMENTATION . '.zip"...');
        
        // Create the archive
        Zip::packNative($this->_getPathDocs());
    }
    
    /**
     * Create the theme archive and store it appropriately
     */
    protected function _packTheme() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . Tasks_1NewProject::$destDir . '" child theme archive...');
        
        // Prepare the child theme path
        $childThemePath = IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . Tasks_1NewProject::$destDir . '-child';
        
        // Create the child theme
        (new WordPress_ChildTheme())->save(
            $childThemePath, 
            IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . Tasks_1NewProject::$destDir . '/' . WordPress::FILE_SCREENSHOT
        );
        
        // Pack the child theme archive
        Zip::packNative($childThemePath);
        
        // Inform the user
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . Tasks_1NewProject::$destDir . '" theme archive...');
        
        // Create the theme archive
        Zip::packNative(IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . Tasks_1NewProject::$destDir);
    }

}

/*EOF*/