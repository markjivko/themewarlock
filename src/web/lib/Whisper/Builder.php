<?php
/**
 * Theme Warlock - Whisper_Builder
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Whisper_Builder {

    // Builder templates
    const TEMPLATE_CALL_TO_ACTION     = 'call-to-action';
    const TEMPLATE_COLOR_DESCRIPTION  = 'color-description';
    const TEMPLATE_INSPIRATION        = 'inspiration';
    
    /**
     * Input directory for the current theme
     * 
     * @var string
     */
    protected $_inputDirectory = null;
    
    /**
     * The predominant colors for the current theme
     * 
     * @var array
     */
    protected $_predominantColors = array();
    
    /**
     * Inspiration words
     * 
     * @var array
     */
    protected $_inspiration = array();
    
    /**
     * Framework target, one of <ul>
     * <li>Framework::TARGET_GOKEYBOARD</li>
     * <li>Framework::TARGET_TOUCHPAL</li>
     * <li>Framework::TARGET_AITYPE</li>
     * </ul>
     * 
     * @var string
     */
    protected $_frameworkTarget = Framework::TARGET_GOKEYBOARD;
    
    /**
     * Framework target variants
     * 
     * @var string
     */
    protected $_frameworkTargetVariants = array(
        Framework::TARGET_PLAYERPRO => array(
            'Player Pro',
            'Player Pro Music Player',
            'Player Pro V3',
            'Player Pro 3.1',
        ),
        Framework::TARGET_POWERAMP => array(
            'Poweramp',
            'Poweramp v2',
            'Poweramp Player',
            'Poweramp Music Player',
        )
    );
    
    /**
     * Current theme's project name
     * 
     * @var string
     */
    protected $_projectName;
    
    /**
     * Used quote as (author, quote)
     * 
     * @var array
     */
    protected $_usedQuote = array();
    
    /**
     * Supported templates
     * 
     * @var string[]
     */
    protected static $_templates = array(
        self::TEMPLATE_INSPIRATION,
        self::TEMPLATE_COLOR_DESCRIPTION,
        self::TEMPLATE_CALL_TO_ACTION,
    );
    
    /**
     * Whisper Builder instance
     *
     * @var Whisper_Builder
     */
    protected static $_instance;
    
    /**
     * Image instance
     * 
     * @var Image
     */
    protected static $_image;
    
    /**
     * Whisper Builder
     */
    protected function __construct() {
        // Get the image instance
        if (!isset(self::$_image)) {
            self::$_image = new Image();
        }
        
        // Create the ReadMe
        $this->createReadme();
    }
    
    /**
     * Create the readme file
     * 
     * @return null
     */
    protected function createReadme() {
        // Prepare the string
        $string = '';
        
        // Go through the keywords
        foreach (Whisper_Builder_Variable::getList() as $methodName => $methodInfo) {
            list($methodDescription, $methodArguments) = $methodInfo;
            
            // Get the method arguments list
            $methodArgumentsList = implode(',', array_keys($methodArguments));
            
            // Prepare the tag format
            $tagFormat = sprintf(
                '{%s%s}',
                $methodName,
                strlen($methodArgumentsList) ? ',' . $methodArgumentsList : ''
            );
            
            // Append the information
            $string .= $tagFormat . ' - ' . $methodDescription . PHP_EOL;
            
            // Describe the method arguments
            if (count($methodArguments)) {
                foreach ($methodArguments as $methodArgument => $methodArgumentDescriptionArray) {
                    // Get the first line
                    $methodArgumentFirstLine = array_shift($methodArgumentDescriptionArray);
                    
                    // Get the argument length
                    $methodArgumentLength = strlen($methodArgument) + 6;
                    
                    // Append the first line
                    $string .= '  * ' . $methodArgument . ': ' . $methodArgumentFirstLine . PHP_EOL;
                    
                    // Append the rest of the lines
                    foreach ($methodArgumentDescriptionArray as $methodArgumentDescription) {
                        $string .= str_repeat(' ', $methodArgumentLength) . $methodArgumentDescription . PHP_EOL;
                    }
                    
                    // Close this line
                    $string .= PHP_EOL;
                }
            }
        }
        
        // Prepend the information
        $string = 'Use one of the following variables for your templates:' . PHP_EOL . PHP_EOL . $string;
        
        // Not yet defined
        if (!file_exists($readmePath = ROOT . '/web/resources/whisper/builder/readme.txt')) {
            file_put_contents($readmePath, '');
        }
        
        // Strings differ
        if ($string !== file_get_contents($readmePath)) {
            file_put_contents($readmePath, $string);
        }
    }

    /**
     * Whisper Builder
     * 
     * @return Whisper_Builder
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Get the predominant colors
     * 
     * @return array
     */
    public function getPredominantColors() {
        return $this->_predominantColors;
    }
    
    /**
     * Return the quote used as (author, quote)
     * 
     * @return array
     */
    public function getUsedQuote() {
        return $this->_usedQuote;
    }
    
    /**
     * Set the items inspiration
     * 
     * @param string[] $inspiration Inspiration words
     */
    public function setInspiration(Array $inspiration) {
        $this->_inspiration = array_values($inspiration);
    }
    
    /**
     * Get the theme inspiration
     * 
     * @return string[] Inspiration words
     */
    public function getInspiration() {
        return $this->_inspiration;
    }
    
    /**
     * Set the current project's name
     * 
     * @param string $projectName Project name
     * @return string The project name set
     * @throws Exception
     */
    public function setProjectName($projectName) {
        // Replace multiple spaces
        $projectName = preg_replace('%\s+%is', ' ', $projectName);
        
        // Get the string value of the project name
        $projectName = trim($projectName);
        
        // Cannot be empty
        if (!strlen($projectName)) {
            throw new Exception('Project name cannot be empty');
        }
        
        // Starting with a digit
        if (preg_match('%^\d%i', $projectName)) {
            throw new Exception('The project name cannot start with a digit');
        }
        
        // Special characters
        if (!preg_match('%^([a-z0-9 ]+)$%i', $projectName)) {
            throw new Exception('Only space and alphanumeric characters are allowed');
        }
        
        // All done
        $this->_projectName = ucfirst($projectName);
        
        // Return the project name
        return $this->_projectName;
    }
    
    /**
     * Get the current theme's project name
     * 
     * @return string/null
     */
    public function getProjectName() {
        // Return the current project name
        return isset($this->_projectName) ? $this->_projectName : null;
    }
    
    /**
     * Set the framework target
     * 
     * @param string $frameworkTarget, one of <ul>
     * <li>Framework::TARGET_GOKEYBOARD</li>
     * <li>Framework::TARGET_TOUCHPAL</li>
     * <li>Framework::TARGET_AITYPE</li>
     * </ul>
     */
    public function setFrameworkTarget($frameworkTarget) {
        // Invalid framework target, revert to default
        if (!in_array($frameworkTarget, array_keys(Framework::$targetIds))) {
            $frameworkTarget = Framework::TARGET_GOKEYBOARD;
        }
        
        // Set the framework target
        $this->_frameworkTarget = $frameworkTarget;
    }
    
    /**
     * Return the current theme's framework target
     * 
     * @return string Framework target, one of one of <ul>
     * <li>Framework::TARGET_GOKEYBOARD</li>
     * <li>Framework::TARGET_TOUCHPAL</li>
     * <li>Framework::TARGET_AITYPE</li>
     * </ul>
     */
    public function getFrameworkTarget() {
        // Return the framework targt
        return $this->_frameworkTarget;
    }
    
    /**
     * Get the current framework target variants
     * 
     * @return array|null
     */
    public function getFrameworkTargetVariants() {
        return isset($this->_frameworkTargetVariants[$this->_frameworkTarget]) ? $this->_frameworkTargetVariants[$this->_frameworkTarget] : null;
    }
    
    /**
     * Sets the Input directory (expecting to find run.csv)
     * 
     * @param string $inputDirectory Path to the current theme's files
     * @return boolean True on success
     * @throws exception
     */
    public function setInputDir($inputDirectory = null) {
        // Must specify the Input directory
        if (empty($inputDirectory)) {
            throw new Exception('Input directory must be specified');
        }
        
        // Remove the final slash
        $inputDirectory = rtrim($inputDirectory, '/\\');
        
        // Already set the Input directory
        if ($this->_inputDirectory === $inputDirectory) {
            return true;
        }
        
        // Themepreview path
        $jpegFiles = glob($inputDirectory . '/*.jpg');
        
        // Files found
        if (!count($jpegFiles)) {
            throw new Exception('JPEG image not found in ' . $inputDirectory);
        }

        // Get the themepreview
        $themepreviewPath = current($jpegFiles);

        // Get the predominant colors
        $this->_predominantColors = array();

        // Load the themepreview image
        $themepreviewResource = self::$_image->load($themepreviewPath);
        
        // Prepare the top border height
        $delta = 50;
        
        // Crop the top border
        $themepreviewResourceCropped = self::$_image->crop($themepreviewResource, 0, $delta, imagesx($themepreviewResource), imagesy($themepreviewResource));
        
        // Store the color names only
        foreach (self::$_image->getColors(array($themepreviewResourceCropped, false), 5, 10, 20) as $color) {
            $this->_predominantColors[] = $color[0];
        }

        // Set the Input directory
        $this->_inputDirectory = $inputDirectory;

        // All went well
        return true;
    }
    
    /**
     * Get the Input directory
     * 
     * @return string Input directory
     */
    public function getInputDir() {
        return $this->_inputDirectory;
    }
    
    /**
     * Get an array of template => [sentences]
     * 
     * @param int     $min      Min number of sentences per template
     * @param int     $max      Max number of sentences per template
     * @param boolean $useQuote Use a quote for the generated description
     * @return array List of sentences
     */
    public function generate($min = 1, $max = 3, $useQuote = false) {
        // Prepare the result
        $result = array();
        
        // Go through all the templates
        foreach (self::$_templates as $template) {
            // Get the resource path
            $resourcePath = ROOT . '/web/resources/whisper/builder/' . $template . '.txt';
            
            // Get the file as an array
            $resourceArray = array_unique(array_filter(file($resourcePath)));
            
            // Shuffle the array
            shuffle($resourceArray);
            
            // Get the elements
            $elements = array_map(
                function($item){
                    $item = trim($item);
                    if (!preg_match('%[\.\;\?\!]$%', $item)) {
                        $item .= '.';
                    }
                    return $item;
                }, 
                array_slice($resourceArray, 0, mt_rand($min, $max))
            );
            
            // Store the result
            foreach ($elements as $key => $value) {
                $elements[$key] = $this->_parse($value);
            }
            
            // Get the sentence
            $result[$template] = implode(' ', $elements);
            
            // Previously mentioned something
            if ($useQuote && count($this->_inspiration) && self::TEMPLATE_INSPIRATION == $template) {
                // Prepare the quotes array
                $quotes = array();
                
                // Reset the used quote
                $this->_usedQuote = array();
                
                // Go through the inspiration items
                foreach ($this->_inspiration as $inspiration) {
                    if (count($foundQuote = Whisper_Inspiration::getQuote($inspiration))) {
                        $quotes[] = $foundQuote;
                    }
                }
                
                // Have a quote
                $this->_usedQuote = count($quotes) ? $quotes[mt_rand(0, count($quotes) - 1)] : Whisper_Inspiration::getQuote();
                
                // Append a quote
                if (isset($this->_usedQuote[Whisper_Inspiration::Q_TEXT])) {
                    $result[$template] .= PHP_EOL . $this->_usedQuote[Whisper_Inspiration::Q_TEXT];
                }
            }
        }
        
        // Return the elements
        return array_filter(array_map('trim', $result));
    }
    
    /**
     * Parse the tags within a text
     * 
     * @param string $text Text
     * @return string
     */
    protected function _parse($text) {
        // Nothing to do
        if (empty($text)) {
            return '';
        }
        
        // Get the number of inpiration tags
        $inspirationTags = 0;
        if (preg_match_all('%\{\s*inspiration\s*\}%i', $text, $matches, PREG_SET_ORDER)) {
            $inspirationTags = count($matches);
        }
        
        // Set the extra information
        Whisper_Builder_Variable::setExtra(array(
            Whisper_Builder_Variable::EXTRA_INSPIRATION_TAGS => $inspirationTags,
        ));
        
        // Perform the replacements
        $newSentence = trim(preg_replace('%\s{2,}%', ' ', preg_replace_callback(
            '%{([^}]*)}%', 
            function($match) {
                return Whisper_Builder_Variable::get($match[1]);
            }, 
            $text
        )));
            
        // Make sure the first character is uppercase
        $newSentence[0] = strtoupper($newSentence[0]);
        
        // All done
        return $newSentence;
    }
}

/* EOF */