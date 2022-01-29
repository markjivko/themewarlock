<?php
/**
 * Theme Warlock - WordPress_Style
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Style {

    /**
     * Common Strings
     */
    const STRING_TABLE_OF_CONTENTS = 'Table Of Contents';
    const STRING_CHAPTER           = 'CHAPTER';
    const STRING_LINE              = 'LINE';
    
    /**
     * WordPress style.css-specific strings
     */
    const WP_THEME_NAME  = 'Theme Name';
    const WP_THEME_URI   = 'Theme URI';
    const WP_AUTHOR      = 'Author';
    const WP_AUTHOR_URI  = 'Author URI';
    const WP_DESCRIPTION = 'Description';
    const WP_VERSION     = 'Version';
    const WP_LICENSE     = 'License';
    const WP_LICENSE_URI = 'License URI';
    const WP_TEXT_DOMAIN = 'Text Domain';
    const WP_TAGS        = 'Tags';
    
    /**
     * WordPress_Style manager
     * 
     * @var WordPress_Style
     */
    protected static $_instance;
    
    /**
     * Extra rules to include in the CSS
     * 
     * @var type 
     */
    protected $_extraRules = array();
    
    /**
     * Rules to include in the CSS
     * 
     * @var type 
     */
    protected $_rules = array();
    
    /**
     * Parsed flag
     * 
     * @var boolean
     */
    protected $_parsed = false;
    
    /**
     * WordPress Style manager
     * 
     * @return WordPress_Style
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    protected function __construct() {
        // Singleton
    }
    
    /**
     * Add CSS rules
     * 
     * @param string $rules CSS rules
     * @param string $title CSS rules heading
     * @param string $level CSS rules heading level
     */
    public function addExtraRule($rules, $title, $level = 1) {
        // Parse the rules
        if (is_array($parsedRules = $this->_parseRules($rules, $level))) {
            // Add the title header
            $this->_extraRules[] = array('', $title, $level);

            // Go through the rules
            foreach ($parsedRules as $extraRule) {
                $this->_extraRules[] = $extraRule;
            }
        } else {
            // Add the title and rules
            $this->_extraRules[] = array($rules, $title, $level);
        }
    }
    
    /**
     * Generate the style.css for the current theme.<br/>
     * This operation must be performed only once
     * 
     * @return boolean
     */
    public function parse() {
        // File already parsed
        if ($this->_parsed) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('Attempting to parse the style.css multiple times...');
            return false;
        }
        
        // No input file provided
        if (!is_file($stylePath = Tasks_1NewProject::getPath() . '/style.css')) {
            Log::check(Log::LEVEL_ERROR) && Log::error('No style.css defined in theme');
            return false;
        }
        
        // Get the available CSS
        if (false !== $parsedRules = $this->_parseRules(file_get_contents($stylePath))) {
            $this->_rules = $parsedRules;
        }
        
        // Prepare the header
        $result = $this->_getHeader() . PHP_EOL;
        
        // Add the TOC
        if (!Tasks::isStaging()) {
            $result .= $this->_getTOC() . PHP_EOL;
        }
        
        // Add the Contents
        $result .= $this->_getContents();
        
        // Re-write the file
        file_put_contents($stylePath, preg_replace('%(\r\n|\r)%', PHP_EOL, $result));
        
        // Prepare the RTL file path
        $rtlPath = Tasks_1NewProject::getPath() . '/style-rtl.css';

        // Run the RTL tool (either in Live mode or when debugging)
        if (!Tasks::isStaging() || Log::LEVEL_DEBUG === Log::getLevel()) {
            // Prepare the RTL command
            $command = '/usr/local/bin/rtlcss --silent ' . escapeshellarg($stylePath) . ' ' . escapeshellarg($rtlPath);
            Log::check(Log::LEVEL_DEBUG) && Log::debug($command);

            // Create the RTL file
            shell_exec($command);
        } else {
            copy($stylePath, $rtlPath);
        }
        
        // Live mode: TOC lines
        if (!Tasks::isStaging()) {
            $this->_insertChapterLines($stylePath);
            $this->_insertChapterLines($rtlPath);
        }
        
        // Store the flag
        $this->_parsed = true;
        
        // All done
        return true;
    }
    
    /**
     * Create the CSS header
     * 
     * @return string
     */
    protected function _getHeader() {
        // Prepare the description
        $description = '';
        if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_DESCRIPTION])) {
            $description = preg_replace('%[\r\n]+%', ' ', Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_DESCRIPTION]);
        }
        
        // Prepare the information
        $information = array(
            // The theme' name, slug and text-domain must be the same
            self::WP_THEME_NAME  => Addons_Utils::getInstance(Model_Project_Config::CATEGORY_CORE)->common(Addons_Utils::COMMON_THEME_NAME),
            self::WP_THEME_URI   => Addons_Utils::getInstance(Model_Project_Config::CATEGORY_CORE)->common(Addons_Utils::COMMON_THEME_URL),
            self::WP_AUTHOR      => Config::get()->authorName,
            self::WP_AUTHOR_URI  => Config::get()->authorUrl,
            self::WP_DESCRIPTION => $description,
            self::WP_VERSION     => Tasks_1NewProject::getVerboseVersion(),
            self::WP_LICENSE     => 'GNU General Public License v2.0',
            self::WP_LICENSE_URI => '<a target="_blank" href="https://www.gnu.org/licenses/gpl-2.0.txt">GNU GPL v2.0</a>',
            self::WP_TEXT_DOMAIN => Tasks_1NewProject::$destDir,
            self::WP_TAGS        => Addons_Utils::getInstance(Model_Project_Config::CATEGORY_CORE)->common(Addons_Utils::COMMON_TAGS_LIST),
        );
        
        // Prepare the result
        $result = '/*' . PHP_EOL;
        
        // Go through the details
        foreach ($information as $key => $value) {
            $result .= '    ' . $key . ': ' . $value . PHP_EOL;
        }
        
        // Prepare the quote
        $quote = Addons_Utils::getInstance(Model_Project_Config::CATEGORY_CORE)->common(Addons_Utils::COMMON_QUOTE);
        
        // Append the quote for whoever is reading this document
        $result .= PHP_EOL . '        ' . $quote . PHP_EOL;
        
        // Add the copyright
        $copyright = sprintf(
            "%s by %s, (C) %s %s",
            Tasks_1NewProject::$destProjectName,
            Config::get()->authorName,
            date('Y'),
            Config::get()->authorUrl
        );
        $result .= PHP_EOL . '    ' . $copyright . PHP_EOL;
        
        // Close the comment
        $result .= '*/' . PHP_EOL . PHP_EOL;
        
        // All done
        return $result;
    }
    
    /**
     * Create the CSS TOC
     * 
     * @return string
     */
    protected function _getTOC($lineLength = 75) {
        // Prepare the ruleset
        $ruleSets = array($this->_rules, $this->_extraRules);
        
        // Prepare the title
        $tocTitleIndent = intval(($lineLength - strlen(self::STRING_TABLE_OF_CONTENTS)) / 2);
        
        // Add the comment
        $result = '/**' . PHP_EOL .
            $this->_getTOCLine('', $lineLength) . 
            $this->_getTOCLine(str_repeat(' ', 2) . self::STRING_TABLE_OF_CONTENTS, $lineLength) . 
            $this->_getTOCLine(str_repeat(' ', 2) . str_repeat('=', strlen(self::STRING_TABLE_OF_CONTENTS)), $lineLength) . 
            $this->_getTOCLine('', $lineLength);
        
        // Prepare the tree
        $tree = array();
        
        // Store the previous level
        $prevLevel = null;
        
        // Go through the ruleset
        foreach ($ruleSets as $ruleSet) {
            foreach ($ruleSet as $rule) {
                list(, $title, $level) = $rule;

                // Append the lines
                $result .= $this->_getTOCLine(' ' . str_repeat('    ', $level) . $this->_getLevelNumbering($level, $tree, $prevLevel) . '. ' . $title, $lineLength);
            }
        }
        
        // Close the comment
        $result .=  $this->_getTOCLine('', $lineLength) . ' */' . PHP_EOL;
        
        // All done
        return $result;
    }
    
    /**
     * Get level numbering
     * 
     * @example <p>Each time this method is called, a new numbered level is provided based on the input; <br/>For 1, 1, 2, 2, 1 the results will be "1", "2", "2.1", "2.2", "3"</p>
     * @param int    $depth      Current TOC item depth
     * @param array  &$tree      Level tree holder
     * @param null   &$prevDepth Previous depth holder
     * @param string $sepChar    (optional) Separation character; default <strong>'.'</strong>
     * @return string Numbered level
     */
    protected function _getLevelNumbering($depth, Array &$tree, &$prevDepth, $sepChar = '.') {
        // Prepare the last element
        $lastElement = (false === end($tree) ? '0' : end($tree));

        // Same level
        if (null === $prevDepth || $prevDepth === $depth) {
            // Increment last place after $sepChar
            $lastElement = preg_replace_callback('%(' . preg_quote($sepChar) . '?)(\d+)$%', function($item){
                return $item[1] . (intval($item[2]) + 1);
            }, $lastElement);
        } else {
            if ($depth > $prevDepth) {
                // Move to the next level
                $lastElement .= str_repeat($sepChar . '1', $depth - $prevDepth);
            } else {
                // Go one level back and increment last place after $sepChar
                $lastElement = preg_replace_callback('%(' . preg_quote($sepChar) . '?)(\d+)(?:' . preg_quote($sepChar) . '\d+){' . ($prevDepth - $depth) . '}$%', function($item) {
                    return $item[1] . (intval($item[2]) + 1);
                }, $lastElement);
            }
        }

        // Store the level
        $prevDepth = $depth;

        // Store the new element
        $tree[] = $lastElement;
        
        // Get the last element
        return $lastElement;
    }
    
    /**
     * Prepare each TOC line
     * 
     * @param string $line
     * @param int $lineLength
     * @return string
     */
    protected function _getTOCLine($line, $lineLength) {
        return ' *' . $line  . PHP_EOL;
    }
    
    /**
     * Add chapter lines to final .css TOC
     * 
     * @param string $stylePath CSS file path
     */
    protected function _insertChapterLines($stylePath) {
        // File not found or invalid
        if (!is_file($stylePath) || !preg_match('%\.css$%i', $stylePath)) {
            return;
        }
        
        // Get the file contents
        $styleContents = file_get_contents($stylePath);
        
        // Find all chapters' start line
        $chapterLines = array_map(function($item){
            return $item + 1;
        }, array_flip(array_map(function($item) {
            return preg_replace('%^\s*\*\s*%', '', $item);
        }, array_filter(preg_split('%\n%', $styleContents), function($item){
            return preg_match('%^ \* (?:\d+\.)+\s+\w%', $item);
        }))));
        
        // Add the chapter lines
        $styleContents = preg_replace_callback('%( \*  ' . preg_quote(self::STRING_TABLE_OF_CONTENTS) . ')(.*?)(\n \*\/)%ims', function($toc) use ($chapterLines) {
            // Get the max TOC entry length
            $maxEntryLength = max(array_map('strlen', preg_split('%\n%', $toc[0]))) + 20;
            
            // Added headings flag
            $addedHeadings = false;
            
            // Add the Line indicators
            return $toc[1] . preg_replace_callback('%( \*\s*)((?:\d+\.)+ .*?)$%m', function($item) use ($chapterLines, $maxEntryLength, &$addedHeadings) {
                $lineNumber = isset($chapterLines[$item[2]]) ? $chapterLines[$item[2]] : null;
                
                // Item not found
                if (null === $lineNumber) {
                    return $item[0];
                }
                
                // Account for the appended headings line
                $lineNumber += 1;
                
                // Append the chapter line number
                $result = $item[0] . str_repeat('.', $maxEntryLength - strlen($item[0]) - strlen($lineNumber)) . $lineNumber;
                
                // Add the headings
                if (!$addedHeadings) {
                    // Mark the event
                    $addedHeadings = true;
                    
                    // Add the heading
                    $result = $item[1] . self::STRING_CHAPTER . str_repeat('.', $maxEntryLength - strlen($item[1] . self::STRING_CHAPTER) - strlen(self::STRING_LINE)) . self::STRING_LINE . PHP_EOL . $result;
                }
                
                // All done
                return $result;
            }, $toc[2]) . $toc[3];
        }, $styleContents);
        
        // Save the file
        file_put_contents($stylePath, $styleContents);
    }
    
    /**
     * Create the CSS contents
     * 
     * @param int $lineLength
     * @return string
     */
    protected function _getContents($lineLength = 0) {
        // Prepare the ruleset
        $ruleSets = array($this->_rules, $this->_extraRules);
        
        // Prepare the result
        $result = '';

        // Prepare the tree
        $tree = array();
        
        // Store the previous level
        $prevLevel = null;
        
        // Go through the ruleset
        foreach ($ruleSets as $ruleSet) {
            foreach ($ruleSet as $rule) {
                list($content, $title, $level) = $rule;
                
                // Add the comment
                $result .= '/*' . str_repeat('*', $lineLength) . PHP_EOL .
                    ' * '. $this->_getLevelNumbering($level, $tree, $prevLevel) . '. ' . $title . PHP_EOL .
                    ' ' . str_repeat('*', $lineLength) . '*/' . PHP_EOL;
                
                // Append the content
                if (strlen($content)) {
                    // Allow for easier debugging
                    $result .= $content . PHP_EOL . PHP_EOL;
                }
            }
        }
        
        // All done
        return $result . PHP_EOL . '/*EOF*/';
    }


    /**
     * Parse the current CSS rules
     * 
     * @param string $cssRules    CSS rules
     * @param int    $offsetLevel Add this much to the rules level
     */
    protected function _parseRules($cssRules, $offsetLevel = 0) {
        // Prepare the result
        $result = array();
        
        // Parse the rules
        if (preg_match_all('%\/\*[^\*]*?[\r\n] *?(#+)( *?[\w\- ]+)[\r\n][^\*]*?\*\/(.*?)(?=\/\*[^\*]*?[\r\n] *?(#+ *?[\w\- ]+)[\r\n]|\Z)%ims', $cssRules, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                // Append the rules
                $result[] = array(
                    trim($match[3]),
                    trim($match[2]),
                    substr_count($match[1], '#') + $offsetLevel,
                );
            }
        } else {
            return false;
        }
        
        // All done
        return $result;
    }

}

/* EOF */