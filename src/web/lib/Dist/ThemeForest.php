<?php
/**
 * Theme Warlock - Dist_ThemeForest
 * 
 * @title      ThemeForest distributables
 * @desc       Tasks to execute when packing the distributables for ThemeForest
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Dist_ThemeForest extends Dist {

    /**
     * Patterns
     */
    const PATTERN_PROFILE_URL = 'https://themeforest.net/user/__X__';
    const PATTERN_LICENSE_URL = 'https://themeforest.net/licenses';
    
    /**
     * Available tasks
     */
    const TASK_PACK_THUMBNAIL               = 'packThumbnail';
    const TASK_PACK_THEME                   = 'packTheme';
    const TASK_PACK_THEME_PREVIEW           = 'packThemePreview';
    const TASK_PACK_MAIN_FILES              = 'packMainFiles';
    const TASK_PACK_MAIN_FILES_DEMO_CONTENT = 'packMainFilesDemoContent';
    
    /**
     * Files
     */
    const FILE_THUMBNAIL = 'thumbnail.png';
    
    /**
     * Folders
     */
    const FOLDER_THEME_PREVIEW    = 'Theme Preview';
    const FOLDER_MAIN_FILES       = 'Main Files';
    const FOLDER_MAIN_F_THEME     = '1. Theme';
    const FOLDER_MAIN_F_DOCS      = '2. Documentation';
    const FOLDER_MAIN_F_SNAPSHOTS = '3. Demo Content';
    const FOLDER_MAIN_F_LICENSING = 'Licensing';
    
    /**
     * Get the available Distributable tasks for the current marketplace; un-associative array<br/>
     * Must include <b>Dist::TASK_PACK_SNAPSHOTS</b>!
     * 
     * @return array
     */
    protected function _getTasks() {
        return array(
            self::TASK_PACK_THUMBNAIL,
            self::TASK_PACK_THEME,
            self::TASK_PACK_THEME_PREVIEW,
            
            self::TASK_PACK_SNAPSHOTS,
            
            self::TASK_PACK_MAIN_FILES_DEMO_CONTENT,
            self::TASK_PACK_MAIN_FILES,
        );
    }
    
    /**
     * Get the path to the documentation
     * 
     * @return string
     */
    protected function _getPathDocs() {
        return IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_MAIN_FILES . '/' . self::FOLDER_MAIN_F_DOCS;
    }

    /**
     * Get the path to the license
     * 
     * @return string
     */
    protected function _getPathLicense() {
        return IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_MAIN_FILES . '/' . self::FOLDER_MAIN_F_LICENSING;
    }
    
    /**
     * Generate the thumbnail
     */
    protected function _packThumbnail() {
        Log::check(Log::LEVEL_INFO) && Log::info('Generating thumbnail...');
        
        // Get the project data
        $projectData = Tasks::$project->getConfig()->getProjectData();

        /* @var $projectIcon Model_Project_Config_Item_Image */
        $projectIcon = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_ICON];
        
        // Get the icon path
        $iconPath = $projectIcon->getPath();
        
        // Revert to the default
        if (!strlen($iconPath) || !is_file($iconPath)) {
            $iconPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . Model_Project_Config::CATEGORY_CORE . '/admin/img/st_icon_512.png';
        }
        
        // Resize the file
        $this->_imageMagick->nudgeFile(
            $iconPath,
            IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FILE_THUMBNAIL,
            0, 0,
            80, 80
        );
    }
    
    /**
     * Create the Main Files archive
     */
    protected function _packMainFiles() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . self::FOLDER_MAIN_FILES . '" archive...');
        
        // Create the "Main Files" archive
        Zip::packNative(IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_MAIN_FILES);
    }
    
    /**
     * Create the "Demo Content" archive<br/>
     * Must be called after <b>Dist::TASK_PACK_SNAPSHOTS</b>
     */
    protected function _packMainFilesDemoContent() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . self::FOLDER_MAIN_F_SNAPSHOTS . '" archives...');
        
        // Prepare the paths
        $pathSource = IO::outputPath() . '/' . self::FOLDER_SNAPSHOTS_DEMO;
        $pathDestination = IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_MAIN_FILES . '/' . self::FOLDER_MAIN_F_SNAPSHOTS;
            
        // Move the folder
        Folder::copyContents($pathSource, $pathDestination);
        Folder::clean($pathSource, true);
    }
    
    /**
     * Generate and pack the theme preview files<br/>
     * Must be called before <b>Dist::TASK_PACK_SNAPSHOTS</b>
     */
    protected function _packThemePreview() {
        Log::check(Log::LEVEL_INFO) && Log::info('Generating "' . self::FOLDER_THEME_PREVIEW . '" images...');
        
        // Create the folder
        if (!is_dir($previewDir = IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_THEME_PREVIEW)) {
            Folder::create($previewDir, 0777, true);
        }
        
        // Get the project data
        $projectData = Tasks::$project->getConfig()->getProjectData();

        /* @var $storePreview Model_Project_Config_Item_Image */
        $storePreview = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_PREVIEW_STORE];
        
        // Prepare the first resource name
        $firstResourcePath = $previewDir . '/01_' . preg_replace('%\W+%', '_', Tasks_1NewProject::$destProjectName . ' by ' . Config::get()->authorName) . '.png';
        
        // Valid resource provided by the designer
        if (strlen($storePreview->getPath()) && is_file($storePreview->getPath())) {
            $this->_imageMagick->resizeFile($storePreview->getPath(), $firstResourcePath, 590, 300);
        } else {
            copy(ROOT . '/web/resources/wordpress/preview-store.png', $firstResourcePath);
        }
        
        // Scale the preview
        $previewWidth = 900;
        $previewHeight = intval($previewWidth * WordPress_Snapshots_Snapshot::PREVIEW_HEIGHT / WordPress_Snapshots_Snapshot::PREVIEW_WIDTH);
        
        // Go through the snapshots
        $increment = 2;
        
        // Go through the snapshots
        foreach (glob(IO::outputPath() . '/' . self::FOLDER_SNAPSHOTS . '/*/' . WordPress_Snapshots_Snapshot::PREVIEW_FILE_NAME) as $snapshotPreviewPath) {
            if (is_file($snapshotPreviewPath)) {
                // Resize the resource
                $this->_imageMagick->nudgeFile(
                    $snapshotPreviewPath, 
                    $previewDir . '/' . sprintf('%02d', $increment) . '_Snapshot_' . basename(dirname($snapshotPreviewPath)) . '.png',
                    0, 0,
                    $previewWidth, $previewHeight,
                    $previewWidth, $previewHeight
                );
                
                // Next image
                $increment++;
            }
        }
        
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . self::FOLDER_THEME_PREVIEW . '" archive...');
        
        // Create the "Theme Preview" archive
        Zip::packNative($previewDir);
    }
    
    /**
     * Create the theme archive and store it appropriately
     */
    protected function _packTheme() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . Tasks_1NewProject::$destDir . '" child theme archive...');
        
        // Prepare the folder
        if (!is_dir($mainFilesThemeDir = IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . self::FOLDER_MAIN_FILES . '/' . self::FOLDER_MAIN_F_THEME . ' - ' . Tasks_1NewProject::$destProjectName . ' by ' . Config::get()->authorName)) {
            Folder::create($mainFilesThemeDir, 0777, true);
        }
        
        // Prepare the child theme path
        $childThemePath = $mainFilesThemeDir . '/' . Tasks_1NewProject::$destDir . '-child';
        
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
        
        // Copy the archive
        copy(
            IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . Tasks_1NewProject::$destDir . '.zip', 
            $mainFilesThemeDir . '/' . Tasks_1NewProject::$destDir . '.zip'
        );
    }

}

/*EOF*/