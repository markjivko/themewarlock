<?php
/**
 * Theme Warlock - Addon
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

abstract class Addon {

    /**
     * Add-on data
     * 
     * @var Model_Project_Config_Item[]
     */
    public $addonData = array();
    
    /**
     * Associative array of names => CSS rules; flavor-specific; used for the WordPress_Customizer methods
     * 
     * @see WordPress_Customizer
     * @var string[]
     */
    public $wpCustomizerCssRules = array();
    
    /**
     * WordPress customizer instance; remains null if the "initCustomizer" method is not defined
     * 
     * @var WordPress_Customizer 
     */
    public $wpCustomizer = null;
    
    /**
     * Plugin instances
     *
     * @var Plugin[]
     */
    public static $pluginInstances = array();
    
    /**
     * Image instance
     * 
     * @var Image
     */
    protected $_image = null;
    
    /**
     * Imagick Instance
     * 
     * @var Imagick
     */
    protected $_imagick = null;

    /**
     * ImageMagick Instance
     * 
     * @var ImageMagick
     */
    protected $_imageMagick = null;
    
    /**
     * Custom icon, default <b>Twitter_Bootstrap_GlyphIcon::GLYPH_FLASH</b>
     * 
     * @example Twitter_Bootstrap_GlyphIcon::GLYPH_FLASH
     * @see Twitter_Bootstrap_GlyphIcon
     * @var string
     */
    public static $addonIcon = Twitter_Bootstrap_GlyphIcon::GLYPH_FLASH;
    
    /**
     * <p>(optional) Custom icon symbol; default <b>empty string</b>;<br/>
     * Any UTF-8 character is supported, including Emojis*.<br/>
     * Please do not use non-printable or empty characters.</p>
     * 
     * @see https://emojipedia.org/two-hearts/
     * @example '💕'
     * @var string
     */
    public static $addonIconSymbol = '';
    
    /**
     * Safe methods; the result will not be escaped
     */
    public static $safeMethods = array(
        'customizer',
    );
    
    /**
     * Add-On constructor
     */
    public function __construct() {
        // Store the image processing instances
        $this->_image = new Image();
        $this->_imagick = new Imagick();
        $this->_imageMagick = new ImageMagick();
    }
    
    /**
     * Add-on allowed options
     * 
     * @return Model_Project_Config_Item[]
     */
    public abstract static function getOptions();
    
    /**
     * Get the corresponding WordPress Core tags
     * 
     * @return string[]
     * @see \WordPress_Tags
     */
    public function getTags() {
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
        return array();
    }
    
    /**
     * Get the WordPress Plugins that are bundled with this add-on
     * 
     * @return string[]
     */
    public function getPlugins() {
        return array();
    }
    
    /**
     * Called when a child WordPress plugin is deployed, right before being packed as a .tar archive.<br/>
     * Useful for working with drawables and other media inside plugins.
     * 
     * @param Drawables_Plugin_Common $pluginCommon WordPress plugin common instance
     */
    public function onPluginDeployment(Drawables_Plugin_Common $pluginCommon) {
        // Nothing to do here
    }
    
    /**
     * Use WP Customizer functionality
     * 
     * @param string $methodOrItem  Allowed values: <ul>
     * <li><b>'_register'</b> - Generate the PHP code that registers all WP Customizer elements</li>
     * <li><b>'_stylize'</b> - Generate the PHP code that registers all WP Customizer CSS scripts</li>
     * <li><b>'dataItem'</b> - Reference any data item as an instance of <b>WordPress_Customizer_Element_Item</b> using the <b>$elementMethod</b></li>
     * </ul>
     * @param string $elementMethod Call any method defined in the current data item; defaults to <strong>'exportVarInit'</strong> for a valid $methodOrItem except "_register" or "_stylize"
     * @return string PHP-escaped string
     */
    public function customizer($methodOrItem, $elementMethod = null) {
        // Customizer is null because the "initCustomizer" method was not defined
        if(null == $this->wpCustomizer) {
            return;
        }

        switch ($methodOrItem) {
            case '_register':
                return $this->wpCustomizer->register();
                break;
            
            case '_stylize':
                return $this->wpCustomizer->stylize();
                break;
        }
        
        // Go through the items
        foreach ($this->wpCustomizer->getElements() as $element) {
            if ($element instanceof WordPress_Customizer_Element_Item) {
                if ($methodOrItem == $element->getKey()) {
                    // Set the default element method
                    if (null == $elementMethod) {
                        $elementMethod = 'exportVarInit';
                    }
                    
                    // Check the method exists
                    if (method_exists($element, $elementMethod)) {
                        // Auto-escape all other methods
                        return preg_match('%^export%', $elementMethod) ? call_user_func(array($element, $elementMethod)) : var_export(call_user_func(array($element, $elementMethod)), true);
                    }
                }
            }
        }
    }

}

/* EOF */