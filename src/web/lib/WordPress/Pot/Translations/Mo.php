<?php
/**
 * Theme Warlock - WordPress_Pot_Translations_Mo
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class WordPress_Pot_Translations_Mo extends WordPress_Pot_Translations {

    /**
     * Fills up with the entries from MO file $filename
     *
     * @param string $filename MO file to load
     */
    public function importFromFile($filename) {
        $reader = new WordPress_Pot_Translations_Reader_File($filename);
        if (!$reader->isResource()) {
            return false;
        }
        return $this->importFromReader($reader);
    }

    /**
     * Import from a portable object
     * 
     * @param WordPress_Pot_Translations_Po $portableObject Portable Object
     * @return WordPress_Pot_Translations_Mo
     */
    public function importFromPo($portableObject) {
        // Replace the entries
        $this->entries = $portableObject->entries;
        
        // Replace the headers
        $this->headers = $portableObject->headers;
        
        // All done
        return $this;
    }
    
    /**
     * @param string $filename
     * @return bool
     */
    public function exportToFile($filename) {
        $fh = fopen($filename, 'wb');
        if (!$fh) {
            return false;
        }
        $res = $this->exportToFileHandle($fh);
        fclose($fh);
        return $res;
    }

    /**
     * @return string|false
     */
    public function export() {
        $tmp_fh = fopen("php://temp", 'r+');
        if (!$tmp_fh) {
            return false;
        }
        $this->exportToFileHandle($tmp_fh);
        rewind($tmp_fh);
        return stream_get_contents($tmp_fh);
    }

    /**
     * @param Translation_Entry $entry
     * @return bool
     */
    public function isEntryGoodForExport($entry) {
        if (empty($entry->translations)) {
            return false;
        }

        if (!array_filter($entry->translations)) {
            return false;
        }

        return true;
    }

    /**
     * @param resource $fh
     * @return true
     */
    public function exportToFileHandle($fh) {
        $entries = array_filter($this->entries, array($this, 'isEntryGoodForExport'));
        
        ksort($entries);
        $magic = 0x950412de;
        $revision = 0;
        $total = count($entries) + 1; // all the headers are one entry
        $originals_lenghts_addr = 28;
        $translations_lenghts_addr = $originals_lenghts_addr + 8 * $total;
        $size_of_hash = 0;
        $hash_addr = $translations_lenghts_addr + 8 * $total;
        $current_addr = $hash_addr;
        fwrite($fh, pack('V*', $magic, $revision, $total, $originals_lenghts_addr, $translations_lenghts_addr, $size_of_hash, $hash_addr));
        fseek($fh, $originals_lenghts_addr);

        // headers' msgid is an empty string
        fwrite($fh, pack('VV', 0, $current_addr));
        $current_addr++;
        $originals_table = chr(0);

        $reader = new WordPress_Pot_Translations_Reader();

        foreach ($entries as $entry) {
            $originals_table .= $this->exportOriginal($entry) . chr(0);
            $length = $reader->strlen($this->exportOriginal($entry));
            fwrite($fh, pack('VV', $length, $current_addr));
            $current_addr += $length + 1; // account for the NULL byte after
        }

        $exported_headers = $this->exportHeaders();
        fwrite($fh, pack('VV', $reader->strlen($exported_headers), $current_addr));
        $current_addr += strlen($exported_headers) + 1;
        $translations_table = $exported_headers . chr(0);

        foreach ($entries as $entry) {
            $translations_table .= $this->exportTranslations($entry) . chr(0);
            $length = $reader->strlen($this->exportTranslations($entry));
            fwrite($fh, pack('VV', $length, $current_addr));
            $current_addr += $length + 1;
        }

        fwrite($fh, $originals_table);
        fwrite($fh, $translations_table);
        return true;
    }

    /**
     * @param Translation_Entry $entry
     * @return string
     */
    public function exportOriginal($entry) {
        //TODO: warnings for control characters
        $exported = $entry->singular;
        
        if ($entry->isPlural) {
            $exported .= chr(0) . $entry->plural;
        }
        
        if ($entry->context) {
            $exported = $entry->context . chr(4) . $exported;
        }
        
        return $exported;
    }

    /**
     * @param WordPress_Pot_Translations_Entry $entry
     * @return string
     */
    public function exportTranslations($entry) {
        //TODO: warnings for control characters
        return $entry->isPlural ? implode(chr(0), $entry->translations) : $entry->translations[0];
    }

    /**
     * @return string
     */
    public function exportHeaders() {
        $exported = '';
        foreach ($this->headers as $header => $value) {
            $exported .= "$header: $value\n";
        }
        return $exported;
    }

    /**
     * @param int $magic
     * @return string|false
     */
    public function getByteOrder($magic) {
        // The magic is 0x950412de
        // bug in PHP 5.0.2, see https://savannah.nongnu.org/bugs/?func=detailitem&item_id=10565
        $magic_little = (int) - 1794895138;
        $magic_little_64 = (int) 2500072158;
        
        // 0xde120495
        $magic_big = ((int) - 569244523) & 0xFFFFFFFF;
        if ($magic_little == $magic || $magic_little_64 == $magic) {
            return WordPress_Pot_Translations_Reader::ENDIAN_LITTLE;
        } else if ($magic_big == $magic) {
            return WordPress_Pot_Translations_Reader::ENDIAN_BIG;
        } 
        return false;
    }

    /**
     * @param WordPress_Pot_Translations_Reader_File $reader
     */
    public function importFromReader($reader) {
        $endian_string = $this->getByteOrder($reader->readint32());
        if (false === $endian_string) {
            return false;
        }
        $reader->setEndian($endian_string);

        $endian = (WordPress_Pot_Translations_Reader::ENDIAN_BIG == $endian_string) ? WordPress_Pot_Translations_Reader::ENDIAN_LETTER_BIG : WordPress_Pot_Translations_Reader::ENDIAN_LETTER_LITTLE;

        $header = $reader->read(24);
        if ($reader->strlen($header) != 24)
            return false;

        // parse header
        $header = unpack("{$endian}revision/{$endian}total/{$endian}originals_lenghts_addr/{$endian}translations_lenghts_addr/{$endian}hash_length/{$endian}hash_addr", $header);
        if (!is_array($header)) {
            return false;
        }

        // support revision 0 of MO format specs, only
        if ($header['revision'] != 0) {
            return false;
        }

        // seek to data blocks
        $reader->seekto($header['originals_lenghts_addr']);

        // read originals' indices
        $originals_lengths_length = $header['translations_lenghts_addr'] - $header['originals_lenghts_addr'];
        if ($originals_lengths_length != $header['total'] * 8) {
            return false;
        }

        $originals = $reader->read($originals_lengths_length);
        if ($reader->strlen($originals) != $originals_lengths_length) {
            return false;
        }

        // read translations' indices
        $translations_lenghts_length = $header['hash_addr'] - $header['translations_lenghts_addr'];
        if ($translations_lenghts_length != $header['total'] * 8) {
            return false;
        }

        $translations = $reader->read($translations_lenghts_length);
        if ($reader->strlen($translations) != $translations_lenghts_length) {
            return false;
        }

        // transform raw data into set of indices
        $originals = $reader->strSplit($originals, 8);
        $translations = $reader->strSplit($translations, 8);

        // skip hash table
        $strings_addr = $header['hash_addr'] + $header['hash_length'] * 4;

        $reader->seekto($strings_addr);

        $strings = $reader->readAll();
        $reader->close();

        for ($i = 0; $i < $header['total']; $i++) {
            $o = unpack("{$endian}length/{$endian}pos", $originals[$i]);
            $t = unpack("{$endian}length/{$endian}pos", $translations[$i]);
            if (!$o || !$t)
                return false;

            // adjust offset due to reading strings to separate space before
            $o['pos'] -= $strings_addr;
            $t['pos'] -= $strings_addr;

            $original = $reader->substr($strings, $o['pos'], $o['length']);
            $translation = $reader->substr($strings, $t['pos'], $t['length']);

            if ('' === $original) {
                $this->setHeaders($this->makeHeaders($translation));
            } else {
                $entry = &$this->makeEntry($original, $translation);
                $this->entries[$entry->key()] = &$entry;
            }
        }
        return true;
    }

    /**
     * Build a Translation_Entry from original string and translation strings,
     * found in a MO file
     *
     * @static
     * @param string $original original string to translate from MO file. Might contain
     * 	0x04 as context separator or 0x00 as singular/plural separator
     * @param string $translation translation string from MO file. Might contain
     * 	0x00 as a plural translations separator
     */
    public function &makeEntry($original, $translation) {
        $entry = new WordPress_Pot_Translations_Entry();
        
        // look for context
        $parts = explode(chr(4), $original);
        if (isset($parts[1])) {
            $original = $parts[1];
            $entry->context = $parts[0];
        }
        
        // look for plural original
        $parts = explode(chr(0), $original);
        $entry->singular = $parts[0];
        
        if (isset($parts[1])) {
            $entry->isPlural = true;
            $entry->plural = $parts[1];
        }
        
        // plural translations are also separated by \0
        $entry->translations = explode(chr(0), $translation);
        return $entry;
    }

    /**
     * @param int $count
     * @return string
     */
    public function selectPluralForm($count) {
        return $this->gettextSelectPluralForm($count);
    }

    /**
     * @return int
     */
    public function getPluralFormsCount() {
        return $this->_nPlurals;
    }

}

/* EOF */