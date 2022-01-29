<?php
/**
 * Theme Warlock - Script
 * 
 * @title      JS/CSS Script bundles
 * @desc       Holds the list of available JS/CSS bundles located in frameworks/scripts
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Script {

    /**
     * Script's version file name
     */
    const FILE_VERSION = 'version.txt';
    
    /**
     * Script names
     */
    const SCRIPT_SHIV             = 'shiv';
    const SCRIPT_FONTELLO         = 'fontello';
    const SCRIPT_PARTICLES        = 'particles';
    const SCRIPT_JQUERY_STORYLINE = 'jquery-storyline';
    const SCRIPT_JQUERY_UI        = 'jquery-ui';
    const SCRIPT_JQUERY_ISOTOPE   = 'jquery-isotope';
    const SCRIPT_JQUERY_VALIDATE  = 'jquery-validate';
    
    /**
     * Common conditionals
     */
    const CONDITIONAL_404 = 'is_404()';
    
    /**
     * Current script name
     * 
     * @var string
     */
    protected $_scriptName = null;
    
    /**
     * Conditional PHP statement associated with this script
     * 
     * @var type 
     */
    protected $_conditional = null;
    
    /**
     * Available scripts
     * 
     * @var string[]
     */
    protected static $_scripts = array();
    
    /**
     * Available conditionals
     * 
     * @var string[]
     */
    protected static $_conditionals = array();
    
    /**
     * Scripts
     * 
     * @param string $scriptName              Script name
     * @param string $conditionalPHPStatement Conditional PHP statement such as 
     * <b>"is_page_template('about.php')"</b>. Must be valid PHP Code!
     */
    public function __construct($scriptName, $conditionalPHPStatement = null) {
        // Scripts not defined
        if (!count(self::$_scripts)) {
            $reflection = new ReflectionClass($this);
            foreach ($reflection->getConstants() as $constantName => $constantValue) {
                // Script name
                if (preg_match('%^SCRIPT_%', $constantName)) {
                    self::$_scripts[] = $constantValue;
                    continue;
                }
                
                // Script conditional
                if (preg_match('%^CONDITIONAL_%', $constantName)) {
                    self::$_conditionals[] = $constantValue;
                    continue;
                }
            }
        }
        
        // Valid script name
        if (in_array($scriptName, self::$_scripts)) {
            $this->_scriptName = $scriptName;
        }
        
        // Conditional provided
        if (null !== $conditionalPHPStatement && in_array($conditionalPHPStatement, self::$_conditionals)) {
            $this->_conditional = $conditionalPHPStatement;
        }
    }
    
    /**
     * Automatic string conversion
     * 
     * @return string
     */
    public function __toString() {
        return $this->_scriptName;
    }
    
    /**
     * Get the script name
     * 
     * @return string
     */
    public function getName() {
        return $this->_scriptName;
    }
    
    /**
     * Get the conditional PHP statement associated with this script
     * 
     * @return string|null
     */
    public function getConditionalPHPStatement() {
        return $this->_conditional;
    }
}

/* EOF */
