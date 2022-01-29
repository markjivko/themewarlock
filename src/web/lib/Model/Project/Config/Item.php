<?php
/**
 * Theme Warlock - Model_Project_Config_Item
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

abstract class Model_Project_Config_Item {

    /**
     * Default file extension
     */
    const EXT_TXT = 'txt';
    
    /**
     * Project Item folder name
     */
    const FOLDER_NAME = 'item';
    
    /**
     * Extra keys
     */
    const KEY_FLAVOR     = '_flavor';
    const KEY_ICON       = '_icon';
    const KEY_CSS        = 'css';
    const KEY_CSS_INLINE = 'cssi';
    const KEY_JS         = 'js';
    
    /**
     * Item details
     */
    const ITEM_TYPE           = 'generic';
    const ITEM_TYPECAST       = '';
    const ITEM_DEFAULT        = '';
    const ITEM_ON_DISK        = false;
    const ITEM_ON_DISK_BINARY = false;

    /**
     * JSON format
     */
    const JSON_TYPE                 = 't';
    const JSON_KEY                  = 'k';
    const JSON_VALUE                = 'v';
    const JSON_IS_LIST              = 'l';
    const JSON_OPTIONS              = 'o';
    const JSON_OPTIONS_STRICT       = 's';
    const JSON_IS_DISABLED          = 'x';
    const JSON_IS_ON_DISK           = 'd';
    const JSON_IS_ON_DISK_BINARY    = 'db';
    const JSON_EXTENSION            = 'e';
    const JSON_META_TITLE           = 'mt';
    const JSON_META_DESCRIPTION     = 'md';
    const JSON_META_SYMBOL          = 'my';
    const JSON_META_DEPENDS         = 'ms';
    const JSON_META_DEPENDS_ADDON   = 'msa';
    const JSON_META_HEADER          = 'mh';
    const JSON_META_URL             = 'mu';
    const JSON_META_REFRESH         = 'mr';
    const JSON_META_OPTIONS_DETAILS = 'mod';
    const JSON_META_OPTIONS_CLONE   = 'moc';
    const JSON_META_OPTIONS_PICKER  = 'mop';
    
    /**
     * Item key
     * 
     * @var string
     */
    protected $_key;
    
    /**
     * Item value
     * 
     * @var mixed
     */
    protected $_value = null;
    
    /**
     * Is this item a list
     * 
     * @var boolean
     */
    protected $_isList = false;
    
    /**
     * List of options
     * 
     * @var array
     */
    protected $_options = array();
    
    /**
     * Strict item
     *
     * @var boolean Limit the result to the options list
     */
    protected $_strict = true;
    
    /**
     * Is this item disabled?
     * 
     * @var boolean
     */
    protected $_isDisabled = false;
    
    /**
     * Item paths on disk GLOB pattern
     * 
     * @var string
     */
    protected $_itemPathOnDisk = null;
    
    /**
     * Item extension (when stored on disk)
     * 
     * @var string
     */
    protected $_itemExtension = self::EXT_TXT;
    
    /**
     * Meta title
     * 
     * @var string
     */
    protected $_metaTitle = '';
    
    /**
     * Meta description
     * 
     * @var string
     */
    protected $_metaDesc = '';
    
    /**
     * Meta symbol
     * 
     * @var string
     */
    protected $_metaSymbol = '';
    
    /**
     * Meta dependency key
     * 
     * @var string
     */
    protected $_metaDepends = '';
    
    /**
     * Meta dependency addon
     * 
     * @var string
     */
    protected $_metaDependsAddon = '';
    
    /**
     * Meta header
     * 
     * @var string
     */
    protected $_metaHeader = '';
    
    /**
     * Meta URL
     * 
     * @var string
     */
    protected $_metaUrl = '';
    
    /**
     * Meta Action
     * 
     * @var string
     */
    protected $_metaRefresh = '';
    
    /**
     * Item options' title/description array<br/>
     * <br/>
     * <code>
     * array(<br/>
     *     "itemName" => array(<br/>
     *         "item title",<br/>
     *         "item description",<br/>
     *     ),<br/>
     * )
     * </code>
     * @var array
     */
    protected $_metaOptionsDetails = array();
    
    /**
     * Meta options clone; used to save space when loading project data
     * 
     * @var string
     */
    protected $_metaOptionsClone = '';
    
    /**
     * Use a drop-down picker for options
     * 
     * @var boolean
     */
    protected $_metaOptionsPicker = false;
    
    /**
     * Sanitizer method
     * 
     * @var callable
     */
    protected $_sanitizer = null;
    
    /**
     * Project configuration item
     * 
     * @param string  $key           Item key
     * @param mixed   $value         Item value (default <b>null</b>)
     * @param boolean $isList        Whether the item is an array (default <b>false</b>)
     * @param array   $options       List of available values (default <b>array()</b>)
     * @param boolean $optionsStrict Limit the result to the options list (default <b>true</b>)
     * @throws Exception
     */
    public function __construct($key, $value = null, $isList = false, Array $options = array(), $optionsStrict = true) {
        // Generic check
        if (static::ITEM_TYPE == self::ITEM_TYPE) {
            throw new Exception('Cannot use a generic item');
        }
        
        // Invalid key
        if (!is_string($key) || strlen(trim($key)) == 0) {
            throw new Exception('The key must be a non-empty string');
        }
        
        // Store the details
        $this->_key = trim($key);
        $this->setIsList($isList);
        $this->setOptions($options);
        $this->setOptionsStrict($optionsStrict);
        $this->setValue($value);
    }
    
    /**
     * Get the item's value
     * 
     * @return mixed
     */
    public function __toString() {
        return $this->getValue();
    }
    
    /**
     * Check whether this item is stored on disk
     * 
     * @return boolean
     */
    public function isOnDisk() {
        return static::ITEM_ON_DISK;
    }
    
    /**
     * Check whether this item is stored on disk and it's a binary file (image, audio etc.)
     * 
     * @return boolean
     */
    public function isOnDiskBinary() {
        return static::ITEM_ON_DISK_BINARY;
    }
    
    /**
     * Load the disk value or filename for binary files
     * 
     * @param string $projectPath Path Project path on disk
     * @param string $addonName   Add-on name (default <b>null</b>)
     * @return Model_Project_Config_Item
     * @throws Exception
     */
    public function load($projectPath, $addonName = null) {
        // Item is stored on disk
        if ($this->isOnDisk()) {
            // Get the actual item
            $this->_itemPathOnDisk = $projectPath . '/' . self::FOLDER_NAME . '/' . $this->getType() . '/' . (null !== $addonName ? ($addonName . '-') : '') . $this->getKey() . '-*.' . $this->getExtension();
            
            // Set the default value
            $value = $this->isList() ? array() : self::ITEM_DEFAULT;
            
            // Custom default value set by code
            if ($value != $this->getValue()) {
                // Prepare the custom values (file paths or file contents)
                $customDefaultValues = $this->isList() ? $this->getValue() : array($this->getValue());
                
                // Go through the list
                foreach ($customDefaultValues as $fileKey => $customDefaultValue) {
                    // Get the destination path
                    $filePathDestination = preg_replace('%\-\*\.%', '-' . $fileKey . '.', $this->_itemPathOnDisk);
                        
                    // Binary
                    if ($this->isOnDiskBinary()) {
                        // Valid binary file path provided
                        if (preg_match('%^\/\w+%', $customDefaultValue) && is_file($customDefaultValue)) {
                            if (!is_file($filePathDestination)) {
                                copy($customDefaultValue, $filePathDestination);
                            }
                        }
                    } else {
                        if (!is_file($filePathDestination)) {
                            file_put_contents($filePathDestination, $customDefaultValue);
                        }
                    }
                }
            }
            
            // Go through the paths
            foreach (glob($this->_itemPathOnDisk) as $pathOnDisk) {
                // Load the file contents
                $fileInformation = $this->isOnDiskBinary() ? basename($pathOnDisk) : file_get_contents($pathOnDisk);
                
                // Only the first element is stored in non-list items
                if (!$this->isList()) {
                    $value = $fileInformation;
                    break;
                } else {
                    // Get the file info
                    $value[] = $fileInformation;
                }
            }
            
            // Validate the value
            $this->_validateAndSet($value);
        }
        
        // All done
        return $this;
    }
    
    /**
     * Item type
     * 
     * @return string
     */
    public function getType() {
        return static::ITEM_TYPE;
    }
    
    /**
     * Get the item key
     * 
     * @return string
     */
    public function getKey() {
        return $this->_key;
    }
    
    /**
     * Get the item extension; applicable only for items that are stored on disk
     * 
     * @return string
     */
    public function getExtension() {
        return $this->_itemExtension;
    }

    /**
     * Set the item extension
     * 
     * @param string $extension
     * @return Model_Project_Config_Item
     */
    public function setExtension($extension) {
        // Clean-up the extension
        $extension = strtolower(preg_replace('%[^a-z0-9]+%i', '', $extension));
        
        // Valid value
        if (strlen($extension)) {
            $this->_itemExtension = $extension;
        }
        
        // All done
        return $this;
    }
    
    /**
     * Get the item value
     * 
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }
    
    /**
     * Set a new value
     * 
     * @param mixed   $value     Item value
     * @param boolean $userInput This value is set by the user (default <b>false</b>)
     * @return Model_Project_Config_Item
     * @throws Exception
     */
    public function setValue($value = null, $userInput = false) {
        // Store the original values
        $valueOriginal = $this->getValue();
        
        // Validate the value
        $this->_validateAndSet($value, $userInput);
        
        // Store the item on the disk
        if ($this->isOnDisk() && $this->_itemPathOnDisk !== null) {
            // Get the parent
            $itemDir = dirname($this->_itemPathOnDisk);

            // Prepare the values to save
            $valuesToSave = $this->isList() ? $this->getValue() : array($this->getValue());
            
            // Remove the unnecessary files
            $globItems = array();
            foreach (glob($this->_itemPathOnDisk) as $itemPath) {
                // Atomic DELETE for binary files
                if ($this->isOnDiskBinary() && false !== $itemKey = array_search(basename($itemPath), $valuesToSave)) {
                    // Store for later use
                    $globItems[$itemKey] = $itemPath;
                    continue;
                }
                
                // Remove everything else
                @unlink($itemPath);
            }
            
            // Clearing the value
            if (self::ITEM_DEFAULT == $this->getValue() || ($this->isList() && !count($this->getValue()))) {
                // Parent no longer needed
                if (0 == count(glob($itemDir . '/*.*'))) {
                    Folder::clean($itemDir, true);
                }
            } else {
                // Create the folder if it does not exist
                if (!is_dir($itemDir)) {
                    Folder::create($itemDir, 0777, true);
                }

                // Get the file index
                $fileIndexes = array_map(function($item){
                    return intval(preg_replace('%^.*?\-(\d+)\.\w+$%', '${1}', basename($item)));
                }, $globItems);
                $fileIndex = count($fileIndexes) ? max($fileIndexes) + 1 : 0;
                
                // Store the files
                foreach ($valuesToSave as $key => $value) {
                    do {
                        // Binary files support CRUD; others are automatically re-created
                        if ($this->isOnDiskBinary()) {
                            // Nothing to do
                            $newFilePath = null;
                                    
                            // Current item
                            if (isset($valueOriginal[$key])) {
                                // Atomic UPDATE event
                                if ($valueOriginal[$key] != $value) {
                                    $newFilePath = preg_replace('%\*(\.\w+)$%', intval(preg_replace('%^.*?\-(\d+)\.\w+$%', '${1}', basename($valueOriginal[$key]))) . '${1}', $this->_itemPathOnDisk);
                                }
                                
                                // Stop here
                                break;
                            }
                        }
                        
                        // Atomic CREATE event
                        $newFilePath = preg_replace('%\*(\.\w+)$%', $fileIndex . '${1}', $this->_itemPathOnDisk);

                        // Increment the index
                        $fileIndex++;
                    } while (false);
                    
                    // Binary - move uploaded files
                    if ($this->isOnDiskBinary()) {
                        // Atomic UPDATE or CREATED events
                        if (null !== $newFilePath && is_file($value)) {
                            // Move the file to the new destination
                            copy($value, $newFilePath);
                            @unlink($value);
                        }
                    } else {
                        // ASCII - copy-paste the contents
                        file_put_contents($newFilePath, $value);
                    }
                }
            }
        }
        
        // All done
        return $this;
    }
    
    /**
     * Validate and store the value
     * 
     * @param mixed   $value     Item value
     * @param boolean $userInput This value is set by the user (default <b>false</b>)
     * @throws Exception
     */
    protected function _validateAndSet($value, $userInput = false) {
        // User modification disallowed
        if ($userInput && $this->isDisabled()) {
            return;
        }
        
        // Default value for the list
        if ($this->isList() && !is_array($value)) {
            $value = array();
        }

        // Validate the value
        if ($this->isList()) {
            // Filter-out invalid values
            $value = array_filter($value, function($v) {
                return $this->_validateValue($v);
            });
        } else {
            // Set the default
            if (!$this->_validateValue($value)) {
                $value = static::ITEM_DEFAULT;
            }
        }
        
        // Sanitizer available
        if (null !== $this->_sanitizer && is_callable($this->_sanitizer)) {
            // Call the sanitizer
            $value = call_user_func($this->_sanitizer, $value);
        }
        
        // Typecasting
        if (strlen(static::ITEM_TYPECAST) || is_array(static::ITEM_TYPECAST)) {
            if ($this->isList()) {
                // Convert to the required type
                $value = array_map(static::ITEM_TYPECAST, $value);
            } else {
                if (static::ITEM_DEFAULT != $value) {
                    $value = call_user_func(static::ITEM_TYPECAST, $value);
                }
            }
        }
        
        // All done
        $this->_value = $value;
    }
    
    /**
     * Set the <b>sanitizer</b> function to use on the <b>value</b> after it passed the validation tests.<br/>
     * The method expects 1 argument (<b>$value</b>) and <b>returns the sanitized value</b>.<br/>
     * Setting <b>$callable</b> to <b>null</b> removes the sanitizer.
     * 
     * @param callable $callable Callable function
     * @return Model_Project_Config_Item
     */
    public function setSanitizer($callable = null) {
        if (null === $callable || is_callable($callable)) {
            $this->_sanitizer = $callable;
        }
        return $this;
    }
    
    /**
     * Get the item sanitizer or <b>null</b>
     * 
     * @return callable|null
     */
    public function getSanitizer() {
        return $this->_sanitizer;
    }
    
    /**
     * Get the whether this item is a list
     * 
     * @return boolean
     */
    public function isList() {
        return $this->_isList;
    }
    
    /**
     * Item is list
     * 
     * @param boolean $isList List (default <b>true</b>)
     * @return Model_Project_Config_Item
     */
    public function setIsList($isList = true) {
        $this->_isList = (boolean) $isList;
        return $this;
    }
    
    /**
     * Get the whether this item is disabled
     * 
     * @return boolean
     */
    public function isDisabled() {
        return $this->_isDisabled;
    }
    
    /**
     * Item is disabled - its value cannot be changed by the user
     * 
     * @param boolean $isDisabled Mark item as disabled (default <b>true</b>)
     * @return Model_Project_Config_Item
     */
    public function setIsDisabled($isDisabled = true) {
        $this->_isDisabled = (boolean) $isDisabled;
        return $this;
    }
    
    /**
     * Get the list of allowed values
     * 
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }
    
    /**
     * Set the list of allowed values
     * 
     * @param array $options List of options, non-associative array (default <b>array()</b>)
     * @return Model_Project_Config_Item
     */
    public function setOptions(Array $options = array()) {
        $this->_options = array_values($options);
        return $this;
    }
    
    /**
     * Get whether to limit the user input to the options list (no other value allowed)
     * 
     * @see Model_Project_Config_Item::getOptions()
     * @return boolean
     */
    public function getOptionsStrict() {
        return $this->_strict;
    }
    
    /**
     * Limit the user input to the options list (no other value allowed)
     * 
     * @param boolean $strict Strict mode (default <b>true</b>)
     * @return Model_Project_Config_Item
     */
    public function setOptionsStrict($strict = true) {
        $this->_strict = (boolean) $strict;
        return $this;
    }
    
    /**
     * Get the meta title
     * 
     * @return string
     */
    public function getMetaTitle() {
        return $this->_metaTitle;
    }
    
    /**
     * Get the item's title or key if the title was not set
     * 
     * @return string
     */
    public function getTitleOrKey() {
        return strlen($this->getMetaTitle()) ? $this->getMetaTitle() : $this->getKey();
    }
    
    /**
     * Set the meta title
     * 
     * @param string $title Item title
     * @return Model_Project_Config_Item
     */
    public function setMetaTitle($title) {
        $this->_metaTitle = trim($title);
        return $this;
    }
    
    /**
     * Get the meta description
     * 
     * @return string
     */
    public function getMetaDescription() {
        return $this->_metaDesc;
    }
    
    /**
     * Set the meta description
     * 
     * @param string $description Item description
     * @return Model_Project_Config_Item
     */
    public function setMetaDescription($description) {
        $this->_metaDesc = trim($description);
        return $this;
    }
    
    /**
     * Get the meta symbol
     * 
     * @return string
     */
    public function getMetaSymbol() {
        return $this->_metaSymbol;
    }
    
    /**
     * Set the meta symbol
     * 
     * @param string $symbol Item symbol
     * @return Model_Project_Config_Item
     */
    public function setMetaSymbol($symbol) {
        $this->_metaSymbol = trim($symbol);
        return $this;
    }
    
    /**
     * Get the meta dependency key; hide this item if the target boolean item is false
     * 
     * @return string
     */
    public function getMetaDepends() {
        return $this->_metaDepends;
    }
    
    /**
     * Set the meta dependency key; hide this item if the target boolean item is false
     * 
     * @param string $booleanItemKey Dependency key
     * @return Model_Project_Config_Item
     */
    public function setMetaDepends($booleanItemKey) {
        $this->_metaDepends = trim($booleanItemKey);
        return $this;
    }
    
    /**
     * Get the meta dependency key addon; hide this item if the target boolean item is false for this addon
     * 
     * @return string
     */
    public function getMetaDependsAddon() {
        return $this->_metaDependsAddon;
    }
    
    /**
     * Set the meta dependency key addon; hide this item if the target boolean item is false for this addon
     * 
     * @param string $addonName Addon name for this dependency
     * @return Model_Project_Config_Item
     */
    public function setMetaDependsAddon($addonName) {
        $this->_metaDependsAddon = trim($addonName);
        return $this;
    }
    
    /**
     * Get the "options clone" key; this item shares the same options with another item
     * 
     * @return string
     */
    public function getMetaOptionsClone() {
        return $this->_metaOptionsClone;
    }
    
    /**
     * Set the "options clone" key; this item shares the same options with another item
     * 
     * @param string $itemKey Parent item options (to clone in project.js)
     * @return Model_Project_Config_Item
     */
    public function setMetaOptionsClone($itemKey) {
        $this->_metaOptionsClone = trim($itemKey);
        return $this;
    }
    
    /**
     * Get whether to use a drop-down picker for this item
     * 
     * @return boolean
     */
    public function getMetaOptionsPicker() {
        return $this->_metaOptionsPicker;
    }
    
    /**
     * Set whether to use a drop-down picker for this item
     * 
     * @param boolean $enabled (optional) Enable the drop-down picker for this item; default <b>true</b>
     * @return Model_Project_Config_Item
     */
    public function setMetaOptionsPicker($enabled = true) {
        $this->_metaOptionsPicker = (boolean) $enabled;
        return $this;
    }
    
    /**
     * Get the meta URL
     * 
     * @return string
     */
    public function getMetaUrl() {
        return $this->_metaUrl;
    }
    
    /**
     * Set the meta URL
     * 
     * @param string $url Item URL
     * @return Model_Project_Config_Item
     */
    public function setMetaUrl($url) {
        // Valid URL
        if (preg_match('%^https?\:\/\/%', $url)) {
            $this->_metaUrl = trim($url);
        }
        return $this;
    }
    
    /**
     * Get the meta refresh action
     * 
     * @return string
     */
    public function getMetaRefresh() {
        return $this->_metaRefresh;
    }
    
    /**
     * Set the meta refresh action; only available for single-value and locally-stored enabled items
     * 
     * @param string $action Method to call in order to automatically populate a field
     * @return Model_Project_Config_Item
     */
    public function setMetaRefresh($action) {
        if (!$this->isList() && !$this->isOnDisk() && !$this->isDisabled()) {
            $this->_metaRefresh = trim($action);
        }
        return $this;
    }
    
    /**
     * Get the item's header
     * 
     * @return string
     */
    public function getMetaHeader() {
        return $this->_metaHeader;
    }
    
    /**
     * Set the item's header; useful for grouping items
     * 
     * @param string $header Item header (user visual aid)
     * @return Model_Project_Config_Item
     */
    public function setMetaHeader($header) {
        $this->_metaHeader = trim($header);
        return $this;
    }
    
    /**
     * Get the available options title/description
     * 
     * @return array <br/>
     * <code>
     * array(<br/>
     *     "itemName" => array(<br/>
     *         "item title",<br/>
     *         "item description",<br/>
     *     ),<br/>
     * )
     * </code>
     */
    public function getMetaOptionsDetails() {
        return $this->_metaOptionsDetails;
    }
    
    /**
     * Set the title/description for the supplied list of options
     * 
     * @param array $optionsDetails Associative array<br/>
     * <code>
     * array(<br/>
     *     "itemName" => array(<br/>
     *         "item title",<br/>
     *         "item description",<br/>
     *     ),<br/>
     * )
     * </code>
     * @return Model_Project_Config_Item
     */
    public function setMetaOptionsDetails($optionsDetails) {
        // Prepare the result
        $result = array();
        
        // Validate the input
        if (is_array($optionsDetails)) {
            foreach ($optionsDetails as $optionName => $optionData) {
                if (is_array($optionData) && 2 == count($optionData)) {
                    $result[$optionName] = $optionData;
                }
            }
        }
        
        // Store the data
        $this->_metaOptionsDetails = $result;
        return $this;
    }
    
    /**
     * Serialize to array<br/>
     * Keys are defined as <b>Model_Project_Config_Item::JSON_*</b>
     * 
     * @return array
     */
    public function toArray() {
        return array(
            self::JSON_TYPE                 => $this->getType(),
            self::JSON_KEY                  => $this->getKey(),
            self::JSON_VALUE                => $this->getValue(),
            self::JSON_IS_LIST              => $this->isList(),
            self::JSON_OPTIONS              => $this->getOptions(),
            self::JSON_OPTIONS_STRICT       => $this->getOptionsStrict(),
            self::JSON_IS_DISABLED          => $this->isDisabled(),
            self::JSON_IS_ON_DISK           => $this->isOnDisk(),
            self::JSON_IS_ON_DISK_BINARY    => $this->isOnDiskBinary(),
            self::JSON_EXTENSION            => $this->getExtension(),
            self::JSON_META_TITLE           => $this->getMetaTitle(),
            self::JSON_META_DESCRIPTION     => $this->getMetaDescription(),
            self::JSON_META_SYMBOL          => $this->getMetaSymbol(),
            self::JSON_META_DEPENDS         => $this->getMetaDepends(),
            self::JSON_META_DEPENDS_ADDON   => $this->getMetaDependsAddon(),
            self::JSON_META_HEADER          => $this->getMetaHeader(),
            self::JSON_META_URL             => $this->getMetaUrl(),
            self::JSON_META_REFRESH         => $this->getMetaRefresh(),
            self::JSON_META_OPTIONS_DETAILS => $this->getMetaOptionsDetails(),
            self::JSON_META_OPTIONS_CLONE   => $this->getMetaOptionsClone(),
            self::JSON_META_OPTIONS_PICKER  => $this->getMetaOptionsPicker(),
        );
    }
    
    /**
     * Get a project item type from key-value associative array
     * 
     * @param string $addonName Add-on name
     * @param array  $data      JSON data
     * @return Model_Project_Config_Item[] Configuration items
     * @throws Exception
     */
    public static function fromKeyValue($addonName, Array $data) {
        // Prepare the result
        $result = array();
        
        // Prepare the flag
        $getOptionsDefined = false;
        
        // Valid add-on definition
        if (file_exists($goPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName . '/' . Addons::GO_FILE_NAME)) {
            require_once $goPath;
            
            /* @var $className Addon */
            $className = 'Addon_' . implode('', array_map(function($item) {return ucfirst(strtolower($item));}, explode('-', basename(dirname($goPath)))));
            
            // Class found
            if (class_exists($className) && method_exists($className, 'getOptions')) {
                if (is_array($addonDefinition = call_user_func(array($className, 'getOptions')))) {
                    // Valid add-on
                    $getOptionsDefined = true;
                    
                    // Go through the details
                    foreach ($addonDefinition as /*@var $allowedConfigItem Model_Project_Config_Item*/ $allowedConfigItem) {
                        // Get the key
                        $key = $allowedConfigItem->getKey();
                        
                        // Variable defined
                        if (isset($data[$key])) {
                            // Set the value
                            $allowedConfigItem->setValue($data[$key]);
                        }
                        
                        // Store the result
                        $result[$key] = $allowedConfigItem;
                    }
                }
                
                // Get the Addon's metadata
                $addonMetaData = Addons::getMeta($addonName);
                
                // Addon icon defined
                $result[self::KEY_ICON] = (new Model_Project_Config_Item_String(
                    self::KEY_ICON, 
                    property_exists($className, 'addonIcon') ? $className::$addonIcon : Addon::$addonIcon)
                );
                
                // Addon icon letter defined
                if (property_exists($className, 'addonIconSymbol')) {
                    $result[self::KEY_ICON]->setMetaSymbol($className::$addonIconSymbol);
                }
                
                // Addon @title defined
                if (isset($addonMetaData[0])) {
                    $result[self::KEY_ICON]->setMetaTitle($addonMetaData[0]);
                }
                
                // Addon @desc defined
                if (isset($addonMetaData[1])) {
                    $result[self::KEY_ICON]->setMetaDescription($addonMetaData[1]);
                }
            }
        }
        
        // Valid number of flavors found
        if (null !== $flavorConfig = Addons_Flavor::getConfigItem($addonName)) {
            // Get the key
            $key = $flavorConfig->getKey();

            // Variable defined
            if (isset($data[$key])) {
                // Set the value
                $flavorConfig->setValue($data[$key]);
            }
                        
            // Add the flavor element
            $result[$key] = $flavorConfig;
        }
        
        // Set custom scripts
        if ($getOptionsDefined) {
            // Inline CSS
            $result[self::KEY_CSS_INLINE] = (new Model_Project_Config_Item_Code(
                self::KEY_CSS_INLINE
            ))
                ->setExtension(Model_Project_Config_Item_Code::EXT_CSS)
                ->setMetaTitle('Inline CSS')
                ->setMetaHeader('UI Tweaks')
                ->setMetaDescription('Adds support for <1.original.getRgb> dynamic color tags');
            
            // Custom CSS
            $result[self::KEY_CSS] = (new Model_Project_Config_Item_Code(
                self::KEY_CSS
            ))->setExtension(Model_Project_Config_Item_Code::EXT_CSS);
            
            // Custom JS
            $result[self::KEY_JS] = (new Model_Project_Config_Item_Code(
                self::KEY_JS
            ))->setExtension(Model_Project_Config_Item_Code::EXT_JS);
        }
        
        // All done
        return $result;
    }
    
    /**
     * Validate item
     * 
     * @throws Exception
     */
    abstract protected function _validateValue($value);

}

/* EOF */