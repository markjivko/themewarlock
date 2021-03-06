<?php

/**
 * Theme Warlock - WordPress_Pot_Translations_Po
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class WordPress_Pot_Translations_Po extends WordPress_Pot_Translations {

    /**
     * Maximum line length
     */
    const PO_MAX_LINE_LEN = 79;
    
    /**
     * File comments
     * 
     * @var string
     */
    protected $_commentsBeforeHeaders = '';

    /**
     * Translation
     * 
     * @param WordPress_Pot_Translations_Entry[] $entries (optional) Translation entries
     */
    public function __construct($entries = array()) {
        // Append each valid entry
        foreach ($entries as $entry) {
            if ($entry instanceof WordPress_Pot_Translations_Entry) {
                $this->entries[] = $entry;
            }
        }
    }
    
    /**
     * Exports headers to a PO entry
     *
     * @return string msgid/msgstr PO entry for this PO file headers, doesn't contain newline at the end
     */
    public function exportHeaders() {
        $headerString = '';
        foreach ($this->headers as $header => $value) {
            $headerString .= "$header: $value\n";
        }
        
        $poified = self::poify($headerString);
        if ($this->_commentsBeforeHeaders) {
            $beforeHeaders = $this->prependEachLine(rtrim($this->_commentsBeforeHeaders) . "\n", '# ');
        } else {
            $beforeHeaders = '';
        }
        
        return rtrim("{$beforeHeaders}msgid \"\"\nmsgstr $poified");
    }

    /**
     * Exports all entries to PO format
     *
     * @param bool $automaticFlags Whether to include flags automatically
     * @return string sequence of mgsgid/msgstr PO strings, doesn't containt newline at the end
     */
    public function exportEntries($automaticFlags = true) {
        //TODO sorting
        return implode("\n\n", array_map(function($item) use($automaticFlags) {
            return $this->exportEntry($item, $automaticFlags);
        }, $this->entries));
    }

    /**
     * Exports the whole PO file as a string
     *
     * @param bool $includeHeaders Whether to include the headers in the export
     * @param bool $automaticFlags Whether to include flags automatically
     * @return string ready for inclusion in PO file string for headers and all the enrtries
     */
    public function export($includeHeaders = true, $automaticFlags = true) {
        $res = '';
        if ($includeHeaders) {
            $res .= $this->exportHeaders();
            $res .= "\n\n";
        }
        
        $res .= $this->exportEntries($automaticFlags);
        return $res;
    }

    /**
     * Same as {@link export}, but writes the result to a file
     *
     * @param string $filename       Where to write the PO string
     * @param bool   $includeHeaders Whether to include the headers in the export, default <b>true</b>
     * @param bool   $automaticFlags Whether to automatically add flags, default <b>true</b>
     * @return bool true on success, false on error
     */
    public function exportToFile($filename, $includeHeaders = true, $automaticFlags = true) {
        $fh = fopen($filename, 'w');
        if (false === $fh) {
            return false;
        }
        
        $res = fwrite($fh, $this->export($includeHeaders, $automaticFlags));
        
        if (false === $res) {
            return false;
        }
        
        return fclose($fh);
    }

    /**
     * Set the current text domain
     * 
     * @param string $textDomain Export Text Domain
     */
    public function setTextDomain($textDomain) {
        foreach ($this->entries as $entry) {
            $entry->setTextDomain($textDomain);
        }
    }
    
    /**
     * Text to include as a comment before the start of the PO contents
     *
     * Doesn't need to include # in the beginning of lines, these are added automatically
     */
    public function setCommentBeforeHeaders($text) {
        $this->_commentsBeforeHeaders = $text;
    }

    /**
     * Formats a string in PO-style
     *
     * @static
     * @param string $string the string to format
     * @return string the poified string
     */
    public static function poify($string) {
        $quote = '"';
        $slash = '\\';
        $newline = "\n";

        $replaces = array(
            "$slash" => "$slash$slash",
            "$quote" => "$slash$quote",
            "\t" => '\t',
        );

        $string = str_replace(array_keys($replaces), array_values($replaces), $string);

        $po = $quote . implode("${slash}n$quote$newline$quote", explode($newline, $string)) . $quote;
        
        // add empty string on first line for readbility
        if (false !== strpos($string, $newline) && (substr_count($string, $newline) > 1 || !($newline === substr($string, -strlen($newline))))) {
            $po = "$quote$quote$newline$po";
        }
        
        // remove empty strings
        $po = str_replace("$newline$quote$quote", '', $po);
        return $po;
    }

    /**
     * Gives back the original string from a PO-formatted string
     *
     * @static
     * @param string $string PO-formatted string
     * @return string enascaped string
     */
    public static function unpoify($string) {
        $escapes = array('t' => "\t", 'n' => "\n", 'r' => "\r", '\\' => '\\');
        $lines = array_map('trim', explode("\n", $string));
        $lines = array_map(array(__CLASS__, 'trimQuotes'), $lines);
        $unpoified = '';
        $previous_is_backslash = false;
        foreach ($lines as $line) {
            preg_match_all('/./u', $line, $chars);
            $chars = $chars[0];
            foreach ($chars as $char) {
                if (!$previous_is_backslash) {
                    if ('\\' == $char) {
                        $previous_is_backslash = true;
                    } else {
                        $unpoified .= $char;
                    }
                } else {
                    $previous_is_backslash = false;
                    $unpoified .= isset($escapes[$char]) ? $escapes[$char] : $char;
                }
            }
        }

        // Standardise the line endings on imported content, technically PO files shouldn't contain \r
        $unpoified = str_replace(array("\r\n", "\r"), "\n", $unpoified);

        return $unpoified;
    }

    /**
     * Inserts $with in the beginning of every new line of $string and
     * returns the modified string
     *
     * @static
     * @param string $string prepend lines in this string
     * @param string $with   prepend lines with this string
     */
    public static function prependEachLine($string, $with) {
        $php_with = var_export($with, true);
        $lines = explode("\n", $string);
        
        // do not prepend the string on the last empty line, artefact by explode
        if ("\n" == substr($string, -1)) {
            unset($lines[count($lines) - 1]);
        }
        
        $res = implode("\n", array_map(create_function('$x', "return $php_with.\$x;"), $lines));
        
        // give back the empty line, we ignored above
        if ("\n" == substr($string, -1)) {
            $res .= "\n";
        }
        
        return $res;
    }

    /**
     * Prepare a text as a comment -- wraps the lines and prepends #
     * and a special character to each line
     *
     * @access private
     * @param string $text the comment text
     * @param string $char character to denote a special PO comment,
     * 	like :, default is a space
     */
    public static function commentBlock($text, $char = ' ') {
        $text = wordwrap($text, self::PO_MAX_LINE_LEN - 3);
        return self::prependEachLine($text, "#$char ");
    }

    /**
     * Builds a string from the entry for inclusion in PO file
     *
     * @param Translation_Entry &$entry         The entry to convert to po string
     * @param bool              $automaticFlags Whether to include flags automatically
     * @return false|string PO-style formatted string for the entry or
     * 	false if the entry is empty
     */
    public function exportEntry(&$entry, $automaticFlags = true) {
        if (null === $entry->singular || '' === $entry->singular) {
            return false;
        }
        
        // Contains PHP sprintf arguments
        if ($automaticFlags && preg_match(WordPress_Pot_Translations_Entry::REGEX_SPRINTF_PLACEHOLDERS, $entry->singular)) {
            // Mark this item as php-format
            if (!in_array(WordPress_Pot_Translations_Entry::FLAG_PHP_FORMAT, $entry->flags)) {
                $entry->flags[] = WordPress_Pot_Translations_Entry::FLAG_PHP_FORMAT;
            }
        }
        
        $po = array();
        
        // Translator comments
        if (!empty($entry->translatorComments)) {
            $po[] = self::commentBlock($entry->translatorComments);
        }
        
        // Comments
        if (!empty($entry->extractedComments)) {
            $po[] = self::commentBlock(trim($entry->extractedComments), '.');
        }
        
        // References
        if (!empty($entry->references)) {
            $po[] = self::commentBlock(trim(implode(' ', $entry->references)), ':');
        }
        
        // Flags
        if (!empty($entry->flags)) {
            $po[] = self::commentBlock(implode(", ", $entry->flags), ',');
        }
        
        // Valid text domain
        if (strlen($entry->textDomain)) {
            $po[] = self::commentBlock($entry->textDomain, '@');
        }
        
        // Context
        if ($entry->context) {
            $po[] = 'msgctxt ' . self::poify($entry->context);
        }
        
        // Singular form
        $po[] = 'msgid ' . self::poify($entry->singular);
        
        if (!$entry->isPlural) {
            $translation = empty($entry->translations) ? '' : $entry->translations[0];
            $translation = self::matchBeginAndEndNewlines($translation, $entry->singular);
            $po[] = 'msgstr ' . self::poify($translation);
        } else {
            $po[] = 'msgid_plural ' . self::poify($entry->plural);
            $translations = empty($entry->translations) ? array('', '') : $entry->translations;
            foreach ($translations as $i => $translation) {
                $translation = self::matchBeginAndEndNewlines($translation, $entry->plural);
                $po[] = "msgstr[$i] " . self::poify($translation);
            }
        }
        
        return implode("\n", $po);
    }

    public static function matchBeginAndEndNewlines($translation, $original) {
        if ('' === $translation) {
            return $translation;
        }

        $original_begin = "\n" === substr($original, 0, 1);
        $original_end = "\n" === substr($original, -1);
        $translation_begin = "\n" === substr($translation, 0, 1);
        $translation_end = "\n" === substr($translation, -1);

        if ($original_begin) {
            if (!$translation_begin) {
                $translation = "\n" . $translation;
            }
        } elseif ($translation_begin) {
            $translation = ltrim($translation, "\n");
        }

        if ($original_end) {
            if (!$translation_end) {
                $translation .= "\n";
            }
        } elseif ($translation_end) {
            $translation = rtrim($translation, "\n");
        }

        return $translation;
    }

    /**
     * @param string $filename
     * @return boolean
     */
    public function importFromFile($filename) {
        $f = fopen($filename, 'r');
        if (!$f) {
            return false;
        }
        $lineno = 0;
        
        while (true) {
            $res = $this->readEntry($f, $lineno);
            if (!$res) {
                break;
            }
            
            if ($res['entry']->singular == '') {
                $this->setHeaders($this->makeHeaders($res['entry']->translations[0]));
            } else {
                $this->addEntry($res['entry']);
            }
        }
        
        $this->readLine($f, 'clear');
        if (false === $res) {
            return false;
        }
        
        if (!$this->headers && !$this->entries) {
            return false;
        }
        
        return true;
    }

    /**
     * @param resource $f
     * @param int      $lineno
     * @return null|false|array
     */
    public function readEntry($f, $lineno = 0) {
        $entry = new WordPress_Pot_Translations_Entry();
        
        // where were we in the last step
        // can be: comment, msgctxt, msgid, msgid_plural, msgstr, msgstr_plural
        $context = '';
        $msgstr_index = 0;
        
        $is_final = create_function('$context', 'return $context == "msgstr" || $context == "msgstr_plural";');
        while (true) {
            $lineno++;
            $line = $this->readLine($f);
            if (!$line) {
                if (feof($f)) {
                    if ($is_final($context)) {
                        break;
                    } elseif (!$context) {// we haven't read a line and eof came
                        return null;
                    }
                }
                return false;
            }
            
            if ($line == "\n") {
                continue;
            }
            
            $line = trim($line);
            if (preg_match('/^#/', $line, $m)) {
                // the comment is the start of a new entry
                if ($is_final($context)) {
                    $this->readLine($f, 'put-back');
                    $lineno--;
                    break;
                }
                
                // comments have to be at the beginning
                if ($context && $context != 'comment') {
                    return false;
                }
                
                // add comment
                $this->addCommentToEntry($entry, $line);
            } elseif (preg_match('/^msgctxt\s+(".*")/', $line, $m)) {
                if ($is_final($context)) {
                    $this->readLine($f, 'put-back');
                    $lineno--;
                    break;
                }
                
                if ($context && $context != 'comment') {
                    return false;
                }
                
                $context = 'msgctxt';
                $entry->context .= self::unpoify($m[1]);
            } elseif (preg_match('/^msgid\s+(".*")/', $line, $m)) {
                if ($is_final($context)) {
                    $this->readLine($f, 'put-back');
                    $lineno--;
                    break;
                }
                
                if ($context && $context != 'msgctxt' && $context != 'comment') {
                    return false;
                }
                
                $context = 'msgid';
                $entry->singular .= self::unpoify($m[1]);
            } elseif (preg_match('/^msgid_plural\s+(".*")/', $line, $m)) {
                if ($context != 'msgid') {
                    return false;
                }
                
                $context = 'msgid_plural';
                $entry->isPlural = true;
                $entry->plural .= self::unpoify($m[1]);
            } elseif (preg_match('/^msgstr\s+(".*")/', $line, $m)) {
                if ($context != 'msgid') {
                    return false;
                }
                
                $context = 'msgstr';
                $entry->translations = array(self::unpoify($m[1]));
            } elseif (preg_match('/^msgstr\[(\d+)\]\s+(".*")/', $line, $m)) {
                if ($context != 'msgid_plural' && $context != 'msgstr_plural') {
                    return false;
                }
                
                $context = 'msgstr_plural';
                $msgstr_index = $m[1];
                $entry->translations[$m[1]] = self::unpoify($m[2]);
            } elseif (preg_match('/^".*"$/', $line)) {
                $unpoified = self::unpoify($line);
                switch ($context) {
                    case 'msgid':
                        $entry->singular .= $unpoified;
                        break;
                    
                    case 'msgctxt':
                        $entry->context .= $unpoified;
                        break;
                    
                    case 'msgid_plural':
                        $entry->plural .= $unpoified;
                        break;
                    
                    case 'msgstr':
                        $entry->translations[0] .= $unpoified;
                        break;
                    
                    case 'msgstr_plural':
                        $entry->translations[$msgstr_index] .= $unpoified;
                        break;
                    
                    default:
                        return false;
                }
            } else {
                return false;
            }
        }
        
        return array('entry' => $entry, 'lineno' => $lineno);
    }

    /**
     * @staticvar string   $last_line
     * @staticvar boolean  $use_last_line
     *
     * @param     resource $f
     * @param     string   $action
     * @return boolean
     */
    public function readLine($f, $action = 'read') {
        static $last_line = '';
        static $use_last_line = false;
        
        if ('clear' == $action) {
            $last_line = '';
            return true;
        }
        
        if ('put-back' == $action) {
            $use_last_line = true;
            return true;
        }
        
        $line = $use_last_line ? $last_line : fgets($f);
        $line = ( "\r\n" == substr($line, -2) ) ? rtrim($line, "\r\n") . "\n" : $line;
        $last_line = $line;
        $use_last_line = false;
        return $line;
    }

    /**
     * @param Translation_Entry $entry
     * @param string            $po_comment_line
     */
    public function addCommentToEntry(&$entry, $po_comment_line) {
        $first_two = substr($po_comment_line, 0, 2);
        $comment = trim(substr($po_comment_line, 2));
        
        if ('#:' == $first_two) {
            $entry->references = array_merge($entry->references, preg_split('/\s+/', $comment));
        } elseif ('#.' == $first_two) {
            $entry->extractedComments = trim($entry->extractedComments . "\n" . $comment);
        } elseif ('#,' == $first_two) {
            $entry->flags = array_merge($entry->flags, preg_split('/,\s*/', $comment));
        } else {
            $entry->translatorComments = trim($entry->translatorComments . "\n" . $comment);
        }
    }

    /**
     * @param string $s
     * @return sring
     */
    public static function trimQuotes($s) {
        if (substr($s, 0, 1) == '"') {
            $s = substr($s, 1);
        }
        
        if (substr($s, -1, 1) == '"') {
            $s = substr($s, 0, -1);
        }
        
        return $s;
    }
    
    /**
     * Merge translation objects
     * 
     * @param WordPress_Pot_Translations_Po $other   Other Translation object
     * @param boolean                       $replace Whether or not to replace the current Entry's meta data with the other object's
     */
    public function mergeOriginalsWith($other, $replace = false) {
        // Perform the replacements
        parent::mergeOriginalsWith($other, $replace);
        
        // Replace the comments as well
        if ($replace) {
            $this->_commentsBeforeHeaders = $other->_commentsBeforeHeaders;
        }
    }
}

/* EOF */