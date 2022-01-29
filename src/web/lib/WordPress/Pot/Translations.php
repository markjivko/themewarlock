<?php
/**
 * Theme Warlock - WordPress_Pot_Translations
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Pot_Translations {

    /**
     * Entries
     * 
     * @var WordPress_Pot_Translations_Entry[]
     */
    public $entries = array();
    
    /**
     * Headers
     * 
     * @var string[]
     */
    public $headers = array();

    /**
     * Plurals
     * 
     * @var int
     */
    protected $_nPlurals = 2;
    
    /**
     * Plural form function
     * 
     * @var callable
     */
    protected $_gettextSelectPluralForm;
    
    /**
     * The gettext implementation of select_plural_form.
     *
     * It lives in this class, because there are more than one descendand, which will use it and
     * they can't share it effectively.
     *
     * @param int $count
     */
    public function gettextSelectPluralForm($count) {
        if (!isset($this->_gettextSelectPluralForm) || is_null($this->_gettextSelectPluralForm)) {
            list($nplurals, $expression) = $this->npluralsAndExpressionFromHeader($this->getHeader(WordPress_Pot::HEADER_PLURAL));
            $this->_nPlurals = $nplurals;
            $this->_gettextSelectPluralForm = $this->makePluralFormFunction($nplurals, $expression);
        }
        
        return call_user_func($this->_gettextSelectPluralForm, $count);
    }

    /**
     * @param string $header
     * @return array
     */
    public function npluralsAndExpressionFromHeader($header) {
        if (preg_match('/^\s*nplurals\s*=\s*(\d+)\s*;\s+plural\s*=\s*(.+)$/', $header, $matches)) {
            $nplurals = (int) $matches[1];
            $expression = trim($this->parenthesizePluralExression($matches[2]));
            return array($nplurals, $expression);
        } 
        
        return array(2, 'n != 1');
    }

    /**
     * Makes a function, which will return the right translation index, according to the
     * plural forms header
     * @param int    $nplurals
     * @param string $expression
     */
    public function makePluralFormFunction($nplurals, $expression) {
        $expression = str_replace('n', '$n', $expression);
        $func_body = "\$index = (int)($expression); return (\$index < $nplurals)? \$index : $nplurals - 1;";
        return create_function('$n', $func_body);
    }

    /**
     * Adds parentheses to the inner parts of ternary operators in
     * plural expressions, because PHP evaluates ternary operators from left to right
     *
     * @param string $expression the expression without parentheses
     * @return string the expression with parentheses added
     */
    public function parenthesizePluralExression($expression) {
        $expression .= ';';
        $res = '';
        $depth = 0;
        for ($i = 0; $i < strlen($expression); ++$i) {
            $char = $expression[$i];
            switch ($char) {
                case '?':
                    $res .= ' ? (';
                    $depth++;
                    break;
                
                case ':':
                    $res .= ') : (';
                    break;
                
                case ';':
                    $res .= str_repeat(')', $depth) . ';';
                    $depth = 0;
                    break;
                
                default:
                    $res .= $char;
            }
        }
        return rtrim($res, ';');
    }

    /**
     * @param string $translation
     * @return array
     */
    public function makeHeaders($translation) {
        $headers = array();
        // sometimes \ns are used instead of real new lines
        $translation = str_replace('\n', "\n", $translation);
        $lines = explode("\n", $translation);
        foreach ($lines as $line) {
            $parts = explode(':', $line, 2);
            if (!isset($parts[1])) {
                continue;
            }
            $headers[trim($parts[0])] = trim($parts[1]);
        }
        return $headers;
    }

    /**
     * @param string $header
     * @param string $value
     */
    public function setHeader($header, $value) {
        $this->headers[$header] = $value;
        if (WordPress_Pot::HEADER_PLURAL == $header) {
            list( $nplurals, $expression ) = $this->npluralsAndExpressionFromHeader($this->getHeader(WordPress_Pot::HEADER_PLURAL));
            $this->_nPlurals = $nplurals;
            $this->_gettextSelectPluralForm = $this->makePluralFormFunction($nplurals, $expression);
        }
    }

    /**
     * Add entry to the PO structure
     *
     * @param array|WordPress_Pot_Translations_Entry &$entry
     * @return bool true on success, false if the entry doesn't have a key
     */
    public function addEntry($entry) {
        if (is_array($entry)) {
            $entry = new WordPress_Pot_Translations_Entry($entry);
        }
        $key = $entry->key();
        if (false === $key) {
            return false;
        }
        $this->entries[$key] = &$entry;
        return true;
    }

    /**
     * @param array|WordPress_Pot_Translations_Entry $entry
     * @return bool
     */
    public function addEntryOrMerge($entry) {
        if (is_array($entry)) {
            $entry = new WordPress_Pot_Translations_Entry($entry);
        }
        $key = $entry->key();
        if (false === $key) {
            return false;
        }
        if (isset($this->entries[$key])) {
            $this->entries[$key]->mergeWith($entry);
        } else {
            $this->entries[$key] = &$entry;
        }
        return true;
    }

    /**
     * @param array $headers
     */
    public function setHeaders($headers) {
        foreach ($headers as $header => $value) {
            $this->setHeader($header, $value);
        }
    }

    /**
     * @param string $header
     */
    public function getHeader($header) {
        return isset($this->headers[$header]) ? $this->headers[$header] : false;
    }

    /**
     * @param WordPress_Pot_Translations_Entry $entry
     */
    public function translateEntry(&$entry) {
        $key = $entry->key();
        return isset($this->entries[$key]) ? $this->entries[$key] : false;
    }

    /**
     * @param string $singular
     * @param string $context
     * @return string
     */
    public function translate($singular, $context = null) {
        // Prepare the entry
        $entry = new WordPress_Pot_Translations_Entry(
            array(
                WordPress_Pot_Translations_Entry::ARG_SINGULAR => $singular, 
                WordPress_Pot_Translations_Entry::ARG_CONTEXT  => $context
            )
        );
        $translated = $this->translateEntry($entry);
        return ($translated && !empty($translated->translations)) ? $translated->translations[0] : $singular;
    }

    /**
     * Given the number of items, returns the 0-based index of the plural form to use
     *
     * Here, in the base Translations class, the common logic for English is implemented:
     * 	0 if there is one element, 1 otherwise
     *
     * This function should be overrided by the sub-classes. For example MO/PO can derive the logic
     * from their headers.
     *
     * @param integer $count number of items
     */
    public function selectPluralForm($count) {
        return 1 == $count ? 0 : 1;
    }

    /**
     * @return int
     */
    public function getPluralFormsCount() {
        return 2;
    }

    /**
     * @param string $singular
     * @param string $plural
     * @param int    $count
     * @param string $context
     */
    public function translatePlural($singular, $plural, $count, $context = null) {
        // Prepare the entry
        $entry = new WordPress_Pot_Translations_Entry(
            array(
                WordPress_Pot_Translations_Entry::ARG_SINGULAR => $singular, 
                WordPress_Pot_Translations_Entry::ARG_PLURAL   => $plural, 
                WordPress_Pot_Translations_Entry::ARG_CONTEXT  => $context
            )
        );
        
        // Translate it
        $translated = $this->translateEntry($entry);
        $index = $this->selectPluralForm($count);
        $totalPluralForms = $this->getPluralFormsCount();
        
        // Get the translated version
        if ($translated && 0 <= $index && $index < $totalPluralForms && is_array($translated->translations) && isset($translated->translations[$index])) {
            return $translated->translations[$index];
        }
        
        // Revert to the default
        return 1 == $count ? $singular : $plural;
    }

    /**
     * Merge $other in the current object.
     *
     * @param Object &$other Another Translation object, whose translations will be merged in this one
     * @return void
     * */
    public function mergeWith($other) {
        foreach ($other->entries as $entry) {
            $this->entries[$entry->key()] = $entry;
        }
    }

    /**
     * Merge translation objects
     * 
     * @param WordPress_Pot_Translations $other   Other Translation object
     * @param boolean                    $replace Whether or not to replace the current Entry's meta data with the other object's
     */
    public function mergeOriginalsWith($other, $replace = false) {
        foreach ($other->entries as $entry) {
            if (!isset($this->entries[$entry->key()])) {
                $this->entries[$entry->key()] = $entry;
            } else {
                $this->entries[$entry->key()]->mergeWith($entry, $replace);
            }
        }
        
        // Make other replacements
        if ($replace) {
            // Prepare the new headers
            $newHeaders = $other->headers;
            
            // Prepare the list of keys to keep
            $keepHeadersList = array(
                WordPress_Pot::HEADER_PLURAL,
                WordPress_Pot::HEADER_LANGUAGE,
            );
            
            // Add/replace the necessary keys
            foreach ($keepHeadersList as $headerKey) {
                if (isset($this->headers[$headerKey])) {
                    $newHeaders[$headerKey] = $this->headers[$headerKey];
                }
            }
            
            // Replace the headers
            $this->headers = $newHeaders;
        }
    }
    
    /**
     * Update entries with cached translations
     */
    public function updateEntriesFromCache() {
        // Language set
        if (isset($this->headers[WordPress_Pot::HEADER_LANGUAGE]) && strlen($this->headers[WordPress_Pot::HEADER_LANGUAGE])) {
            // Set the headers and entries
            WordPress_Pot_Translations_Cache::getInstance($this->headers[WordPress_Pot::HEADER_LANGUAGE])
                ->updateHeaders($this->headers)
                ->updateEntries($this->entries);
        }
    }
}

/* EOF */