<?php
/**
 * Theme Warlock - WordPress_Pot_Translations_Cache
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Pot_Translations_Cache {

    /**
     * Languages
     */
    const LANG_DE = 'de_DE';
    const LANG_ES = 'es_ES';
    const LANG_FR = 'fr_FR';
    const LANG_IT = 'it_IT';
    const LANG_PT = 'pt_BR';
    const LANG_RO = 'ro_RO';
    const LANG_RU = 'ru_RU';
    
    /**
     * Language codes and names in English and native
     */
    const LANGUAGES_VERBOSE = array(
        self::LANG_DE => array("German", "Deutsch"),
        self::LANG_ES => array("Spanish", "Español"),
        self::LANG_FR => array("French", "Français"),
        self::LANG_IT => array("Italian", "Italiano"),
        self::LANG_PT => array("Portuguese", "Português"),
        self::LANG_RO => array("Romanian", "Română"),
        self::LANG_RU => array("Russian", "Русский"),
    );
    
    /**
     * Array of translations
     * 
     * @var WordPress_Pot_Translations_Po
     */
    protected $_translations = null;
    
    /**
     * Cache file path
     * 
     * @var string
     */
    protected $_cacheFilePath = null;
    
    /**
     * List of allowed caching languages
     * 
     * @var string[]
     */
    protected static $_languages = array();
    
    /**
     * Current language
     * 
     * @var string
     */
    protected $_language = null;
    
    /**
     * Singleton instances
     * 
     * @var WordPress_Pot_Translations_Cache[] 
     */
    protected static $_instances = array();
    
    /**
     * Singleton factory
     * 
     * @throws Exception
     */
    public static function getInstance($language) {
        if (!isset(self::$_instances[$language])) {
            self::$_instances[$language] = new self($language);
        }
        return self::$_instances[$language];
    }
    
    /**
     * Validate all translations<ul>
     * <li>All languages have the same entries</li>
     * <li>All analogous entries have the same plural definition</li>
     * <li>All analogous entries have the same sprintf() arguments (both singular and plural forms, when available)</li>
     * </ul>
     * 
     * @param string $referenceLanguage Reference language
     * @return boolean|array True on success, An associative array of {language=>[errorsStrings,...]} on error
     * @throws Exception
     */
    public static function validateAll($referenceLanguage = self::LANG_RO) {
        // Validate the reference language
        if (!in_array($referenceLanguage, self::getLanguages())) {
            throw new Exception('Invalid reference language');
        }
        
        // Prepare the language cache instances
        $instances = array();
        foreach (self::getLanguages() as $language) {
            if ($referenceLanguage == $language) {
                continue;
            }
            $instances[$language] = self::getInstance($language);
        }
        
        // Prepare the errors
        $errors = array();
        
        // Prepare the reference entries
        $referenceEntries = self::getInstance($referenceLanguage)->getEntries();
        
        // Find the missing keys from the reference language
        $missingKeys = array();
        foreach ($instances as $language => /*@var $cacheInstance WordPress_Pot_Translations_Cache*/ $cacheInstance) {
            foreach (array_diff_assoc($cacheInstance->getEntries(), $referenceEntries) as /*@var $missingEntry WordPress_Pot_Translations_Entry*/ $missingEntry) {
                if (!isset($missingKeys[$missingEntry->singular])) {
                    $missingKeys[$missingEntry->singular] = array();
                }
                $missingKeys[$missingEntry->singular][] = $language;
            }
        }
        
        // Store the missing keys in errors
        foreach ($missingKeys as $missingEntrySingular => $languages) {
            if (!isset($errors[$referenceLanguage])) {
                $errors[$referenceLanguage] = array();
            }
            $errors[$referenceLanguage][] = 'Missing entry "' . $missingEntrySingular . '" as defined in ' . implode(', ' , $languages);
        }
        
        // Validate all entries from the source language
        foreach ($referenceEntries as $entry) {
            // Validate the cache translations
            foreach ($entry->translations as $translation) {
                if (strlen($translation)) {
                    try {
                        $cacheInstance->_translationValidate($entry->singular, $translation);
                    } catch (Exception $exc) {
                        if (!isset($errors[$referenceLanguage])) {
                            $errors[$referenceLanguage] = array();
                        }
                        $errors[$referenceLanguage][] = $exc->getMessage();
                    }
                }
            }
                    
            // Go through all other languages
            foreach ($instances as $language => /*@var $cacheInstance WordPress_Pot_Translations_Cache*/ $cacheInstance) {
                // Get the entries
                $cacheEntries = $cacheInstance->getEntries();
                
                try {
                    // Not set
                    if (!isset($cacheEntries[$entry->key()])) {
                        throw new Exception('Could not find entry "' . $entry->singular . '"');
                    }

                    // Get the other language's entry
                    $cacheEntry = $cacheEntries[$entry->key()];

                    // Validate the sprintf() arguments for the singular form
                    $cacheInstance->_translationValidate($entry->singular, $cacheEntry->singular);

                    // Validate the cache translations
                    foreach ($cacheEntry->translations as $translation) {
                        if (strlen($translation)) {
                            $cacheInstance->_translationValidate($entry->singular, $translation);
                        }
                    }

                    // Plural form validation
                    if ($entry->isPlural && !$cacheEntry->isPlural) {
                        throw new Exception('Entry "' . $entry->singular . '" should have a plural form');
                    }
                    if (!$entry->isPlural && $cacheEntry->isPlural) {
                        throw new Exception('Entry "' . $entry->singular . '" should not have a plural form');
                    }

                    // Plural sprintf() arguments validation
                    if ($entry->isPlural) {
                        $cacheInstance->_translationValidate($entry->singular, $entry->plural);
                        $cacheInstance->_translationValidate($entry->singular, $cacheEntry->plural);
                    }
                } catch (Exception $exc) {
                    if (!isset($errors[$language])) {
                        $errors[$language] = array();
                    }
                    $errors[$language][] = $exc->getMessage();
                }
            }
        }
        
        // Display the errors
        if (count($errors)) {
            return $errors;
        }
        
        // All went well
        return true;
    }
    
    /**
     * Get the list of allowed (available) languages
     */
    public static function getLanguages() {
        // Prepare the list once
        if (!count(self::$_languages)) {
            // Prepare a reflection
            $iterator = new ReflectionClass(__CLASS__);

            // Get the languages
            foreach ($iterator->getConstants() as $constantName => $constantValue) {
                if (preg_match('%^LANG_%', $constantName)) {
                    self::$_languages[] = $constantValue;
                }
            }
        }
        
        // All done
        return self::$_languages;
    }
    
    /**
     * Get the languages as langaugeCode => languageName
     * 
     * @return array
     */
    public static function getLanguagesVerbose($includeOriginal = false) {
        // Prepare the result
        $result = array();
        
        // Go through the values
        foreach (self::LANGUAGES_VERBOSE as $languageCode => $languageData) {
            list($langNameEnglish, $langNameNative) = $languageData;
            $result[$languageCode] = $langNameEnglish . ($includeOriginal ? ' (' . $langNameNative . ')' : '');
        }
        
        // All done
        return $result;
    }
    
    /**
     * Entry cache
     * 
     * @param string $language Cached language
     * @throws Exception
     */
    public function __construct($language) {
        // Store the current language
        $this->_language = $language;
        
        // Prepare the temporary folder
        if (!is_dir($cacheDir = ROOT . '/web/temp/translations-cache')) {
            Folder::create($cacheDir, 0777, true);
        }
        
        // Not a valid language
        if (!in_array($this->_language, self::getLanguages())) {
            throw new Exception('Invalid caching language: ' . $this->_language);
        }
        
        // Load the Portable Object
        $this->_translations = (new WordPress_Pot_Translations_Po());
        
        // Store the cache file
        if (!is_file($this->_cacheFilePath = $cacheDir . '/' . $this->_language . '.po')) {
            // Set the default headers
            $this->_translations->setHeaders(array(
                WordPress_Pot::HEADER_MIME_VERSION              => '1.0',
                WordPress_Pot::HEADER_CONTENT_TYPE              => 'text/plain; charset=UTF-8',
                WordPress_Pot::HEADER_CONTENT_TRANSFER_ENCODING => '8bit',
                WordPress_Pot::HEADER_X_GENERATOR               => Config::get()->authorName,
            ));
            
            // Store the file
            $this->_translations->exportToFile($this->_cacheFilePath);
        } else {
            // Import from file
            $this->_translations->importFromFile($this->_cacheFilePath);
        }
    }
    
    /**
     * Set the new headers; only overrides the following header keys:<ul>
     * <li>WordPress_Pot::HEADER_PLURAL</li>
     * <li>WordPress_Pot::HEADER_LANGUAGE</li>
     * </ul>
     * 
     * @param array $headers
     * @return WordPress_Pot_Translations_Cache
     */
    public function updateHeaders($headers) {
        // Not a valid language
        if (null === $this->_translations) {
            return $this;
        }
        
        // Prepare the new headers
        $newHeaders = $this->_translations->headers;

        // Prepare the list of keys to keep
        $keepHeadersList = array(
            WordPress_Pot::HEADER_PLURAL,
            WordPress_Pot::HEADER_LANGUAGE,
        );

        // Save flag
        $saveFlag = false;
        
        // Add/replace the necessary keys
        foreach ($keepHeadersList as $headerKey) {
            if (isset($headers[$headerKey])) {
                // Need to save the .PO file
                if (!isset($newHeaders[$headerKey]) || $newHeaders[$headerKey] != $headers[$headerKey]) {
                    $saveFlag = true;
                }
            
                // Store the new header
                $newHeaders[$headerKey] = $headers[$headerKey];
            }
        }

        // Replace the headers
        $this->_translations->headers = $newHeaders;
        
        // Save to the cached file
        $saveFlag && $this->_save();
        
        // All done
        return $this;
    }
    
    /**
     * Perform a translation of all empty strings using Google Translate
     * 
     * @param boolean $showInTaskBar (optional) Whether or not to send a 
     * TaskbarNotifier message when the translation begins; default <b>false</b>
     * @return WordPress_Pot_Translations_Cache
     * @throws Exception
     */
    public function googleTranslate($showInTaskBar = false) {
        // Get the 2-character language
        $language = preg_replace('%_\w+$%i', '', $this->_language);

        // Get the needed translations
        $strings = array();

        // Get the plurals count
        $pluralsCount = $this->getPluralsCount();
        
        // Go through the items
        $entries = $this->getEntries();
        foreach ($entries as /*@var WordPress_Pot_Translations_Entry $entry*/ $entry) {
            if ($entry->isPlural) {
                for ($translationKey = 0; $translationKey < $pluralsCount; $translationKey++) {
                    if (!isset($entry->translations[$translationKey]) || !strlen($entry->translations[$translationKey])) {
                        $strings[] = $translationKey == 0 ? $entry->singular : $entry->plural;
                    }
                }
            } else {
                if (!isset($entry->translations[0]) || !strlen($entry->translations[0])) {
                    $strings[] = $entry->singular;
                }
            }
        }

        do {
            // No new tasks
            if (!count($strings)) {
                break;
            }

            // Log the event
            $logMessage = 'Google Translate - ' . $this->_language . ' - Automatic translation of ' . count($strings) . ' string' . (count($strings) == 1 ? '' : 's');
            $showInTaskBar ? TaskbarNotifier::sendMessage('Translations - ' . $this->_language, $logMessage) : (Log::check(Log::LEVEL_INFO) && Log::info($logMessage));

            // Translate
            $stringsTranslated = Translation::getBatch($strings, Translation::LANG_EN, $language);

            // Set the translations back
            foreach ($entries as /*@var WordPress_Pot_Translations_Entry $entry*/ $entry) {
                if ($entry->isPlural) {
                    for ($translationKey = 0; $translationKey < $pluralsCount; $translationKey++) {
                        if (!isset($entry->translations[$translationKey]) || !strlen($entry->translations[$translationKey])) {
                            // Update the translation
                            try {
                                $this->setEntryTranslation(
                                    $entry->key(), 
                                    $translationKey, 
                                    array_shift($stringsTranslated),
                                    true
                                );
                            } catch (Exception $exc) {
                                // Log the event
                                $showInTaskBar && TaskbarNotifier::sendMessage(
                                    'Translations - ' . $language, 
                                    $exc->getMessage(), 
                                    TaskbarNotifier::TYPE_ERROR
                                );

                                // Log this for future reference
                                Log::check(Log::LEVEL_ERROR) && Log::error($exc->getMessage(), $exc->getFile(), $exc->getLine());
                            }
                        }
                    }
                } else {
                    if (!isset($entry->translations[0]) || !strlen($entry->translations[0])) {
                        // Update the translation
                        try {
                            $this->setEntryTranslation(
                                $entry->key(), 
                                0, 
                                array_shift($stringsTranslated),
                                true
                            );
                        } catch (Exception $exc) {
                            // Log the event
                            $showInTaskBar && TaskbarNotifier::sendMessage(
                                'Translations - ' . $language, 
                                $exc->getMessage(), 
                                TaskbarNotifier::TYPE_ERROR
                            );

                            // Log this for future reference
                            Log::check(Log::LEVEL_ERROR) && Log::error($exc->getMessage(), $exc->getFile(), $exc->getLine());
                        }
                    }
                }
            }
        } while (false);
        
        // All done
        return $this;
    }
    
    /**
     * Set the a specific translation (by index) for an item
     * 
     * @param string  $entryKey         Entry key
     * @param int     $translationIndex Translation index
     * @param string  $translationValue Translation value
     * @param boolean $autoTranslation  The string was automatically translated
     * @return string Translation value - sanitized
     * @throws Exception
     */
    public function setEntryTranslation($entryKey, $translationIndex, $translationValue, $autoTranslation = false) {
        // Invalid translation index
        if ($translationIndex < 0 || $translationIndex >= $this->getPluralsCount()) {
            throw new Exception('Invalid translation index');
        }
        
        // Sanitize the translation
        $translationValue = $this->_translationSanitize($this->_getEntry($entryKey)->singular, $translationValue);
        
        // Validate the sprintf() placeholders
        $this->_translationValidate($this->_getEntry($entryKey)->singular, $translationValue);
        
        // Store the translation
        $this->_getEntry($entryKey)->translations[$translationIndex] = $translationValue;
        
        // Set the auto-translation flag
        if ($autoTranslation) {
            // Add the flag
            $this->_getEntry($entryKey)->flagAdd(WordPress_Pot_Translations_Entry::FLAG_FUZZY);
        } else {
            // Remove the flag
            $this->_getEntry($entryKey)->flagRemove(WordPress_Pot_Translations_Entry::FLAG_FUZZY);
        }
        
        // Save
        $this->_save();
        
        // All done
        return $translationValue;
    }
    
    /**
     * Sanitize the translation; trim, remove extra spaces, add/remove punctuation mark
     * 
     * @param string $singularForm     Singular form
     * @param string $translationValue Translation
     * @return sring
     */
    protected function _translationSanitize($singularForm, $translationValue) {
        // Remove spaces
        $translationValue = trim(preg_replace('%\s+%', ' ' , strip_tags($translationValue)));
        
        // Prepare the punctuation mark regex
        $punctuationRegEx = '%(\.{3}|[\;\:\.\?\!])$%';
        
        // The sentence must end with a punctuation mark
        if (preg_match($punctuationRegEx, $singularForm, $punctuationMatches)) {
            if (!preg_match($punctuationRegEx, $translationValue)) {
                if (strlen($translationValue)) {
                    $translationValue .= $punctuationMatches[1];
                }
            }
        } else {
            // The sencence must end with no punctuation mark
            $translationValue = preg_replace($punctuationRegEx, '', $translationValue);
        }
        
        // All done
        return $translationValue;
    }
    
    /**
     * Validate the sprintf() placeholders
     * 
     * @param string $singularForm     Singular form
     * @param string $translationValue Translation
     * @throws Exception
     */
    protected function _translationValidate($singularForm, $translationValue) {
        // HTML Entities and HTML tags are not allowed!
        $regExHtml = '%(?:\&\w+;|<.+?>)%ims';
        if (preg_match($regExHtml, $singularForm) || preg_match($regExHtml, $translationValue)) {
            throw new Exception('HTML entities and tags are not allowed in entry "' . $singularForm . '"');
        }
        
        // Find all the singular form placeholders
        $singularPlaceholders = array();
        if (preg_match_all(WordPress_Pot_Translations_Entry::REGEX_SPRINTF_PLACEHOLDERS, $singularForm, $singularMatches)) {
            // Get the placeholders
            list($singularPlaceholders) = $singularMatches;
        }
        
        // Find all the translation placeholders
        $translationPlaceholders = array();
        if (preg_match_all(WordPress_Pot_Translations_Entry::REGEX_SPRINTF_PLACEHOLDERS, $translationValue, $translationMatches)) {
            // Get the placeholders
            list($translationPlaceholders) = $translationMatches;
        }
        
        // Prepare the counters
        $counters = array();
        foreach (array($singularPlaceholders, $translationPlaceholders) as $counterKey => $placeholders) {
            if (!isset($counters[$counterKey])) {
                $counters[$counterKey] = array();
            }
            foreach ($placeholders as $placeholder) {
                if (!isset($counters[$counterKey][$placeholder])) {
                    $counters[$counterKey][$placeholder] = 0;
                }
                $counters[$counterKey][$placeholder]++;
            }
        }
        
        // Compute the difference
        $differenceExpected = array_diff_assoc($counters[0], $counters[1]);
 
        // Invalid translation
        if (count($differenceExpected)) {
            // Prepare the expected texts
            $expected = array();
            foreach ($differenceExpected as $placeholder => $placeholderCount) {
                $expected[] = $placeholderCount . ' x ' . $placeholder;
            }
            
            // Log the error
            Log::check(Log::LEVEL_DEBUG) && Log::debug(array($singularForm, $translationValue, $expected));
            
            // Inform the user
            throw new Exception('Invalid translation. Expecting ' . implode(', ', $expected) . ' in entry "' . $singularForm 
                . '", instead got "' . $translationValue . '"');
        }
        
        // Compute the difference
        $differenceUnexpected = array_diff_assoc($counters[1], $counters[0]);
 
        // Invalid translation
        if (count($differenceUnexpected)) {
            // Prepare the expected texts
            $expected = array();
            foreach ($differenceUnexpected as $placeholder => $placeholderCount) {
                $expected[] = $placeholderCount . ' &times; ' . $placeholder;
            }
            
            // Log the error
            Log::check(Log::LEVEL_DEBUG) && Log::debug(array($singularForm, $translationValue, $expected));
            
            // Inform the user
            throw new Exception('Invalid translation. Did not expect ' . implode(', ', $expected) . ' in entry "' . $singularForm . '"');
        }
    }
    
    /**
     * Get the plurals count
     * 
     * @return int
     * @throws Exception
     */
    public function getPluralsCount() {
        // Not a valid language
        if (null === $this->_translations) {
            throw new Exception('Translations not available for this language: ' . $this->_language);
        }
        
        // Get the NPlurals
        list($nPlurals, ) = $this->_translations->npluralsAndExpressionFromHeader($this->_translations->getHeader(WordPress_Pot::HEADER_PLURAL));
        
        // All done
        return $nPlurals;
    }
    
    /**
     * Get all the entries for this language
     * 
     * @return WordPress_Pot_Translations_Entry[]
     * @throws Exception
     */
    public function getEntries() {
        // Not a valid language
        if (null === $this->_translations) {
            throw new Exception('Translations not available for this language: ' . $this->_language);
        }
        
        // All done
        return $this->_translations->entries;
    }
    
    /**
     * Delete an entry by key
     * 
     * @param string $entryKey Entry key
     * @throws Exception
     */
    public function deleteEntry($entryKey) {
        // Not a valid language
        if (null === $this->_translations) {
            throw new Exception('Translations not available for this language: ' . $this->_language);
        }
        
        // Remove the item
        if (isset($this->_translations->entries[$entryKey])) {
            unset($this->_translations->entries[$entryKey]);

            // Save
            $this->_save();
        }
    }
    
    /**
     * Get an entry by key
     * 
     * @param string $entryKey Entry key
     * @return WordPress_Pot_Translations_Entry
     * @throws Exception
     */
    protected function _getEntry($entryKey) {
        // Not a valid language
        if (null === $this->_translations) {
            throw new Exception('Translations not available for this language: ' . $this->_language);
        }
        
        // Not a valid entry key
        if (!isset($this->_translations->entries[$entryKey])) {
            throw new Exception('Entry with key "' . $entryKey . '" not found');
        }
        
        return $this->_translations->entries[$entryKey];
    }
    
    /**
     * Update the translations for an entry
     * 
     * @param WordPress_Pot_Translations_Entry[] $entries Translations entries
     * @return WordPress_Pot_Translations_Cache
     */
    public function updateEntries($entries) {
        // Not a valid language
        if (null === $this->_translations) {
            return $this;
        }
        
        // Save flag
        $saveFlag = false;

        // Go through the entries
        foreach ($entries as $entry) {
            // Get the entry key
            if (false === $entryKey = $entry->key()) {
                continue;
            }

            // Translation found
            if (isset($this->_translations->entries[$entryKey])) {
                // Get the chahed entry
                $cachedEntry = $this->_translations->entries[$entryKey];
                
                // Update the translations
                $entry->translations = $cachedEntry->translations;
                
                // Update the entry flags
                $entry->flags = $cachedEntry->flags;
            } else {
                // Needs save
                $saveFlag = true;
                
                // Prepare an entry clone
                $entryClone = new WordPress_Pot_Translations_Entry(array(
                    WordPress_Pot_Translations_Entry::ARG_SINGULAR     => $entry->singular,
                    WordPress_Pot_Translations_Entry::ARG_PLURAL       => $entry->isPlural,
                    WordPress_Pot_Translations_Entry::ARG_CONTEXT      => $entry->context,
                    WordPress_Pot_Translations_Entry::ARG_TRANSLATIONS => $entry->translations,
                ));
                $entryClone->plural = $entry->plural;
                $entryClone->extractedComments = $entry->extractedComments;
                
                // Store it
                $this->_translations->addEntry($entryClone);
            }
        }

        // Save to the cached file
        $saveFlag && $this->_save();
        
        // All done
        return $this;
    }
    
    /**
     * Save the current .PO to cache
     * 
     * @return WordPress_Pot_Translations_Cache
     */
    protected function _save() {
        // Not a valid language
        if (null === $this->_translations) {
            return $this;
        }
        
        // Export without the php-format flag
        $this->_translations->exportToFile($this->_cacheFilePath, true, false);
        
        // All done
        return $this;
    }

}

/* EOF */