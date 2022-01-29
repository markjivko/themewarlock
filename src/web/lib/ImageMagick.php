<?php
/**
 * Theme Warlock - ImageMagick
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class ImageMagick {
    
    /**
     * Resize types
     */
    const RESIZE_TYPE_PAD     = 'pad';
    const RESIZE_TYPE_CROP    = 'crop';
    const RESIZE_TYPE_DISTORT = 'distort';
    
    /**
     * Temporary directory
     * 
     * @var string
     */
    protected $_tempDir;
    
    /**
     * Image instance
     * 
     * @var Image
     */
    protected static $_image;
    
    /**
     * ImageMagick wrapper
     */
    public function __construct() {
        // Get the temps screenshot directory
        if (!is_dir($this->_tempDir = ROOT . '/web/' . IO::tempFolder() . '/image-magick')) {
            mkdir($this->_tempDir, 0777, true);
        }
    }
    
    /**
     * Get an Image instance
     * 
     * @return Image
     */
    protected function _image() {
        if (!isset(self::$_image)) {
            self::$_image = new Image();
        }
        return self::$_image;
    }
    
    /**
     * Gaussian blur - resource
     * 
     * @param resource &$resource Image resource
     * @param int      $amount    Blur amount
     */
    public function gaussianBlurResource(&$resource, $amount = 30) {
        // Execute the command
        $this->_commandResource($resource, $this->_gaussianBlurCommand($amount));
    }
    
    /**
     * Gaussian Blur - file
     * 
     * @param string $in     Input file
     * @param string $out    Output file
     * @param int    $amount Blur amount
     */
    public function gaussianBlurFile($in, $out, $amount = 30) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_gaussianBlurCommand($amount));
    }
    
    /**
     * Optimize a PNG
     * 
     * @param string $in  Input file
     * @param string $out Output file
     */
    public function optimizePng($in, $out) {
        $this->_commandFile($in, $out, $this->_optimizePngCommand());
    }
    
    /**
     * Gaussian Blur - file
     * 
     * @param string $in     Input file
     * @param string $out    Output file
     */
    public function convertGif($in, $out) {
        // Clean-up
        foreach (glob(dirname($out) . '/' . basename($out, '.png') . '*') as $framePath) {
            unlink($framePath);
        }
        
        // Execute the command
        $this->_commandFile($in, $out, $this->_convertGifCommand());
        
        // Go through the files
        foreach (glob(dirname($out) . '/' . basename($out, '.png') . '*') as $framePath) {
            // Get the index
            $index = preg_replace('%^.*?\-(\d+)%', '$1', basename($framePath, '.png'));

            // Rename the file
            rename($framePath, dirname($out) . '/' . basename($out, '.png') . '_' . $index . '.png');
        }
    }
    
    /**
     * Convert a PNG file to a SVG image; treats white a transparent; ignores color gradients
     *  
     * @param string  $in       Input file
     * @param string  $out      Output file
     * @param string  $colorHex Path color (Hex RGB)
     * @param int     $width    Output SVG width
     * @param int     $height   Output SVG height
     * @param boolean $inverse  Inverse the SVG
     * @throws Exception
     */
    public function pngToSvg($in, $out, $colorHex = '#ffffff', $width = null, $height = null, $inverse = false) {
        // Sanitize the color
        if (!preg_match('%^#[a-f\d]{6}%i', $colorHex)) {
            $colorHex = '#ffffff';
        }
        
        // Validate the PNG path
        if (!preg_match('%\.png$%i', basename($in)) || !is_file($in)) {
            throw new Exception('$in should be a valid PNG file');
        }
        
        // Validate the SVG path
        if (!preg_match('%\.svg$%i', basename($out)) || !is_dir(dirname($out))) {
            throw new Exception('$out should be a valid SVG path');
        }
        
        // Get a unique ID
        $id = $this->getUniqueId();

        // Get the Input path
        $pbmPath = $this->_tempDir . '/' . $id . '.pbm';
        
        // PNG -> PBM
        $this->_commandFile($in, $pbmPath, $this->_convertPngToPbmCommand());
        
        // Prepare the WxH options
        $whCommand = '';
        if (null !== $width) {
            $whCommand .= ' -W ' . round($width / 90, 3);
        }
        if (null !== $height) {
            $whCommand .= ' -H ' . round($height / 90, 3);
        }
        
        // Prepare the PO-Trace command
        $pbmToSvgCommand = 'potrace -s ' . escapeshellarg($pbmPath) . ' -o ' . escapeshellarg($out) . ' -C ' . escapeshellarg($colorHex) . ($inverse ? ' -i' : '') . ' --group -r 90' . $whCommand;
        
        // Log this command
        Log::check(Log::LEVEL_DEBUG) && Log::debug($pbmToSvgCommand);
        
        // Execute it
        exec($pbmToSvgCommand, $output, $pbmToSvgResult);
        
        // Remove the temporary file
        @unlink($pbmPath);
        
        // Valid output
        if (0 != $pbmToSvgResult || !is_file($out)) {
            // Remove the defective output
            is_file($out) && @unlink($out);
            
            // Warn about the error
            throw new Exception('Could not convert to SVG');
        }
        
        // Prepare the copypright
        $copyright = 'Copyright (c) ' . date('Y') . ' ' . Config::get()->authorName . ', ' . Config::get()->authorUrl;
        
        // Get the contents
        $svgContents = file_get_contents($out);
        
        // Replace the contents
        $svgContents = preg_replace('%(<\s*metadata\s*>).*?(<\s*\/\s*metadata\s*>)%ims', '${1}' . $copyright . '${2}', $svgContents);
        
        // Prepare a DOM object
        $dom = new DOMDocument();

        // Format the output
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        
        // End initial block
        $dom->loadXML($svgContents);
        
        // Save the file
        file_put_contents($out, $dom->saveXML());
    }
    
    /**
     * Gaussian blur - resource
     * 
     * @param resource &$resource  Image resource
     * @param int      $brightness Blur amount
     * @param int      $contrast   Blur amount
     */
    public function brightnessContrastResource(&$resource, $brightness = 0, $contrast = 0) {
        // Execute the command
        $this->_commandResource($resource, $this->_brightnessContrastCommand($brightness, $contrast));
    }
    
    /**
     * Gaussian Blur - file
     * 
     * @param string $in         Input file
     * @param string $out        Output file
     * @param int    $brightness Brightness
     * @param int    $contrast   Contrast
     */
    public function brightnessContrastFile($in, $out, $brightness = 0, $contrast = 0) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_brightnessContrastCommand($brightness, $contrast));
    }
    
    /**
     * Gradient overlay - resource
     * 
     * @param resource &$resource   Image resource
     * @param array    $gradient    Array of RGB[A] arrays: R(red) 0-255, G(green) 0-255, B(blue) 0-255, A (alpha, optional) 0-1
     * @param boolean  $overlayMode Overlay mode (Image::BLEND_MODE_*)
     * @param boolean  $ignoreAlpha Ignore the alpha channel
     */
    public function gradientOverlayResource(&$resource, Array $gradient, $overlayMode = Image::BLEND_MODE_OVERLAY, $ignoreAlpha = true) {
        // Not enough colors
        if (count($gradient) < 2) {
            throw new Exception('Not enought colors. At least 2 needed.');
        }
        
        // Get the image width and height
        list($width, $height) = array(imagesx($resource), imagesy($resource));
        
        // Execute the command
        $this->_commandResource($resource, $this->_gradientOverlayCommand($gradient, $overlayMode, $ignoreAlpha, $width, $height));
    }
    
    /**
     * Gradient overlay - file
     * 
     * @param string   $in          Input file
     * @param string   $out         Output file
     * @param array    $gradient    Array of RGB[A] arrays: R(red) 0-255, G(green) 0-255, B(blue) 0-255, A (alpha, optional) 0-1
     * @param boolean  $overlayMode Overlay mode (Image::BLEND_MODE_*)
     * @param boolean  $ignoreAlpha Ignore the alpha channel
     */
    public function gradientOverlayFile($in, $out, Array $gradient, $overlayMode = Image::BLEND_MODE_OVERLAY, $ignoreAlpha = true) {
        // Not enough colors
        if (count($gradient) < 2) {
            throw new Exception('Not enought colors. At least 2 needed.');
        }
        
        // Get the image width and height
        list($width, $height) = getimagesize($in);
        
        // Execute the command
        $this->_commandFile($in, $out, $this->_gradientOverlayCommand($gradient, $overlayMode, $ignoreAlpha, $width, $height));
    }
    
    /**
     * Gradient blur - resource
     * 
     * @param resource &$resource Image resource
     * @param int      $amount    Gradient amount
     * @param array    $gradient  Gradient of white, black or grey
     */
    public function gradientBlurResource(&$resource, $amount = 5, Array $gradient = array()) {
        // Get the image width and height
        list($width, $height) = array(imagesx($resource), imagesy($resource));
        
        // Default gradient
        if (!count($gradient)) {
            $gradient = array(
                array(255, 255, 255),
                array(0, 0, 0),
            );
        }
        
        // Execute the command
        $this->_commandResource($resource, $this->_gradientBlurCommand($amount, $gradient, $width, $height));
    }
    
    /**
     * Gradient Blur - file
     * 
     * @param string $in       Input file
     * @param string $out      Output file
     * @param int    $amount   Gradient amount
     * @param array  $gradient Gradient of white, black or grey
     */
    public function gradientBlurFile($in, $out, $amount = 5, Array $gradient = array()) {
        // Get the image width and height
        list($width, $height) = getimagesize($in);
        
        // Default gradient
        if (!count($gradient)) {
            $gradient = array(
                array(255, 255, 255),
                array(0, 0, 0),
            );
        }
        
        // Execute the command
        $this->_commandFile($in, $out, $this->_gradientBlurCommand($amount, $gradient, $width, $height));
    }
    
    /**
     * Flip - resource
     * 
     * @param resource &$resource  Image resource
     * @param boolean  $horizontal True for horizontal, false for vertical
     */
    public function flipResource(&$resource, $horizontal = true) {
        // Execute the command
        $this->_commandResource($resource, $this->_flipCommand($horizontal));
    }
    
    /**
     * Flip - file
     * 
     * @param string  $in         Input file
     * @param string  $out        Output file
     * @param boolean $horizontal True for horizontal, false for vertical
     */
    public function flipFile($in, $out, $horizontal = true) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_flipCommand($horizontal));
    }
    
    /**
     * Swirl - resource
     * 
     * @param resource &$resource Image resource
     * @param int      $degrees   Swirl degrees
     */
    public function swirlResource(&$resource, $degrees = 60) {
        // Execute the command
        $this->_commandResource($resource, $this->_swirlCommand($degrees));
    }
    
    /**
     * Swirl - file
     * 
     * @param string $in      Input file
     * @param string $out     Output file
     * @param int    $degrees Swirl degrees
     */
    public function swirlFile($in, $out, $degrees = 60) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_swirlCommand($degrees));
    }
    
    /**
     * Append - resources
     * 
     * @param resource[] $resources  Image resources
     * @param boolean    $horizontal True for horizontal, false for vertical
     */
    public function appendResources($resources, $horizontal = true) {
        // Execute the command
        return $this->_commandResource($resources, $this->_appendCommand($horizontal));
    }
    
    /**
     * Append - files
     * 
     * @param string[] $files      Input files
     * @param string   $out        Output file
     * @param boolean  $horizontal True for horizontal, false for vertical
     */
    public function appendFiles($files, $out, $horizontal = true) {
        // Execute the command
        $this->_commandFile($files, $out, $this->_appendCommand($horizontal));
    }
    
    /**
     * Crop - resource
     * 
     * @param resource $resource Input files
     * @param int      $x1       First X coordinate
     * @param int      $y1       First Y coordinate
     * @param int      $x2       Last X coordinate
     * @param int      $y2       Last Y coordinate
     */
    public function cropResource($resource, $x1, $y1, $x2, $y2) {
        // Execute the command
        return $this->_commandResource($resource, $this->_cropCommand($x1, $y1, $x2, $y2));
    }
    
    /**
     * Crop - file
     * 
     * @param string $in  Input files
     * @param string $out Output file
     * @param int    $x1  First X coordinate
     * @param int    $y1  First Y coordinate
     * @param int    $x2  Last X coordinate
     * @param int    $y2  Last Y coordinate
     */
    public function cropFile($in, $out, $x1, $y1, $x2, $y2) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_cropCommand($x1, $y1, $x2, $y2));
    }
    
    /**
     * Info - file
     * 
     * @param string $in Input file
     * @return File info
     */
    public function infoFile($in) {
        // Execute the command
        return $this->_commandFile($in, null, $this->_infoCommand());
    }
    
    /**
     * Composite - resource
     * 
     * @param resource &$resource     Image resource
     * @param string   $compositePath Composite path
     * @param int      $amount        Amount
     */
    public function compositeResource(&$resource, $compositePath, $amount = 100) {
        // Execute the command
        $this->_commandResource($resource, $this->_compositeCommand($compositePath, $amount));
    }
    
    /**
     * Composite - file
     * 
     * @param string $in            Input file
     * @param string $out           Output file
     * @param string $compositePath Composite path
     * @param int    $amount        Amount
     */
    public function compositeFile($in, $out, $compositePath, $amount = 100) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_compositeCommand($compositePath, $amount));
    }
    
    /**
     * Rotate - resource
     * 
     * @param resource &$resource Image resource
     * @param int      $degrees   Degrees
     */
    public function rotateResource(&$resource, $degrees) {
        // Execute the command
        $this->_commandResource($resource, $this->_rotateCommand($degrees));
    }
    
    /**
     * Rotate - file
     * 
     * @param string $in      Input file
     * @param string $out     Output file
     * @param int    $degrees Degrees
     */
    public function rotateFile($in, $out, $degrees) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_rotateCommand($degrees));
    }
    
    /**
     * Perspective - resource
     * 
     * @param resource &$resource Image GD resource
     * @param string   $p1        'x,y' values of point 1
     * @param string   $p2        'x,y' values of point 2
     * @param string   $p3        'x,y' values of point 3
     * @param string   $p4        'x,y' values of point 4
     */
    public function perspectiveResource(&$resource, $p1 = null, $p2 = null, $p3 = null, $p4 = null) {
        // Execute the command
        $this->_commandResource($resource, $this->_perspectiveCommand($p1, $p2, $p3, $p4));
    }
    
    /**
     * Perspective - file
     * 
     * @param string $in      Input file
     * @param string $out     Output file
     * @param string $p1      'x,y' values of point 1
     * @param string $p2      'x,y' values of point 2
     * @param string $p3      'x,y' values of point 3
     * @param string $p4      'x,y' values of point 4
     */
    public function perspectiveFile($in, $out, $p1 = null, $p2 = null, $p3 = null, $p4 = null) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_perspectiveCommand($p1, $p2, $p3, $p4));
    }
    
    /**
     * Color convert - resource
     * 
     * @param resource $resource      Image resource
     * @param array    $colors        List of colors
     * @param boolean  $gray          Convert to grayscale
     * @param boolean  $levelPlus     Use the + operator
     * @param int      $contrastCount Apply a contrast adjustment X number of times
     */
    public function levelResource(&$resource, Array $colors, $gray = false, $levelPlus = true, $contrastCount = 0) {
        // Execute the command
        $this->_commandResource($resource, $this->_levelCommand($colors, $gray, $levelPlus, $contrastCount));
    }
    
    /**
     * Color convert a- file
     * 
     * @param string   $in            Input file
     * @param string   $out           Output file
     * @param array    $colors        List of colors
     * @param boolean  $gray          Convert to grayscale
     * @param boolean  $levelPlus     Use the + operator
     * @param int      $contrastCount Apply a contrast adjustment X number of times
     */
    public function levelFile($in, $out, Array $colors, $gray = false, $levelPlus = true, $contrastCount = 0) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_levelCommand($colors, $gray, $levelPlus, $contrastCount));
    }
    
    /**
     * Color convert - resource
     * 
     * @param resource $resource Image resource
     * @param int      $amount   LCHuv Hue Shift amount (0 to 200, 100 is no change)
     */
    public function lchuvShiftResource(&$resource, $amount) {
        // Execute the command
        $this->_commandResource($resource, $this->_lchuvShiftCommand($amount));
    }
    
    /**
     * Color convert a- file
     * 
     * @param string $in     Input file
     * @param string $out    Output file
     * @param int    $amount LCHuv Hue Shift amount (0 to 200, 100 is no change)
     */
    public function lchuvShiftFile($in, $out, $amount) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_lchuvShiftCommand($amount));
    }
    
    /**
     * Draw - resource
     * 
     * @param resource &$resource Image resource
     * @param string   $path      Draw path
     * @param string   $fillColor Fill color (HEX)
     */
    public function drawResource(&$resource, $path, $fillColor) {
        // Execute the command
        $this->_commandResource($resource, $this->_drawCommand($path, $fillColor));
    }
    
    /**
     * Draw - file
     * 
     * @param string $in        Input file
     * @param string $out       Output file
     * @param string $path      Draw path
     * @param string $fillColor Fill color (HEX)
     */
    public function drawFile($in, $out, $path, $fillColor) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_drawCommand($path, $fillColor));
    }
    
    /**
     * Bezier line - resource
     * 
     * @param resource &$resource Image resource
     * @param array    $points    Bezier points
     * @param array    $lineColor Line color (RGB)
     * @param int      $lineWidth Line width
     */
    public function bezierResource(&$resource, Array $points, Array $lineColor, $lineWidth) {
        // Execute the command
        $this->_commandResource($resource, $this->_bezierCommand($points, $lineColor, $lineWidth));
    }
    
    /**
     * Bezier line - file
     * 
     * @param string $out       Output file
     * @param array  $points    Bezier Points
     * @param array  $lineColor Line color (RGB)
     * @param int    $lineWidth Line width
     */
    public function bezierFile($out, Array $points, Array $lineColor, $lineWidth) {
        // Execute the command
        $this->_commandFile(null, $out, $this->_bezierCommand($points, $lineColor, $lineWidth));
    }
    
    /**
     * Shepard's power distortion - resource
     * 
     * @param resource &$resource Image resource
     * @param string   $map       Distortion map (x1Start,y1Start x1End,y1End ...)
     * @param int      $power     Distortion power
     */
    public function shepardsResource(&$resource, $map, $power = 2) {
        // Execute the command
        $this->_commandResource($resource, $this->_shepardsCommand($map, $power));
    }
    
    /**
     * Shepard's power distortion - file
     * 
     * @param string $in    Input file
     * @param string $out   Output file
     * @param string $map   Distortion map (x1Start,y1Start x1End,y1End ...)
     * @param int    $power Distortion power
     */
    public function shepardsFile($in, $out, $map, $power = 2) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_shepardsCommand($map, $power));
    }
    
    /**
     * Color overlay, blend mode color - resource
     * 
     * @param resource &$resource Image resource
     * @param string   $colorHex  Color in Hex format
     */
    public function colorOverlayResource(&$resource, $colorHex) {
        // Execute the command
        $this->_commandResource($resource, $this->_colorOverlayCommand($colorHex));
    }
    
    /**
     * Color overlay, blend mode color - file
     * 
     * @param string $in       Input file
     * @param string $out      Output file
     * @param string $colorHex Color in Hex format
     */
    public function colorOverlayFile($in, $out, $colorHex) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_colorOverlayCommand($colorHex));
    }
    
    /**
     * Translate - resource
     * 
     * @param resource $resource Image resource
     * @param int      $x        Translation ammount on X
     * @param int      $y        Translation ammount on Y
     * @param int $canvW New canvas width (before translation)
     * @param int $canvH New canvas height (before translation)
     * @param int $imgW  New image width (before canvas resize)
     * @param int $imgH  New image height (before canvas resize)
     */
    public function nudgeResource(&$resource, $x = null, $y = null, $canvW = null, $canvH = null, $imgW = null, $imgH = null) {
        // Execute the command
        $this->_commandResource($resource, $this->_nudgeCommand($x, $y, $canvW, $canvH, $imgW, $imgH));
    }
    
    /**
     * Translate - file
     * 
     * @param string $in    Input file
     * @param string $out   Output file
     * @param int    $x     Translation ammount on X
     * @param int    $y     Translation ammount on Y
     * @param int    $canvW New canvas width (before translation)
     * @param int    $canvH New canvas height (before translation)
     * @param int    $imgW  New image width (before canvas resize)
     * @param int    $imgH  New image height (before canvas resize)
     */
    public function nudgeFile($in, $out, $x = null, $y = null, $canvW = null, $canvH = null, $imgW = null, $imgH = null) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_nudgeCommand($x, $y, $canvW, $canvH, $imgW, $imgH));
    }
    
    /**
     * Resize - image
     * 
     * @param resource $resource   Image resource
     * @param int      $width      New image width
     * @param int      $height     New image height
     * @param string   $resizeType (optional) Resize type, one of <ul>
     * <li>ImageMagick::RESIZE_TYPE_CROP</li>
     * <li>ImageMagick::RESIZE_TYPE_PAD</li>
     * <li>ImageMagick::RESIZE_TYPE_DISTORT</li>
     * </ul> default <b>ImageMagick::RESIZE_TYPE_CROP</b>
     */
    public function resizeResource(&$resource, $width, $height, $resizeType = self::RESIZE_TYPE_CROP) {
        // Execute the command
        $this->_commandResource($resource, $this->_resizeCommand($width, $height, $resizeType));
    }
    
    /**
     * Resize - file
     * 
     * @param string $in         Input file
     * @param string $out        Output file
     * @param int    $width      New image width
     * @param int    $height     New image height
     * @param string $resizeType (optional) Resize type, one of <ul>
     * <li>ImageMagick::RESIZE_TYPE_CROP</li>
     * <li>ImageMagick::RESIZE_TYPE_PAD</li>
     * <li>ImageMagick::RESIZE_TYPE_DISTORT</li>
     * </ul> default <b>ImageMagick::RESIZE_TYPE_CROP</b>
     */
    public function resizeFile($in, $out, $width, $height, $resizeType = self::RESIZE_TYPE_CROP) {
        // Execute the command
        $this->_commandFile($in, $out, $this->_resizeCommand($width, $height, $resizeType));
    }
    
    /**
     * Get the gaussian blur ImageMagick command
     * 
     * @param int $amount Blur amount
     * @return string
     */
    protected function _gaussianBlurCommand($amount) {
        return 'convert __IN__ -blur 0x' . $amount . ' __OUT__';
    }
    
    /**
     * Get the GIF conversion ImageMagick command
     * 
     * @return string
     */
    protected function _convertGifCommand() {
        return 'convert -coalesce __IN__ __OUT__';
    }
    
    /**
     * Optimize PNG command
     * 
     * @return string
     */
    protected function _optimizePngCommand() {
        return 'convert __IN__ -depth 8 __OUT__';
    }
    
    /**
     * Get the gaussian blur ImageMagick command
     * 
     * @param int $brightness Brightness
     * @param int $contrast   Contrast
     * @return string
     */
    protected function _brightnessContrastCommand($brightness, $contrast) {
        return 'convert -brightness-contrast ' . $brightness . 'x' . $contrast . ' __IN__  __OUT__';
    }
    
    /**
     * Convert a PNG file to a Portable BitMap file Command
     * Treats white as transparent
     * 
     * @return string
     */
    protected function _convertPngToPbmCommand() {
        return 'convert __IN__ -fuzz 15% -transparent white \( -clone 0 -fill black -draw "color 0,0 reset" \) -compose atop -composite -alpha remove __OUT__';
    }
    
    /**
     * Prepare the gradient overlay command
     * 
     * @param array   $gradient    Array of RGB[A] arrays: R(red) 0-255, G(green) 0-255, B(blue) 0-255, A (alpha, optional) 0-1
     * @param boolean $overlayMode Overlay mode (Image::BLEND_MODE_*)
     * @param boolean $ignoreAlpha Ignore the alpha channel
     * @param int     $width       Image width
     * @param int     $height      Image height
     * @return string ImageMagick convert command
     */
    protected function _gradientOverlayCommand(Array $gradient, $overlayMode = Image::BLEND_MODE_OVERLAY, $ignoreAlpha = true, $width, $height) {
        // Get the blend mode
        switch ($overlayMode) {
            case Image::BLEND_MODE_NORMAL:
                $overlay = 'Over';
                break;
            
            case Image::BLEND_MODE_SCREEN:
                $overlay = 'Screen';
                break;
            
            case Image::BLEND_MODE_DIFFERENCE:
                $overlay = 'Difference';
                break;
            
            case Image::BLEND_MODE_COLOR_BURN:
                $overlay = 'ColorBurn';
                break;
            
            case Image::BLEND_MODE_COLOR:
                $overlay = 'Colorize';
                break;
            
            case Image::BLEND_MODE_HUE:
                $overlay = 'Hue';
                break;
            
            case Image::BLEND_MODE_VIVID_LIGHT:
                $overlay = 'VividLight';
                break;
            
            case Image::BLEND_MODE_PLUS:
                $overlay = 'Plus';
                break;
            
            case Image::BLEND_MODE_MINUS:
                $overlay = 'Minus';
                break;
            
            case Image::BLEND_MODE_SATURATE:
                $overlay = 'Saturate';
                break;
            
            case Image::BLEND_MODE_OVERLAY:
            case Image::BLEND_MODE_DESATURATE:
                $overlay = 'Overlay';
                break;
        }

        // Prepare the command
        $result = 'convert __IN__ '
            // Set to greyscale in desaturation mode
            . (Image::BLEND_MODE_DESATURATE == $overlayMode ? ' -grayscale rec709luma' : '') 
            // Create a dynamic, alpha-aware gradient layer
            . ' ( ' . $this->_gradientList($gradient) .' +append -filter Spline -rotate 90 -resize ' . $width . 'x' . $height . '! ' . ($ignoreAlpha ? ' __IN__ -compose DstIn -composite' : '') . ' )'
            // Apply color blending to everything except desaturated areas
            . (Image::BLEND_MODE_COLOR == $overlayMode ? ' ( -clone 0 -set colorspace RGB -colorspace HSV -separate -delete 0,2 -white-threshold 25% -gaussian 0x1 ) ' : '')
            // Combine with the original picture
            . ' -compose ' . $overlay . ' -composite ' 
            // Save
            . ' __OUT__';

        // All done
        return $result;
    }
    
    /**
     * Prepare the resize command
     * 
     * @param int      $width      New image width
     * @param int      $height     New image height
     * @param string   $resizeType (optional) Resize type, one of <ul>
     * <li>ImageMagick::RESIZE_TYPE_CROP</li>
     * <li>ImageMagick::RESIZE_TYPE_PAD</li>
     * <li>ImageMagick::RESIZE_TYPE_DISTORT</li>
     * </ul> default <b>ImageMagick::RESIZE_TYPE_CROP</b>
     * @return string
     */
    protected function _resizeCommand($width, $height, $resizeType = self::RESIZE_TYPE_CROP) {
        // Sanitize the width
        $width = intval($width);
        $width = $width <= 0 ? 1 : $width;
        
        // Sanitize the height
        $height = intval($height);
        $height = $height <= 0 ? 1 : $height;
        
        // Prepare the resize argument
        $resizeArg = "${width}x${height}";
        switch ($resizeType) {
            case self::RESIZE_TYPE_PAD:
                $resizeArg = "${width}x${height}";
                break;
            
            case self::RESIZE_TYPE_CROP:
                $resizeArg = "${width}x${height}^";
                break;
            
            case self::RESIZE_TYPE_DISTORT:
                $resizeArg = "${width}x${height}!";
                break;
        }
        
        // All done
        return 'convert __IN__ -resize ' . $resizeArg . ' -gravity center -background transparent -extent ' . $width . 'x' . $height . ' __OUT__';
    }
    
    /**
     * Prepare the nudge command with the ability to resize the image, resize the canvas then translate the image
     * 
     * @param int $x     Translation ammount on X
     * @param int $y     Translation ammount on Y
     * @param int $canvW New canvas width (before translation)
     * @param int $canvH New canvas height (before translation)
     * @param int $imgW  New image width (before canvas resize)
     * @param int $imgH  New image height (before canvas resize)
     */
    protected function _nudgeCommand($x = null, $y = null, $canvW = null, $canvH = null, $imgW = null, $imgH = null) {
        // Translate the values into mathematical notation
        $x = (intval($x) < 0 ? '-' : '+') . abs($x);
        $y = (intval($y) < 0 ? '-' : '+') . abs($y);;
        
        // Get the new canvas width and height
        $canvW = intval($canvW);
        $canvH = intval($canvH);
        
        // Prepare the canvas
        $canvas = '';
        if ($canvW > 0 && $canvH > 0) {
            $canvas = ' -extent ' . $canvW . 'x' . $canvH;
        }
        
        // Get the default image width and height
        if (null === $imgW) {
            $imgW = $canvW;
        }
        if (null === $imgH) {
            $imgH = $canvH;
        }
        
        // Get the new image width and height
        $imgW = intval($imgW);
        $imgH = intval($imgH);
        
        // Prepare the image
        $image = '';
        if ($imgW > 0 && $imgH > 0) {
            $image = ' -resize ' . $imgW . 'x' . $imgH . '!';
        }
        
        // All done
        return 'convert __IN__ -background none' . $image . $canvas . ' -page ' . $x . $y . ' -flatten __OUT__';
    }
    
    /**
     * Prepare the flip-flop command
     * 
     * @param boolean $horizontal True for horizontal, false for vertical
     * @return string ImageMagick convert command
     */
    protected function _flipCommand($horizontal = true) {
        // Prepare the command
        return 'convert __IN__ '
            // Flip or flop
            . ($horizontal ? '-flop' : '-flip')
            // Save
            . ' __OUT__';
    }
    
    /**
     * Convert an RGBA gradient list into a command string
     * 
     * @param array[] $gradient List of RGBA colors
     * @return string
     */
    protected function _gradientList(Array $gradient) {
        // Prepare the result
        $result = array();

        // Go through the gradient
        foreach ($gradient as $rgba) {
            // Assume Alpha = 1
            if (!isset($rgba[3])) {
                $rgba[3] = 1;
            }
            
            // Alpha layer too large
            if ($rgba[3] > 1) {
                $rgba[3] = 1;
            }

            // Prepare a linear displacement
            $result[] = 'xc:rgba(' . implode(',', $rgba) . ')';
        }
        
        // All done
        return implode(' ', $result);
    }
    
    /**
     * Get the gradient blur ImageMagick command
     * 
     * @param int   $amount   Blur amount
     * @param array $gradient Gradient of white, black or grey
     * @param int   $width    Image width
     * @param int   $height   Image height
     * @return string
     */
    protected function _gradientBlurCommand($amount, $gradient, $width, $height) {
        return 'convert __IN__ ' 
            // Create a dynamic, alpha-aware gradient layer
            . ' ( ' . $this->_gradientList($gradient) .' +append -filter Gaussian -rotate 90 -resize ' . $width . 'x' . $height . '! )'
            // Blurred composition
            . ' -compose blur -define compose:args=' . $amount 
            // Save file
            . ' -composite __OUT__';
    }

    /**
     * Get the swirl ImageMagick command
     * 
     * @param int $degrees Swirl degrees
     * @return string
     */
    protected function _swirlCommand($degrees) {
        return 'convert __IN__ -swirl ' . $degrees . ' __OUT__';
    }

    /**
     * Get the append ImageMagick command
     * 
     * @param boolean $horizontal True for horizontal, false for vertical
     * @return string
     */
    protected function _appendCommand($horizontal) {
        return 'convert __IN__ ' . ($horizontal ? '+' : '-') . 'append __OUT__';
    }

    /**
     * Get the crop ImageMagick command
     * 
     * @param int $x1 First X coordinate
     * @param int $y1 First Y coordinate
     * @param int $x2 Last X coordinate
     * @param int $y2 Last Y coordinate
     * @return string
     */
    protected function _cropCommand($x1, $y1, $x2, $y2) {
        return 'convert __IN__ -crop ' . ($x2 - $x1) . 'x' . ($y2 - $y1) . ($x1 >= 0 ? '+' : '-') . abs($x1) . ($y1 >= 0 ? '+' : '-') . abs($y1) . ' __OUT__';
    }
    
    /**
     * Get the info ImageMagick command
     * 
     * @return string
     */
    protected function _infoCommand() {
        return 'convert __IN__ -format %r info:';
    }
    
    /**
     * Get the composite ImageMagick command
     * 
     * @param string $compositePath Composite path
     * @param int    $amount        Amount
     * @return string
     */
    protected function _compositeCommand($compositePath, $amount) {
        return 'composite.exe ' . escapeshellarg($compositePath) . ' __IN__ -displace ' . $amount . 'x' . $amount . ' __OUT__';
    }
    
    /**
     * Get the rotate ImageMagick command
     * 
     * @param int $degrees Degrees
     * @return string
     */
    protected function _rotateCommand($degrees) {
        return 'convert __IN__ -background none -rotate ' . $degrees . ' __OUT__ ';
    }
    
    /**
     * Get the perspective ImageMagick command
     * 
     * @param string $p1 'x,y' values of point 1
     * @param string $p2 'x,y' values of point 2
     * @param string $p3 'x,y' values of point 3
     * @param string $p4 'x,y' values of point 4
     * @return string
     */
    protected function _perspectiveCommand($p1, $p2, $p3, $p4) {
        // Left, top
        list($x1, $y1) = explode(',', $p1);

        // Right, top
        list($x2, $y2) = explode(',', $p2);

        // Right, bottom
        list($x3, $y3) = explode(',', $p3);

        // Left, bottom
        list($x4, $y4) = explode(',', $p4);

        // Get the X and Y values separately
        $xValues = array();
        $yValues = array();
        
        // Make adjustments to mimic the results of the Image class equivalent method
        for ($i = 1; $i <= 4; $i++) {
            $xValues[] = ${'x' . $i};
            $yValues[] = ${'y' . $i};
        }
        
        // Create a new canvas
        $canvasWidth = max($xValues) + 1;
        $canvasHeight = max($yValues) + 1;

        // All done
        return "convert __IN__ -matte -virtual-pixel transparent -interpolate Spline +distort Perspective \"0,0 $x1,$y1 %w,0 $x2,$y2 %w,%h $x3,$y3 0,%h $x4,$y4\" -background transparent -gravity SouthEast -extent ${canvasWidth}x${canvasHeight} __OUT__";
    }
    
    /**
     * Get the rotate ImageMagick command
     * 
     * @param array   $colors        List of colors
     * @param boolean $gray          Convert to grayscale
     * @param boolean $levelPlus     Use the + operator
     * @param int     $contrastCount Apply a contrast adjustment X number of times
     * @return string
     */
    protected function _levelCommand(Array $colors, $gray = false, $levelPlus = true, $contrastCount = 0) {
        return 'convert __IN__ ' . str_repeat('-contrast ', $contrastCount) . ($levelPlus ? '+' : '-') . 'level-colors ' . implode(',', $colors) . ' ' . ($gray ? '-colorspace gray' : '') . ' __OUT__ ';
    }
    
    /**
     * Get the rotate ImageMagick command
     * 
     * @param int $amount LCHuv Hue Shift amount (0 to 200, 100 is no change)
     * @return string
     */
    protected function _lchuvShiftCommand($amount) {
        return 'convert __IN__ -define modulate:colorspace=LCHuv -modulate 100,100,' . $amount . ' __OUT__ ';
    }
    
    /**
     * Get the draw ImageMagick command
     * 
     * @param string $path      Path
     * @param string $fillColor HEX color
     * @return string
     */
    protected function _drawCommand($path, $fillColor) {
        // M 20,55 A 100,100 0 0,0 25,10 A 100,100 0 0,1 70,5 A 100,100 0 0,1 20,55 Z
        return 'convert __IN__ -background none -fill "' . $fillColor . '" -draw "path \'' . $path . '\'" __OUT__ ';
    }
    
    /**
     * Get the draw ImageMagick command
     * 
     * @param array  $points    Bezier points
     * @param array  $lineColor RGB color
     * @param int    $lineWidth Line width
     * @return string
     */
    protected function _bezierCommand(Array $points, Array $lineColor, $lineWidth) {
        // Integers expected
        $points = array_values(array_map('intval', $points));
        
        // Get the staring point
        $startX = array_shift($points);
        $startY = array_shift($points);
        
        // Validate the result
        if (count($points) % 4 != 0) {
            throw new Exception('Invalid number of points (multiple of 4 + 1 expected)');
        }

        // Prepare the extremities
        $minX = 0;
        $minY = 0;
        $maxX = 0;
        $maxY = 0;
        
        // Prepare the quads
        $quadsCommands = array();
        $quads = array();
        foreach ($points as $pointKey => $point) {
            if ($pointKey % 2 == 0) {
                if ($point > $maxX) {
                    $maxX = $point;
                }
                if ($point < $minX) {
                    $minX = $point;
                }
            } else {
                // Care for the line width
                $point += $lineWidth;
                if ($point > $maxY) {
                    $maxY = $point;
                }
                if ($point < $minY) {
                    $minY = $point;
                }
            }
            
            // append the point
            $quads[] = $point;
            
            // Reached the number
            if (4 == count($quads)) {
                // Store the command
                $quadsCommands[] = sprintf(
                    'Q %d,%d %d,%d',
                    $quads[0],
                    $quads[1],
                    $quads[2],
                    $quads[3]
                );
                
                // Reset
                $quads = array();
            }
        }
        
        // Get the canvas dimensions
        $width = ($maxX - $minX) * 2;
        $height = ($maxY - $minY) * 2;

        // Prepare the command
        $result = 'convert -size ' . $width . 'x' . $height . ' xc:none -fill none -stroke "rgb(' . implode(', ', $lineColor) . ')" -strokewidth ' . $lineWidth . ' -draw "path \'M ' . $startX . ',' . $startY . ' ' . implode(' ', $quadsCommands) . '\' " -trim +repage __OUT__';

        // All done
        return $result;
    }
    
    /**
     * Get the Shepard's power distortion command
     * 
     * @param string $map   Distortion map (x1Start,y1Start x1End,y1End ...)
     * @param int    $power Power
     * @return string
     */
    protected function _shepardsCommand($map, $power = 2) {
        return 'convert __IN__ -virtual-pixel Black -define shepards:power=' . $power . '.0 -distort Shepards "' . $map . '" __OUT__';
    }
    
    /**
     * Get the color overlay, blend mode color command
     * 
     * @param string $colorHex Color in Hex format
     * @return string
     */
    protected function _colorOverlayCommand($colorHex) {
        return 'convert __IN__ ( +clone +level-colors ' . $colorHex . ' ) -compose Hue -composite  __OUT__';
    }
    
    /**
     * Run a command directly on a file
     * 
     * @param type $in
     * @param type $out
     * @param string $command
     */
    protected function _commandFile($in, $out, $command) {
        // Input Output
        $io = array(
            '__IN__'  => is_array($in) ? implode(' ', array_map('escapeshellarg', $in)) : escapeshellarg($in),
            '__OUT__' => escapeshellarg($out),
        );
        
        // Add the In and Out files
        $command = str_replace(array_keys($io), array_values($io), $command);

        // Log the command
        Log::check(Log::LEVEL_DEBUG) && Log::debug($command);
        
        // Execute the command
        return trim(shell_exec($command));
    }
    
    /**
     * Generate an unique ID
     * 
     * @return string
     */
    public function getUniqueId() {
        return getmypid() . '-'. md5(uniqid(null, true)) . '-' . mt_rand(1000, 9999);
    }
    
    /**
     * Save the resource temporarily to the hard-drive, perform the operations then reload it
     * 
     * @param resource &$resource Image GD resource
     * @param string   $command   ImageMagick command
     */
    protected function _commandResource(&$resource, $command) {
        // Multiple resources
        if (is_array($resource)) {
            // Prepare the Input array
            $inputArray = array();
            
            // Go through the resources
            foreach ($resource as $resourceToSave) {
                // Get a unique ID
                $id = $this->getUniqueId();
                
                // Get the Input path
                $inputPath = $inputPath = $this->_tempDir . '/' . $id . '-in.png';
                
                // Append the file
                $inputArray[] = $inputPath;
                
                // Save the resource
                $this->_image()->save($resourceToSave, 'png', $inputPath);
            }
        } else {
            // Get a unique ID
            $id = $this->getUniqueId();
        
            // Get the Input path
            $inputPath = $this->_tempDir . '/' . $id . '-in.png';
            
            // Prepare the Input array
            $inputArray = array($inputPath);
            
            // Save the resource
            $this->_image()->save($resource, 'png', $inputPath);
        }
        
        // Get the Output path
        $outputPath = $this->_tempDir . '/' . $id . '-out.png';
        
        // Input Output
        $io = array(
            '__IN__'  => implode(' ', array_map('escapeshellarg', $inputArray)),
            '__OUT__' => escapeshellarg($outputPath),
        );
        
        // Add the In and Out files
        $command = str_replace(array_keys($io), array_values($io), $command);

        // Log the command
        Log::check(Log::LEVEL_DEBUG) && Log::debug($command);
        
        // Execute the command
        $result = shell_exec($command);
        
        // Destroy the old resource
        is_resource($resource) && imagedestroy($resource);
        
        // Load the resource
        $resource = $this->_image()->load($outputPath);
        
        // Remove the temporary file
        foreach ($inputArray as $inputFilePath) {
            unlink($inputFilePath);
        }
        unlink($outputPath);
        
        // Return the resource
        return $resource;
    }
}

/*EOF*/
