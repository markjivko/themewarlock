<?php

/**
 * Theme Warlock - Console
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class Console {

    /**
     * Cache for CLI checks
     *
     * @var boolean
     */
    protected static $_isCli;
    
    /**
     * Check if currently running in a Command Line Interface
     * 
     * @return boolean
     */
    protected static function _isCli() {
        if (!isset(self::$_isCli)) {
            self::$_isCli = ("cli" === php_sapi_name());
        }
        return self::$_isCli;
    }
    
    /**
     * Output a header H1
     * 
     * @param string $text    Text
     * @param int    $spacing Prefix spacing size
     * @return null
     */
    public static function h1($text, $spacing = 2) {
        // Log the message
        $debugBacktrace = debug_backtrace(null, 2);
        Log::check(Log::LEVEL_INFO) && Log::info($text, $debugBacktrace[0]['file'], $debugBacktrace[0]['line']);

        if (self::_isCli()) {
            // Output the message
            echo PHP_EOL . str_repeat(' ', $spacing) . $text . PHP_EOL . str_repeat(' ', $spacing) . str_repeat('=', strlen($text)) . PHP_EOL;
        }
    }

    /**
     * Output a header H2
     * 
     * @param string $text    Text
     * @param int    $spacing Prefix spacing size
     * @return null
     */
    public static function h2($text, $spacing = 2) {
        // Log the message
        $debugBacktrace = debug_backtrace(null, 2);
        Log::check(Log::LEVEL_DEBUG) && Log::debug($text, $debugBacktrace[0]['file'], $debugBacktrace[0]['line']);

        if (self::_isCli()) {
            // Output the message
            echo PHP_EOL . str_repeat(' ', $spacing) . $text . PHP_EOL . str_repeat(' ', $spacing) . str_repeat('-', strlen($text)) . PHP_EOL;
        }
    }

    /**
     * Output a header H3
     * 
     * @param string $text    Text
     * @param int    $spacing Prefix spacing size
     * @return null
     */
    public static function h3($text, $spacing = 2) {
        // Log the message
        $debugBacktrace = debug_backtrace(null, 2);
        Log::check(Log::LEVEL_DEBUG) && Log::debug($text, $debugBacktrace[0]['file'], $debugBacktrace[0]['line']);

        if (self::_isCli()) {
            // Output the message
            echo PHP_EOL . str_repeat(' ', $spacing) . $text . PHP_EOL . str_repeat(' ', $spacing) . str_repeat('-', strlen($text)) . PHP_EOL;
        }
    }

    /**
     * Output a paragraph
     * 
     * @param string  $text    Text
     * @param boolean $allgood False for error paragraph
     * @param string  $file    File
     * @param string  $line    Line
     * @return null
     */
    public static function p($text, $allgood = true, $file = '', $line = '') {
        // Log the message
        if ('' == $file || '' == $line) {
            $debugBacktrace = debug_backtrace(null, 2);
            $file = $debugBacktrace[0]['file'];
            $line = $debugBacktrace[0]['line'];
        }

        // Log this information
        if (!$allgood) {
            Log::check(Log::LEVEL_WARNING) && Log::warning($text, $file, $line);
        } else {
            Log::check(Log::LEVEL_DEBUG) && Log::debug($text, $file, $line);
        }

        if (self::_isCli()) {
            // Output the message
            echo ' ' . ($allgood ? ' ' : '!') . ' ' . $text . PHP_EOL;
        }
    }

    /**
     * Output a list
     * 
     * @param array $list Text
     */
    public static function li($list, $character = '-', $width = 85) {
        // Log the message
        $debugBacktrace = debug_backtrace(null, 2);
        Log::check(Log::LEVEL_DEBUG) && Log::debug($list, $debugBacktrace[0]['file'], $debugBacktrace[0]['line']);

        if (self::_isCli()) {
            // Output the message
            foreach ($list as $key => $item) {
                // Prepare the prefix
                $prefix = '   ' . $character . ' ' . (is_int($key) ? '' : $key . ': ');

                // Prepare the word wrap length
                $wordWrapLength = strlen($prefix) < $width ? $width - strlen($prefix) : null;

                // Prepare the item
                $itemComponents = preg_split('%[\r\n]+%', $item);

                // Add spacing
                $itemComponents = array_map(function($item) use ($prefix) { return str_repeat(' ', 6) . $item;}, $itemComponents);

                // Recompose the item
                $item = str_repeat(' ', strlen($prefix)) . trim(implode(PHP_EOL, $itemComponents));

                // Output the line
                echo $prefix . trim(wordwrap($item, $wordWrapLength, PHP_EOL . str_repeat(' ', 6))) . PHP_EOL;
            }
        }
    }
    
    /**
     * Output a list of options and return the answer
     * 
     * @param array   $options       Options list
     * @param string  $help          (optional) Help string
     * @param string  $format        (optional) List item format compatible with sprintf
     * @param string  $current       (optional) Current list item key
     * @param string  $currentFormat (optional) Current list item format compatible with sprintf
     * @param boolean $otherAllowed  (optional) Other value except from the ones listed is allowed
     * @return string Option key
     */
    public static function options($options, $help = '', $format = '', $current = null, $currentFormat = '', $otherAllowed = false) {      
        // Inform the user
        if ('' !== $help) {
            Console::p(rtrim($help, ':') . ':');
        }
        
        // Yes/no options
        if (true === $options) {
            $options = array(Csv::TRUE => 'Yes', Csv::FALSE => 'No');
        }
        
        // Log the options
        foreach ($options as $key => $description) {
            // Current element
            if ($current === $key && null !== $current) {
                if ('' === $currentFormat) {
                    $description = '> ' . $description . ' <';
                } else {
                    $description = sprintf($currentFormat, $description);
                }
            } else {
                // Provided a format
                if ('' !== $format) {
                    $description = sprintf($format, $description);
                }
            }
            
            // Output the option
            Console::p('[' . $key . '] ' . $description); 
        }

        // Get the answer
        while (true) {
            if (self::_isCli()) {
                // Get the answer
                $answer = trim(Input::get());

                // Empty answer but current provided
                if (0 != $answer && empty($answer) && null !== $current) {
                    $answer = $current;
                }

                // Valid answer
                if (in_array($answer, array_map('strval', array_keys($options)), true)) {
                    break;
                }
            } else {
                if (in_array($answer = trim(Input::get()), array_map('strval', array_keys($options)), true)) {
                    break;
                }
            }
            
            // Another option is allowed (but not empty)
            if (!empty($answer) && $otherAllowed) {
                break;
            }
        }

        // All done
        return $answer;
    }
}

/*EOF*/