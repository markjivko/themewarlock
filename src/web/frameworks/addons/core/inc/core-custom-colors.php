<?php
/**
 * Add support for custom colors
 * 
 * @link https://developer.wordpress.org/themes/customize-api/
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

if (!class_exists('St_Colors')) {
    /**
     * Custom colors palette value
     */
    class St_Colors_Palette_Value {
        /**
         * Alpha channel value; [0,255]
         * 
         * @var int
         */
        protected $_alphaValue = 255;
        
        /**
         * Color value
         * 
         * @var int[] RGB Array
         */
        protected $_value;

        /**
         * Palette color value
         * 
         * @param array $rgbArray   RGB color array
         * @param int   $alphaValue Alpha channel value in [0,255] range
         */
        public function __construct(Array $rgbArray, $alphaValue = 255) {
            // Ignore the Alpha value (if any)
            $this->_value = array_slice(array_values($rgbArray), 0, 3);
            
            // Store the alpha channel
            $this->_alphaValue = $alphaValue;
        }
        
        /**
         * Get the color in hex format
         * 
         * @example "#000000"
         * @return string
         */
        public function hex() {
            return sprintf("#%02x%02x%02x", $this->_value[0], $this->_value[1], $this->_value[2]);
        }
        
        /**
         * Get the color in rgb() format
         * 
         * @example "rgb(0,0,0)"
         * @return string
         */
        public function rgb() {
            return 'rgb(' . implode(', ', $this->_value) . ')';
        }
        
        /**
         * Get the color in rgba() format
         * 
         * @param int $alphaValue (optional) Alpha channel value [0,255]
         * @example "rgba(0,0,0,0.5)"
         * @return string
         */
        public function rgba($alphaValue = null) {
            // Set the default value for the Alpha channel
            if (null === $alphaValue) {
                $alphaValue = $this->_alphaValue;
            }
            
            // Alpha channel is optional
            if (255 === $alphaValue) {
                return $this->rgb();
            }
            
            // Set the alpha channel in [0,1] range
            return 'rgba(' . implode(', ', $this->_value) . ', ' . round($alphaValue / 255, 3) . ')';
        }
        
        /**
         * Get the original color as an RGB array
         * 
         * @return int[] RGB values
         */
        public function value() {
            return $this->_value;
        }
    }
    
    /**
     * Custom colors palette
     */
    class St_Colors_Palette {
        
        // Cache constants
        const CACHE_PREFIX_RGB_TO_HSL = 'rgbToHsl';
        const CACHE_PREFIX_HSL_TO_RGB = 'hslToRgb';
        
        // HSL-RGB conversion cache
        protected static $_cache = array();

        /**
         * Alpha channel value; [0,255]
         * 
         * @var int
         */
        protected $_alphaValue = 255;
        
        /**
         * Original RGB color
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_original;

        /**
         * Complement RGB color
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_complement;

        /**
         * Array of analogous variations
         * 
         * @var St_Colors_Palette_Value[]
         */
        protected $_analogousVariations;

        /**
         * Array of complementary analogous variations
         * 
         * @var St_Colors_Palette_Value[]
         */
        protected $_complementAnalogousVariations;

        /**
         * Array of shade variations
         * 
         * @var St_Colors_Palette_Value[]
         */
        protected $_shadeVariations;

        /**
         * Array of complementary shade variations
         *
         * @var St_Colors_Palette_Value[]
         */
        protected $_complementShadeVariations;

        /**
         * Array of saturation variations
         * 
         * @var St_Colors_Palette_Value[]
         */
        protected $_saturationVariations;

        /**
         * Array of complementary saturation variations
         * 
         * @var St_Colors_Palette_Value[]
         */
        protected $_complementSaturationVariations;

        /**
         * Color contrasting with the original
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_contrast;

        /**
         * Color contrasting with the complement
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_complementContrast;

        /**
         * Black/White contrasting with the original
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_contrastNonColor;

        /**
         * Black/White contrasting with the complement
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_complementContrastNonColor;

        /**
         * Pure color
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_pure;

        /**
         * Complement pure color
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_complementPure;

        /**
         * Black or white version
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_nonColor;

        /**
         * Complement black or white version
         * 
         * @var St_Colors_Palette_Value
         */
        protected $_complementNonColor;

        /**
         * HSL cache
         * 
         * @var array Array of ["R,G,B" => HSL array]
         */
        protected $_hslCache = array();

        /**
         * Palette constructor
         * 
         * @param string $colorHex Hex RRGGBB color
         */
        public function __construct($colorHex, $alphaValue = 255) {
            // The Alpha value is an Integer
            $alphaValue = intval($alphaValue);
            
            // From 0 to 255
            $alphaValue = $alphaValue < 0 ? 0 : ($alphaValue > 255 ? 255 : $alphaValue);
            
            // Store the alpha channel
            $this->_alphaValue = $alphaValue;
            
            // Save the original color
            $this->_original = new St_Colors_Palette_Value($this->_hexToRgb($colorHex), $this->_alphaValue);
        }

        /**
         * Returns the Input color
         * 
         * @return St_Colors_Palette_Value
         */
        public function original() {
            return $this->_original;
        }

        /**
         * Get the complement of the provided color
         * 
         * @return St_Colors_Palette_Value
         */
        public function complement() {
            if (!isset($this->_complement)) {
                // Get the HSL value of the original color
                $hsl = $this->_rgbToHsl($this->original()->value());

                // Get the new hue value
                $hsl[0] += 180;

                // Greater value, needs adjusting
                $hsl[0] = $hsl[0] > 360 ? $hsl[0] - 360 : $hsl[0];

                // Store the value back as RGB
                $this->_complement = new St_Colors_Palette_Value($this->_hslToRgb($hsl), $this->_alphaValue);
            }

            return $this->_complement;
        }

        /**
         * Return the analogous variations
         * 
         * @param int $amount Hue variation amount (degrees)
         * @param int $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        public function analogousVariations($amount = 5, $count = 10) {
            if (!isset($this->_analogousVariations)) {
                $this->_analogousVariations = $this->_variations($this->original()->value(), $amount, $count);
            }

            return $this->_analogousVariations;
        }

        /**
         * Return the analogous variations on the complementary color
         * 
         * @param int $amount Hue variation amount (degrees)
         * @param int $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        public function complementAnalogousVariations($amount = 5, $count = 10) {
            if (!isset($this->_complementAnalogousVariations)) {
                $this->_complementAnalogousVariations = $this->_variations($this->complement()->value(), $amount, $count);
            }

            return $this->_complementAnalogousVariations;
        }

        /**
         * Get a lighter shade of the original color
         * 
         * @return St_Colors_Palette_Value
         */
        public function lighter() {
            return $this->shadeVariations()[count($this->shadeVariations()) - 1];
        }
        
        /**
         * Get a lighter shade of the complementary color
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementLighter() {
            return $this->complementShadeVariations()[count($this->complementShadeVariations()) - 1];
        }
        
        /**
         * Get a darker shade of the original color
         * 
         * @return St_Colors_Palette_Value
         */
        public function darker() {
            return $this->shadeVariations()[0];
        }
        
        /**
         * Get a darker shade of the complementary color
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementDarker() {
            return $this->complementShadeVariations()[0];
        }
        
        /**
         * Get the lightest shade of the original color
         * 
         * @return St_Colors_Palette_Value
         */
        public function lightest() {
            return $this->shadeVariations(4)[count($this->shadeVariations(4)) - 1];
        }
        
        /**
         * Get the lightest shade of the complementary color
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementLightest() {
            return $this->complementShadeVariations(4)[count($this->complementShadeVariations(4)) - 1];
        }
        
        /**
         * Get the darkest shade of the original color
         * 
         * @return St_Colors_Palette_Value
         */
        public function darkest() {
            return $this->shadeVariations(4)[0];
        }
        
        /**
         * Get the darkest shade of the complementary color
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementDarkest() {
            return $this->complementShadeVariations(4)[0];
        }
        
        /**
         * Return the shade variations
         * 
         * @param int $amount Luminosity variation amount (percent)
         * @param int $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        public function shadeVariations($amount = 2, $count = 10) {
            if (!isset($this->_shadeVariations)) {
                $this->_shadeVariations = $this->_shades($this->original()->value(), $amount, $count);
            }

            return $this->_shadeVariations;
        }

        /**
         * Return the shade variations on the complementary color
         * 
         * @param int $amount Luminosity variation amount (percent)
         * @param int $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        public function complementShadeVariations($amount = 2, $count = 10) {
            if (!isset($this->_complementShadeVariations)) {
                $this->_complementShadeVariations = $this->_shades($this->complement()->value(), $amount, $count);
            }

            return $this->_complementShadeVariations;
        }

        /**
         * Return the saturation variations
         * 
         * @param int $amount Saturation variation amount (percent)
         * @param int $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        public function saturationVariations($amount = 20, $count = 10) {
            if (!isset($this->_saturationVariations)) {
                $this->_saturationVariations = $this->_saturations($this->original()->value(), $amount, $count);
            }

            return $this->_saturationVariations;
        }

        /**
         * Return the saturation variations on the complementary color
         * 
         * @param int $amount Saturation variation amount (percent)
         * @param int $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        public function complementSaturationVariations($amount = 20, $count = 10) {
            if (!isset($this->_complementSaturationVariations)) {
                $this->_complementSaturationVariations = $this->_saturations($this->complement()->value(), $amount, $count);
            }

            return $this->_complementSaturationVariations;
        }

        /**
         * Get the contrasting color
         * 
         * @return St_Colors_Palette_Value
         */
        public function contrast() {
            if (!isset($this->_contrast)) {
                $this->_contrast = new St_Colors_Palette_Value($this->_contrast($this->original()->value()), $this->_alphaValue);
            }

            return $this->_contrast;
        }

        /**
         * Get the complement contrasting color
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementContrast() {
            if (!isset($this->_complementContrast)) {
                $this->_complementContrast = new St_Colors_Palette_Value($this->_contrast($this->complement()->value()), $this->_alphaValue);
            }

            return $this->_complementContrast;
        }
        
        /**
         * Get the contrasting black/white
         * 
         * @return St_Colors_Palette_Value
         */
        public function contrastNonColor() {
            if (!isset($this->_contrastNonColor)) {
                $this->_contrastNonColor = new St_Colors_Palette_Value($this->_contrast($this->original()->value(), true), $this->_alphaValue);
            }

            return $this->_contrastNonColor;
        }

        /**
         * Get the complement contrasting black/white
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementContrastNonColor() {
            if (!isset($this->_complementContrastNonColor)) {
                $this->_complementContrastNonColor = new St_Colors_Palette_Value($this->_contrast($this->complement()->value(), true), $this->_alphaValue);
            }

            return $this->_complementContrastNonColor;
        }

        /**
         * Get the pure color
         * 
         * @return St_Colors_Palette_Value
         */
        public function pure() {
            if (!isset($this->_pure)) {
                $this->_pure = new St_Colors_Palette_Value($this->_pure($this->original()->value()), $this->_alphaValue);
            }

            return $this->_pure;
        }

        /**
         * Get the complement pure color
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementPure() {
            if (!isset($this->_complementPure)) {
                $this->_complementPure = new St_Colors_Palette_Value($this->_pure($this->complement()->value()), $this->_alphaValue);
            }

            return $this->_complementPure;
        }

        /**
         * Get the best fit (pure color or contrast)
         * 
         * @return St_Colors_Palette_Value
         */
        public function pureContrast() {
            return new St_Colors_Palette_Value($this->_pureContrast($this->original()->value(), $this->pure()->value(), $this->contrast()->value()), $this->_alphaValue);
        }

        /**
         * Get the best fit (pure color or contrast)
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementPureContrast() {
            return new St_Colors_Palette_Value($this->_pureContrast($this->complement()->value(), $this->complementPure()->value(), $this->complementContrast()->value()), $this->_alphaValue);
        }

        /**
         * Get the black or white version of the original color
         * 
         * @return St_Colors_Palette_Value
         */
        public function nonColor() {
            if (!isset($this->_nonColor)) {
                $this->_nonColor = new St_Colors_Palette_Value($this->_nonColor($this->original()->value()), $this->_alphaValue);
            }

            return $this->_nonColor;
        }

        /**
         * Get the black or white version of the complement
         * 
         * @return St_Colors_Palette_Value
         */
        public function complementNonColor() {
            if (!isset($this->_complementNonColor)) {
                $this->_complementNonColor = new St_Colors_Palette_Value($this->_nonColor($this->complement()->value()), $this->_alphaValue);
            }

            return $this->_complementNonColor;
        }
        
        /**
         * Get the best fit (pure color or contrast)
         * 
         * @param array $original Original RGB color
         * @param array $pure     Pure RGB color
         * @param array $contrast Contrast RGB color
         * @return array RGB color
         */
        protected function _pureContrast($original, $pure, $contrast) {
            if ($this->_getSimilarity($original, $pure)) {
                return $contrast;
            }

            return $pure;
        }

        /**
         * Determine wheter 2 colors are similar
         * 
         * @param array $colorA Color A
         * @param array $colorB Color B
         * @param int $distinctPercent Distinct percent
         * @return boolean
         */
        protected function _getSimilarity($colorA, $colorB, $distinctPercent = 15) {
            // Get the HSL version of color A
            $hslA = $this->_rgbToHsl($colorA);

            // Get the HSL version of color B
            $hslB = $this->_rgbToHsl($colorB);

            // Get the Hue diff in degrees
            $hueDiff = abs($hslB[0] - $hslA[0]);
            $hueDiff = $hueDiff > 180 ? (360 - $hueDiff) : $hueDiff;

            // Hue identity percent
            $hueIdentityPercent = -100/180 * $hueDiff + 100;

            // Saturation identity percent
            $satIdentityPercent = -100 * abs($hslB[1] - $hslA[1]) + 100;

            // Luminosity identity percent
            $lumIdentityPercent = -100 * abs($hslB[2] - $hslA[2]) + 100;

            // Get the actual identity percent
            $identityPercent = pow($hueIdentityPercent * $satIdentityPercent * $lumIdentityPercent, 1/3);

            // Colors are distinct
            if ($identityPercent >= 100 - $distinctPercent) {
                // Colors are similar
                return true;
            }

            // All done
            return false;
        }

        /**
         * Get the black or white version of the provided color
         * 
         * @param array $color RGB color
         * @return array RGB color
         */
        protected function _nonColor(Array $color) {
            // Get the HSL values
            $hsl = $this->_rgbToHsl($color);

            // Set the saturation to 0
            $hsl[1] = 0;

            // Set the brightness
            $hsl[2] = $hsl[2] >= 0.5 ? 1 : 0;

            // All done
            return $this->_hslToRgb($hsl);
        }

        /**
         * Get the pure color based on the Input
         * 
         * @param array $color RGB color
         * @return array RGB color
         */
        protected function _pure(Array $color) {
            // Get the HSL values
            $hsl = $this->_rgbToHsl($color);

            // Set the saturation to 1
            $hsl[1] = 1;

            // All done
            return $this->_hslToRgb($hsl);
        }

        /**
         * Get a palette of shade variations on a provided color
         * 
         * @param array $color  RGB color
         * @param int   $amount Luminosity variation amount (percent)
         * @param int   $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        protected function _shades(Array $color, $amount = 5, $count = 10) {
            // Prepare the result
            $result = array();

            // Get the hue, saturation, luminosity
            $hsl = $this->_rgbToHsl($color);

            // Get the starting hue variation
            $luminosityVariation = - ceil($amount * $count) / 2;

            // Go through the count
            for ($i = 1; $i <= $count; $i++) {
                // Increase the hue
                $luminosityVariation += $amount;

                // New hue
                $newLuminosity = $hsl[2] + $luminosityVariation / 100;

                // Value too high
                if ($newLuminosity > 1) {
                    $newLuminosity = 1;
                }

                // Value too low
                if ($newLuminosity < 0) {
                    $newLuminosity = 0;
                }

                // Store the result
                $result[] = new St_Colors_Palette_Value($this->_hslToRgb(array($hsl[0], $hsl[1], $newLuminosity)), $this->_alphaValue);
            }

            // All done
            return $result;
        }

        /**
         * Get a palette of saturation variations on a provided color
         * 
         * @param array $color  RGB color
         * @param int   $amount Saturation variation amount (percent)
         * @param int   $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        protected function _saturations(Array $color, $amount = 20, $count = 10) {
            // Prepare the result
            $result = array();

            // Get the hue, saturation, luminosity
            $hsl = $this->_rgbToHsl($color);

            // Get the starting hue variation
            $saturationVariation = - ceil($amount * $count) / 2;

            // Go through the count
            for ($i = 1; $i <= $count; $i++) {
                // Increase the hue
                $saturationVariation += $amount;

                // New hue
                $newSaturation = $hsl[1] + $saturationVariation / 100;

                // Value too high
                if ($newSaturation > 1) {
                    $newSaturation = 1;
                }

                // Value too low
                if ($newSaturation < 0) {
                    $newSaturation = 0;
                }

                // Store the result
                $result[] = new St_Colors_Palette_Value($this->_hslToRgb(array($hsl[0], $newSaturation, $hsl[2])), $this->_alphaValue);
            }

            // All done
            return $result;
        }

        /**
         * Get a palette of analogous variations on a provided color
         * 
         * @param array $color  RGB color
         * @param int   $amount Hue variation amount (degrees)
         * @param int   $count  Number of colors
         * @return St_Colors_Palette_Value[]
         */
        protected function _variations(Array $color, $amount = 5, $count = 10) {
            // Prepare the result
            $result = array();

            // Get the hue, saturation, luminosity
            $hsl = $this->_rgbToHsl($color);

            // Get the starting hue variation
            $hueVariation = - ceil($amount * $count) / 2;

            // Go through the count
            for ($i = 1; $i <= $count; $i++) {
                // Increase the hue
                $hueVariation += $amount;

                // New hue
                $newHue = $hsl[0] + $hueVariation;

                // Value too high
                if ($newHue > 360) {
                    $newHue -= 360;
                }

                // Value too low
                if ($newHue < 0) {
                    $newHue += 360;
                }

                // Store the result
                $result[] = new St_Colors_Palette_Value($this->_hslToRgb(array($newHue, $hsl[1], $hsl[2])), $this->_alphaValue);
            }

            // All done
            return $result;
        }

        /**
         * Get a color contrasting with the Input
         * 
         * @param array   $color    RGB color
         * @param boolean $nonColor Return Black/White
         * @return array RGB color
         */
        protected function _contrast(Array $color, $nonColor = false) {
            // Get the hue, saturation, luminosity
            list($hue, $saturation, $luminosity) = $this->_rgbToHsl($color);
            
            // Get the new luminosity
            $l = 1 - $luminosity;
            
            // Set the brightness
            if ($nonColor) {
                // Black/White
                $l = ($l >= 0.35 ? 1 : 0);
                
                // Stop here
                return $this->_hslToRgb(array($hue, $saturation, $l));
            }

            // Get the luminosity delta
            $dL = -4 * pow($l, 2) + 4 * $l;

            // Get the new saturation
            $s = abs($saturation - $dL);

            // Get the saturation delta
            $dS = -4 * pow($s, 2) + 4 * $s;

            // Combined delta
            $dSL = ($dL + $dS) / 2;

            // Prepare the new hue
            $h = $hue;

            // Color luminosity is [0.35, 0.65]
            if ($dL >= 0.91) {
                // Get the oposite color
                $h = $hue + 180 * $dSL;

                // Round the color circle
                if ($h > 360) {
                    $h -= 360;
                }
            }
            
            // All done
            return $this->_hslToRgb(array($h, $s, $l));
        }
        
        /**
         * Prepare a cache key as a string
         * 
         * @param mixed  $key    Key
         * @param string $prefix Cache prefix
         * @return string Cache key
         */
        protected static function _cacheKey($key, $prefix = self::CACHE_PREFIX_RGB_TO_HSL) {
            return $prefix . '-' . var_export($key, true);
        }

        /**
         * Get a chached value
         * 
         * @param mixed  $key    Key
         * @param string $prefix Cache prefix
         * @return mixed Null if nothing found
         */
        protected static function _cacheGet($key, $prefix = self::CACHE_PREFIX_RGB_TO_HSL) {
            // Get the key
            $fullKey = self::_cacheKey($key, $prefix);

            // Found a cached element
            if (isset(self::$_cache[$fullKey])) {
                // Return it
                return self::$_cache[$fullKey];
            }

            // Nothing found
            return null;
        }
        
        /**
         * Set element in cache
         * 
         * @param mixed  $key    Key
         * @param mixed  $value  Value - anything except null
         * @param string $prefix Cache prefix
         * @return boolean False if value is null
         */
        protected static function _cacheSet($key, $value, $prefix = self::CACHE_PREFIX_RGB_TO_HSL) {
            // Test the value
            if (null !== $value) {
                self::$_cache[self::_cacheKey($key, $prefix)] = $value;

                // All went well
                return true;
            }

            // Invalid value
            return false;
        }

        /**
         * RGB to HSL
         * 
         * @param array $rgbArray RGB colors
         */
        protected function _rgbToHsl($rgbArray) {
            // Get R, G, B
            list($r, $g, $b) = array_values($rgbArray);

            // Call the cache
            if (null !== $cachedHsl = self::_cacheGet(array($r, $g, $b), self::CACHE_PREFIX_RGB_TO_HSL)) {
                return $cachedHsl;
            }

            // Reduce them
            $r /= 255;
            $g /= 255;
            $b /= 255;

            // Get the max
            $max = max($r, $g, $b);

            // Get the min
            $min = min($r, $g, $b);

            // Get the luminance
            $l = ( $max + $min ) / 2.0;

            // Get the difference
            $d = $max - $min;

            // No difference
            if ($d == 0) {
                $h = $s = 0; // achromatic
            } else {
                $s = $d / ( 1 - abs(2 * $l - 1) );
                switch ($max) {
                    case $r:
                        $h = 60 * fmod(( ( $g - $b ) / $d), 6);
                        if ($b > $g) {
                            $h += 360;
                        }
                        break;

                    case $g:
                        $h = 60 * ( ( $b - $r ) / $d + 2 );
                        break;

                    case $b:
                        $h = 60 * ( ( $r - $g ) / $d + 4 );
                        break;
                }
            }

            // Prepare HLS
            $result = array(round($h, 0), $s, $l);

            // Save to cache
            self::_cacheSet(array($r, $g, $b), $result, self::CACHE_PREFIX_RGB_TO_HSL);

            // All done
            return $result;
        }

        /**
         * Convert HSL to RGB
         * 
         * @param array $hslArray HSL array
         * @return array
         */
        protected function _hslToRgb($hslArray) {
            // Get H, S, L
            list($h, $s, $l) = array_values($hslArray);

            // Call the cache
            if (null !== $cachedRgb = self::_cacheGet(array($h, $s, $l), self::CACHE_PREFIX_HSL_TO_RGB)) {
                return $cachedRgb;
            }

            // Get stuff
            $c = ( 1 - abs(2 * $l - 1) ) * $s;
            $x = $c * ( 1 - abs(fmod(( $h / 60), 2) - 1) );
            $m = $l - ( $c / 2 );

            if ($h < 60) {
                $r = $c;
                $g = $x;
                $b = 0;
            } else if ($h < 120) {
                $r = $x;
                $g = $c;
                $b = 0;
            } else if ($h < 180) {
                $r = 0;
                $g = $c;
                $b = $x;
            } else if ($h < 240) {
                $r = 0;
                $g = $x;
                $b = $c;
            } else if ($h < 300) {
                $r = $x;
                $g = 0;
                $b = $c;
            } else {
                $r = $c;
                $g = 0;
                $b = $x;
            }

            $r = ($r + $m) * 255;
            $g = ($g + $m) * 255;
            $b = ($b + $m) * 255;

            $r = $r < 0 ? 0 : $r;
            $g = $g < 0 ? 0 : $g;
            $b = $b < 0 ? 0 : $b;

            // Prepare the result
            $result = array(floor($r), floor($g), floor($b));

            // Store in cache
            self::_cacheSet(array($h, $s, $l), $result, self::CACHE_PREFIX_HSL_TO_RGB);

            // All done
            return $result;
        }
        
        /**
         * HEX to RGB
         * 
         * @param string $hexColor HEX color (support for ARGB added)
         * @return array R,G,B,A(0-255)
         */
        protected function _hexToRgb($hexColor) {
            // Replace the starting #
            $hexColor = preg_replace('%^#%', '', $hexColor);

            // Short color
            if (strlen($hexColor) < 6) {
                $hexColor = str_pad($hexColor, 6, $hexColor, STR_PAD_RIGHT);
            }

            // Invalid lenght
            if (8 !== strlen($hexColor)) {
                $hexColor = str_pad($hexColor, 8, "ff", STR_PAD_LEFT);
            }

            // Get the channels
            $channels = array_map('hexdec', str_split($hexColor, 2));

            // Set the alpha channel
            $channels[] = array_shift($channels);

            // Return the result
            return $channels;
        }
    }
    
    /**
     * Custom Colors helper class
     * 
     * @example St_Colors::get()->color(1)->original()->toRgb();
     */
    class St_Colors {
        /**
         * Instance of St_Colors
         * 
         * @var St_Colors
         */
        protected static $_instance = null;
        
        /**
         * List of color palettes
         * 
         * @var St_Colors_Palette[]
         */
        protected $_palettes = array();
        
        /**
         * Singleton instance of St_Colors
         * 
         * @return St_Colors
         */
        public static function get() {
            if (null === self::$_instance) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        /**
         * Custom Colors
         */
        protected function __construct() {
{foreach.core.getColors}
            // Get custom color #{@Key} palette
            $this->_palettes[{@Key}] = new St_Colors_Palette(get_theme_mod('st_color{@Key}', {@value.default}), {@value.alpha});
{/foreach.core.getColors}
        }
        
        /**
         * Get the color palette
         * 
         * @param int $colorKey Color Palette key
         * @return St_Colors_Palette
         */
        public function color($colorKey) {
            return isset($this->_palettes[$colorKey]) ? $this->_palettes[$colorKey] : $this->_palettes[1];
        }
    }
}

