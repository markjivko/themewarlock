<?php
/**
 * Theme Warlock - Addon_Core404
 * 
 * @title      404 Page
 * @desc       Display custom 404 (not found) pages
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addon_Core404 extends Addon {
    
    /**
     * Custom icon
     * 
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_TREE_CONIFER;
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public static function getOptions() {
        return array();
    }   
    
    /**
     * Get the available plugins
     * 
     * @return string[]
     */
    public function getPlugins() {
        return array();
    }
    
    /**
     * <p>Get the needed script packages (CSS, JS and IMG) that are bundled with this add-on<br/>
     * Must be defined in frameworks/scripts or a URL<br/>
     * <b>Warning</b>: If any of these scripts are required by WordPress plugins, please reference them in their corresponding go.php file!</p>
     * 
     * @return string[]|Script[]
     */
    public function getScripts() {
        // Prepare the result
        $result = array();
        
        // Get the flavor
        if(isset($this->addonData[Model_Project_Config_Item::KEY_FLAVOR])) {
            $flavorValue = $this->addonData[Model_Project_Config_Item::KEY_FLAVOR]->getValue();
            
            // Prepare the scripts
            switch ($flavorValue) {
                case Addons::FLAVOR_NAME_DEFAULT:
                    $result[] = new Script(
                        Script::SCRIPT_PARTICLES, 
                        Script::CONDITIONAL_404
                    );
                break;
            }
        }
        
        // All done
        return $result;
    }
}
    
/* EOF */