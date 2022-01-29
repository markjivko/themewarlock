<?php
/**
 * Theme Warlock - Plugin
 * 
 * @title      Plugin instance
 * @desc       Describe the functionality of each plugin
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

abstract class Plugin {

    /**
     * Use a "st" prefix for this plugin
     */
    const USE_PREFIX = true;
    
    const PLUGIN_RPG = 'rpg';
    const PLUGIN_CONTENT_TYPE_WIDGET_BLOCK = 'content-type-widget-block';
    const PLUGIN_REVSLIDER = 'revslider';
    const PLUGIN_JS_COMPOSER = 'js_composer';
    const PLUGIN_THEME_CHECK = 'theme-check';
    
    const PLUGIN_VC_CATEGORY_GRID = 'vc-category-grid';
    const PLUGIN_VC_SOCIAL_AREA = 'vc-social-area';
    const PLUGIN_VC_CONTACT_SLIDER = 'vc-contact-slider';
    const PLUGIN_VC_VC_CONTACT_SLIDER = 'vc-vc-contact-slider';
    const PLUGIN_VC_BUNDLE = 'vc-bundle';
    const PLUGIN_VC_TWITTER = 'vc-twitter';
    const PLUGIN_VC_SWIPE_BLOCKS = 'vc-swipe-blocks';
    const PLUGIN_VC_TESTIMONIALS = 'vc-testimonials';
    const PLUGIN_VC_PRICING_TABLE = 'vc-pricing-table';
    const PLUGIN_VC_BADGE_WALL = 'vc-badge-wall';
    const PLUGIN_VC_BLOG_POSTS = 'vc-blog-posts';
    const PLUGIN_VC_CALL_TO_ACTION = 'vc-call-to-action';
    
    const FOLDER_JS  = Model_Project_Config_Item_Code::EXT_JS;
    const FOLDER_CSS = Model_Project_Config_Item_Code::EXT_CSS;
    const FOLDER_IMG = 'img';
    const FILE_MAIN  = 'main';
    
    /**
     * Current Bootstrap framework version
     * 
     * @var string
     */
    protected $_bootstrapVersion = '4.1.3';
    
    /**
     * Plugin name
     *
     * @var string
     */
    protected $_pluginName = '';
    
    /**
     * Plugin slug
     * 
     * @var string
     */
    protected $_pluginSlug = '';
    
    /**
     * Plugin variable name (PHP-compliant variable name)
     *
     * @var string
     */
    protected $_pluginNameVar = '';
    
    /**
     * Plugin class name
     *
     * @var string
     */
    protected $_pluginNameClass = '';
    
    /**
     * Parent Add-on instance
     * 
     * @var Addon
     */
    protected $_addonInstance = null;

    /**
     * Plugin
     * 
     * @param string $pluginName    Plugin's name
     * @param Addon  $addonInstance Plugin's parent Add-on instance
     */
    public function __construct($pluginName, $addonInstance) {
        $this->_pluginName      = $pluginName;
        $this->_pluginSlug      = static::USE_PREFIX ? ('st-' . $this->_pluginName) : $this->_pluginName;
        $this->_pluginNameVar   = preg_replace('%\W+%', '_', $this->_pluginSlug);
        $this->_pluginNameClass = preg_replace('% %', '', ucwords(preg_replace('%\W+%', ' ', $this->_pluginSlug)));
        $this->_addonInstance   = $addonInstance;
    }
    
    /**
     * Get the plugin's current version
     */
    abstract public function getVersion();
    
    /**
     * Get this plugin's parent Add-on instance
     * 
     * @return Addon
     */
    public function getParentAddon() {
        return $this->_addonInstance;
    }
    
    /**
     * Get this plugin's name
     * 
     * @return string
     */
    public function getName() {
        return $this->_pluginName;
    }
    
    /**
     * Get this plugin's slug
     * 
     * @return string
     */
    public function getSlug() {
        return $this->_pluginSlug;
    }
    
    /**
     * Get the source path to this plugin (in Framework::FOLDER_NAME/plugins); no trailing slashes
     * 
     * @return string
     */
    public function getSourcePath() {
        return ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_PLUGINS. '/' . $this->_pluginName;
    }
    
    /**
     * Get the PHP-compliant variable name for this plugin
     * 
     * @return string
     */
    public function getNameVar() {
        return $this->_pluginNameVar;
    }
    
    /**
     * Get the camel-cased version of the plugin - used for class names
     * 
     * @return string
     */
    public function getNameClass() {
        return $this->_pluginNameClass;
    }
    
    /**
     * Get the current version of the Bootstrap framework in use
     * 
     * @return string
     */
    public function getBootstrapVersion() {
        return $this->_bootstrapVersion;
    }
    
    /**
     * Get the needed script packages (CSS, JS and IMG) that are bundled with this WordPress plugin.<br/>
     * Must be defined in Framework::FOLDER_NAME/scripts or a URL.<br/>
     * Use the <b>{utils.common.enqueueScripts.plugin-name}</b> method to enqueue the scripts following "add_action('wp_enqueue_scripts', ...)".
     * 
     * @return string[]
     */
    public function getScripts() {
        return array();
    }

    /**
     * Whether to use a "st" prefix for this plugin
     * 
     * @return boolean
     */
    public function usePrefix() {
        return static::USE_PREFIX;
    }
    
}

/* EOF */