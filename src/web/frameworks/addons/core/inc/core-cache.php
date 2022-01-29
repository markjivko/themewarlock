<?php
/**
 * {project.destProjectName} Core Cache
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

class St_CoreCache {

    /**
     * Retrieves the absolute path (eg: /home/user/public_html/wp-content/themes/my_theme) to the directory of the current theme.
     * 
     * @link https://developer.wordpress.org/reference/functions/get_template_directory/
     */
    const GET_TEMPLATE_DIRECTORY = 'get_template_directory';
    
    /**
     * Retrieve theme directory URI.
     * 
     * @link https://developer.wordpress.org/reference/functions/get_template_directory_uri/
     */
    const GET_TEMPLATE_DIRECTORY_URI = 'get_template_directory_uri';
    
    /**
     * Retrieves the URL to the content directory.
     * 
     * @link https://developer.wordpress.org/reference/functions/content_url/
     */
    const CONTENT_URL = 'content_url';
    
    /**
     * Data storage
     */
    protected static $_results = array();
    
    /**
     * Get a result from cache
     * 
     * @param   string $functionName WordPress function name
     * @param[] mixed  $args         (optional) Additional arguments are passed along
     */
    public static function get($functionName) {
        // Get the function arguments
        $functionArguments = func_get_args();

        // Get the function name
        $functionName = trim(array_shift($functionArguments));

        // Prepare the cache key; md5 to avoid unnecessary memory consumption
        $cacheKey = $functionName . (count($functionArguments) ? json_encode($functionArguments) : '');
        
        // Function result not generated
        if (!isset(self::$_results[$cacheKey])) {
            // Prepare the result
            self::$_results[$cacheKey] = null;
            
            // Valid function
            if (function_exists($functionName)) {
                self::$_results[$cacheKey] = call_user_func_array($functionName, $functionArguments);
            }
        }

        // All done
        return self::$_results[$cacheKey];
    }

}

/* EOF */