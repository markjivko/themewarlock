<?php
/**
 * Theme Warlock - WordPress_Pot_StringExtractor
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Pot_StringExtractor {

    /**
     * WordPress i18n methods and their extraction rules
     * 
     * @var array
     */
    protected $_rules = array();
    
    /**
     * Comments prefix
     * 
     * @var string
     */
    protected $_commentPrefix = 'translators:';

    /**
     * Current text domain
     * 
     * @var string
     */
    protected $_textDomain = '';
    
    /**
     * String Extractor
     * 
     * @param array $rules WordPress i18n methods and their extraction rules
     */
    public function __construct(Array $rules) {
        $this->_rules = $rules;
    }

    /**
     * Set the text domain
     * 
     * @param string $textDomain Text domain
     * @return WordPress_Pot_StringExtractor
     */
    public function setTextDomain($textDomain) {
        $this->_textDomain = trim($textDomain);
        return $this;
    }
    
    /**
     * Get the current text domain
     * 
     * @return string
     */
    public function getTextDomain() {
        return $this->_textDomain;
    }
    
    /**
     * Extract strings from a directory
     * 
     * @param string   $dir      Directory
     * @param string[] $excludes (optional) Excluded files regular expressions
     * @param string[] $includes (optional) Included files regular expressions
     * @param string   $prefix   (optional) File name prefix
     * @return WordPress_Pot_Translations
     */
    public function extractFromDirectory($dir, $excludes = array(), $includes = array(), $prefix = '') {
        $oldCwd = getcwd();
        chdir($dir);
        $translations = new WordPress_Pot_Translations();
        foreach ((array) scandir('.') as $fileName) {
            if ('.' == $fileName || '..' == $fileName) {
                continue;
            }
            if (preg_match('/\.php$/', $fileName) && $this->_doesFileNameMatch($prefix . $fileName, $excludes, $includes)) {
                $extracted = $this->extractFromFile($fileName, $prefix);
                $translations->mergeOriginalsWith($extracted);
            }
            if (is_dir($fileName)) {
                $extracted = $this->extractFromDirectory($fileName, $excludes, $includes, $prefix . $fileName . '/');
                $translations->mergeOriginalsWith($extracted);
            }
        }
        chdir($oldCwd);
        return $translations;
    }

    /**
     * Extract strings from a PHP file
     * 
     * @param string $fileName PHP File path
     * @param string $prefix   PHP File name prefix
     * @return WordPress_Pot_Translations
     */
    public function extractFromFile($fileName, $prefix) {
        return $this->extractFromCode(file_get_contents($fileName), $prefix . $fileName);
    }
    
    /**
     * Extract translations from PHP code
     * 
     * @param string $code     PHP Code
     * @param string $fileName PHP File Name - with prefix
     * @return WordPress_Pot_Translations
     */
    public function extractFromCode($code, $fileName) {
        // Prepare the translations
        $translations = new WordPress_Pot_Translations();
        
        // Go through the function calls
        foreach ($this->_findFunctionCalls(array_keys($this->_rules), $code) as $call) {
            $entry = $this->_entryFromCall($call, $fileName);
            if (is_array($entry)) {
                foreach ($entry as $single_entry) {
                    $translations->addEntryOrMerge($single_entry);
                }
            } elseif ($entry) {
                $translations->addEntryOrMerge($entry);
            }
        }
        
        // All done
        return $translations;
    }

    /**
     * Is the current file a possible holder of i18n calls?
     * 
     * @param string   $path     File path
     * @param string[] $excludes (optional) Excluded files regular expressions
     * @param string[] $includes (optional) Included files regular expressions
     * @return boolean
     */
    public function _doesFileNameMatch($path, $excludes, $includes) {
        if ($includes) {
            $matched_any_include = false;
            foreach ($includes as $include) {
                if (preg_match('#^' . $include . '$#', $path)) {
                    $matched_any_include = true;
                    break;
                }
            }
            if (!$matched_any_include) {
                return false;
            }
        }
        
        if ($excludes) {
            foreach ($excludes as $exclude) {
                if (preg_match('#^' . $exclude . '$#', $path)) {
                    return false;
                }
            }
        }
        
        return true;
    }

    /**
     * Convert a call to a WordPress_Pot_Translations_Entry
     * 
     * @param array  $call     Call details. An array of associative arrays with these keys: <ul>
     * <li><b>name</b> - Name of the function</li>
     * <li><b>args</b> - Array for the function arguments. Each string literal is represented by itself, other arguments are represented by null</li>
     * <li><b>line</b> - Line number</li>
     * </ul>
     * @param string $fileName PHP File name - with prefix
     * @return boolean|null|WordPress_Pot_Translations_Entry|WordPress_Pot_Translations_Entry[] <ul>
     * <li><b>null</b> - If no rule was defined for the current call</li>
     * <li><b>false</b> - If no arguments were defined for the current call</li>
     * <li><b>WordPress_Pot_Translations_Entry</b> - Singular form</li>
     * <li><b>WordPress_Pot_Translations_Entry[]</b> - Multiple plural forms</li>
     * </ul>
     */
    public function _entryFromCall($call, $fileName) {
        $rule = isset($this->_rules[$call['name']]) ? $this->_rules[$call['name']] : null;
        if (!$rule) {
            return null;
        }
        
        $entry = new WordPress_Pot_Translations_Entry();
        $multiple = array();
        $complete = false;
        for ($i = 0; $i < count($rule); ++$i) {
            if ($rule[$i] && (!isset($call['args'][$i]) || !is_string($call['args'][$i]) || '' == $call['args'][$i] )) {
                return false;
            }
            switch ($rule[$i]) {
                case WordPress_Pot_Translations_Entry::ARG_STRING:
                    if ($complete) {
                        $multiple[] = $entry;
                        $entry = new WordPress_Pot_Translations_Entry();
                        $complete = false;
                    }
                    $entry->singular = $call['args'][$i];
                    $complete = true;
                    break;
                    
                case WordPress_Pot_Translations_Entry::ARG_SINGULAR:
                    if ($complete) {
                        $multiple[] = $entry;
                        $entry = new WordPress_Pot_Translations_Entry();
                        $complete = false;
                    }
                    $entry->singular = $call['args'][$i];
                    $entry->isPlural = true;
                    break;
                    
                case WordPress_Pot_Translations_Entry::ARG_PLURAL:
                    $entry->plural = $call['args'][$i];
                    $entry->isPlural = true;
                    $complete = true;
                    break;
                
                case WordPress_Pot_Translations_Entry::ARG_CONTEXT:
                    $entry->context = $call['args'][$i];
                    foreach ($multiple as &$single_entry) {
                        $single_entry->context = $entry->context;
                    }
                    break;
            }
        }
        
        if (isset($call['line']) && $call['line']) {
            $references = array($fileName . ':' . $call['line']);
            $entry->references = $references;
            foreach ($multiple as &$single_entry) {
                $single_entry->references = $references;
            }
        }
        
        if (isset($call['comment']) && $call['comment']) {
            $comments = rtrim($call['comment']) . "\n";
            $entry->extractedComments = $comments;
            foreach ($multiple as &$single_entry) {
                $single_entry->extractedComments = $comments;
            }
        }
        
        if ($multiple && $entry) {
            $multiple[] = $entry;
            return $multiple;
        }

        return $entry;
    }

    /**
     * Finds all the function calls in the PHP code
     * 
     * @param string[] $functionNames Function names
     * @param string   $code          PHP code
     * @return array Function calls. An array of associative arrays with these keys: <ul>
     * <li><b>name</b> - Name of the function</li>
     * <li><b>args</b> - Array for the function arguments. Each string literal is represented by itself, other arguments are represented by null</li>
     * <li><b>line</b> - Line number</li>
     * </ul>
     */
    public function _findFunctionCalls($functionNames, $code) {
        $tokens = token_get_all($code);
        $function_calls = array();
        $latest_comment = false;
        $in_func = false;
        foreach ($tokens as $token) {
            $id = $text = null;
            
            if (is_array($token)) {
                list( $id, $text, $line ) = $token;
            }
            
            if (T_WHITESPACE == $id) {
                continue;
            }
            
            if (T_STRING == $id && in_array($text, $functionNames) && !$in_func) {
                $in_func = true;
                $paren_level = -1;
                $args = array();
                $func_name = $text;
                $func_line = $line;
                $func_comment = $latest_comment ? $latest_comment : '';

                $just_got_into_func = true;
                $latest_comment = false;
                continue;
            }
            
            if (T_COMMENT == $id) {
                $text = preg_replace('%^\s+\*\s%m', '', $text);
                $text = str_replace(array("\r\n", "\n"), ' ', $text);
                $text = trim(preg_replace('%^(/\*|//)%', '', preg_replace('%\*/$%', '', $text)));
                if (0 === stripos($text, $this->_commentPrefix)) {
                    $latest_comment = $text;
                }
            }
            
            if (!$in_func) {
                continue;
            }
            
            if ('(' == $token) {
                $paren_level++;
                if (0 == $paren_level) { // start of first argument
                    $just_got_into_func = false;
                    $current_argument = null;
                    $current_argument_is_just_literal = true;
                }
                continue;
            }
            
            if ($just_got_into_func) {
                // there wasn't a opening paren just after the function name -- this means it is not a function
                $in_func = false;
                $just_got_into_func = false;
            }
            
            if (')' == $token) {
                if (0 == $paren_level) {
                    $in_func = false;
                    $args[] = $current_argument;
                    $call = array('name' => $func_name, 'args' => $args, 'line' => $func_line);
                    if ($func_comment) {
                        $call['comment'] = $func_comment;
                    }

                    do {
                        // Text domain validation
                        if (strlen($this->_textDomain)) {
                            // No match
                            if (end($args) !== $this->_textDomain) {
                                break;
                            }
                        }
                        
                        // Store the call
                        $function_calls[] = $call;
                    } while (false);
                }
                $paren_level--;
                continue;
            }
            
            if (',' == $token && 0 == $paren_level) {
                $args[] = $current_argument;
                $current_argument = null;
                $current_argument_is_just_literal = true;
                continue;
            }
            
            if (T_CONSTANT_ENCAPSED_STRING == $id && $current_argument_is_just_literal) {
                // we can use eval safely, because we are sure $text is just a string literal
                eval('$current_argument = ' . $text . ';');
                continue;
            }
            
            $current_argument_is_just_literal = false;
            $current_argument = null;
        }
        
        return $function_calls;
    }
}

/* EOF */