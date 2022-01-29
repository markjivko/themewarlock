    <?php
/**
 * Theme Warlock - ColorPicker
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class ColorPicker {
    
    /**
     * Image instance
     * 
     * @var Image
     */
    protected $_image;
    
    /**
     * Final working canvas
     * 
     * @var resource GD
     */
    protected $_canvas;
    
    /**
     * Canvas width
     * 
     * @var int
     */
    protected $_width;
    
    /**
     * Canvas height
     * 
     * @var int
     */
    protected $_height;
    
    /**
     * Main colors (array of Palette objects)
     * 
     * @var Palette[]
     */
    protected $_colors = array();
    
    /**
     * Color picker constructor
     * 
     * @param string Multiple image path(s), each one overlays the other
     */
    public function __construct($imagePath) {
        // Get the image instance
        $this->_image = new Image();
        
        // Go through each argument
        foreach (func_get_args() as $imagePath) {
            // Nine-patch
            if ((boolean) preg_match('%\.9\.png$%i', $imagePath)) {
                // Get the nine-patch object
                $ninePatch = NinePatch::get($imagePath);
                
                // Get the overlay
                $overlay = $ninePatch->getResource();
            } else {
                // Classic image
                $overlay = $this->_image->load($imagePath);
            }

            // Blank canvas
            if (!isset($canvas)) {
                $canvas = $this->_image->canvas(imagesx($overlay), imagesy($overlay));
            }
            
            // Get the crop width and height
            $cropWidth = min(imagesx($canvas), imagesx($overlay));
            $cropHeight = min(imagesy($canvas), imagesy($overlay));
            
            // Overlay the image
            $canvas = $this->_image->overlay($overlay, $canvas);
            
            // Get the x and y
            $x = (imagesx($canvas) - imagesx($overlay)) / 2;
            $y = (imagesy($canvas) - imagesy($overlay)) / 2;
            
            // Crop the final canvas
            $canvas = $this->_image->crop($canvas, $x, $y, $x + $cropWidth, $y + $cropHeight);
        }
        
        // Save the canvas
        $this->_canvas = $canvas;
        
        // Store the canvas dimensions
        $this->_width = imagesx($this->_canvas);
        $this->_height = imagesy($this->_canvas);
        
        // Get the colors (as precise as possible)
        foreach ($this->_image->getColors(array($canvas, false), 2, 10) as $color) {
            // Save the RGB color
            $this->_colors[] = new Palette(Image::$colors[$color[0]]);
        }
    }
    
    /**
     * Get the main colors as Palette objects
     * 
     * @return Palette[] Array of Palette objects
     */
    public function getColors() {
        return $this->_colors;
    }
    
    /**
     * Get the distinct colors as Palette objects
     * 
     * @param int $number          Number of distinct colors to return
     * @param int $distinctPercent Percentage
     * @return Palette[] Array of Palette objects
     */
    public function getDistinct($number = 2, $distinctPercent = 15) {
        // Prepare the result
        $result = array();
        
        // HSL versions
        $resultHsl = array();
        
        // Go through the found colors
        foreach ($this->_colors as $color) {
            // Get the HSL version
            $hsl = $color->hsl();
            
            // Found a similar color flag
            $similar = false;
            
            // Go through the matched HSLs
            foreach ($resultHsl as $foundHSL) {
                // Get the Hue diff in degrees
                $hueDiff = abs($foundHSL[0] - $hsl[0]);
                $hueDiff = $hueDiff > 180 ? (360 - $hueDiff) : $hueDiff;
                
                // Hue identity percent
                $hueIdentityPercent = -100/180 * $hueDiff + 100;
                
                // Saturation identity percent
                $satIdentityPercent = -100 * abs($foundHSL[1] - $hsl[1]) + 100;
                
                // Luminosity identity percent
                $lumIdentityPercent = -100 * abs($foundHSL[2] - $hsl[2]) + 100;
                
                // Get the actual identity percent
                $identityPercent = pow($hueIdentityPercent * $satIdentityPercent * $lumIdentityPercent, 1/3);

                // Colors are distinct
                if ($identityPercent >= 100 - $distinctPercent) {
                    // Found a similar color
                    $similar = true;
                    
                    // Stop here
                    break;
                }
            }
            
            // This is a distinct color
            if (!$similar) {
                // Save the color
                $result[] = $color;
                
                // Save the HSL for later comparison
                $resultHsl[] = $hsl;
            }
        }
        
        // Nothing found
        if (count($result) == 0) {
            $result[] = new Palette(array(mt_rand(1, 10), mt_rand(1, 10), mt_rand(1, 10)));
        }
        
        // Not enough results
        if (count($result) < $number) {           
            // Get the shade variations
            $shadeVariations = array();
            
            // Append shade variations
            foreach ($result as $resultPalette) {
                $shadeVariations = array_merge($shadeVariations, $resultPalette->shadeVariations($distinctPercent, $number));
            }
            
            // Shuffle
            shuffle($shadeVariations);

            // Append to the result
            while (count($result) < $number) {
                // Get the palette for the current color
                $currentColorPalette = new Palette(array_shift($shadeVariations));
                
                // Get the analagous variations
                $analagousVariations = $currentColorPalette->analogousVariations($distinctPercent * 1.8, 1);
                
                // Append to the result
                $result[] = new Palette($analagousVariations[0]);
            }
        }
        
        // All done
        return array_slice($result, 0, $number);
    }
    
    /**
     * Get the distinct color pure contrasts as Palette objects
     * 
     * @param int $number          Number of distinct colors to return
     * @param int $distinctPercent Percentage
     * @return Palette[] Array of Palette objects
     */
    public function getDistinctPureContrast($number = 2, $distinctPercent = 15) {
        // Get the distinct colors
        $distinctColors = $this->getDistinct($number, $distinctPercent);
        
        // Prepare the result
        $result = array();
        
        // Go through the colors
        foreach ($distinctColors as $distinctColor) {
            $result[] = new Palette($distinctColor->pureContrast());
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the canvas image resource
     * 
     * @return resource GD
     */
    public function getCanvas() {
        return $this->_canvas;
    }
    
    /**
     * Get the canvas width
     * 
     * @return int
     */
    public function getWidth() {
        return $this->_width;
    }
    
    /**
     * Get the canvas height
     * 
     * @return int
     */
    public function getHeight() {
        return $this->_height;
    }
    
    /**
     * Pick a color from the canvas; if not provided, "center" will be used
     * 
     * @param int $x X coordinate
     * @param int $y Y coordinate
     * @return Palette Palette instance of the color
     */
    public function pick($x = null, $y = null) {
        if (null === $x) {
            $x = round(imagesx($this->_canvas) / 2);
        }
        
        if (null === $y) {
            $y = round(imagesy($this->_canvas) / 2);
        }
        
        // Positive values
        $x = abs($x);
        $y = abs($x);
        
        // Don't exceed the edges
        $x = $x > $this->_width - 1 ? $this->_width - 1 : $x;
        $y = $y > $this->_height - 1 ? $this->_height - 1 : $y;
        
        // Get the color
        return new Palette($this->_image->pick($this->_canvas, $x, $y));
    }
}

/*EOF*/
