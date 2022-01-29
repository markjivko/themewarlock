<?php
/**
 * Theme Warlock - WebView
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WebView extends View {
    
    // PlaceHolders
    const PH_TITLE  = 'title';
    const PH_HEAD   = 'head';
    const PH_MENU   = 'menu';
    const PH_NAVBAR = 'navbar';
    
    /**
     * Git version
     * 
     * @var string
     */
    protected static $_scriptVersion = null;
    
    /**
     * Set the menu placeholder
     */
    public function __construct() {
        // Not authenticated yet
        if (null == Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL)) {
            return;
        }
        
        // Get the menu
        $menu = WebMenu::getInstance()->getMenu();
        
        // Go through the menu
        foreach ($menu as $menuController) {
            if ($menuController[WebMenu::CONTROLLER_CURRENT]) {
                foreach ($menuController[WebMenu::CONTROLLER_ITEMS] as $menuItem) {
                    if ($menuItem[WebMenu::ITEM_CURRENT]) {
                        // Set the title
                        $this->setTitle($menuController[WebMenu::CONTROLLER_NAME] . ' - ' .$menuItem[WebMenu::ITEM_NAME]);
                        break 2;
                    }
                }
            }
        }
        
        // Set the placeholder
        $this->setPlaceholder(self::PH_MENU, $menu);
        
        // Display the navbar part
        echo $this->getPart(self::PH_NAVBAR);
    }
    
    /**
     * Set the title
     * 
     * @param string $title Document title
     * @return null
     */
    public function setTitle($title) {
        $this->setPlaceholder(self::PH_TITLE, $title);
    }
    
    /**
     * Get the title
     * 
     * @return string|null Document title
     */
    public function getTitle() {
        return $this->getPlaceholder(self::PH_TITLE);
    }
    
    /**
     * Set the head
     * 
     * @param string $head Document head
     * @return null
     */
    public function setHead($head) {
        return $this->setPlaceholder(self::PH_HEAD, $head);
    }
    
    /**
     * Get the head
     * 
     * @return string|null Document head
     */
    public function getHead() {
        return $this->getPlaceholder(self::PH_HEAD);
    }
    
    /**
     * Git version
     * 
     * @return string
     */
    public static function getScriptVersion() {
        if (null == self::$_scriptVersion) {
            self::$_scriptVersion = substr(Git::getRevisionLocal(), 0, 8);
        }
        return self::$_scriptVersion;
    }
    
    /**
     * Append a JS script
     * 
     * @param string $jsScriptName JavaScript script name, without the .js suffix
     */
    public function addJs($jsScriptName) {
        // Get the head placeholder
        $head = $this->getHead();
        
        // Append the JS
        $head .= '<script src="/js/' . $jsScriptName . '.js?version=' . self::getScriptVersion() . '"></script>' . PHP_EOL;
        
        // Save the placeholder
        $this->setHead($head);
    }
    
    /**
     * Append a CSS script
     * 
     * @param string $cssScriptName CSS script name, without the .css suffix
     */
    public function addCss($cssScriptName) {
        // Get the head placeholder
        $head = $this->getHead();
        
        // Append the JS
        $head .= '<link rel="stylesheet" type="text/css" href="/css/' . $cssScriptName . '.css?version=' . self::getScriptVersion() . '" />' . PHP_EOL;
        
        // Save the placeholder
        $this->setHead($head);
    }
    
}

/*EOF*/