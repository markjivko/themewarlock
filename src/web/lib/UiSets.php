<?php
/**
 * Theme Warlock - UiSets
 * 
 * @title      UI Sets handler
 * @desc       Handles previews of Bootstrap CSS themes
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class UiSets {

    /**
     * Available sets
     */
    const SET_DEFAULT = 'default';
    const SET_LUMEN = 'lumen';
    
    /**
     * UI Sets info file
     */
    const FILE_INFO = 'info.json';
    
    /**
     * Singleton instance of UiSet
     *
     * @var UiSets
     */
    protected static $_instance;
    
    /**
     * Available UI Sets
     * 
     * @var string[]
     */
    protected static $_uiSets = array();
    
    /**
     * UI Set Objects
     * 
     * @var UiSets_Set[]
     */
    protected static $_uiSetObjects = array();
    
    /**
     * Singleton
     */
    protected function __construct() {
        // Get the available CSS files
        $uiSets = array_map(
            function($item){
                return basename($item, '.css');
            }, 
            glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/*.css')
        );
            
        // Get the data
        if (is_file($infoPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . self::FILE_INFO)) {
            self::$_uiSets = @json_decode(file_get_contents($infoPath), true);
            if (!is_array(self::$_uiSets)) {
                self::$_uiSets = array();
            }
        }
        
        // Get the described ui sets
        $describedUiSets = array_keys(self::$_uiSets);
        
        // Save flag
        $saveFlag = count(array_merge(array_diff($uiSets, $describedUiSets), array_diff($describedUiSets, $uiSets))) > 0;
        
        // Prepare the result
        $result = array();

        // Prepare the constants
        $constantLines = array();

        // Go through each set
        foreach ($uiSets as $uiSet) {
            // Store the data
            $result[$uiSet] = isset(self::$_uiSets[$uiSet]) ? self::$_uiSets[$uiSet] : array(ucfirst($uiSet), '-');

            // Append the constant line
            $constantLines[] = '    const SET_' . strtoupper(preg_replace('%\W+%', '_', $uiSet)) . ' = ' . var_export($uiSet, true) . ';';
        }

        // Save the file
        if ($saveFlag) {
            file_put_contents($infoPath, json_encode($result, JSON_PRETTY_PRINT));
            
            // Replace the constants in dev mode only
            if (AppMode::equals(AppMode::DEVELOPMENT)) {
                file_put_contents(
                    __FILE__, 
                    preg_replace(
                        '%(\/\*\*\s*\*\s*Available sets\s*\*\/)(.*?)(\s*\/\*\*)%ims', 
                        '${1}' . PHP_EOL . implode(PHP_EOL, $constantLines) . '${3}', 
                        file_get_contents(__FILE__)
                    )
                );
            }
        }
        
        // Replace the current UI data
        self::$_uiSets = $result;
    }
    
    /**
     * Save the current objects
     * 
     * @return UiSets
     */
    protected function _saveData() {
        // Make sure all objects are initialized
        $this->getAll();
        
        // Get the export string
        $newJson = json_encode(
            array_map(
                function(/*@var $item UiSets_Set */$item) {
                    return $item->toArray();
                }, 
                self::$_uiSetObjects
            ), 
            JSON_PRETTY_PRINT
        );

        // Get the file contents
        $originalJson = file_get_contents($filePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/' . self::FILE_INFO);
        
        // Save needed
        if ($newJson !== $originalJson) {
            file_put_contents($filePath, $newJson);
        }
        
        // All done
        return $this;
    }
    
    /**
     * Singleton instance of UiSet
     * 
     * @return UiSets
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Get all available UI Sets
     * 
     * @return UiSets_Set[]
     */
    public function getAll() {
        // Initialize all objects
        if (count(self::$_uiSets) !== count(self::$_uiSetObjects)) {
            foreach (array_keys(self::$_uiSets) as $uiSetName) {
                $this->get($uiSetName);
            }
        }
        
        // All done
        return self::$_uiSetObjects;
    }
    
    /**
     * Get all available UI Sets in array format
     * 
     * @return array
     */
    public function getAllArray() {
        return array_map(
            function($item) {
                return $item->toArray();
            }, 
            $this->getAll()
        );
    }
    
    /**
     * Get a UI Set by name
     * 
     * @param string $uiSetName UI Set name
     * @return UiSets_Set
     * @throws Exception
     */
    public function get($uiSetName) {
        // Cast to string and trim
        $uiSetName = trim($uiSetName);
        
        // Invalid UI Set
        if (!isset(self::$_uiSets[$uiSetName])) {
            throw new Exception('Invalid UI Set "' . $uiSetName . '"');
        }
        
        // Cache miss
        if (!isset(self::$_uiSetObjects[$uiSetName])) {
            // Prepare the object
            self::$_uiSetObjects[$uiSetName] = new UiSets_Set(
                $uiSetName,
                self::$_uiSets[$uiSetName][0],
                self::$_uiSets[$uiSetName][1]
            );
        }
        
        // All done
        return self::$_uiSetObjects[$uiSetName];
    }
    
}

/*EOF*/