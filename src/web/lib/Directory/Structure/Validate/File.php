<?php

/**
 * Theme Warlock - Directory Structure Validate File
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * File validation class
 */
class Directory_Structure_Validate_File extends Directory_Structure_Validate_Abstract {

    /**
     * Get file size
     * 
     * @param string $file File path
     * @return int Value in kB
     */
    protected function _getSize($file) {
        // Return the size
        return filesize($file) / 1024;
    }

    /**
     * Vaidate images dimensions and type
     * 
     * @param array  $images    Images list
     * @param int    $width     Width
     * @param int    $height    Height
     * @param int    $minWidth  Width
     * @param int    $minHeight Height
     * @param string $type      Mime type
     */
    protected function _validateDimensionsAndType(Array $images = array(), $width = null, $height = null, $type = null, $minWidth = null, $minHeight = null) {
        // Nothing to check
        if (!count($images) || (null === $width && null === $height && null === $type && null == $minWidth && null == $minHeight)) {
            return;
        }

        // Go through all the images
        foreach ($images as $image) {
            // Get the image information
            list($imageWidth, $imageHeight, $imageType) = @getimagesize($this->_currentDir . '\\' . $image);

            // Validate the image type
            if (null !== $type) {
                $imageType = image_type_to_mime_type($imageType);
                if ($type != $imageType) {
                    throw new Exception('Image type mismatch. Found ' . $imageType . ', expected ' . $type . ' in "' . $image . '"');
                }
            }

            // Validate 9-patch
            if (preg_match('%\.9\.png$%i', $image)) {
                try {
                    // Load the file as a 9-patch
                    $ninePatch = NinePatch::get($this->_currentDir . '\\' . $image);

                    // A repair has been made
                    if ($ninePatch->repairDone) {
                        // Get the relative path
                        $relativePath = substr($this->_currentDir . '\\' . $image, strlen(Directory_Structure::getInputFolder()) + 1);

                        // Log this
                        Log::check(Log::LEVEL_INFO) && Log::info('9-patch repaired: ' . $relativePath);
                    }
                } catch (Oddity $odd) {
                    throw new Exception('Invalid 9-patch "' . $image . '": '. $odd->getMessage());
                }
            }
            
            // Validate the image width
            if (null !== $width) {
                if ($width != $imageWidth) {
                    throw new Exception('Image width mismatch. Found ' . $imageWidth . ', expected ' . $width . ' in "' . $image . '"');
                }
            }
            
            // Validate the image min width
            if (null !== $minWidth) {
                if ($minWidth > $imageWidth) {
                    throw new Exception('Image width too small. Found ' . $imageWidth . ', expected at least ' . $minWidth . ' in "' . $image . '"');
                }
            }

            // Validate the image height
            if (null !== $height) {
                if ($height != $imageHeight) {
                    throw new Exception('Image height mismatch. Found ' . $imageHeight . ', expected ' . $height . ' in "' . $image . '"');
                }
            }

            // Validate the image min height
            if (null !== $minHeight) {
                if ($minHeight > $imageHeight) {
                    throw new Exception('Image height too small. Found ' . $imageHeight . ', expected at least ' . $minHeight . ' in "' . $image . '"');
                }
            }
        }
    }

}

/*EOF*/