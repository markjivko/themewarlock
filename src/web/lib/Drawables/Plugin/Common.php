<?php
/**
 * Theme Warlock - Drawables_Plugin_Common
 * 
 * @title      Common Plugin Drawable manipulations
 * @desc       Store common methods used when managing the drawables in plugins
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Drawables_Plugin_Common {

    /**
     * Singleton instances of Drawables_Plugin_Common
     * 
     * @var Drawables_Plugin_Common[]
     */
    protected static $_instances = array();

    /**
     * Plugin instance
     * 
     * @var Plugin
     */
    protected $_plugin = null;
    
    /**
     * Final path to the plugin in the theme, before archiving
     * 
     * @var string
     */
    protected $_pluginPath = null;

    /**
     * Common Plugin Drawables
     * 
     * @param Plugin $plugin     Plugin instance
     * @param string $pluginPath Final path to the plugin in the theme, before archiving
     */
    protected function __construct(Plugin $plugin, $pluginPath) {
        $this->_plugin = $plugin;
        $this->_pluginPath = $pluginPath;
    }
    
    /**
     * 
     * @param Plugin $plugin     Plugin Instance
     * @param string $pluginPath Final path to the plugin in the theme, before archiving
     * @return type
     */
    public static function getInstance(Plugin $plugin, $pluginPath) {
        // Prepare the instance key
        $instanceKey = $plugin->getSlug() . '/' . $plugin->getName();
        
        // Instance not initialized
        if (!isset(self::$_instances[$instanceKey])) {
            self::$_instances[$instanceKey] = new self($plugin, $pluginPath);
        }
        
        // Singleton instance
        return self::$_instances[$instanceKey];
    }
    
    /**
     * Get the associated Plugin instance
     * 
     * @return Plugin
     */
    public function getPlugin() {
        return $this->_plugin;
    }
    
    /**
     * Get the final path to the plugin in the theme, before archiving
     * 
     * @return string
     */
    public function getPluginPath() {
        return $this->_pluginPath;
    }
    
    /**
     * Replace the image in "{pluginPath}/<b>$relativePath</b>" ONLY if it exists (does not add a new file)
     * 
     * @param Model_Project_Config_Item_Image $imageItem    Image item
     * @param string                          $relativePath Path relative to the plugin destination; ex.: "img/backgorund.jpg"
     * @return Drawables_Plugin_Common
     */
    public function replaceImageFile(Model_Project_Config_Item_Image $imageItem, $relativePath) {
        // Get the image path
        $defaultImagePath = $imageItem->getPath();
        
        // Valid file
        if (strlen($defaultImagePath) && is_file($defaultImagePath)) {
            // Replace the file
            if (strlen($relativePath) && is_file($pluginImagePath = $this->_pluginPath . '/' . $relativePath)) {
                copy($defaultImagePath, $pluginImagePath);
            }
        }
        
        // All done
        return $this;
    }

    /**
     * Create a 256x256 icon for WPBakery Page Builder (<b>$iconItem</b> is null) or replace the current icon with the one provided - ONLY if an icon was already defined in "{pluginPath}/vc-elements/icon.png"
     * 
     * @param Model_Project_Config_Item_Image $iconItem (optional) Icon
     * @return Drawables_Plugin_Common
     */
    public function createIcon(Model_Project_Config_Item_Image $iconItem = null) {
        // Plugin icon found
        if (is_file($pluginIconPath = $this->_pluginPath . '/vc-elements/icon.png')) {
            // Get the icon path
            $iconPath = (null === $iconItem ? '' : $iconItem->getPath());

            // Valid file
            if (strlen($iconPath) && is_file($iconPath)) {
                copy($iconPath, $pluginIconPath);
            } else {
                // Get the plugin's default icon path; skip this procedure in staging mode
                if (is_file($defaultIconPath = $this->_plugin->getSourcePath() . '/vc-elements/icon.png')) {
                    // Get the project data
                    $projectData = Tasks::$project->getConfig()->getProjectData();

                    /* @var $projectIconItem Model_Project_Config_Item_Image */
                    $projectIconItem = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_ICON];
                    
                    // Get the logo path
                    $logoPath = $projectIconItem->getPath();
                    
                    // Valid logo defined
                    if (strlen($logoPath) && is_file($logoPath)) {
                        // Set the gradient colors
                        $colorA = '#FFFFFF';
                        $colorB = '#F5F5F5';

                        // Set the logo padding and circle rim
                        $logoPadding = 10;
                        $circleRim = 5;

                        // Get the canvas width
                        list($canvasWidth) = getimagesize($defaultIconPath);

                        // Prepare the new logo width
                        $logoWidth = intval($canvasWidth * 0.5);

                        // Calculate the circle arguments
                        $circleCenter = $logoWidth / 2 + $circleRim;
                        $circleDiameter = $circleCenter * 2;
                        $circleOffset = $canvasWidth - $circleDiameter - $logoPadding + $circleRim;

                        // Prepare the command
                        $command = 'convert ' . 
                            escapeshellarg($defaultIconPath) . ' ' .
                            '\( ' .
                                "-size ${circleDiameter}x${circleDiameter} gradient:'$colorA'-'$colorB' " . 

                                '\( ' .
                                    '+clone -fill Black -colorize 100 ' .
                                    "-fill White " . 
                                    "-draw 'circle $circleCenter,$circleCenter $circleCenter,0' " . 
                                '\) ' . 
                                '-alpha off ' . 
                                '-compose CopyOpacity -composite ' . 
                                "-geometry +${circleOffset}+${circleOffset} " . 
                            '\) ' .
                            '-compose over -composite ' . 
                            '\( ' . 
                                escapeshellarg($logoPath) . ' ' . 
                                '-background none ' . 
                                '-resize ' . $logoWidth . 'x' . $logoWidth . '! ' . 
                                '-extent ' . $canvasWidth . 'x' . $canvasWidth . ' ' . 
                                '-page +' . ($canvasWidth - $logoWidth - $logoPadding) . '+' . ($canvasWidth - $logoWidth - $logoPadding) . ' ' . 
                                '-flatten ' .
                            '\) ' . 
                            '-compose over -composite ' .
                            escapeshellarg($pluginIconPath);

                        // Log the command
                        Log::check(Log::LEVEL_DEBUG) && Log::debug($command);

                        // Generate the new logo
                        passthru($command);
                    }
                }
            }
        }
        
        // All done
        return $this;
    }
}

/* EOF */