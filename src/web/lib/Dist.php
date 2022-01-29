<?php
/**
 * Theme Warlock - Dist
 * 
 * @title      Exported resources store
 * @desc       Define the folder structure for the "dist" folder
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

abstract class Dist {

    /**
     * Patterns
     */
    const PATTERN_PROFILE_URL = '__X__';
    const PATTERN_LICENSE_URL = '';
    
    /**
     * Marketplaces
     */
    const MARKET_THEMEFOREST     = 'ThemeForest';
    const MARKET_MOJO_THEMES     = 'MojoThemes';
//    const MARKET_CREATIVE_MARKET = 'CreativeMarket';

    /**
     * Available tasks
     */
    const TASK_STORE_THEME      = 'storeTheme';
    const TASK_GENERATE_DOCS    = 'generateDocs';
    const TASK_OPTIMIZE_IMAGES  = 'optimizeImages';
    const TASK_PACK_EXPORT      = 'packExport';
    const TASK_PACK_SNAPSHOTS   = 'packSnapshots';
    const TASK_PACK_UPLOADABLES = 'packUploadables';
    
    /**
     * Download Keys
     */
    const DOWNLOAD_KEY_MARKETPLACE  = "marketplace";
    const DOWNLOAD_KEY_EXPORT       = "export";
    const DOWNLOAD_KEY_DOCS         = "docs";
    
    /**
     * Files
     */
    const FILE_DOCS_SCREENSHOT = 'screenshot.png';
    
    /**
     * Folders
     */
    const FOLDER_DOCS             = 'docs';
    const FOLDER_SNAPSHOTS        = 'snapshots';
    const FOLDER_SNAPSHOTS_DEMO   = 'snapshots-demo';
    const FOLDER_EXPORT           = 'export';
    const FOLDER_EXPORT_CONFIG    = 'config';
    const FOLDER_MARKETPLACE      = 'marketplace';
    
    /**
     * ImageMagick helper
     * 
     * @var ImageMagick
     */
    protected $_imageMagick = null;
    
    /**
     * Singleton instances
     */
    protected static $_instances = array();
    
    /**
     * Available marketplaces IDs
     * 
     * @var array Associative array
     */
    protected static $_marketplacesIds = array();
    
    /**
     * Distributable
     */
    protected function __construct() {
        // Get the ImageMagick instance
        if (null === $this->_imageMagick) {
            $this->_imageMagick = new ImageMagick();
        }
    }
    
    /**
     * Get a distributable instance for the desired marketplace.<br/>
     * If the marketplace is not available, fall back to Dist::MARKET_THEMEFOREST
     * 
     * @param string $marketPlace Marketplace, one of <b>Dist::MARKET_*</b>
     * @return Dist
     */
    public static function getInstance($marketPlace = self::MARKET_THEMEFOREST) {
        // Cache miss
        if (!isset(self::$_instances[$marketPlace])) {
            // Prepare the class name
            $className = 'Dist_' . $marketPlace;
            
            // Class not found
            if (!class_exists($className)) {
                $className = 'Dist_' . self::MARKET_THEMEFOREST;
            }
            
            // Get the instance
            self::$_instances[$marketPlace] = new $className();
        }
        
        // All done
        return self::$_instances[$marketPlace];
    }
    
    /**
     * Get the defined marketplace IDs
     * 
     * @example array('ThemeForest' => 'User')
     * @return array Associative array of {marketplace} => {id}
     */
    public static function getMarketplacesIds() {
        // Cache miss
        if (!count(self::$_marketplacesIds)) {
            // Get the config items
            $authorMarketIds = array_filter(array_map('trim', explode(',', Config::get()->authorMarketIds)));

            // Get the key-value pairs
            $authorMarkets = array();
            foreach ($authorMarketIds as $authorMarketId) {
                // Get the key-value pair
                $keyValue = array_values(array_filter(array_map('trim', explode(':', $authorMarketId))));

                // Valid entry
                if (2 === count($keyValue)) {
                    $authorMarkets[$keyValue[0]] = $keyValue[1];
                }
            }
            
            // Fitler the available marketplaces
            $marketPlaces = array_values(
                array_filter(
                    (new ReflectionClass(__CLASS__))->getConstants(), 
                    function($key) {
                        return preg_match('%^MARKET_%', $key);
                    }, 
                    ARRAY_FILTER_USE_KEY
                )
            );

            // Go through the available marketplaces
            foreach ($marketPlaces as $marketPlaceName) {
                // Get the ID
                $marketPlaceId = isset($authorMarkets[$marketPlaceName]) ? $authorMarkets[$marketPlaceName] : Config::get()->authorName;

                // Store it
                self::$_marketplacesIds[$marketPlaceName] = $marketPlaceId;
            }
        }
        
        return self::$_marketplacesIds;
    }
    
    /**
     * Get the available Distributable tasks for the current marketplace; un-associative array<br/>
     * Must include <b>Dist::TASK_PACK_SNAPSHOTS</b>!
     * 
     * @return array
     */
    protected abstract function _getTasks();
    
    /**
     * Get the path to copy the generated documentation in
     * 
     * @return string
     */
    protected abstract function _getPathDocs();
    
    /**
     * Get the path to store the license in
     * 
     * @return string
     */
    protected abstract function _getPathLicense();
    
    /**
     * Get the MarketPlace name
     * 
     * @return string
     */
    public static function getName() {
        return preg_replace('%^Dist_%', '', get_called_class());
    }
    
    /**
     * Get the profile URL for the current marketplace
     * 
     * @return string
     */
    public function getProfileUrl() {
        return $this->_getPattern(static::PATTERN_PROFILE_URL);
    }
    
    /**
     * Get the Licenses URL for the current marketplace
     * 
     * @return string
     */
    public function getLicenseUrl() {
        return $this->_getPattern(static::PATTERN_LICENSE_URL);
    }
    
    /**
     * Get a parsed pattern by name for the current marketplace
     * 
     * @param string $pattern Pattern name
     * @return string
     */
    protected function _getPattern($pattern) {
        // Get the available MarketPlace IDs
        $marketPlaceIds = self::getMarketplacesIds();
        
        // Get the current marketplace ID
        $marketPlaceId = isset($marketPlaceIds[self::getName()]) ? $marketPlaceIds[self::getName()] : Config::get()->authorName;
        
        // Replace the __X__
        return str_replace('__X__', $marketPlaceId, $pattern);
    }
    
    /**
     * Get the available Distributable tasks for the current marketplace
     * 
     * @return array Associative array of {task name} => {complete percentage}
     */
    public function getTasks() {
        // Get this marketplace's tasks
        $marketplaceTasks = $this->_getTasks();
        
        // Sanitize
        if (!is_array($marketplaceTasks)) {
            $marketplaceTasks = array();
        }
        
        // The snapshots task is mandatory
        if (!in_array(self::TASK_PACK_SNAPSHOTS, $marketplaceTasks)) {
            $marketplaceTasks[] = self::TASK_PACK_SNAPSHOTS;
        }
        
        // Un-associative array
        $marketplaceTasks = array_values($marketplaceTasks);
        
        // Prepare the result
        $result = array(
            self::TASK_STORE_THEME     => 5,
            self::TASK_GENERATE_DOCS   => 10,
            self::TASK_OPTIMIZE_IMAGES => 20,
            self::TASK_PACK_EXPORT     => 30,
        );
        
        // Append the extra tasks
        foreach ($marketplaceTasks as $taskKey => $taskName) {
            // Store the task name and adjusted percentage
            $result[$taskName] = intval(30 + ($taskKey + 1) / count($marketplaceTasks) * 60);
        }
        
        // Add the final task
        $result[self::TASK_PACK_UPLOADABLES] = 100;
        
        // All done
        return $result;
    }
    
    /**
     * Run a task for the current marketplace
     * 
     * @param string $taskName Task to execute
     * @return mixed
     */
    public function runTask($taskName) {
        $methodName = '_' . $taskName;
        if (method_exists($this, $methodName)) {
            return call_user_func(array($this, $methodName));
        }
        
        // No method found
        return null;
    }
    
    /**
     * Store the theme - before image optimization
     */
    protected function _storeTheme() {
        Log::check(Log::LEVEL_INFO) && Log::info('Storing theme "' . Tasks_1NewProject::$destDir . '"\'s source code...');
        
        // Copy the current project source code to the corresponding "dist" folder
        Folder::copyContents(Tasks_1NewProject::getPath(), IO::outputPath() . '/' . self::FOLDER_MARKETPLACE . '/' . Tasks_1NewProject::$destDir);
        
        // Store the theme locally for the WordPress_Publisher_Remote tasks
        Folder::copyContents(Tasks_1NewProject::getPath(), IO::outputPath() . '/' . Tasks_1NewProject::$destDir);
    }
    
    /**
     * Optimize all image resources
     */
    protected function _optimizeImages() {
        Log::check(Log::LEVEL_INFO) && Log::info('Optimizing images...');
        
        // Go through all the generated resources
        foreach (glob(IO::outputPath() . '/*', GLOB_ONLYDIR) as $source) {
            // Do not optimize some resources
            if (in_array(basename($source), array(self::FOLDER_SNAPSHOTS))) {
                Log::check(Log::LEVEL_DEBUG) && Log::debug('- Skipped optimization of ' . basename($source));
                continue;
            }
            
            // Go through files recursively
            foreach ($iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $item) {
                if (!$item->isDir()) {
                    // Get the item path
                    $itemPath = strval($item);

                    // Get the basename once
                    $itemBasename = basename($item);

                    // Never copy Thumbs.db or unintentionally copied files
                    if ('Thumbs.db' === $itemBasename || false !== strpos($itemBasename, ' - Copy')) {
                        @unlink($item);
                        continue;
                    }

                    // Optimize the image size
                    Image::optimize($itemPath);
                }
            }
        }
    }
    
    /**
     * Generate the documentation and licensing (if needed)
     */
    protected function _generateDocs() {
        Log::check(Log::LEVEL_INFO) && Log::info('Generating the Documentation...');
        
        // Prepare the save path
        $docsArchivePath = IO::outputPath() . '/' . self::FOLDER_DOCS . '/' . Tasks_1NewProject::$destDir;
        
        // Generate the documentation
        WordPress_Docs::getInstance()->save(
            $docsArchivePath,
            // Store the screenshot
            IO::outputPath() . '/' . self::FOLDER_DOCS . '/' . self::FILE_DOCS_SCREENSHOT
        );
        
        Log::check(Log::LEVEL_INFO) && Log::info('Creating the Documentation archive...');
        
        // Store the documentation in the theme
        if (null !== $this->_getPathDocs()) {
            Folder::copyContents($docsArchivePath, $this->_getPathDocs());
        }
        
        // Create the docs archive
        Zip::packNative($docsArchivePath);
        
        // Generate the licensing
        if (null !== $this->_getPathLicense()) {
            Log::check(Log::LEVEL_INFO) && Log::info('Generating the License...');
            WordPress_License::getInstance()->save($this->_getPathLicense());
        }
    }
    
    /**
     * Create the "Export" Archive.<br/>
     * Used to restore/import projects across domains/users
     */
    protected function _packExport() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating the "' . self::FOLDER_EXPORT . '" archive...');
        
        // Create the folder
        Folder::create($exportPath = IO::outputPath() . '/' . self::FOLDER_EXPORT, 0777, true);
        
        // Store all the snapshots
        foreach (glob(IO::outputPath() . '/' . self::FOLDER_SNAPSHOTS . '/*', GLOB_ONLYDIR) as $liveSnapshotFolder) {
            Folder::copyContents(
                $liveSnapshotFolder, 
                $exportPath . '/' . basename($liveSnapshotFolder)
            );
        }
        
        // Create the config folder
        Folder::create($exportSettingsPath = $exportPath . '/' . self::FOLDER_EXPORT_CONFIG, 0777, true);
        
        // Save the options
        copy(dirname(IO::outputPath()) . '/run.csv', $exportSettingsPath . '/run.csv');
        
        // Save the disk-stored options
        if (is_dir($inputItemPath = dirname(IO::outputPath()) . '/' . Model_Project_Config_Item::FOLDER_NAME)) {
            Folder::copyContents(
                $inputItemPath, 
                $exportSettingsPath . '/' . Model_Project_Config_Item::FOLDER_NAME
            );
        }
        
        // Create the "Export" archive
        Zip::packNative($exportPath);
    }
    
    /**
     * Pack the snapshots and demo content
     */
    protected function _packSnapshots() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . self::FOLDER_SNAPSHOTS . '" archives...');
        
        // Convert original snapshots to archives
        foreach (glob(IO::outputPath() . '/' . self::FOLDER_SNAPSHOTS . '/*', GLOB_ONLYDIR) as $snapshotPath) {
            Zip::packNative($snapshotPath);
        }
        
        // Convert "Demo Content" snapshots to archives
        foreach (glob(IO::outputPath() . '/' . self::FOLDER_SNAPSHOTS_DEMO . '/*', GLOB_ONLYDIR) as $snapshotPath) {
            // Store the preview file associated with each snapshot
            if (is_file($previewPath = $snapshotPath . '/' . WordPress_Snapshots_Snapshot::PREVIEW_FILE_NAME)) {
                // Optimize the image
                Image::optimize($previewPath);
                
                // Store it for the client to easily identify each demo content archive
                copy($previewPath, dirname($snapshotPath) . '/' . basename($snapshotPath) . '.png');
            } else {
                // Invalid snapshot
                Log::check(Log::LEVEL_WARNING) && Log::warning('Invalid snapshot "' . basename($snapshotPath) . '", missing preview');
                
                // Remove it from the list
                Folder::clean($snapshotPath, true);
                
                // Moving on
                continue;
            }
            
            // Pack the archive
            Zip::packNative($snapshotPath);
        }
    }
    
    /**
     * Pack the final uploadables
     */
    protected function _packUploadables() {
        Log::check(Log::LEVEL_INFO) && Log::info('Creating "' . self::FOLDER_MARKETPLACE . '.zip" archive...');
        
        // Create the theme archive
        Zip::packNative(IO::outputPath() . '/' . Tasks_1NewProject::$destDir);
        
        // Create the archive
        Zip::packNative(IO::outputPath() . '/' . self::FOLDER_MARKETPLACE);
    }
}

/* EOF */