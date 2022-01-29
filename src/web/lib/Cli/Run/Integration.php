<?php
/**
 * Theme Warlock - Cli Run Integration
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Cli_Run_Integration {
    /**
     * Core plugins
     */
    const CORE_PLUGINS = array(
        Plugin::PLUGIN_THEME_CHECK,
    );
    
    /**
     * Options that must be stored as JSON arrays
     */
    const JSON_OPTIONS = array(
        self::OPT_PROJECT_ADDONS,
        self::OPT_PROJECT_PLUGINS,
        self::OPT_PROJECT_TAGS,
        self::OPT_FONT_H1_OPTIONS,
        self::OPT_FONT_H2_OPTIONS,
        self::OPT_FONT_H3_OPTIONS,
        self::OPT_FONT_TEXT_OPTIONS,
        self::OPT_FONT_MENU_OPTIONS,
        self::OPT_FONT_BUTTON_OPTIONS,
        self::OPT_FONT_LINK_OPTIONS,
    );
    
    /**
     * Framework ID - the folder where info.php is stored
     */
    const FRAMEWORK_ID = 'framework_id';
    
    /**
     * Framework Target
     * 
     * @see Framework::TARGET_*
     */
    const FRAMEWORK_TARGET = 'framework_target';

    /**
     * Options
     * 
     * @var array
     */
    public static $options = array();
    
    /**
     * Start time
     * 
     * @var float
     */
    public static $startTime = 0;
    
    /**
     * Array of arrays - stores the info.php contents for all available framework types
     * 
     * @var array
     */
    public static $availableFrameworks;
    
    /**
     * Batch mode
     * 
     * @var string
     */
    public static $batchMode = null;
    
    /**
     * Script options
     */
    const OPT_PROJECT_NAME              = 'projectName';
    const OPT_PROJECT_DESCRIPTION       = 'projectDescription';
    const OPT_PROJECT_DESCRIPTION_STORE = 'projectDescriptionStore';
    const OPT_PROJECT_QUOTE             = 'projectQuote';
    const OPT_PROJECT_TAGS              = 'projectTags';
    const OPT_PROJECT_VERSION           = 'projectVersion';
    const OPT_PROJECT_FRAMEWORK         = 'projectFramework';
    const OPT_PROJECT_MARKETPLACE       = 'projectMarketplace';
    const OPT_PROJECT_ADDONS            = 'projectAddons';
    const OPT_PROJECT_PLUGINS           = 'projectPlugins';
    const OPT_PROJECT_ICON              = 'projectIcon';
    const OPT_PROJECT_PREVIEW           = 'projectPreview';
    const OPT_PROJECT_PREVIEW_STORE     = 'projectPreviewStore';
    const OPT_PROJECT_HEADER_LINK_COLOR = 'projectHeaderLinkColor';
    const OPT_PROJECT_HEADER_TEXT_COLOR = 'projectHeaderTextColor';
    const OPT_PROJECT_UI_SET            = 'projectUiSet';
    const OPT_PROJECT_CONTENT_WIDTH     = 'projectContentWidth';
    const OPT_PROJECT_SIDEBAR_WIDTH     = 'projectSidebarWidth';
    const OPT_PROJECT_USE_STORYLINE     = 'projectUseStoryline';
    const OPT_PROJECT_USE_WIDGET_BLOCKS = 'projectUseWidgetBlocks';
    
    const OPT_COLOR_1_NAME              = 'projectColor1Name';
    const OPT_COLOR_2_NAME              = 'projectColor2Name';
    const OPT_COLOR_3_NAME              = 'projectColor3Name';
    const OPT_COLOR_4_NAME              = 'projectColor4Name';
    const OPT_COLOR_1_DEFAULT           = 'projectColor1Default';
    const OPT_COLOR_2_DEFAULT           = 'projectColor2Default';
    const OPT_COLOR_3_DEFAULT           = 'projectColor3Default';
    const OPT_COLOR_4_DEFAULT           = 'projectColor4Default';
    
    const OPT_FONT_H1_NAME              = 'projectFontH1Name';
    const OPT_FONT_H2_NAME              = 'projectFontH2Name';
    const OPT_FONT_H3_NAME              = 'projectFontH3Name';
    const OPT_FONT_TEXT_NAME            = 'projectFontTextName';
    const OPT_FONT_MENU_NAME            = 'projectFontMenuName';
    const OPT_FONT_BUTTON_NAME          = 'projectFontButtonName';
    const OPT_FONT_LINK_NAME            = 'projectFontLinkName';
    const OPT_FONT_QUOTE_NAME           = 'projectFontQuoteName';
    const OPT_FONT_CODE_NAME            = 'projectFontCodeName';
    const OPT_FONT_H1_OPTIONS           = 'projectFontH1Options';
    const OPT_FONT_H2_OPTIONS           = 'projectFontH2Options';
    const OPT_FONT_H3_OPTIONS           = 'projectFontH3Options';
    const OPT_FONT_TEXT_OPTIONS         = 'projectFontTextOptions';
    const OPT_FONT_MENU_OPTIONS         = 'projectFontMenuOptions';
    const OPT_FONT_BUTTON_OPTIONS       = 'projectFontButtonOptions';
    const OPT_FONT_LINK_OPTIONS         = 'projectFontLinkOptions';
    
    const OPT_SCREENSHOTS_EFFECT        = 'screenshotsEffect';
    const OPT_VERSION_TEMPLATE          = 'versionTemplate';
    
    /**
     * Options explained
     * 
     * Each item must have the following values: <ul>
     * <li>Title</li>
     * <li>Description</li>
     * <li>(optional) Symbol</li>
     * <li>(optional) URL</li>
     * </ul>
     */
    const OPT_DETAILS = array(
        self::OPT_PROJECT_NAME              => array(
            'Theme name', 
            'Choose a name for this WordPress theme - this must be unique'
        ),
        self::OPT_PROJECT_DESCRIPTION       => array(
            'Theme description', 
            'Describe this WordPress theme to the end user; no HTML allowed'
        ),
        self::OPT_PROJECT_DESCRIPTION_STORE => array(
            'Store description', 
            'Describe this WordPress theme to potential clients on the selected distribution channel'
        ),
        self::OPT_PROJECT_QUOTE             => array(
            'Motto', 
            'Provide an inspirational quote for this theme. Use the "Refresh" functionality to grab a new quote or type in your own.'
        ),
        self::OPT_PROJECT_TAGS              => array(
            'Theme tags', 
            'Describe your theme with tags', 
            '', 
            'https://make.wordpress.org/themes/handbook/review/required/theme-tags/'
        ),
        self::OPT_PROJECT_VERSION           => array(
            'Theme version', 
            'Set this WordPress theme\'s version'
        ),
        self::OPT_PROJECT_FRAMEWORK         => array(
            'Theme framework', 
            'Each theme framework has specific functionality and Add-Ons'
        ),
        self::OPT_PROJECT_MARKETPLACE       => array(
            'Theme marketplace', 
            'Choose the intended distribution channel for this theme'
        ),
        self::OPT_PROJECT_PLUGINS           => array(
            'WordPress Plug-ins', 
            'Include useful 3rd party WordPress Plug-ins with this theme'
        ),
        self::OPT_PROJECT_ICON              => array(
            'Theme icon', 
            'Set an icon for this WordPress theme'
        ),
        self::OPT_PROJECT_PREVIEW           => array(
            'Theme preview', 
            'Set a preview for this WordPress theme'
        ),
        self::OPT_PROJECT_PREVIEW_STORE     => array(
            'Store preview', 
            'Set a Store Listing Preview for this WordPress theme'
        ),
        self::OPT_PROJECT_UI_SET            => array(
            'Core UI set', 
            'Select a core User Interface set of CSS rules for this themes. Core UI sets define everything except for the grid system.'
        ),
        self::OPT_PROJECT_CONTENT_WIDTH     => array(
            'Content Width', 
            'Set the total content width available'
        ),
        self::OPT_PROJECT_SIDEBAR_WIDTH     => array(
            'Sidebar Width', 
            'Set the total width available for all sidebars, in %'
        ),
        self::OPT_PROJECT_USE_STORYLINE     => array(
            'Storyline', 
            'Enable the use of StoryLine.js for this theme\'s addons'
        ),
        self::OPT_PROJECT_USE_WIDGET_BLOCKS   => array(
            'Widget Blocks', 
            'Enable the use of the WPBakery Page Builder editor for this theme\'s widget areas (e.g.: header, footer and sidebars)'
        ),
        
        self::OPT_COLOR_1_NAME              => array(
            'First color name',
            'Set the name for the first color in Customizer',
        ),
        self::OPT_COLOR_2_NAME              => array(
            'Second color name',
            'Set the name for the second color in Customizer',
        ),
        self::OPT_COLOR_3_NAME              => array(
            'Third color name',
            'Set the name for the third color in Customizer',
        ),
        self::OPT_COLOR_4_NAME              => array(
            'Text color name',
            'Set the name for the fourth color in Customizer',
        ),
        self::OPT_COLOR_1_DEFAULT              => array(
            'First color default value',
            'Set the default value for the first color in Customizer; referenced as <1> in Inline CSS',
        ),
        self::OPT_COLOR_2_DEFAULT              => array(
            'Second color default value',
            'Set the default value for the second color in Customizer; referenced as <2> in Inline CSS',
        ),
        self::OPT_COLOR_3_DEFAULT              => array(
            'Third color default value',
            'Set the default value for the third color in Customizer; referenced as <3> in Inline CSS',
        ),
        self::OPT_COLOR_4_DEFAULT              => array(
            'Text color default value',
            'Set the default value for the fourth color in Customizer; referenced as <4> in Inline CSS',
        ),
        
        self::OPT_FONT_H1_NAME => array(
            'H1 font name',
            'Set the name for the H1 font class in Customizer',
        ),
        self::OPT_FONT_H2_NAME => array(
            'H2 font name',
            'Set the name for the H2 font class in Customizer',
        ),
        self::OPT_FONT_H3_NAME => array(
            'H3 font name',
            'Set the name for the H3 font class in Customizer',
        ),
        self::OPT_FONT_TEXT_NAME => array(
            'Text font name',
            'Set the name for the Text font class in Customizer',
        ),
        self::OPT_FONT_MENU_NAME => array(
            'Menu font name',
            'Set the name for the Menu font class in Customizer',
        ),
        self::OPT_FONT_BUTTON_NAME => array(
            'Button font name',
            'Set the name for the Button font class in Customizer',
        ),
        self::OPT_FONT_LINK_NAME => array(
            'Link font name',
            'Set the name for the Link font class in Customizer',
        ),
        self::OPT_FONT_QUOTE_NAME => array(
            'Quote font name',
            'Set the name for the Quote font class in Customizer',
        ),
        self::OPT_FONT_CODE_NAME => array(
            'Code font name',
            'Set the name for the Code font class in Customizer',
        ),
        self::OPT_FONT_H1_OPTIONS => array(
            'H1 font options',
            'Set the options for the H1 font class in Customizer; referenced as <f-h1> in Inline CSS',
        ),
        self::OPT_FONT_H2_OPTIONS => array(
            'H2 font options',
            'Set the options for the H2 font class in Customizer; referenced as <f-h2> in Inline CSS',
        ),
        self::OPT_FONT_H3_OPTIONS => array(
            'H3 font options',
            'Set the options for the H3 font class in Customizer; referenced as <f-h3> in Inline CSS',
        ),
        self::OPT_FONT_TEXT_OPTIONS => array(
            'Text font options',
            'Set the options for the Text font class in Customizer; referenced as <f-text> in Inline CSS',
        ),
        self::OPT_FONT_MENU_OPTIONS => array(
            'Menu font options',
            'Set the options for the Menu font class in Customizer; referenced as <f-menu> in Inline CSS',
        ),
        self::OPT_FONT_BUTTON_OPTIONS => array(
            'Button font options',
            'Set the options for the Button font class in Customizer; referenced as <f-button> in Inline CSS',
        ),
        self::OPT_FONT_LINK_OPTIONS => array(
            'Link font options',
            'Set the options for the Link font class in Customizer; referenced as <f-link> in Inline CSS',
        ),
        self::OPT_PROJECT_HEADER_LINK_COLOR  => array(
            'Admin header link color', 
            'Set the links and loading bar color - avoid white'
        ),
        self::OPT_PROJECT_HEADER_TEXT_COLOR => array(
            'Admin header text color', 
            'Set the text color for the admin header - avoid white'
        ),
        self::OPT_VERSION_TEMPLATE          => array(
            'Version template', 
            'Set the version version template. Ex: "9.x" or "9.9.x" or "9.x,-10"',
            'Caution',
        ),
    );
    
    /**
     * Internal options
     */
    const IOPT_APPLY_PREVIEW     = '_applyPreview';
    const IOPT_TASK              = '_internal';
    const IOPT_STAGING           = '_staging';
    const IOPT_SNAPSHOT_ID       = '_snapshotId';
    const IOPT_TASK_ADDITIVE     = '_taskAdditive';
    const IOPT_TASK_PACKING      = '_taskPacking';
    const IOPT_TASK_USER_ID      = '_taskUserId';
    const IOPT_TASK_PROJECT_ID   = '_taskProjectId';
    
    /**
     * Unsaveable options
     * 
     * @var array
     */
    public static $unsaveableOptions = array();
    
    /**
     * Values the user can configure
     * 
     * @var array
     */
    protected $_userConfigurables = array(
        // Variable name => array(Description, mandatory, default value, allowed values)
        self::OPT_PROJECT_NAME        => array('Project Name', true),
        self::OPT_PROJECT_FRAMEWORK   => array('Project Framework', true, Framework::ID_ONEPAGE),
        self::OPT_PROJECT_MARKETPLACE => array('Project Marketplace', true, Dist::MARKET_THEMEFOREST),
        self::OPT_PROJECT_VERSION     => array('Project Version (must be an integer)', false, 1),
    );
    
    /**
     * Extra tools
     */
    const TOOL_LOG_VIEWER    = 'log-viewer';
    const TOOL_GIT_PULL      = 'git-pull';
    const TOOL_WP            = 'wp';
    const TOOL_SENDMAIL      = 'sendmail';
    const TOOL_CRON          = 'cron';
    const TOOL_SCAFFOLDING   = 'scaffolding';
    const TOOL_I18N          = 'i18n';
    const TOOL_TEST          = 'test';
    const TOOL_INSTALL       = 'install';
    const TOOL_HELP          = 'help';
    const TOOL_EXTRA         = 'extra';
    const TOOL_GENERATE      = 'generate';
    const TOOL_PUBLISH       = 'publish';
    
    /**
     * Tools details
     * 
     * @var Tools information
     */
    protected static $_tools = array(
        self::TOOL_LOG_VIEWER  => 'Launch a new terminal with a tail -f on the logs',
        self::TOOL_GIT_PULL    => 'Update the current repository',
        self::TOOL_WP          => 'WordPress tools',
        self::TOOL_CRON        => 'Cron task runner',
        self::TOOL_SCAFFOLDING => 'Scaffolding tool to create plugins and addons quickly from partially pre-configured templates',
        self::TOOL_I18N        => 'Prepare plugins internationalization',
        self::TOOL_GENERATE    => 'Generate project deliverables',
        self::TOOL_PUBLISH     => 'Publish the project to the live WP Multisite installation specified at {config.authorThemesUrl}',
        self::TOOL_TEST        => 'Runs the temporary code in Test.php',
        self::TOOL_HELP        => 'This help tool',
    );
    
    /**
     * Class constructor
     */
    public function __construct() {
        // Store the start time
        self::$startTime = microtime(true);
        
        // Get the batch mode
        $this->_getBatchMode();

        // Get the user options
        $this->_getUserOptions();
    }

    /**
     * Get the batch mode
     * 
     * @global array $argv Command-line arguments
     */
    protected function _getBatchMode() {
        global $argv;
        if (isset($argv[1])) {
            self::$batchMode = $argv[1];
        }
    }

    /**
     * Get the user options
     * 
     * @return null
     */
    protected function _getUserOptions() {
        global $argv;

        // Mix some themes to create a new one
        do {
            switch (self::$batchMode) {
                case self::TOOL_WP:
                    WordPress::run();
                    break 2;
                
                case self::TOOL_CRON:
                    Cron::run(isset($argv[2]) && !empty($argv[2]) ? $argv[2] : null);
                    break 2;
                
                case self::TOOL_SCAFFOLDING:
                    Scaffolding::run(isset($argv[2]) && !empty($argv[2]) ? $argv[2] : null);
                    break 2;
                
                case self::TOOL_I18N:
                    Scaffolding::i18n(isset($argv[2]) && !empty($argv[2]) ? $argv[2] : null, isset($argv[3]) && !empty($argv[3]) ? $argv[3] : null);
                    break 2;
                
                case self::TOOL_GENERATE:
                    Model_Project::generate(isset($argv[2]) && !empty($argv[2]) ? $argv[2] : null, isset($argv[3]) && !empty($argv[3]) ? $argv[3] : null);
                    break 2;
                
                case self::TOOL_PUBLISH:
                    WordPress_Publisher::run(isset($argv[2]) && !empty($argv[2]) ? $argv[2] : null, isset($argv[3]) && !empty($argv[3]) ? $argv[3] : null, isset($argv[4]) && !empty($argv[4]) ? $argv[4] : null);
                    break 2;

                case self::TOOL_SENDMAIL:
                    Notifier::getInstance()->sendEmail(isset($argv[2]) && !empty($argv[2]) ? $argv[2] : null);
                    break 2;

                case self::TOOL_LOG_VIEWER:
                    // Start the log viewer in a new terminal window
                    Process::startAsync('gnome-terminal -- bash -c "tail -f ' . ROOT . '/web/log/log.txt | sed \"s#.*WARNING#\x1b[33m&#; s#.*ERROR#\x1b[31m&#; s#.*INFO#\x1b[36m&#; s#.*Ajax\:#\x1b[32m&#; s#.*DEBUG#\x1b[37m&#\"" & disown');
                    break 2;

                case self::TOOL_GIT_PULL:
                    Git::run(isset($argv[2]) && 'async' == $argv[2]);
                    break 2;

                case self::TOOL_TEST:
                    // Run a local test
                    Test::run();
                    break 2;
                
                case self::TOOL_INSTALL:
                    try {
                        Installer::run(isset($argv[2]) ? $argv[2] : null);
                    } catch (Exception $exc) {
                        Console::p($exc->getMessage(), false);
                    }
                    break 2;

                case self::TOOL_HELP:
                    self::_outputHelp();
                    break 2;
            }
            
            // Get the predefined data
            $data = $this->_getPredefinedRun();
            if (file_exists(IO::inputPath() . '/run.csv') && null == $data) {
                Console::p("Invalid \"run.csv\" file (check your log.txt).\nPlease configure this project first.", false);
            }

            // Log this
            Log::check(Log::LEVEL_DEBUG) && Log::debug('User options:');

            // Valid run.csv and not in prepare mode
            if (null !== $data) {
                // Auto-increment the project version
                $data[self::OPT_PROJECT_VERSION] = intval($data[self::OPT_PROJECT_VERSION]);

                // Set the options
                self::$options = $data;

                // Forced options
                if (isset($argv[3]) && strlen($argv[3])) {
                    $json = $argv[3];
                    if (is_array($forcedOptions = @json_decode($json, true))) {
                        foreach ($forcedOptions as $key => $value) {
                            if (in_array($key, $this->_availableOptions) && self::OPT_PROJECT_NAME !== $key) {
                                self::$options[$key] = $value;
                            }
                        }
                    }
                }
            } else {
                Console::p('No data in run.csv', false);
                break;
            }

            // Output the help
            if (!isset(self::$options[Cli_Run_Integration::IOPT_TASK_ADDITIVE])) {
                self::_outputHelp(70, '=', 'Generating project "' . $data[Cli_Run_Integration::OPT_PROJECT_NAME] . '"', $data);
            }
            
            // Log the snapshot
            if (isset(self::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID])) {
                Console::h1('> Snapshot #' . self::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID]);
            }
            
            // Log these options
            Log::check(Log::LEVEL_DEBUG) && Log::debug(self::$options);

            // Run the tasks
            Tasks::run();
        } while (false);
        
        // Stop here
        exit();
    }
    
    /**
     * Get the predefined run information
     * 
     * @return array|null
     */
    protected function _getPredefinedRun() {
        global $argv;
        if (file_exists($file = IO::inputPath() . '/run.csv')) {
            // Prepare the data
            $data = Csv::getData($file);
            
            // Custom run
            if (isset($argv[1]) && self::TOOL_EXTRA == $argv[1]) {
                // Get the argument
                if (isset($argv[2])) {
                    // Get the string
                    $jsonString = @base64_decode($argv[2]);
                    
                    // Get the array
                    $jsonArray = @json_decode($jsonString, true);
                    
                    // Valid array
                    if (is_array($jsonArray)) {
                        // Get a reflection
                        $classReflection = new ReflectionClass(self::class);
                        
                        // Get the allowed keys
                        $allowedKeys = array();
                        foreach($classReflection->getConstants() as $constKey => $constValue) {
                            if (preg_match('%^I?OPT_%', $constKey)) {
                                $allowedKeys[] = $constValue;
                            }
                        }
                        
                        // Parse the provided data
                        foreach ($jsonArray as $key => $value) {
                            if (in_array($key, $allowedKeys) && (is_string($value) || is_numeric($value))) {
                                $data[$key] = $value;
                            }
                        }
                    }
                }
            }

            // Data is not empty
            if (!empty($data)) {
                // Set the defaults if they were not provided
                foreach ($this->_userConfigurables as $key => $keyInfo) {
                    if (!isset($data[$key])) {
                        $data[$key] = isset($keyInfo[2]) ? $keyInfo[2] : '';
                    }
                }

                // Forcefully add the defaults, if they were stored in the run.csv
                foreach ($data as $key => $value) {
                    if (isset($this->_userConfigurables[$key])) {
                        $this->_userConfigurables[$key][2] = $value;
                    }
                }

                // Return the data
                return $data;
            }
        }

        // Nothing to do
        return null;
    }
    
    /**
     * 
     * @param type $totalLength
     * @param type $character
     * @param type $customTitle
     * @param array $data
     */
    protected static function _outputHelp($totalLength = 70, $character = '=', $customTitle = null, Array $data = array()) {
        do {
            // Prepare the help text
            $help = '';
            
            // Set the CopyRight
            $copy = '2019' . ((int) date('Y') > 2019 ? ' - ' . date('Y') : '');

            // All done
            $title = 'Theme Warlock' . (null === $customTitle ? ' Command Line Interface' : ' - ' . $customTitle);

            // Start the buffer
            ob_start();
            
            if (count($data)) {
                // Project options mode
                Console::h2('Project information');
                
                // Store the options
                $options = $data;
                foreach (self::JSON_OPTIONS as $key) {
                    // Get the addons
                    $optionsAddonsArray = isset($options[$key]) ? $options[$key] : array();

                    // Fallback
                    if (!is_array($optionsAddonsArray)) {
                        $optionsAddonsArray = array();
                    }

                    // Update the options
                    $options[$key] = implode(', ', Cli_Run_Integration::OPT_PROJECT_ADDONS == $key ? array_keys($optionsAddonsArray) : $optionsAddonsArray);
                }
            } else {
                // Help mode
                Console::h2('Available tools');
                
                // Get the reflection
                $classReflection = new ReflectionClass(self::class);

                // Prepare the options
                $options = array();
                foreach (self::$_tools as $toolName => $toolDescription) {
                    $options['tw ' . $toolName] =  $toolDescription;
                }
            }
            
            // List the available tools
            Console::li($options, count($data) ? '*' : '$', 80);
            
            // Store them as text
            $help = ob_get_clean();
        } while (false);
        
        // Prepare the dashed line
        $dashedLine = str_repeat($character, $totalLength);

        // Append the title, copyright and authors
        $help = implode("\n", array(
            $title, 
            $help, 
            'Copyright (C) ' . $copy . ' Mark Jivko, https://markjivko.com', 
        ));
        $helpArray = explode("\n", $help);
        array_unshift($helpArray, '');
        array_push($helpArray, '');
        foreach ($helpArray as $key => $value) {
            $helpArray[$key] = $character . '  ' . str_pad($value, $totalLength - 6, ' ', STR_PAD_RIGHT) . '  ' . $character;
        }

        // Output the result
        echo PHP_EOL . '  ' . $dashedLine . "\n  " . implode("\n  ", $helpArray) . "\n  " . $dashedLine . "\n\n";

    }
}

/*EOF*/