/**
 * Register the custom colors
 * 
 * @param WP_Customize_Manager $wp_customize WordPress Customize Manager
 */
function {project.prefix}_customize_custom_colors_register($wp_customize) {
{foreach.core.getColors}
    // st_color{@Key}: color setting
    $wp_customize->add_setting('st_color{@Key}', array(
        'default'           => {@value.default},
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_attr',
    ));
            
    // st_color{@Key}: color control
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'st_color{@Key}', array(
        'section' => 'colors',
        'label'   => __({@value.name}, '{project.destDir}'),
    )));
{/foreach.core.getColors}
}
add_action('customize_register', '{project.prefix}_customize_custom_colors_register');

/**
 * Generate the custom CSS
 * 
 * @return string
 */
function {project.prefix}_customize_custom_colors_css() {
    // Prepare the result
    $result = '';
{foreach.core.getInlineCssCode}
    // Add custom color handling for {@Key}
    $result .= {@Value} . PHP_EOL;
{/foreach.core.getInlineCssCode}
    // All done
    return trim($result);
}

/**
 * Enqueue the custom colors CSS
 * 
 * @uses {project.prefix}_customize_custom_colors_css()
 */
function {project.prefix}_customize_custom_colors_enqueue_styles() {
    wp_add_inline_style('{project.destDir}-style', {project.prefix}_customize_custom_colors_css());
}
add_action('wp_enqueue_scripts', '{project.prefix}_customize_custom_colors_enqueue_styles', 2000);

/*EOF*/