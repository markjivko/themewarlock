<?php
/**
 * Theme Warlock - Config_Items_Descriptor_Entry
 * 
 * @title      Entry for a Config_Items_Descriptor
 * @desc       Store an entry for each property of Config_Items
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Config_Items_Descriptor_Entry {

    // Flags
    const FLAG_VAR     = 'var';
    const FLAG_ALLOWED = 'allowed';
    
    // Data types
    const TYPE_INT     = 'int';
    const TYPE_INTEGER = 'integer';
    const TYPE_FLOAT   = 'float';
    const TYPE_DOUBLE  = 'double';
    const TYPE_BOOL    = 'bool';
    const TYPE_BOOLEAN = 'boolean';
    const TYPE_SIZE    = 'size';
    const TYPE_STRING  = 'string';
    
    // Known suffixes
    const SUFFIX_PATH  = 'path';
    const SUFFIX_EMAIL = 'email';
    const SUFFIX_URL   = 'url';
    
    /**
     * Entry name
     * 
     * @var string
     */
    protected $_name = null;
    
    /**
     * Entry value
     * 
     * @var string
     */
    protected $_value = null;
    
    /**
     * Entry description
     *
     * @var string
     */
    protected $_description = '';
    
    /**
     * Entry type
     * 
     * @var string
     */
    protected $_type = self::TYPE_STRING;
    
    /**
     * Allowed values (options)
     *
     * @var string[]|null
     */
    protected $_options = null;
    
    /**
     * Property suffix
     * 
     * @var string|null
     */
    protected $_suffix = null;
    
    /**
     * Allowed config entry flags
     * 
     * @var string[]
     */
    protected static $_allowedFlags = array();
    
    /**
     * Allowed data types
     * 
     * @var string[]
     */
    protected static $_allowedTypes = array();
    
    /**
     * Allowed suffixes
     * 
     * @var string[]
     */
    protected static $_allowedSuffixes = array();
    
    /**
     * Config Item Descriptor Entry
     * 
     * @param ReflectionProperty $item
     */
    public function __construct(ReflectionProperty $item) {
        // Prepare the allowed types/flags/suffixes
        if (!count(self::$_allowedTypes)) {
            // Prepare the reflection
            $reflection = new ReflectionClass($this);
            
            // Go through the constants
            foreach ($reflection->getConstants() as $constantName => $constantValue) {
                // Prepare the constant prefix
                $constantPrefix = strtolower(preg_replace('%^(\w+?)_.*?$%i', '${1}', $constantName));
                
                // Allocate
                switch ($constantPrefix) {
                    case 'flag':
                        self::$_allowedFlags[] = $constantValue;
                        break;
                    
                    case 'suffix':
                        self::$_allowedSuffixes[] = $constantValue;
                        break;
                    
                    case 'type':
                        self::$_allowedTypes[] = $constantValue;
                        break;
                }
            }
        }
        
        // Prepare the name
        $this->_name = $item->getName();
        
        // Get the configuration value
        $config = Config::get(false);
        
        // Entry defined
        if (isset($config[$this->_name])) {
            $this->_value = $config[$this->_name];
        }
        
        // Get the comment
        $comment = trim(preg_replace('%^\s*(?:\/\*\*|\*\/|\*)*%m', '', $item->getDocComment()));
        
        // Prepare the flags
        $flags = array();
        
        // Parse the entries and description
        $this->_description = trim(preg_replace_callback(
            '%^\s*\@(\w+)(.*?)$%ms', 
            function($item) use (&$flags) {
                if (in_array($item[1], self::$_allowedFlags)) {
                    $flags[$item[1]] = trim($item[2]);
                }
                
                // Remove the entry
                return '';
            }, 
            $comment
        ));
        
        // Store the flags
        foreach ($flags as $flagType => $flagValue) {
            switch ($flagType) {
                case self::FLAG_VAR:
                    // Validate the type
                    if (!in_array($flagValue, self::$_allowedTypes)) {
                        $flagValue = self::TYPE_STRING;
                    }
                    
                    // Save the type
                    $this->_type = $flagValue;
                    break;
                
                case self::FLAG_ALLOWED:
                    // Store allowed value
                    $this->_options = array_values(
                        array_filter(
                            array_map(
                                'trim', 
                                explode(',', $flagValue)
                            )
                        )
                    );
                    break;
            }
        }
        
        // Prepare the Suffix
        $suffix = strtolower(preg_replace('%^.*?([A-Z][a-z]+)$%', '${1}', $this->getName()));
        
        // Validate it
        if (in_array($suffix, self::$_allowedSuffixes)) {
            $this->_suffix = $suffix;
        }
    }
    
    /**
     * Get the entry name
     * 
     * @return string
     */
    public function getName() {
        return $this->_name;
    }
    
    /**
     * Get the entry description
     * 
     * @return string
     */
    public function getDescription() {
        return $this->_description;
    }
    
    /**
     * Get the entry value
     * 
     * @return string
     */
    public function getValue() {
        return $this->_value;
    }
    
    /**
     * Set the entry value
     * 
     * @param string $value
     * @throws Exception
     */
    public function setValue($value) {
        // Validate by type
        switch ($this->getType()) {
            case self::TYPE_INT:
            case self::TYPE_INTEGER:
                if (!preg_match('%^\d+$%', $value)) {
                    throw new Exception('Invalid integer');
                }
                break;
                
            case self::TYPE_FLOAT:
            case self::TYPE_DOUBLE:
                if (!preg_match('%^(?:\d+\.\d+|\d+)$%', $value)) {
                    throw new Exception('Invalid float number');
                }
                break;
                
            case self::TYPE_BOOL:
            case self::TYPE_BOOLEAN:
                if (!preg_match('%^(?:yes|no)$%', $value)) {
                    throw new Exception('Invalid boolean, "yes" or "no" expected');
                }
                break;
                
            case self::TYPE_SIZE:
                if (!preg_match('%^[1-9]\d*[KMGTPEZY]?$%', $value)) {
                    throw new Exception('Invalid size, "10K" format expected. K, M, G, T, P, E, Z, Y allowed');
                }
                break;
                
            case self::TYPE_STRING:
                // No line breaks, no extra spaces
                $value = trim(preg_replace('%[\r\n]+%', '', $value));
                break;
        }
        
        // Validate options
        if (null !== $this->getOptions()) {
            if (!in_array($value, $this->getOptions())) {
                throw new Exception('Invalid value, expected one of: "' . implode('", "', $this->getOptions()) . '"');
            }
        }
        
        // Validate by suffix
        if (null !== $this->getSuffix()) {
            switch ($this->getSuffix()) {
                case self::SUFFIX_EMAIL:
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        throw new Exception('Invalid E-mail address');
                    }
                    break;
                    
                case self::SUFFIX_PATH:
                    if (!preg_match('%^\/(?:[\w]+\/)*(?:\w+|\w+\.\w+)*$%', $value)) {
                        throw new Exception('Invalid Path');
                    }
                    break;
                
                case self::SUFFIX_URL:
                    if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        throw new Exception('Invalid URL');
                    }
                    break;
            }
        }

        // Validate value changed
        if ($value === $this->_value) {
            Log::debug('Config value not changed for entry "' . $this->getName() . '", no need to rewrite');
            return false;
        }
        
        // WordPress version change
        if ('wpVersion' === $this->getName()) {
            // Validate the version
            if (!preg_match('%^(?:\d+\.)+\w+$%', $value)) {
                throw new Exception('Invalid version value "' . $value . '"');
            }
            
            // Prepare the download location
            if (!is_dir($tempPath = ROOT . '/web/temp/repos')) {
                Folder::create($tempPath, 0777, true);
            }
            
            // Download the file
            if (!is_file("{$tempPath}/wordpress-{$value}.zip")) {
                shell_exec(
                    'wget'
                        . ' ' . escapeshellarg("https://wordpress.org/wordpress-{$value}.zip")
                        . ' -P ' . escapeshellarg($tempPath)
                );
            }
            
            // Initialize the repo
            if (!is_dir($repoPath = "$tempPath/$value")) {
                shell_exec(
                    'unzip -q'
                        . ' ' . escapeshellarg("{$tempPath}/wordpress-{$value}.zip")
                        . ' -d ' . escapeshellarg($repoPath)
                );
                        
                // Move from "/wordpress" to root
                Folder::copyContents("{$tempPath}/{$value}/wordpress", $repoPath);
                Folder::clean("{$tempPath}/{$value}/wordpress", true);
            }
            
            // Add missing files
            Folder::copyContents(ROOT . '/web/resources/wordpress/repos', $repoPath);
            
            // Prepare replacements
            $replacementsFlag = 'TW-FIX';
            $replacements = [
                'filter_iframe_security_headers' => '$headers',
                'send_frame_options_header'      => '',
                'wp_version_check'               => ''
            ];
            
            // Go through the PHP files, searching for the flags
            foreach(Folder::findFiles('%.*\.php$%i', $repoPath) as $filePath) {
                $flagAdded = false;
                $fileContents = file_get_contents($filePath);
                
                foreach ($replacements as $funcName => $funcReturn) {
                    $fileContents = preg_replace_callback(
                        '%(\bfunction\s+' . preg_quote($funcName) . '\s*\(.*?\)\s*?)\{([^\}]+)\}%ims', 
                        function($item) use ($funcReturn, $replacementsFlag, &$flagAdded) {
                            if (!preg_match('%\/\/\s*@' . preg_quote($replacementsFlag) . '%i', $item[2])) {
                                $flagAdded = true;
                                return $item[1] . '{'
                                    . PHP_EOL . str_repeat(' ', 4) . '// @' . $replacementsFlag
                                    . PHP_EOL . str_repeat(' ', 4) . 'return' . (strlen($funcReturn) ? (' ' . $funcReturn) : '') . ';'
                                    . PHP_EOL . $item[2] 
                                . '}';
                            }
                        }, 
                        $fileContents
                    );
                }
                if ($flagAdded) {
                    file_put_contents($filePath, $fileContents);
                }
            }
            
            // Re-initialize the sandbox
            Folder::clean(Config::getWpPath(true));
            Folder::copyContents($repoPath, Config::getWpPath(true));
        }
        
        // Store it internally
        $this->_value = $value;

        // Write to disk
        $configIniPath = ROOT . '/web/config/config.ini';
        
        // Valid file
        if (is_file($configIniPath)) {
            // Get the file contents
            $configIniContents = file_get_contents($configIniPath);
            
            // Escape the value
            $valueEscaped = $this->getValue();
            if (self::TYPE_STRING === $this->getType()) {
                $valueEscaped = is_numeric($value) ? $value : '"' . $value . '"';
            }
            
            // Replace the value
            $configIniContentsModified = preg_replace(
                '%^\s*' . $this->getName() . '\s*\=\s*.*?$%m', 
                $this->getName() . ' = ' . $valueEscaped, 
                $configIniContents
            );
            
            // Needs a rewrite
            if ($configIniContentsModified !== $configIniContents) {
                file_put_contents($configIniPath, $configIniContentsModified);
                return true;
            }
        }
        
        // Could not save
        Log::warning('Config.ini not found, could not save entry "' . $this->getName() . '"!');
        return false;
    }
    
    /**
     * Get the entry type
     * 
     * @return string
     */
    public function getType() {
        return $this->_type;
    }
    
    /**
     * Get the entry options
     * 
     * @return string[]|null List of allowed values or Null if not restricted
     */
    public function getOptions() {
        return $this->_options;
    }
    
    /**
     * Get the entry suffix
     * 
     * @return string|null
     */
    public function getSuffix() {
        return $this->_suffix;
    }
}

/*EOF*/