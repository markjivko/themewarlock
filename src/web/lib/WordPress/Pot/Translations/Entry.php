<?php

/**
 * Theme Warlock - WordPress_Pot_Translations_Entry
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class WordPress_Pot_Translations_Entry {

    const ARG_STRING       = 'string';
    const ARG_SINGULAR     = 'singular';
    const ARG_PLURAL       = 'plural';
    const ARG_TRANSLATIONS = 'translations';
    const ARG_CONTEXT      = 'context';
    
    const FLAG_PHP_FORMAT = 'php-format';
    const FLAG_FUZZY      = 'fuzzy';
    
    /**
     * RegEx for sprintf() placeholders
     */
    const REGEX_SPRINTF_PLACEHOLDERS = '/(?<!\%)\%(?:\d+\$)?(?:[\'\+\-\d]+)?[bcdeufosx]/ims';
    
    /**
     * Whether the entry contains a string and its plural form, default is false
     *
     * @var boolean
     */
    public $isPlural = false;
    public $context = null;
    public $singular = null;
    public $plural = null;
    public $translations = array();
    public $translatorComments = '';
    public $extractedComments = '';
    public $textDomain = '';
    public $references = array();
    public $flags = array();

    /**
     * @param array $args associative array, support following keys:
     * 	- singular (string) -- the string to translate, if omitted and empty entry will be created
     * 	- plural (string) -- the plural form of the string, setting this will set {@link $is_plural} to true
     * 	- translations (array) -- translations of the string and possibly -- its plural forms
     * 	- context (string) -- a string differentiating two equal strings used in different contexts
     * 	- translator_comments (string) -- comments left by translators
     * 	- extracted_comments (string) -- comments left by developers
     * 	- references (array) -- places in the code this strings is used, in relative_to_root_path/file.php:linenum form
     * 	- flags (array) -- flags like php-format
     */
    public function __construct($args = array()) {
        // if no singular -- empty object
        if (!isset($args[WordPress_Pot_Translations_Entry::ARG_SINGULAR])) {
            return;
        }
        
        // get member variable values from args hash
        foreach ($args as $varname => $value) {
            if (property_exists($this, $varname)) {
                $this->$varname = $value;
            }
        }
        
        if (isset($args[WordPress_Pot_Translations_Entry::ARG_PLURAL]) && $args[WordPress_Pot_Translations_Entry::ARG_PLURAL]) {
            $this->isPlural = true;
        }
        
        if (!is_array($this->translations)) {
            $this->translations = array();
        }
        
        if (!is_array($this->references)) {
            $this->references = array();
        }
        
        if (!is_array($this->flags)) {
            $this->flags = array();
        }
        
    }

    /**
     * Get the string representation of this entry
     * 
     * @return string
     */
    public function __toString() {
        return $this->singular . ' (' . ($this->isPlural ? $this->plural : 'singular') . ')';
    }
    
    /**
     * Add a flag
     * 
     * @param string $flag
     */
    public function flagAdd($flag) {
        // Append the flag
        if (!in_array($flag, $this->flags)) {
            $this->flags[] = $flag;
        }
    }
    
    /**
     * Remove a flag
     * 
     * @param string $flag
     */
    public function flagRemove($flag) {
        // Remove the flag
        $this->flags = array_filter(
            $this->flags, 
            function($item) use ($flag) {
                return $item != $flag;
            }
        );
    }
    
    /**
     * Set the text domain for this entry
     * 
     * @param stirng $textDomain Text domain
     */
    public function setTextDomain($textDomain) {
        $this->textDomain = trim($textDomain);
    }
    
    /**
     * Generates a unique key for this entry
     *
     * @return string|bool the key or false if the entry is empty
     */
    public function key() {
        if (null === $this->singular || '' === $this->singular) {
            return false;
        }

        // Prepend context and EOT, like in MO files
        $key = !$this->context ? $this->singular : $this->context . chr(4) . $this->singular;
        
        // Standardize on \n line endings
        $key = str_replace(array("\r\n", "\r"), "\n", $key);

        return $key;
    }

    /**
     * @param WordPress_Pot_Translations_Entry $other
     */
    public function mergeWith($other, $replace = false) {
        $this->flags = $replace ? $other->flags : array_unique(array_merge($this->flags, $other->flags));
        $this->references = $replace ? $other->references : array_unique(array_merge($this->references, $other->references));
        
        if ($replace) {
            $this->extractedComments = $other->extractedComments;
        } else {
            if ($this->extractedComments != $other->extractedComments) {
                $this->extractedComments .= $other->extractedComments;
            }
        }
        
        // Always replace the textdomain
        $this->textDomain = $other->textDomain;
        
        // Trim the value
        $this->extractedComments = trim($this->extractedComments);
    }

}

/* EOF */