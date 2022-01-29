<?php
/**
 * Theme Warlock - Cache
 * 
 * @title      Cache
 * @desc       Data repository
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Cache {

    /**
     * Temporary data storage
     *
     * @var array
     */
    protected static $_data = array();
    
    /**
     * Get the cached data
     * 
     * @param string $cacheKey (optional) Extra cache key salt
     * @return mixed|null Get the cached value or null on failure
     */
    public static function get($cacheKey = null) {
        // Prepare the cache key
        $fullCacheKey = self::_getCacheKey($cacheKey);

        // Get the data
        return isset(self::$_data[$fullCacheKey]) ? self::$_data[$fullCacheKey] : null;
    }
    
    /**
     * Store a value in cache
     * 
     * @param mixed  $value    Value to store in cache
     * @param string $cacheKey (optional) Extra cache key salt
     */
    public static function set($value, $cacheKey = null) {
        // Prepare the cache key
        $fullCacheKey = self::_getCacheKey($cacheKey);

        // (re)write the data
        self::$_data[$fullCacheKey] = $value;
    }
    
    /**
     * Compute the cache key
     * 
     * @param string $cacheKey (optional) Extra cache key salt
     * @return string
     */
    protected static function _getCacheKey($cacheKey = null) {
        // Get the information
        $debugBacktrace = debug_backtrace(null, 3);
        
        // Prepare the caller information
        $callerInfo = end($debugBacktrace);
        
        // Create an unique cache key
        return $callerInfo['file'] . '::' . $callerInfo['function'] . (null === $cacheKey ? '' : '::' . $cacheKey);
    }

}

/* EOF */
