<?php
/**
 * Theme Warlock theme checks
 */
class ThemeWarlock implements themecheck {

    /**
     * Error list
     * 
     * @var string[]
     */
    protected $error = array();

    /**
     * Perform file checks
     * 
     * @param string[] $php_files
     * @param string[] $css_files
     * @param string[] $other_files
     * @return boolean
     */
    function check($php_files, $css_files, $other_files) {
        // Prepare the result
        $status = true;

        // Prepare the RegEx Checks
        $checks = array(
            // Beginning of PHP file, after the comments (if any), check for WPINC
            '%^\s*<\?php\b\s*(?:\/\*.*?\*\/|\/\/[^\r\n]*[\r\n]\s*)*\s*if\s*\(\s*\!\s*defined\s*\(\s*[\'"]WPINC[\'"]\s*\)\s*\)\s*\{\s*die\b%ims' => 'PHP File not protected against direct access with <b>!defined(\'WPINC\')</b> check at the beginning of the file!',
        );

        // Go through all the PHP Files
        foreach ($php_files as $php_key => $phpfile) {
            foreach ($checks as $key => $check) {
                checkcount();
                if (!preg_match($key, $phpfile, $matches)) {
                    // Get the file name
                    $filename = tc_filename($php_key);
                    
                    // Append the error
                    $this->error[] = sprintf('<span class="tc-lead tc-warning">WARNING</span>: <strong>%1$s</strong>. %2$s', $filename, $check);
                    
                    // Set the flag
                    $status = false;
                }
            }
        }

        // All done
        return $status;
    }

    /**
     * Get the error list
     * 
     * @return string[]
     */
    function getError() {
        return $this->error;
    }
}

// Add our theme checks
$themechecks[] = new ThemeWarlock();

/*EOF*/