<?php

/**
 * Theme Warlock - Directory Structure
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

/**
 * Defines a directory structure for a particular framework
 */
class Directory_Structure {

    /**
     * Used to refer to the directory structure in info.php
     */
    const LAYOUT = 'layout';

    /**
     * The actual Input folder to perform the validation on
     * 
     * @var string
     */
    protected static $_inputFolder = '';
    
    /**
     * Directory structure
     * 
     * @var array
     */
    protected $_structure = array();
    
    /**
     * Validated files array
     * 
     * @var array
     */
    protected $_validatedFiles = array();

    /**
     * Create a directory structure object
     */
    public function __construct() {
        // Set the Input folder default
        self::setInputFolder(IO::inputPath());
        
        // Get the arguments
        $arguments = func_get_args();

        // Go through each one, preparing the structure
        if (count($arguments)) {
            foreach ($arguments as $arg) {
                // Force casting to string so __toString method gets invoked
                if (is_a($arg, 'Directory_Structure_Abstract')) {
                    $this->_structure[] = unserialize((string) $arg);
                }
            }
        }
    }

    /**
     * Get the Input folder
     * 
     * @return string
     */
    public static function getInputFolder() {
        return str_replace('/', '\\', rtrim(self::$_inputFolder, '/\\'));
    }
    
    /**
     * Set the Input folder
     * 
     * @param string $folder Folder
     * @return null
     */
    public static function setInputFolder($folder) {
       self::$_inputFolder = $folder; 
    }
    
    /**
     * Validate the structure, based on the current Input
     * 
     * @return boolean
     */
    public function validate() {
        Console::p('Validating input...');
        echo PHP_EOL;
        return $this->_validateStructure($this->_structure);
    }
    
    /**
     * Get the validated files
     * 
     * @return array
     */
    public function getValidatedFiles() {
        return $this->_validatedFiles;
    }

    /**
     * Return a string representation of the rules set
     * 
     * @return string Structure description
     */
    public function describe() {
        return $this->_describeStructure($this->_structure);
    }

    /**
     * Description recursive helper
     * 
     * @param array  $struct Input structure validation
     * @param string $start  Path to the current file/folder
     * @return boolean
     */
    protected function _describeStructure($struct, $start = '') {
        // Prepare the result
        $result = '';

        // Go through each item in the structure
        foreach ($struct as $item) {
            if ('folder' == $item['_type']) {
                // Get the folder name
                $result .= $this->_describeFolder($item, $start);
            } else {
                // Get the file name
                $result .= $this->_describeFile($item, $start);
            }
        }

        // All done
        return $result;
    }

    /**
     * Describe a folder
     * 
     * @param array  $struct Input structure validation
     * @param string $start  Path to the current file or folder
     * @return string Description
     */
    protected function _describeFolder($item, $start) {
        // Get the folder descriptor
        $folderDescriptor = new Directory_Structure_Describe_Folder(trim($start, '\\'));

        // Return the folder description
        return $folderDescriptor->run($item);
    }

    /**
     * Describe a file
     * 
     * @param array  $struct Input structure validation
     * @param string $start  Path to the current file or folder
     * @return string Description
     */
    protected function _describeFile($item, $start) {
        // Get the file descriptor
        $fileDescriptor = new Directory_Structure_Describe_File(trim($start, '\\'));

        // Return the file description
        return $fileDescriptor->run($item);
    }

    /**
     * Validation recursive helper
     * 
     * @param array  $struct Input structure validation
     * @param string $start  Path to the current file/folder
     * @return boolean
     */
    protected function _validateStructure($struct, $start = '') {
        // Prepare the result
        $result = true;

        // Go through each item in the structure
        foreach ($struct as $item) {
            try {
                if ('folder' == $item['_type']) {
                    // Get the folder name
                    $folderNames = $this->_validateFolder($item, $start);

                    // Go through each result
                    foreach ($folderNames as $folder) {
                        // Log this folder
                        Log::check(Log::LEVEL_DEBUG) && Log::debug('Found folder "' . $start . '/' . $folder . '", as expected');

                        // Validate the folder children's structure
                        $result = $result && $this->_validateStructure($item['_contents'], trim($start . '\\' . $folder, '\\'));
                    }
                } else {
                    // Get the file name
                    $fileNames = $this->_validateFile($item, $start);

                    // Log this file
                    foreach ($fileNames as $file) {
                        Log::check(Log::LEVEL_DEBUG) && Log::debug('Found file "' . $start . '/' . $file . '", as expected');
                        
                        // Store the validated files
                        $this->_validatedFiles[] = $start . '/' . $file;
                    }
                }
            } catch (Exception $exc) {
                // Log the message
                Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());

                // Warn the user
                Console::p($exc->getMessage(), false);

                // Stop here
                $result = false;
            }
        }

        // All done
        return $result;
    }
    
    /**
     * Validate a folder
     * 
     * @param array  $struct Input structure validation
     * @param string $start  Path to the current file or folder
     * @throws Exception
     * @return string Folder name
     */
    protected function _validateFolder($item, $start = '') {
        // Get the folder validator
        $folderValidator = new Directory_Structure_Validate_Folder(self::getInputFolder() . '\\' . trim($start, '\\'));

        // Return the folder name after validation
        return $folderValidator->run($item);
    }

    /**
     * Validate a file
     * 
     * @param array  $struct Input structure validation
     * @param string $start  Path to the current file/folder
     * @throws Exception
     * @return string File name
     */
    protected function _validateFile($item, $start = '') {
        // Get the file validator
        $fileValidator = new Directory_Structure_Validate_File(self::getInputFolder() . '\\' . trim($start, '\\'));

        // Return the file name after validation
        return $fileValidator->run($item);
    }

}

/*EOF*/