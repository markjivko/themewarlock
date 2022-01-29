<?php
/**
 * Theme Warlock - Palette
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Palette {
    
    /**
     * Original RGB color
     * 
     * @var array
     */
    protected $_original;
    
    /**
     * Complement RGB color
     * 
     * @var array
     */
    protected $_complement;
    
    /**
     * Array of analogous variations
     * 
     * @var array
     */
    protected $_analogousVariations;
    
    /**
     * Array of complementary analogous variations
     * 
     * @var array
     */
    protected $_complementAnalogousVariations;
    
    /**
     * Array of shade variations
     * 
     * @var array
     */
    protected $_shadeVariations;
    
    /**
     * Array of complementary shade variations
     *
     * @var array
     */
    protected $_complementShadeVariations;
    
    /**
     * Array of saturation variations
     * 
     * @var array
     */
    protected $_saturationVariations;
    
    /**
     * Array of complementary saturation variations
     * 
     * @var array
     */
    protected $_complementSaturationVariations;
    
    /**
     * Color contrasting with the original
     * 
     * @var array
     */
    protected $_contrast;
    
    /**
     * Color contrasting with the complement
     * 
     * @var array
     */
    protected $_complementContrast;
    
    /**
     * Pure color
     * 
     * @var array
     */
    protected $_pure;
    
    /**
     * Complement pure color
     * 
     * @var array
     */
    protected $_complementPure;
    
    /**
     * Black or white version
     * 
     * @var array
     */
    protected $_nonColor;
    
    /**
     * Complement black or white version
     * 
     * @var array
     */
    protected $_complementNonColor;
    
    /**
     * Image instance
     * 
     * @var Image
     */
    protected $_image;
    
    /**
     * HSL cache
     * 
     * @var array Array of ["R,G,B" => HSL array]
     */
    protected $_hslCache = array();
    
    /**
     * Palette constructor
     * 
     * @param array $color RGB color
     */
    public function __construct(Array $color) {
        // Save the original color
        $this->_original = array_values($color);
        
        // Get the image instance
        $this->_image = new Image();
    }
    
    /**
     * Returns the Input color
     * 
     * @return array RGB color
     */
    public function original() {
        return $this->_original;
    }
    
    /**
     * Get the complement of the provided color
     * 
     * @return array RGB
     */
    public function complement() {
        if (!isset($this->_complement)) {
            // Get the HSL value of the original color
            $hsl = $this->_hsl($this->_original);

            // Get the new hue value
            $hsl[0] += 180;

            // Greater value, needs adjusting
            $hsl[0] = $hsl[0] > 360 ? $hsl[0] - 360 : $hsl[0];

            // Store the value back as RGB
            $this->_complement = $this->_image->hslToRgb($hsl);
        }
        
        return $this->_complement;
    }
    
    /**
     * Return the analogous variations
     * 
     * @param int $amount Hue variation amount (degrees)
     * @param int $count  Number of colors
     * @return array Array of RGB arrays
     */
    public function analogousVariations($amount = 5, $count = 10) {
        if (!isset($this->_analogousVariations)) {
            $this->_analogousVariations = $this->_variations($this->_original, $amount, $count);
        }
        
        return $this->_analogousVariations;
    }
    
    /**
     * Return the analogous variations on the complementary color
     * 
     * @param int $amount Hue variation amount (degrees)
     * @param int $count  Number of colors
     * @return array Array of RGB arrays
     */
    public function complementAnalogousVariations($amount = 5, $count = 10) {
        if (!isset($this->_complementAnalogousVariations)) {
            $this->_complementAnalogousVariations = $this->_variations($this->complement(), $amount, $count);
        }
        
        return $this->_complementAnalogousVariations;
    }
    
    /**
     * Return the shade variations
     * 
     * @param int $amount Luminosity variation amount (percent)
     * @param int $count  Number of colors
     * @return array Array of RGB arrays
     */
    public function shadeVariations($amount = 5, $count = 10) {
        if (!isset($this->_shadeVariations)) {
            $this->_shadeVariations = $this->_shades($this->_original, $amount, $count);
        }
        
        return $this->_shadeVariations;
    }
    
    /**
     * Return the shade variations on the complementary color
     * 
     * @param int $amount Luminosity variation amount (percent)
     * @param int $count  Number of colors
     * @return array Array of RGB arrays
     */
    public function complementShadeVariations($amount = 5, $count = 10) {
        if (!isset($this->_complementShadeVariations)) {
            $this->_complementShadeVariations = $this->_shades($this->complement(), $amount, $count);
        }
        
        return $this->_complementShadeVariations;
    }

    /**
     * Return the saturation variations
     * 
     * @param int $amount Saturation variation amount (percent)
     * @param int $count  Number of colors
     * @return array Array of RGB arrays
     */
    public function saturationVariations($amount = 20, $count = 10) {
        if (!isset($this->_saturationVariations)) {
            $this->_saturationVariations = $this->_saturation($this->_original, $amount, $count);
        }
        
        return $this->_saturationVariations;
    }
    
    /**
     * Return the saturation variations on the complementary color
     * 
     * @param int $amount Saturation variation amount (percent)
     * @param int $count  Number of colors
     * @return array Array of RGB arrays
     */
    public function complementSaturationVariations($amount = 20, $count = 10) {
        if (!isset($this->_complementSaturationVariations)) {
            $this->_complementSaturationVariations = $this->_saturation($this->complement(), $amount, $count);
        }
        
        return $this->_complementSaturationVariations;
    }
    
    /**
     * Get the contrasting color
     * 
     * @return array RGB color
     */
    public function contrast() {
        if (!isset($this->_contrast)) {
            $this->_contrast = $this->_contrast($this->_original);
        }
        
        return $this->_contrast;
    }
    
    /**
     * Get the complement contrasting color
     * 
     * @return array RGB color
     */
    public function complementContrast() {
        if (!isset($this->_complementContrast)) {
            $this->_complementContrast = $this->_contrast($this->complement());
        }
        
        return $this->_complementContrast;
    }
    
    /**
     * Get the HSL version of the original
     * 
     * @return array HSL color
     */
    public function hsl() {
        return $this->_hsl($this->_original);
    }
    
    /**
     * Get the HSL values for an RGB Input
     * 
     * @param array $color RGB color
     * @return array HSL color
     */
    protected function _hsl($color) {
        // Get the color name
        $colorName = implode(',', $color);
        
        // Get the HSL from the cache
        if (!isset($this->_hslCache[$colorName])) {
            $this->_hslCache[$colorName] = $this->_image->rgbToHsl(array_values($color));
        }
        
        // All done
        return $this->_hslCache[$colorName];
    }
    
    /**
     * Get the pure color
     * 
     * @return array RGB color
     */
    public function pure() {
        if (!isset($this->_pure)) {
            $this->_pure = $this->_pure($this->_original);
        }
        
        return $this->_pure;
    }
    
    /**
     * Get the complement pure color
     * 
     * @return array RGB color
     */
    public function complementPure() {
        if (!isset($this->_complementPure)) {
            $this->_complementPure = $this->_pure($this->complement());
        }
        
        return $this->_complementPure;
    }
    
    /**
     * Get the best fit (pure color or contrast)
     * 
     * @return array RGB color
     */
    public function pureContrast() {
        return $this->_pureContrast($this->_original, $this->pure(), $this->contrast());
    }
    
    /**
     * Get the best fit (pure color or contrast)
     * 
     * @return array RGB color
     */
    public function complementPureContrast() {
        return $this->_pureContrast($this->complement(), $this->complementPure(), $this->complementContrast());
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
        $hslA = $this->_hsl($colorA);
        
        // Get the HSL version of color B
        $hslB = $this->_hsl($colorB);

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
     * Get the black or white version of the original color
     * 
     * @return array RGB color
     */
    public function nonColor() {
        if (!isset($this->_nonColor)) {
            $this->_nonColor = $this->_nonColor($this->_original);
        }
        
        return $this->_nonColor;
    }
    
    /**
     * Get the black or white version of the complement
     * 
     * @return array RGB color
     */
    public function complementNonColor() {
        if (!isset($this->_complementNonColor)) {
            $this->_complementNonColor = $this->_nonColor($this->complement());
        }
        
        return $this->_complementNonColor;
    }
    
    /**
     * Get the black or white version of the provided color
     * 
     * @param array $color RGB color
     * @return array RGB color
     */
    protected function _nonColor(Array $color) {
        // Get the HSL values
        $hsl = $this->_hsl($color);
        
        // Set the saturation to 0
        $hsl[1] = 0;
        
        // Set the brightness
        $hsl[2] = $hsl[2] >= 0.5 ? 1 : 0;
        
        // All done
        return $this->_image->hslToRgb($hsl);
    }
    
    /**
     * Get the pure color based on the Input
     * 
     * @param array $color RGB color
     * @return array RGB color
     */
    protected function _pure(Array $color) {
        // Get the HSL values
        $hsl = $this->_hsl($color);
        
        // Set the saturation to 1
        $hsl[1] = 1;
        
        // All done
        return $this->_image->hslToRgb($hsl);
    }
    
    /**
     * Get a palette of shade variations on a provided color
     * 
     * @param array $color  RGB color
     * @param int   $amount Luminosity variation amount (percent)
     * @param int   $count  Number of colors
     * @return array Array of RGB arrays
     */
    protected function _shades(Array $color, $amount = 5, $count = 10) {
        // Prepare the result
        $result = array();
        
        // Get the hue, saturation, luminosity
        $hsl = $this->_hsl($color);
        
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
            $result[] = $this->_image->hslToRgb(array($hsl[0], $hsl[1], $newLuminosity));
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
     * @return array Array of RGB arrays
     */
    protected function _saturation(Array $color, $amount = 20, $count = 10) {
        // Prepare the result
        $result = array();
        
        // Get the hue, saturation, luminosity
        $hsl = $this->_hsl($color);

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
            $result[] = $this->_image->hslToRgb(array($hsl[0], $newSaturation, $hsl[2]));
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
     * @return array Array of RGB arrays
     */
    protected function _variations(Array $color, $amount = 5, $count = 10) {
        // Prepare the result
        $result = array();
        
        // Get the hue, saturation, luminosity
        $hsl = $this->_hsl($color);
        
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
            $result[] = $this->_image->hslToRgb(array($newHue, $hsl[1], $hsl[2]));
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get a color contrasting with the Input
     * 
     * @param array $color  RGB color
     * @return array RGB color
     */
    protected function _contrast(Array $color) {
        // Get the hue, saturation, luminosity
        list($hue, $saturation, $luminosity) = $this->_hsl($color);
        
        // Get the new luminosity
        $l = 1 - $luminosity;
        
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
        return $this->_image->hslToRgb(array($h, $s, $l));
    }
}

/*EOF*/