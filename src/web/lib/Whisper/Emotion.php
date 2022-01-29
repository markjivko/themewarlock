<?php
/**
 * Theme Warlock - Whisper_Emotion
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Whisper_Emotion {

    const D_COLORS     = 'colors';
    const D_COLORS_DEF = 'colors-def';
    const D_POSITIVE   = 'positive';
    
    /**
     * Data
     * 
     * @var array
     */
    protected static $_data;
    
    /**
     * Return the positive emotions associated with a color
     * 
     * @param string $color Color name - as provided by Image::$colors
     * @return string[] Positive emotions
     */
    public static function getByColor($color) {
        // Color not defined
        if (!isset(Image::$colors[$color])) {
            return self::getRandom();
        }
        
        // Initialize the data store
        self::_init();
        
        // Get the image instance
        $image = new Image();
        
        // Get the color by name
        $colorName = $image->getColorName($image->rgbToHex(Image::$colors[$color]), self::$_data[self::D_COLORS_DEF]);
        
        // Prepare the emotions list
        $emotions = self::$_data[self::D_COLORS][$colorName];
        
        // Get the positive emotions array
        $positiveEmotions = self::$_data[self::D_POSITIVE];
        
        // Shuffle them
        shuffle($positiveEmotions);
        
        // Append 5 positive emotions
        for ($i = 0; $i <= 4; $i++) {
            $emotions[] = $positiveEmotions[$i];
        }
        
        // Shuffle the results
        shuffle($emotions);

        // All done
        return $emotions;
    }
    
    /**
     * Initialize the data
     * 
     * @return null
     */
    protected static function _init() {
        if (!isset(self::$_data)) {
            self::$_data = json_decode(file_get_contents(ROOT . '/web/resources/whisper/data/emotions.json'), true);
        }
    }
    
    /**
     * Return a random positive emotion
     * 
     * @return string Positive emotion
     */
    public static function getRandom() {
        // Initialize the data store
        self::_init();
        
        // Get a random emotion
        return self::$_data[self::D_POSITIVE][mt_rand(0, count(self::$_data[self::D_POSITIVE]) - 1)];
    }
}

/* EOF */