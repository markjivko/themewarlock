<?php
/**
 * Theme Warlock - Whisper_Builder_Keyword
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Whisper_Builder_Variable {
    
    // Variables
    const VAR_SYNONYM     = 'synonym';
    const VAR_THEME       = 'theme';
    const VAR_EMOTION     = 'emotion';
    const VAR_KEYWORD     = 'keyword';
    const VAR_COLOR       = 'color';
    const VAR_COLOUR      = 'colour';
    const VAR_INSPIRATION = 'inspiration';
    const VAR_ALT         = 'alt';
    
    // Extra items
    const EXTRA_INSPIRATION_TAGS = 'extra_inspiration_tags';
    
    // Internal extra items
    const INTRO_INSPIRATION_USED = 'intro_inspiration_used';
    
    /**
     * Implemented variables
     * 
     * @var string[]
     */
    protected static $_variables = array(
        self::VAR_SYNONYM,
        self::VAR_THEME,
        self::VAR_COLOR,
        self::VAR_COLOUR,
        self::VAR_EMOTION,
        self::VAR_KEYWORD,
        self::VAR_INSPIRATION,
        self::VAR_ALT,
    );
    
    /**
     * Extra information
     * 
     * @see Whisper_Builder_Variable::EXTRA_* constants
     * @var array
     */
    protected static $_extra = array();
    
    /**
     * Last color mentioned
     * 
     * @var string
     */
    protected static $_lastColor = null;
    
    /**
     * Return the list of available variables
     * 
     * @return string[]
     */
    public static function getList() {
        // Prepare the result
        $result = array();
        
        // Go through each keyword
        foreach (self::$_variables as $keyword) {
            // Get the method name
            $methodName = self::_computeMethodName($keyword);
            
            // Get the method reflection
            $methodReflection = new ReflectionMethod(__CLASS__, $methodName);
            
            // Prepare the method arguments
            $methodArguments = array();
            
            // Get the comment lines
            $commentLines = array_filter(array_map(function($item){
                // Remove the starting and ending lines
                $item = preg_replace('%(\*\/$|^\/\*+)%', '', $item);
                
                // Remove the leading *
                return trim(preg_replace('%^\s*\*\s*%', '', $item));
            }, preg_split('%[\r\n]+%', $methodReflection->getDocComment())));
            
            // Prepare the parameter descriptions
            $parameterDescriptions = array();
            
            // Get the current parameter descriptions pointer
            $parameterDescriptionsPointer = null;
            
            // Prepare the comments
            $comments = array();
            
            // Get the current comment pointer
            $commentsPointer = 'description';
            
            // Go through the lines
            foreach ($commentLines as $commentLine) {
                // Empty parameter description
                $parameterDescription = null;
                
                // A new comment pointer
                if (preg_match('%^@(\w+)\s*(.*)%', $commentLine, $matches)) {
                    // Set the current comment pointer
                    $commentsPointer = $matches[1];
                    
                    // Set the new comment line
                    $commentLine = $matches[2];
                }
                
                // Parameter?
                if ('param' == $commentsPointer) {
                    if (preg_match('%\$(\w+)\s*(.*)%', $commentLine, $paramMatches)) {
                        // Set the new parameter
                        $parameterDescriptionsPointer = $paramMatches[1];

                        // Set the description
                        $parameterDescription = $paramMatches[2];
                    } else {
                        $parameterDescription = $commentLine;
                    }
                }
                
                // Append parameter descriptions
                if (null !== $parameterDescriptionsPointer) {
                    if (null !== $parameterDescription) {
                        if (!isset($parameterDescriptions[$parameterDescriptionsPointer])) {
                            $parameterDescriptions[$parameterDescriptionsPointer] = array();
                        }
                        $parameterDescriptions[$parameterDescriptionsPointer][] = $parameterDescription;
                    }
                }
                
                // Append the line to the current pointer
                if (!isset($comments[$commentsPointer])) {
                    $comments[$commentsPointer] = array();
                }
                $comments[$commentsPointer][] = $commentLine;
            }
            
            // Go through the reflection
            foreach ($methodReflection->getParameters() as $parameter) {
                // Add the parameter and its description
                $methodArguments[$parameter->name] = isset($parameterDescriptions[$parameter->name]) ? $parameterDescriptions[$parameter->name] : null;
            }
            
            // Append to the result
            $result[$keyword] = array(
                isset($comments['description']) ? implode(PHP_EOL, $comments['description']) : null,
                $methodArguments,
            );
        }

        // All done
        return $result;
    }
    
    /**
     * Extra information. Acts as a session refresh
     * 
     * @param array $extra Extra information
     */
    public static function setExtra(Array $extra = array()) {
        // Reset the extra array
        self::$_extra = array();
        
        // Get an introspection
        $classIntrospection = new ReflectionClass(__CLASS__);
        
        // Go through the constants
        foreach ($classIntrospection->getConstants() as $constantName => $constantValue) {
            // Extra constants
            if (preg_match('%^EXTRA_%', $constantName)) {
                if (isset($extra[$constantValue])) {
                    self::$_extra[$constantValue] = $extra[$constantValue];
                }
            }
        }
    }
    
    /**
     * Get the extras; if an item is specified, return that value only
     * 
     * @param string $item One of Whisper_Builder_Variable::EXTRA_* constants
     * @return mixed Array if $item is null; Item value if $item is specified, null otherwise
     */
    public static function getExtra($item = null) {
        if (null !== $item) {
            return isset(self::$_extra[$item]) ? self::$_extra[$item] : null;
        }
        
        return self::$_extra;
    }
    
    /**
     * Get the translation for a template variable
     * 
     * @param string $variableName Whisper Builder variable name
     * @return string Translated keyword
     */
    public static function get($variableName) {
        // Get the arguments
        $arguments = array_filter(array_map('trim', explode(',', $variableName)));
        
        // No arguments
        if (!count($arguments)) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('Invalid variable name');
            return null;
        }
        
        // Get the function name
        $functionName = array_shift($arguments);
        
        // Get wether the function is capitalized
        $capitalized = strtoupper($functionName[0]) == $functionName[0];
        
        // Translate the name into a method
        $methodName = self::_computeMethodName($functionName);
        
        // Method not found
        if (!method_exists(__CLASS__, $methodName)) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('No method defined for "' . $variableName . '"');
            return null;
        }
        
        // Call the method
        $result = call_user_func_array(array(__CLASS__, $methodName), $arguments);
        
        // Need capitalization
        if ($capitalized) {
            $result = ucfirst($result);
        }
        
        // All done
        return $result;
    }

    /**
     * Construct the method based on the variable name
     * 
     * @param string $variableName Variable name
     * @return string
     */
    protected static function _computeMethodName($variableName) {
        return '_var' . implode('', array_map('ucfirst', explode(':', strtolower($variableName))));
    }
    
    /**
     * Synonym
     * 
     * @param string $word Common English noun, adjective or adverb
     * @return string String for {syn}
     */
    protected static function _varSynonym($word = '') {
        // No word defined
        if (empty($word)) {
            return '';
        }
        
        // Get a random synonym
        return Whisper_Synonym::getRandom($word);
    }

    /**
     * Current theme's name
     * 
     * @return string Current theme's name
     */
    protected static function _varTheme() {
        // Get the builder
        $builder = Whisper_Builder::getInstance();
        
        // Get the theme's name
        return $builder->getProjectName();
    }

    /**
     * Positive emotion. The last invoked {color} will be used for reference.
     * 
     * @return string Positive emotion; random emotion of no {color} was used before
     */
    protected static function _varEmotion() {
        // Last color's emotion
        if (null !== self::$_lastColor) {
            // Get the emotions
            $emotions = Whisper_Emotion::getByColor(self::$_lastColor);
            
            // Return one
            if (count($emotions)) {
                return $emotions[mt_rand(0, count($emotions) - 1)];
            }
        }
        
        // Random emotion
        return Whisper_Emotion::getRandom();
    }

    /**
     * Random keyword from keywords.txt
     * 
     * @return string Random keyword
     */
    protected static function _varKeyword() {
        // Intentionally hard-coded
        $keywords = array_map('trim', explode(',', file_get_contents(ROOT . '/web/resources/whisper/builder/keywords.txt')));
        
        // Get the builder
        $builder = Whisper_Builder::getInstance();
        
        // Get the framework information
        $frameworkTarget = $builder->getFrameworkTarget();
        
        // Normal casing for Poweramp
        if (Framework::TARGET_POWERAMP == $frameworkTarget) {
            $frameworkTarget = ucfirst(strtolower($frameworkTarget));
        }
        
        // Append and prepend the framework
        foreach ($keywords as $keyword) {
            if (!preg_match('%' . preg_quote($keyword) . '%i', $frameworkTarget)) {
                $keywords[] = $frameworkTarget . ' ' . $keyword;
            }
        }
        
        // Add the framework target to the list
        $keywords[] = $frameworkTarget;
        
        // Append the variants
        if (null !== $frameworkTargetVariants = $builder->getFrameworkTargetVariants()) {
            foreach ($frameworkTargetVariants as $variant) {
                $keywords[] = $variant;
            }
        }
        
        // Shuffle the list
        shuffle($keywords);
        
        // Return a random element
        return $keywords[0];
    }

    /**
     * One of the predominant colors
     * 
     * @return string Predominant color's name
     */
    protected static function _varColor() {
        // Get the builder
        $builder = Whisper_Builder::getInstance();
        
        // Get the predominant colors
        $colors = $builder->getPredominantColors();
        
        // Shuffle the array
        shuffle($colors);
        
        // Store this color
        self::$_lastColor = $colors[0];
        
        // Return the last color
        return self::$_lastColor;
    }
    
    /**
     * Alias of {color}
     * 
     * @return string Predominant color's name
     */
    protected static function _varColour() {
        return self::_varColor();
    }

    /**
     * Inspiration word from the user-defined list
     * 
     * @return string Inspiration word(s)
     */
    protected static function _varInspiration() {
        // Get the builder
        $builder = Whisper_Builder::getInstance();
        
        // Get the framework information
        $inspirationArray = $builder->getInspiration();
        
        // More than one tag was specified
        if (self::getExtra(self::EXTRA_INSPIRATION_TAGS) > 1) {
            // Verify the extra item is an array
            if (!isset(self::$_extra[self::INTRO_INSPIRATION_USED])) {
                self::$_extra[self::INTRO_INSPIRATION_USED] = array();
            }
            
            // Get the remaining items array
            $remainingItems = array_values(array_diff($inspirationArray, self::$_extra[self::INTRO_INSPIRATION_USED]));
            
            // Items remaining
            if (count($remainingItems)) {
                // Prepare the result
                $result = $remainingItems[mt_rand(0, count($remainingItems) - 1)];
                
                // Store it
                self::$_extra[self::INTRO_INSPIRATION_USED][] = $result;
            } else {
                // No other choice but to return a random item
                $result = $inspirationArray[mt_rand(0, count($inspirationArray) - 1)];
            }
            
            // All done
            return $result;
        }
        
        // Get the last item from the array
        $lastItem = array_pop($inspirationArray);
        
        // Prepare the string
        $result = trim(implode(', ', $inspirationArray));
        
        // Get the final result
        $result = strlen($result) > 1 ? ($result . ' and ') . $lastItem : $lastItem;
        
        // All done
        return $result;
    }

    /**
     * Random alternative
     * 
     * @param string $list Comma-separated list of alternatives
     * @return string Inspiration word(s)
     */
    protected static function _varAlt($list = null) {
        // Get the method arguments
        $arguments = array_values(array_filter(array_map('trim', func_get_args())));
        
        // No arguments defined
        if (!count($arguments)) {
            return '';
        }
        
        // Return a random argument
        return $arguments[mt_rand(0, count($arguments) - 1)];
    }
}

/* EOF */