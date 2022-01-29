<?php
/**
 * Theme Warlock - WordPress_Docs
 * 
 * @title      Documentation Generator
 * @desc       Generate the documentation for the current theme
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Docs {

    /**
     * Designated folder name inside the "go" folder
     */
    const FOLDER_NAME     = '_docs';
    
    // Section details
    const SECTION_TITLE       = 'title';
    const SECTION_DESCRIPTION = 'description';
    const SECTION_CONTENT     = 'content';
    
    // Snapshot details
    const SNAPSHOT_TITLE       = 'title';
    const SNAPSHOT_DESCRIPTION = 'description';
    const SNAPSHOT_URL         = 'url';
    
    /**
     * Singleton instance of WordPress_Docs
     * 
     * @var WordPress_Docs
     */
    protected static $_instance = null;
    
    /**
     * Activated addons
     * 
     * @var array of Model_Project_Config_Item[]
     */
    protected $_addons = array();
    
    /**
     * Cache the results to calls of "getSections"
     * 
     * @var array
     */
    protected $_sectionsCache = array();
    
    /**
     * Snapshots cache
     * 
     * @var array
     */
    protected $_snapshotsCache = null;
    
    /**
     * Screenshot instance
     *
     * @var Screenshot
     */
    protected $_screenshot = null;
    
    /**
     * ImageMagick instance
     * 
     * @var ImageMagick
     */
    protected $_imageMagick = null;
    
    /**
     * Singleton
     */
    protected function __construct() {
        // Screenshot instance
        $this->_screenshot = new Screenshot();
        
        // ImageMagick instance
        $this->_imageMagick = new ImageMagick();
    }
    
    /**
     * Singleton instance of WordPress_Docs
     * 
     * @return WordPress_Docs
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Append the addon configuration
     * 
     * @param string                      $addonName Addon Name
     * @param Model_Project_Config_Item[] $addonData (optional) Addon Data; default <b>null</b>
     * @return WordPress_Docs
     */
    public function appendAddon($addonName, $addonData = null) {
        // Addon data not defined
        if (null === $addonData) {
            $addonData = Addons::getConfig($addonName, null, IO::inputPath());
        }
        
        // Store the data
        $this->_addons[$addonName] = $addonData[$addonName];
        return $this;
    }
    
    /**
     * Get the list of available snapshots
     * 
     * @return array Associative array of <br/>
     * <b>Addon Name</b> => array(<ul>
     *      <li><b>WordPress_Docs::SNAPSHOT_TITLE</b>       => Title</li>
     *      <li><b>WordPress_Docs::SNAPSHOT_DESCRIPTION</b> => Description</li>
     *      <li><b>WordPress_Docs::SNAPSHOT_URL</b>         => URL</li>
     * </ul>)
     */
    public function getSnapshots() {
        // Cache miss
        if (null === $this->_snapshotsCache) {
            // Initialize the cache
            $this->_snapshotsCache = array();

            // Prepare the snapshots
            $snapshots = WordPress_Snapshots::getInstance();
            
            // Go through all the snapshots
            foreach ($snapshots->getAll() as /*@var $snapshot WordPress_Snapshots_Snapshot */$snapshot) {
                try {
                    // Get the site suffix
                    $siteSuffix = WordPress_Publisher_Remote::computeSiteSlugSuffix($snapshot);
                    
                    // Get the site slug
                    $siteSlug = strlen($siteSuffix) ? ('-' . $siteSuffix) : '';

                    // Store the data
                    $this->_snapshotsCache[$snapshot->getId()] = array(
                        self::SNAPSHOT_TITLE       => strlen($snapshot->getTitle()) ? $snapshot->getTitle() : ('Snapshot #' . $snapshot->getId()),
                        self::SNAPSHOT_DESCRIPTION => $snapshot->getDescription(),
                        self::SNAPSHOT_URL         => Config::get()->authorThemesUrl . '/' . Tasks_1NewProject::$destDir . $siteSlug,
                    );
                } catch (Exception $exc) {
                    Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                }
            }
        }
        
        // All done
        return $this->_snapshotsCache;
    }
    
    /**
     * Get the list of Documentation sections (addon-specific docs)
     * 
     * @return array Associative array of <br/>
     * <b>Addon Name</b> => array(<ul>
     *      <li><b>WordPress_Docs::SECTION_TITLE</b>       => Title</li>
     *      <li><b>WordPress_Docs::SECTION_DESCRIPTION</b> => Description</li>
     *      <li><b>WordPress_Docs::SECTION_CONTENT</b>     => Content</li>
     * </ul>)
     */
    public function getSections() {
        // Prepare the cache key
        $cacheKey = implode(',', array_keys($this->_addons));
        
        // Cache miss
        if (!isset($this->_sectionsCache[$cacheKey])) {
            // Prepare the sections
            $this->_sectionsCache[$cacheKey] = array();

            // Go through the addons
            foreach ($this->_addons as $addonName => /*@var $addonData Model_Project_Config_Item[]*/ $addonData) {
                // readme.md file defined
                if (is_file($readMePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName . '/' . Addons::GO_FOLDER_NAME . '/' . self::FOLDER_NAME . '/readme.md')) {
                    // Get the readme
                    $contents = file_get_contents($readMePath);

                    // Parse the data keys for this addon
                    $contents = Addons::getInstance()->parseDataKeys($contents, $addonName);
                    
                    // Replace the images paths
                    $contents = preg_replace('%\(img\/%', '(src/img/' . $addonName . '/', $contents);

                    // Parse the text
                    $contents = Parsedown::instance()->text($contents);
                    
                    // Increment the headings
                    $contents = preg_replace_callback('%<(\/?)\s*h(\d)\s*>%', function($item){
                        // Prepare the headhing
                        $heading = intval($item[2]) + 3;
                        
                        // Out of bounds
                        if ($heading > 6) {
                            $heading = 6;
                        }
                        
                        // Replace the HTML tag
                        return '<' . $item[1] . 'h' . ($heading) . '>';
                    }, $contents);
                    
                    // Wrap the images in cards
                    $contents = preg_replace_callback('%<img.*?alt="(.*?)".*?>%ims', function($item){
                        return '<div class="row justify-content-center">'
                            . '<div class="card card-auto">'
                                . $item[0]
                                . '<div class="card-block">'
                                    . '<p class="card-text">' . (isset($item[1]) ? $item[1] : '') .'</p>'
                                . '</div>'
                            . '</div>'
                        . '</div>';
                    }, $contents);
                    
                    // Add support for icons
                    $contents = preg_replace('%\[:([\w\-]+):\]%', '<i class="icon fa fa-${1}"></i>', $contents);
                    
                    // All links are no-follow
                    $contents = preg_replace('%<a\b(.*?)>(.*?)<\s*\/\s*a\s*>%ims', '<a target="_blank" rel="nofollow"${1}><i class="icon fa fa-link"></i> ${2}</a>', $contents);

                    // Get the icon object
                    /*@var $iconObject Model_Project_Config_Item_String*/
                    $iconObject = $this->_addons[$addonName][Model_Project_Config_Item::KEY_ICON];

                    // Append the section
                    $this->_sectionsCache[$cacheKey][$addonName] = array(
                        self::SECTION_TITLE       => $iconObject->getMetaTitle(),
                        self::SECTION_DESCRIPTION => $iconObject->getMetaDescription(),
                        self::SECTION_CONTENT     => $contents,
                    );
                }
            }
        }
        
        // All done
        return $this->_sectionsCache[$cacheKey];
    }
    
    /**
     * Save the theme documentation at the specified path, creating extra drawables
     * 
     * @param string $savePath       Documentation folder
     * @param string $screenshotPath (optional) Take a screenshot of the 
     * documentation and save it at this location; default <b>null</b>
     * @return WordPress_Docs
     */
    public function save($savePath, $screenshotPath = null) {
        // Get the ImageMagick instance
        $imageMagick = new ImageMagick();
        
        // Clean-up
        if (is_dir($savePath)) {
            Folder::clean($savePath);
        }
        
        // Store all files in SRC
        Folder::create($srcSavePath = $savePath . '/src', 0777, true);

        // Add the docs scaffolding (ignore the root index.php file)
        foreach (glob(ROOT . '/web/resources/wordpress/docs/*', GLOB_ONLYDIR) as $scaffoldDir) {
            Folder::copyContents($scaffoldDir, $srcSavePath . '/' . basename($scaffoldDir));
        }

        // Parse the keys for the main CSS and JS
        foreach (array('js/functions.js', 'css/style.css') as $relativePath) {
            // Prepare the file path
            $filePath = $srcSavePath . '/' . $relativePath;
            
            // Get the file contents
            $fileContents = file_get_contents($filePath);
            
            // Parse the contents
            $fileContents = Addons::getInstance()->parseDataKeys($fileContents, Model_Project_Config::CATEGORY_CORE);
            
            // Save the file
            file_put_contents($filePath, $fileContents);
        }
        
        // Get the project data
        $projectData = Tasks::$project->getConfig()->getProjectData();
        
        // Prepare the image directory
        Folder::create($srcSavePath . '/img', 0777, true);

        /* @var $storePreview Model_Project_Config_Item_Image */
        $storePreview = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_PREVIEW_STORE];
        
        // Create the store preview
        if (strlen($storePreview->getPath()) && is_file($storePreview->getPath())) {
            $this->_imageMagick->resizeFile($storePreview->getPath(),  $srcSavePath . '/img/preview-store.png', 600, 320);
        } else {
            copy(ROOT . '/web/resources/wordpress/preview-store.png', $srcSavePath . '/img/preview-store.png');
        }
        
        /* @var $projectIconItem Model_Project_Config_Item_Image */
        $projectIconItem = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_ICON];
        
        // Create the favicon
        $imageMagick->nudgeFile(
            strlen($projectIconItem->getPath()) && is_file($projectIconItem->getPath()) ?
            $projectIconItem->getPath() :
            ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . Model_Project_Config::CATEGORY_CORE . '/admin/img/st_icon_512.png', 
            $srcSavePath . '/img/favicon.png',
            0, 0,
            100, 100,
            100, 100
        );
        Image::optimize($srcSavePath . '/img/favicon.png');
        
        // Prepare the snapshots
        $snapshots = WordPress_Snapshots::getInstance();

        // Go through all the snapshots
        foreach ($snapshots->getAll() as /*@var $snapshot WordPress_Snapshots_Snapshot */$snapshot) {
            // Create the favicon
            $imageMagick->nudgeFile(
                $snapshot->getPreviewPath(),
                $srcSavePath . '/img/snapshot-' . $snapshot->getId() . '.png',
                0, 0,
                520, 390,
                520, 390
            );
            Image::optimize($srcSavePath . '/img/snapshot-' . $snapshot->getId() . '.png');
        }
        
        // Go through the addons
        foreach ($this->_addons as $addonName => /*@var $addonData Model_Project_Config_Item[]*/ $addonData) {
            // readme.md file defined
            if (is_file($readMePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName . '/' . Addons::GO_FOLDER_NAME . '/' . self::FOLDER_NAME . '/readme.md')) {
                // Copy the images
                if (is_dir($imagesPath = dirname($readMePath) . '/img')) {
                    Folder::copyContents($imagesPath, $srcSavePath . '/img/' . $addonName);
                }
            }
        }
        
        // Get the Docs template
        $contents = file_get_contents(ROOT . '/web/resources/wordpress/docs/index.html');
        
        // Parse the keys
        $contents = Addons::getInstance()->parseDataKeys($contents, Model_Project_Config::CATEGORY_CORE);
        
        // Save the index
        file_put_contents($savePath . '/index.html', $contents);
        
        // Grab the screenshot
        if (null !== $screenshotPath) {
            if (!is_dir(dirname($screenshotPath))) {
                Folder::create(dirname($screenshotPath), 0777, true);
            }
            $this->_screenshot->grab('file://' . $savePath . '/index.html', $screenshotPath);
        }
        
        // All done
        return $this;
    }
}

/* EOF */