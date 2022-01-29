<?php
/**
 * Theme Warlock - Pattern
 * 
 * @title      Pattern generator
 * @desc       Create pattern images - useful for placehodlers and defaults
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Pattern {

    // Patterns
    const PATTERN_A  = 'a.jpg';
    const PATTERN_AA = 'aa.png';
    const PATTERN_AB = 'ab.png';
    const PATTERN_AC = 'ac.png';
    const PATTERN_AE = 'ae.jpg';
    const PATTERN_AF = 'af.png';
    const PATTERN_AG = 'ag.jpg';
    const PATTERN_AH = 'ah.png';
    const PATTERN_AI = 'ai.png';
    const PATTERN_AJ = 'aj.png';
    const PATTERN_AK = 'ak.png';
    const PATTERN_AL = 'al.png';
    const PATTERN_AM = 'am.jpg';
    const PATTERN_AN = 'an.jpg';
    const PATTERN_AO = 'ao.gif';
    const PATTERN_AP = 'ap.jpg';
    const PATTERN_AQ = 'aq.jpg';
    const PATTERN_AR = 'ar.png';
    const PATTERN_AS = 'as.gif';
    const PATTERN_AT = 'at.png';
    const PATTERN_AU = 'au.jpg';
    const PATTERN_AV = 'av.gif';
    const PATTERN_AW = 'aw.png';
    const PATTERN_AX = 'ax.jpg';
    const PATTERN_AY = 'ay.jpg';
    const PATTERN_AZ = 'az.png';
    const PATTERN_B  = 'b.jpg';
    const PATTERN_BA = 'ba.jpg';
    const PATTERN_BB = 'bb.jpg';
    const PATTERN_BC = 'bc.png';
    const PATTERN_C  = 'c.jpg';
    const PATTERN_D  = 'd.png';
    const PATTERN_E  = 'e.png';
    const PATTERN_F  = 'f.jpg';
    const PATTERN_G  = 'g.gif';
    const PATTERN_H  = 'h.jpg';
    const PATTERN_I  = 'i.jpg';
    const PATTERN_J  = 'j.jpg';
    const PATTERN_L  = 'l.jpg';
    const PATTERN_O  = 'o.jpg';
    const PATTERN_P  = 'p.gif';
    const PATTERN_Q  = 'q.png';
    const PATTERN_R  = 'r.png';
    const PATTERN_S  = 's.png';
    const PATTERN_T  = 't.gif';
    const PATTERN_U  = 'u.png';
    const PATTERN_V  = 'v.png';
    const PATTERN_W  = 'w.png';
    const PATTERN_X  = 'x.png';
    const PATTERN_Y  = 'y.png';
    const PATTERN_Z  = 'z.png';
    const PATTERN_ZZ = 'zz.jpg';
    
    /**
     * Auto Cover mode. <br/>
     * The cover mode is automatically selected based on file name prefix:<ul>
     * <li><b>"x_"</b> - Pattern::COVER_MODE_SKIP</li>
     * <li><b>"o_"</b> - Pattern::COVER_MODE_OVERLAY</li>
     * <li>(default) - Pattern::COVER_MODE_NORMAL</li>
     * </ul>
     */
    const COVER_MODE_AUTO    = 'auto';
    
    /**
     * Blend mode "Normal"
     */
    const COVER_MODE_NORMAL  = 'normal';
    
    /**
     * Blend mode "Overlay"
     */
    const COVER_MODE_OVERLAY = 'overlay';
    
    /**
     * Blend mode "Swirl"
     */
    const COVER_MODE_SWIRL = 'swirl';
    
    /**
     * Skips covering the current image
     */
    const COVER_MODE_SKIP    = 'skip';
    
    /**
     * File prefix for blend mode "Overlay"
     */
    const FILE_PREFIX_OVERLAY = 'o';
    
    /**
     * File prefix for blend mode "Swirl"
     */
    const FILE_PREFIX_SWIRL   = 's';
    
    /**
     * File prefix for blend mode "Skip"
     */
    const FILE_PREFIX_SKIP    = 'x';
    
    /**
     * Auto-populated array of available patterns
     * 
     * @var array
     */
    protected $_patternsList = array();
    
    /**
     * Auto-populated array of available cover modes
     * 
     * @var array
     */
    protected $_coverModesList = array();
    
    /**
     * Pattern to use
     * 
     * @var string
     */
    protected $_pattern;
    
    /**
     * Pattern builder
     */
    public function __construct() {
        $reflectionClass = new ReflectionClass($this);
        
        // Store the available patterns
        foreach($reflectionClass->getConstants() as $constantName => $constantValue) {
            if (preg_match('%^PATTERN_%', $constantName)) {
                $this->_patternsList[] = $constantValue;
                continue;
            }
            if (preg_match('%^COVER_MODE_%', $constantName)) {
                $this->_coverModesList[] = $constantValue;
                continue;
            }
        }
        
        // Set a random pattern
        $this->_pattern = $this->_patternsList[mt_rand(0, count($this->_patternsList) - 1)];
    }
    
    /**
     * Choose a pattern; if none is provided, a random pattern will be used instead
     * 
     * @param string $pattern (optional) Pattern to use, one of <b>Pattern::PATTERN_*</b>; default <b>null</b>
     * @return Pattern
     */
    public function setPattern($pattern = null) {
        // Validate the pattern
        if (null === $pattern || !in_array($pattern, $this->_patternsList)) {
            $pattern = $this->_patternsList[mt_rand(0, count($this->_patternsList) - 1)];
        }
        
        // Store the pattern
        $this->_pattern = $pattern;
        
        // All done
        return $this;
    }
    
    /**
     * Get the currently used pattern
     * 
     * @return string
     */
    public function getPattern() {
        return $this->_pattern;
    }
    
    /**
     * Get the path to the current pattern
     * 
     * @return string
     */
    public function getPatternPath() {
        return ROOT . '/web/resources/patterns/' . $this->_pattern;
    }
    
    /**
     * Get all available patterns
     */
    public function getAll() {
        return $this->_patternsList;
    }
    
    /**
     * Cover an image with the current pattern
     * 
     * @param string $imagePath  Input path
     * @param string $outputPath (optional) Output path; default <b>null</b>
     * @param string $coverMode  (optional) Cover mode; default <b>Pattern::COVER_MODE_AUTO</b>
     * @see Pattern::COVER_MODE_AUTO
     * @return Pattern
     * @throws Exception
     */
    public function cover($imagePath, $outputPath = null, $coverMode = self::COVER_MODE_AUTO) {
        // Invalid image extension
        if (!preg_match('%\.(?:png|jpe?g|gif)$%i', $imagePath)) {
            throw new Exception('Invalid file extension, expecting PNG or JPEG files');
        }
        
        if (null === $outputPath) {
            $outputPath = $imagePath;
        }
        
        // Sanitize the cover mode
        if (!in_array($coverMode, $this->_coverModesList)) {
            $coverMode = self::COVER_MODE_AUTO;
        }
        
        // Compute based on the file name
        if (self::COVER_MODE_AUTO === $coverMode) {
            if (preg_match('%^([a-z])[\-_]%i', basename($imagePath), $prefixMatches)) {
                switch ($prefixMatches[1]) {
                    case self::FILE_PREFIX_SKIP:
                        $coverMode = self::COVER_MODE_SKIP;
                        break;
                    
                    case self::FILE_PREFIX_OVERLAY:
                        $coverMode = self::COVER_MODE_OVERLAY;
                        break;
                    
                    case self::FILE_PREFIX_SWIRL:
                        $coverMode = self::COVER_MODE_SWIRL;
                        break;
                    
                    default:
                        $coverMode = self::COVER_MODE_NORMAL;
                        break;
                }
            } else {
                $coverMode = self::COVER_MODE_NORMAL;
            }
        }
        
        // Skip mode
        if (self::COVER_MODE_SKIP === $coverMode) {
            // Replace the output
            if ($imagePath !== $outputPath) {
                @copy($imagePath, $outputPath);
            }
            
            // Stop here
            return;
        }
        
        // Get the image info
        $imageInfo = getimagesize($imagePath);
        
        // Not a valid image
        if (!is_array($imageInfo)) {
            throw new Exception('File "' . $imagePath . '" is not a valid image');
        }
        
        // Get the image width and height
        list($width, $height) = $imageInfo;
        
        // Prepare the tile path
        $tilePath = escapeshellarg(ROOT . '/web/resources/patterns/' . $this->_pattern);
        
        // Prepare the escaped destination path
        $destPath = escapeshellarg($outputPath);
        
        // Prepare the escaped input path
        $imagePath = escapeshellarg($imagePath);
        
        // Prepare the "blend mode normal" command
        $command = "convert -size {$width}x{$height} tile:{$tilePath} {$imagePath} -channel a -alpha on -compose Dst_In -composite {$destPath}";
        
        // Change the command
        switch ($coverMode) {
            case self::COVER_MODE_OVERLAY:
                $command = "convert"
                    . " \( -size {$width}x{$height}"
                        . " tile:{$tilePath} {$imagePath}"
                        . " -channel a"
                        . " -alpha on"
                        . " -compose Dst_In"
                        . " -composite \)"
                    . " {$imagePath}"
                    . " -compose Overlay"
                    . " -composite "
                    . " {$destPath}";
                break;
            
            case self::COVER_MODE_SWIRL:
                // Swirl command
                $command = "convert"
                    . " {$imagePath}"
                    . " -swirl 180"
                    . " {$destPath}";
                break;
        }
        
        // Log the command
        Log::check(Log::LEVEL_DEBUG) && Log::debug($command);
        
        // Execute the command
        shell_exec($command);
        
        // All done
        return $this;
    }
    
    /**
     * Generate a pattern image of the given dimensions
     * 
     * @param int    $width    Image width; between <b>50</b> and <b>5000</b>
     * @param int    $height   Image height; ; between <b>50</b> and <b>5000</b>
     * @param string $destPath Image path
     * @return Pattern
     */
    public function generate($width, $height, $destPath) {
        // Sanitize the width
        $width = intval($width);
        $width = $width < 50 ? 50 : ($width > 5000 ? 5000 : $width);
        
        // Sanitize the height
        $height = intval($height);
        $height = $height < 50 ? 50 : ($height > 5000 ? 5000 : $height);
        
        // Prepare the tile path
        $tilePath = escapeshellarg(ROOT . '/web/resources/patterns/' . $this->_pattern);
        
        // Prepare the escaped destination path
        $destPath = escapeshellarg($destPath);
        
        // Prepare the command
        $command = "convert -size {$width}x{$height} tile:{$tilePath} {$destPath}";
        
        // Log it
        Log::check(Log::LEVEL_DEBUG) && Log::debug($command);
        
        // Create the canvas
        shell_exec($command);
        
        // All done
        return $this;
    }

}

/* EOF */
