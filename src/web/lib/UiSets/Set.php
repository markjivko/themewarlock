<?php
/**
 * Theme Warlock - UiSets_Set
 * 
 * @title      Bootstrap CSS theme
 * @desc       Wrapper for an UI set
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class UiSets_Set extends UiSets {

    /**
     * Set Name
     *
     * @var string
     */
    protected $_name;
    
    /**
     * Set Title
     *
     * @var string
     */
    protected $_title;
    
    /**
     * Set Description
     * 
     * @var string
     */
    protected $_description;
    
    /**
     * Path to the image preview
     * 
     * @var string
     */
    protected $_previewPath;
    
    /**
     * Bootstrap UI Set
     * 
     * @param string $name        UI Set name
     * @param string $title       UI Set title
     * @param string $description UI Set description
     * @throws Exception
     */
    protected function __construct($name, $title, $description) {
        // Store the name
        $this->_name = trim($name);
        
        // Invalid file name
        if (!file_exists(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . $this->_name . '.css')) {
            throw new Exception('Invalid UI Set "' . $this->_name . '"');
        }
        
        // Store the title
        $this->_title = trim($title);
        
        // Store the description
        $this->_description = trim($description);
        
        // Store the preview path
        $this->_previewPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . $this->_name . '.png';
        
        // Development mode
        if (AppMode::equals(AppMode::DEVELOPMENT)) {
            // Regenerate the previews
            if (!is_file($this->getPreviewPath())) {
                $this->generatePreview();
            }
        }
    }
    
    /**
     * Method not available
     * 
     * @deprecated
     * @return UiSets_Set
     */
    public function getAll() {
        // Nothing to do
        return $this;
    }
    
    /**
     * Get the current set
     * 
     * @return UiSets_Set
     */
    public function get($uiSetName = null) {
        return $this;
    }
    
    /**
     * Get the UI Set name
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Get the UI Set title
     * 
     * @return string
     */
    public function getTitle() {
        return $this->_title;
    }
    
    /**
     * Set the title
     * 
     * @param string $title UI Set Title
     * @return UiSets_Set
     */
    public function setTitle($title) {
        // Store the title
        $this->_title = trim($title);
        
        // Trigger a save
        $this->_saveData();
        
        // All done
        return $this;
    }
    
    /**
     * Get the UI Set description
     * 
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }
    
    /**
     * Set the UI Set description
     * 
     * @param string $description UI Set Description
     * @return UiSets_Set
     */
    public function setDescription($description) {
        // Store the description
        $this->_description = trim($description);
        
        // Trigger a save
        $this->_saveData();
        
        // All done
        return $this;
    }
    
    /**
     * Convert the UI set into a saveable array
     * 
     * @return string[]
     */
    public function toArray() {
        return array(
            $this->getName(),
            $this->getDescription()
        );
    }
    
    /**
     * Get the preview image path
     * 
     * @return string
     */
    public function getPreviewPath() {
        return $this->_previewPath;
    }
    
    /**
     * Generate the preview image
     * 
     * @return UiSets_Set
     */
    public function generatePreview() {
        // Get the template
        $template = str_replace(
            array(
                '__NAME__',
                '__TITLE__',
            ), 
            array(
                $this->getName(),
                $this->getTitle(),
            ), 
            file_get_contents(ROOT . '/web/resources/' . Framework::FOLDER_UI_SETS . '/index.phtml')
        );
        
        // Prepare the temporary path
        $tempPath = ROOT . '/web/temp/' . Framework::FOLDER_UI_SETS . '/' . uniqid() . '.html';
        
        // Create the directory structure
        if (!is_dir(dirname($tempPath))) {
            Folder::create(dirname($tempPath), 0777, true);
        }
        
        // Save the file
        file_put_contents($tempPath, $template);
        
        // Grab a screenshot
        $screenshot = new Screenshot();
        $screenshot->grab(
            'file://' . realpath($tempPath), 
            $this->getPreviewPath(),
            590
        );
        
        // Optimize the file
        Image::optimize($this->getPreviewPath());
        
        // Remove the file
        @unlink($tempPath);
        
        // All done
        return $this;
    }
    
}

/*EOF*/