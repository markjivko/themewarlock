<?php
/**
 * Theme Warlock - Addons_Utils
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addons_Utils {
    
    // Common tags
    const COMMON_TAGS_LIST      = 'tagsList';
    const COMMON_THEME_URL      = 'themeUrl';
    const COMMON_THEME_DOCS_URL = 'themeDocsUrl';
    const COMMON_THEME_NAME     = 'themeName';
    const COMMON_COPYRIGHT      = 'copyright';
    const COMMON_QUOTE          = 'quote';
    
    // Parse tags
    const PARSE_STRING          = 'string';
    const PARSE_STRING_TRIM     = 'trim';
    const PARSE_STRING_STRIP_NL = 'stripNl';
    const PARSE_STRING_MINI_JS  = 'miniJs';
    const PARSE_STRING_MINI_CSS = 'miniCss';
    const PARSE_COUNT           = 'count';
    
    /**
     * Safe methods; the result will not be escaped
     */
    public static $safeMethods = array(
        'common',
    );
    
    /**
     * Singleton instance
     * 
     * @var Addons_Utils
     */
    protected static $_instance = null;
    
    /**
     * Data tag content
     * 
     * @var string
     */
    protected $_content = '';
    
    /**
     * Current addon
     * 
     * @var string
     */
    protected $_addonName = '';
    
    /**
     * Current plugin name
     * 
     * @var string
     */
    protected $_pluginName = null;
    
    /**
     * Current plugin slug
     * 
     * @var string
     */
    protected $_pluginSlug = null;
    
    /**
     * Copyright
     * 
     * @var string
     */
    protected $_commonCopyrightResult = '';
    
    /**
     * Tags list
     * 
     * @var string
     */
    protected $_commonTagsListResult = '';
    
    /**
     * Current theme's URL
     * 
     * @var string
     */
    protected $_commonThemeUrl = '';
    
    /**
     * Current theme documentation's URL
     * 
     * @var string
     */
    protected $_commonThemeDocsUrl = '';
    
    /**
     * Current theme's Name
     * 
     * @var string
     */
    protected $_commonThemeName = '';
    
    /**
     * Current marketplace name
     * 
     * @var string
     */
    protected $_commonMarketName = '';
    
    /**
     * Current marketplace author profile URL
     * 
     * @var string
     */
    protected $_commonMarketProfileUrl = '';
    
    /**
     * Current marketplace licenses URL
     * 
     * @var string
     */
    protected $_commonMarketLicenseUrl = '';
    
    /**
     * Quote
     *
     * @var string
     */
    protected $_commonQuoteResult = null;
    
    /**
     * Singleton
     */
    protected function __construct() {}
    
    /**
     * Get a singleton instance of Addons_Utils and set the current addon's name
     * 
     * @param string $addonName  Add-on name
     * @param string $pluginName (optional) Current plugin's name
     * @return Addons_Utils
     */
    public static function getInstance($addonName, $pluginName = null, $dataContent = null) {
        // Instance not initialized
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        
        // (Re-)Set the data content
        self::$_instance->_content = $dataContent;
        
        // (Re-)Set the addon name
        self::$_instance->_addonName = (Model_Project_Config::CATEGORY_CORE != trim($addonName) ? 'addon-' : '') . trim($addonName);
        
        // (Re-)Set the plugin name
        self::$_instance->_pluginName = $pluginName;
        
        // (Re-)Set the plugin slug
        self::$_instance->_pluginSlug = self::$_instance->_pluginName;
        if (isset(Addon::$pluginInstances[self::$_instance->_pluginName]) && Addon::$pluginInstances[self::$_instance->_pluginName] instanceof Plugin) {
            self::$_instance->_pluginSlug = Addon::$pluginInstances[self::$_instance->_pluginName]->getSlug();
        }
        
        // All done
        return self::$_instance;
    }

    /**
     * Common utilities
     * 
     * @param string $utility Utility name
     * @return mixed|null Utility result or null if no utility found
     */
    public function common($utility) {
        return $this->_handleCall(__FUNCTION__, func_get_args());
    }
    
    /**
     * Parse utilities
     * 
     * @param string $utility Utility name
     * @return mixed|null Utility result or null if no utility found
     */
    public function parse($utility) {
        return $this->_handleCall(__FUNCTION__, func_get_args());
    }

    /**
     * Color utilities
     * 
     * @param string $utility Utility name
     * @return mixed|null Utility result or null if no utility found
     */
    public function color($utility) {
        return $this->_handleCall(__FUNCTION__, func_get_args());
    }
    
    /**
     * Handle the call
     * 
     * @param string $group     Call group
     * @param array  $arguments Method arguments
     * @return mixed|null Utility result or null if no utility found
     */
    protected function _handleCall($group, $arguments) {
        // Prepare the utility
        $utility = array_shift($arguments);
        
        // Prepare the method name
        $methodName = '_' . $group . ucfirst($utility);
        
        // Method found
        if (method_exists($this, $methodName)) {
            // All done
            return call_user_func_array(array($this, $methodName), $arguments);
        }
    }
    
    /**
     * Parse strings
     * 
     * {utils.parse.string.X}{/utils.parse.string.X}
     */
    protected function _parseString($stringMethod = null) {
        // Prepare the result
        $result = $this->_content;
        
        // Go through the allowed string methods
        switch ($stringMethod) {
            // Trim
            case self::PARSE_STRING_TRIM:
                $result = trim($result);
                break;
            
            // Remove return cariages and new lines, trimming the string as well
            case self::PARSE_STRING_STRIP_NL:
                $result = trim(preg_replace('% ?[\n\r]+%', ' ', $result));
                break;
            
            // Minify JS
            case self::PARSE_STRING_MINI_JS:
                if (AppMode::equals(AppMode::PRODUCTION)) {
                    $result = PHP_EOL . trim(preg_replace(
                        // Remove comments and line breaks
                        array('%(?:\/\*.*?\*\/|(?<!\:)\/\/.*?(?:[\r\n]|$)|[\r\n]+)%ims', '% {2,}%'),
                        array('', ' '),
                        $result
                    ));
                }
                break;
            
            // Minify CSS
            case self::PARSE_STRING_MINI_CSS:
                if (AppMode::equals(AppMode::PRODUCTION)) {
                    $result = trim(preg_replace(
                        // Remove comments and line breaks
                        array('%(?:\/\*.*?\*\/|[\r\n]+)%ims', '% {2,}%'),
                        array('', ' '),
                        $result
                    ));
                }
                break;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the size of an array that is returned by the $addonName->$methodName() call.
     * 
     * @param string $addonName  Addon name
     * @param string $methodName Method name
     * @return int
     */
    protected function _parseCount($addonName = null, $methodName = null) {
        // Get the addon instance
        $addonInstance = Addons_Listener::get($addonName);
        
        // Valid method
        if (null !== $addonInstance && method_exists($addonInstance, $methodName)) {
            // Get the result
            $methodResult = $addonInstance->$methodName();
            
            // Valid array
            if (is_array($methodResult)) {
                return count($methodResult);
            }
        }
        
        // Invalid result
        return 0;
    }
    
    /**
     * Prepare the PHP code that enqueues scripts either for the main theme or 
     * for an individual WordPress plugin.
     * 
     * @return string PHP code
     */
    protected function _commonEnqueueScripts() {
        // Get all the scripts
        $scripts = array(
            Plugin::FOLDER_JS => array(),
            Plugin::FOLDER_CSS => array(),
        );
        
        // Store the script conditionals
        $scriptsConditionals = array();
        
        // Prepare the data source
        $dataSource = array();

        // Plugin specified
        if (null !== $this->_pluginName) {
            do {
                if (isset(Addon::$pluginInstances[$this->_pluginName]) && Addon::$pluginInstances[$this->_pluginName] instanceof Plugin && method_exists(Addon::$pluginInstances[$this->_pluginName], Addons::METHOD_NAME_GET_SCRIPTS)) {
                    // Get the scripts
                    $dataSource = array(
                        'plugin-' . $this->_pluginName => Addon::$pluginInstances[$this->_pluginName]->getScripts()
                    );
                    
                    // Stop here
                    break;
                }
                
                // Something went wrong, nothing to show
                return '';
            } while (false);
        } else {
            $dataSource = Addons_Listener::run('scripts', Addons_Listener::TYPE_GET);
        }

        // Validate the coder's input
        if (!is_array($dataSource)) {
            $dataSource = array();
        }
        
        // Go throught the scripts
        foreach($dataSource as $addonScripts) {
            if (is_array($addonScripts) && count($addonScripts)) {
                foreach($addonScripts as $scriptInformation) {
                    // Extra script info provided
                    if ($scriptInformation instanceof Script) {
                        // Get the script name
                        $scriptName = $scriptInformation->getName();
                        
                        // Valid conditional found
                        if (null !== $scriptInformation->getConditionalPHPStatement() && null !== $scriptName) {
                            $scriptsConditionals[$scriptName] = $scriptInformation->getConditionalPHPStatement();
                        }
                    } else {
                        // Probably a valid script name string
                        $scriptName = $scriptInformation;
                    }
                    
                    // Not a valid script
                    if (!is_string($scriptName)) {
                        continue;
                    }
                    
                    // Go through the data
                    foreach (array_keys($scripts) as $fileType) {
                        // Don't queue the same script twice
                        if (isset($scripts[$fileType][$scriptName])) {
                            continue;
                        }
                        
                        // External script; supports just one file type at a time
                        if (preg_match('%^https?\:\/\/%', $scriptName)) {
                            do {
                                // CSS file OR JS file, not both
                                if ((Plugin::FOLDER_CSS == $fileType && !preg_match('%\.' . Plugin::FOLDER_CSS . '$%', $scriptName)) || (Plugin::FOLDER_CSS != $fileType && preg_match('%\.' . Plugin::FOLDER_CSS . '$%', $scriptName))) {
                                    break;
                                } 
                                
                                // Version makes no sense, as the file can change at any time
                                $scripts[$fileType][$scriptName] = null;
                            } while(false);
                        }
                        
                        // Local script
                        if (is_file(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCRIPTS . '/' . $scriptName . '/' . $fileType . '/' . Plugin::FILE_MAIN . '.' . $fileType)) {
                            // Prepare the version
                            $scriptVersion = '1.0.0';

                            // Script::FILE_VERSION found
                            if (is_file($scriptVersionFile = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_SCRIPTS . '/' . $scriptName . '/' . Script::FILE_VERSION)) {
                               // Valid version string
                                if (preg_match('%^(?:\d+\.)*\d+$%', $scriptVersionText = trim(file_get_contents($scriptVersionFile)))) {
                                    $scriptVersion = $scriptVersionText;
                                }
                            }
                            
                            // Store the script
                            $scripts[$fileType][$scriptName] = $scriptVersion;
                        }
                    }
                }
            }
        }
        
        // Prepare the result
        $result = '';
        
        // Go through the scripts
        foreach ($scripts as $fileType => $scriptInfo) {
            foreach ($scriptInfo as $scriptName => $scriptVersion) {
                // Prepare the script file path
                $scriptRelativePath = $fileType . '/' . $scriptName . '/' . Plugin::FILE_MAIN . '.' . $fileType;
                
                // Get the script path
                $scriptPathExported = preg_match('%^https?\:\/\/%', $scriptName) ? 
                    var_export($scriptName, true) : 
                    (null === $this->_pluginName ? 
                        ('St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . ' . var_export('/' . $scriptRelativePath, true)): 
                        ("plugins_url() . " . var_export('/' . (preg_match(Plugin_Bundle::getRegEx(), $this->_pluginName) ? Plugin_Bundle::getName() . '/' : '') . $this->_pluginSlug . '/' . $scriptRelativePath, true))
                    );
                
                // Get the clean script name
                $scriptNameClean = preg_match('%^https?\:\/\/%', $scriptName) ? 
                    preg_replace(array('%^https?\:\/\/%', '%\/.*?$%'), '', $scriptName): 
                    $scriptName;
                
                // Prepare the full script name
                $scriptNameFullExported = var_export(Tasks_1NewProject::$destDir . '-' . preg_replace('%\W%', '-', $scriptNameClean), true);
                
                // Prepare the exports
                $scriptVersionExported = var_export($scriptVersion, true);
                
                // Prepare the templates
                switch ($fileType) {
                    case Plugin::FOLDER_JS:
                        // Conditional
                        if (isset($scriptsConditionals[$scriptName])) {
                            $result .= 
<<<"JS"
// Enqueue the $scriptNameClean $fileType script
if ({$scriptsConditionals[$scriptName]}) {
    wp_enqueue_script(
        $scriptNameFullExported, 
        $scriptPathExported, 
        array('jquery'), 
        $scriptVersionExported
    );
}
JS;
                        } else {
                            $result .= 
<<<"JS"
// Enqueue the $scriptNameClean $fileType script
wp_enqueue_script(
    $scriptNameFullExported, 
    $scriptPathExported, 
    array('jquery'), 
    $scriptVersionExported
);
JS;
                        }
                        
                        // HTML5 Shiv
                        if ('shiv' == $scriptName) {
                            $result .= PHP_EOL . PHP_EOL . 
<<<"JS"
// IE Shiv
add_filter(implode('_', array('script', 'loader', 'tag')), function(\$tag, \$handle) {
    if ($scriptNameFullExported === \$handle) {
        \$tag = "<!--[if lt IE 9]>\$tag<![endif]-->";
    }                   
    return \$tag;
}, 10, 2 );
JS;
                        }
                        break;
                    
                    case Plugin::FOLDER_CSS:
                        // Conditional
                        if (isset($scriptsConditionals[$scriptName])) {
                            $result .= 
<<<"CSS"
// Enqueue the $scriptNameClean $fileType stylesheet
if ({$scriptsConditionals[$scriptName]}) {
    wp_enqueue_style(
        $scriptNameFullExported, 
        $scriptPathExported, 
        array(), 
        $scriptVersionExported
    );
}
CSS;
                        } else {
                            $result .= 
<<<"CSS"
// Enqueue the $scriptNameClean $fileType stylesheet
wp_enqueue_style(
    $scriptNameFullExported, 
    $scriptPathExported, 
    array(), 
    $scriptVersionExported
);
CSS;
                        }
                        break;
                }
                
                // New line
                $result .= PHP_EOL . PHP_EOL;
            }
        }
        
        // Trim the result
        return trim($result);
    }
    
    /**
     * Get the current theme's URL
     * 
     * @return string
     */
    protected function _commonThemeUrl() {
        if (!strlen($this->_commonThemeUrl)) {
            $this->_commonThemeUrl = Config::get()->authorThemesUrl . '/' . strtolower(Tasks_1NewProject::$destDir);
        }
        return $this->_commonThemeUrl;
    }
    
    /**
     * Get the current theme documentation's URL
     * 
     * @return string
     */
    protected function _commonThemeDocsUrl() {
        if (!strlen($this->_commonThemeDocsUrl)) {
            $this->_commonThemeDocsUrl = Config::get()->authorThemesUrl . '/docs/' . strtolower(Tasks_1NewProject::$destDir);
        }
        return $this->_commonThemeDocsUrl;
    }
    
    /**
     * Get the current theme's name
     * 
     * @return string
     */
    protected function _commonThemeName() {
        if (!strlen($this->_commonThemeName)) {
            $this->_commonThemeName = Tasks_1NewProject::getDestDir(
                Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_NAME],
                false
            );
        }
        return $this->_commonThemeName;
    }
    
    /**
     * Get the current marketplace name
     * 
     * @return string
     */
    protected function _commonMarketName() {
        if (!strlen($this->_commonMarketName)) {
            // Get the MarketPlace
            $this->_commonMarketName = isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_MARKETPLACE]) ? 
                Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_MARKETPLACE] : 
                Dist::MARKET_THEMEFOREST;
        }
        return $this->_commonMarketName;
    }
    
    /**
     * Get the current marketplace profile url
     * 
     * @return string
     */
    protected function _commonMarketProfileUrl() {
        if (!strlen($this->_commonMarketProfileUrl)) {
            // Prepare the profile
            $this->_commonMarketProfileUrl = Dist::getInstance($this->_commonMarketName())->getProfileUrl();
        }
        return $this->_commonMarketProfileUrl;
    }
    
    /**
     * Get the current marketplace license url
     * 
     * @return string
     */
    protected function _commonMarketLicenseUrl() {
        if (!strlen($this->_commonMarketLicenseUrl)) {
            // Prepare the profile
            $this->_commonMarketLicenseUrl = Dist::getInstance($this->_commonMarketName())->getLicenseUrl();
        }
        return $this->_commonMarketLicenseUrl;
    }
    
    /**
     * Get a comma-separated list of tags for this theme
     */
    protected function _commonTagsList() {
        if (!strlen($this->_commonTagsListResult)) {
            // Get the project data
            $projectData = Tasks::$project->getConfig()->getProjectData();

            /* @var $subjectTagsItem Model_Project_Config_Item_String */
            $subjectTagsItem = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_TAGS];

            // Get all the tags
            $coreTags = array();
            foreach(Addons_Listener::run('tags', Addons_Listener::TYPE_GET) as $addonTags) {
                if (is_array($addonTags) && count($addonTags)) {
                    foreach($addonTags as $tag) {
                        if (in_array($tag, WordPress_Tags::getCoreTags())) {
                            $coreTags[] = $tag;
                        }
                    }
                }
            }

            // Prepare the subject tags
            $subjectTags = array();
            if(is_array($subjectTagsItem->getValue()) && count($subjectTagsItem->getValue())) {
                $subjectTags = $subjectTagsItem->getValue();
            }

            // Prepare the final tags
            $tags = array_values(array_unique(array_merge($coreTags, $subjectTags)));

            // Store the tags list
            $this->_commonTagsListResult = implode(', ', $tags);
        }
        
        return $this->_commonTagsListResult;
    }
    
    /**
     * Prepare the copyright string for use in all file headers in comments.<br/>
     * Each line except the first will <b>start with " * "</b>.<br/>
     * For use in CSS, JS and PHP headers.
     * 
     * @return string
     */
    protected function _commonCopyright() {
        if (!strlen($this->_commonCopyrightResult)) {
            // Prepare the result
            $copyrightLines = array();

            // Prepare the rest of the lines
            $otherLines = array(
                array('theme',       Tasks_1NewProject::$destProjectName),
                array('version',     Tasks_1NewProject::getVerboseVersion()),
                array('link',        $this->_commonThemeUrl()),
                array('author',      Config::get()->authorName . ' <' . Config::get()->authorEmail . '>'),
                array('textdomain',  Tasks_1NewProject::$destDir),
                array('copyright',   '(C) ' . date('Y') . ' - ' . Config::get()->authorUrl . ' - All rights reserverd.'),
                array('license',     'GNU General Public License version 2.0 or later'),
            );
            
            // Quote found
            if (strlen($this->_commonQuote())) {
                $otherLines[] = array('motto', $this->_commonQuote());
            }

            // Format the copyright lines
            foreach ($otherLines as $otherLinesData) {
                list($key, $value) = $otherLinesData;
                $copyrightLines[] = (count($copyrightLines) ? '* @' : '@') . str_pad($key, 10) . ' ' . $value;
            }
            
            // Store the project name for the template
            $gnuCopyleft = <<<"GNU"
* 
* This is free Software: you can redistribute it and/or modify it under the
* terms of the GNU General Public License as published by the Free Software 
* Foundation, either version 2 of the License, or, at your option, any later 
* version.
* 
* The above copyright notice and this permission notice shall be included in 
* all copies or substantial portions of the Software.
* 
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
* SOFTWARE. See the GNU General Public License for more details.
* 
* You should have received a copy of the GNU General Public License along with 
* this Software. If not, visit https://www.gnu.org/licenses/gpl-2.0.html
GNU;
            
            
            // Save the string
            $this->_commonCopyrightResult = implode(PHP_EOL, $copyrightLines) . PHP_EOL . $gnuCopyleft;
        }
        
        // All done
        return $this->_commonCopyrightResult;
    }
    
    /**
     * Check whether or not we are in Staging mode
     * 
     * @param string $nonFlag (optional) Whether to negate the staging value; default <b>empty string</b>
     * @return boolean
     */
    public function staging($nonFlag = '') {
        return ('non' === $nonFlag) ? !Tasks::isStaging() : Tasks::isStaging();
    }
    
    /**
     * Current theme's quote
     * 
     * @return string
     */
    protected function _commonQuote() {
        if (null === $this->_commonQuoteResult) {
            do {
                // Out of project mode
                if (null === Tasks::$project) {
                    // Get the quote data
                    $quote = Whisper_Inspiration::getQuote();

                    // Store the result
                    $this->_commonQuoteResult = sprintf(
                        '%2$s | %1$s',
                        $quote['text'],
                        $quote['author']
                    );
                    break;
                }

                // Get the project data
                $projectData = Tasks::$project->getConfig()->getProjectData();

                /* @var $quoteItem Model_Project_Config_Item_String */
                $quoteItem = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_QUOTE];

                // Store the quote
                $this->_commonQuoteResult = $quoteItem->getValue();
            } while (false);
        }
        
        return $this->_commonQuoteResult;
    }
    
    /**
     * Convert an Model_Project_Config_Item_Color value to a CSS-compatible rgba() statement
     * 
     * @param string $colorKey Color key
     */
    protected function _colorRgba($colorKey) {
        /*@var $colorItem Model_Project_Config_Item_Color*/
        $colorItem = $this->_getItemByKey($colorKey);

        // Color item
        if ($colorItem instanceof Model_Project_Config_Item_Color) {
            return 'rgba(' . implode(', ', $colorItem->getRgba()) . ')';
        }
        
        // Color not found
        throw new Exception('Color "' . $colorKey . '" not defined');
    }
    
    /**
     * Convert an Model_Project_Config_Item_Color value to a WordPress-compatible 6-characters color
     * 
     * @param string $colorKey Color key
     */
    protected function _colorWp($colorKey) {
        /*@var $colorItem Model_Project_Config_Item_Color*/
        $colorItem = $this->_getItemByKey($colorKey);

        // Color item
        if ($colorItem instanceof Model_Project_Config_Item_Color) {
            return $colorItem->getWpColor();
        }
        
        // Color not found
        throw new Exception('Color "' . $colorKey . '" not defined');
    }
    
    /**
     * Get an item by key
     * 
     * @param string $itemKey Item key
     * @return Model_Project_Config_Item|null
     */
    protected function _getItemByKey($itemKey) {
        // Get the project data
        $projectData = Tasks::$project->getConfig()->getProjectData();
        
        // Found our object
        if (isset($projectData[$this->_addonName]) && isset($projectData[$this->_addonName][$itemKey])) {
            // Configuration item
            if ($projectData[$this->_addonName][$itemKey] instanceof Model_Project_Config_Item) {
                return $projectData[$this->_addonName][$itemKey];
            }
        }
        
        // Nothing found
        return null;
    }
}

/* EOF */