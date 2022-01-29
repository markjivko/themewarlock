<?php
/**
 * Theme Warlock - Translate
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Translation {
    // Language constants
    const LANG_AF = 'af';
    const LANG_SQ = 'sq';
    const LANG_AR = 'ar';
    const LANG_HY = 'hy';
    const LANG_AZ = 'az';
    const LANG_EU = 'eu';
    const LANG_BE = 'be';
    const LANG_BG = 'bg';
    const LANG_CA = 'ca';
    const LANG_ZH_CN = 'zh-CN';
    const LANG_ZH_TW = 'zh-TW';
    const LANG_HR = 'hr';
    const LANG_CS = 'cs';
    const LANG_DA = 'da';
    const LANG_NL = 'nl';
    const LANG_ET = 'et';
    const LANG_EN = 'en';
    const LANG_TL = 'tl';
    const LANG_FI = 'fi';
    const LANG_FR = 'fr';
    const LANG_GL = 'gl';
    const LANG_KA = 'ka';
    const LANG_DE = 'de';
    const LANG_EL = 'el';
    const LANG_HT = 'ht';
    const LANG_IW = 'iw';
    const LANG_HI = 'hi';
    const LANG_HU = 'hu';
    const LANG_IS = 'is';
    const LANG_ID = 'id';
    const LANG_GA = 'ga';
    const LANG_IT = 'it';
    const LANG_JA = 'ja';
    const LANG_KO = 'ko';
    const LANG_LV = 'lv';
    const LANG_LT = 'lt';
    const LANG_MK = 'mk';
    const LANG_MS = 'ms';
    const LANG_MT = 'mt';
    const LANG_NO = 'no';
    const LANG_FA = 'fa';
    const LANG_PL = 'pl';
    const LANG_PT = 'pt';
    const LANG_RO = 'ro';
    const LANG_RU = 'ru';
    const LANG_SR = 'sr';
    const LANG_SK = 'sk';
    const LANG_SL = 'sl';
    const LANG_ES = 'es';
    const LANG_SW = 'sw';
    const LANG_SV = 'sv';
    const LANG_TH = 'th';
    const LANG_TR = 'tr';
    const LANG_UK = 'uk';
    const LANG_UR = 'ur';
    const LANG_VI = 'vi';
    const LANG_CY = 'cy';
    const LANG_YI = 'yi';
    
    /**
     * Special Google Translate session token
     * 
     * @var string
     */
    protected static $_fn;
    
    /**
     * Store the current language duo
     * 
     * @var string
     */
    protected static $_langDuo = '';
    
    /**
     * Perform a Google translation
     * 
     * @param string  $value          Text to translate
     * @param string  $from           Original language
     * @param string  $to             Translation language
     * @param boolean $ignoreTextSize Ignore the text size
     * @return string
     */
    public static function get($value, $from, $to, $ignoreTextSize = false) {
        // Log this
        Log::check(Log::LEVEL_DEBUG) && Log::debug('<translation_request from="' . $from . '" to="' . $to . '">' . PHP_EOL . $value . PHP_EOL . '</translation_request>');
        
        // Store the language duo
        self::$_langDuo = "$from > $to";
        
        // Replace some characters
        $value = str_replace(
            array('’'), 
            array('\''), 
            $value
        );
        
        // Invalid string
        if (!strlen($value)) {
            return $value;
        }
        
        // Split the text into
        $result = '';
        if (!$ignoreTextSize) {
            $textSplit = self::_textSplit($value);
            if (count($textSplit) > 1) {
                foreach ($textSplit as $text) {
                    $result .= self::get($text, $from, $to, true) . str_pad('', self::_countFinalChars($text, "\r\n"), "\r\n");
                }
                return $result;
            }
        }

        // Sleep time in microseconds
        usleep(mt_rand(200, 400) * 1000);

        try {
            // User agent
            $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0';

            // Prepare the headers
            $curlHeaders = array (
                'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                'User-Agent: ' . $userAgent,
                'DNT: 1',
                'Referer: https://translate.google.com/',
                'Host: translate.google.com',
                'Referrer: translate.google.com',
                'Cache-Control: no-cache',
            );
            
            // Prepare the options
            $options = array(
                CURLOPT_POST            => true,
                CURLOPT_POSTFIELDS      => 'q=' . rawurlencode($value),
                CURLOPT_USERAGENT       => $userAgent,
                CURLOPT_RETURNTRANSFER  => true,
                CURLOPT_HEADER          => false,
                CURLOPT_HTTPHEADER      => $curlHeaders,
                CURLOPT_FOLLOWLOCATION  => true,
                CURLOPT_REDIR_PROTOCOLS => CURLPROTO_ALL,
                CURLOPT_AUTOREFERER     => true,
                CURLOPT_CONNECTTIMEOUT  => 30,
                CURLOPT_TIMEOUT         => 30,
                CURLOPT_MAXREDIRS       => 10,
                CURLOPT_SSL_VERIFYPEER  => false,
                CURLOPT_FAILONERROR     => true,
                CURLOPT_URL             => self::_getUrl($value, $from, $to),
            );

            // Initialize the CURL
            $ch = curl_init();
            
            // Set the options
            curl_setopt_array($ch, $options);
            
            // Count the errors
            $requestRetries = 0;
            
            // Execute the request
            do {
                if (false !== $json = curl_exec($ch)) {
                    break;
                }
                
                // Need to stop the zombies!
                if ($requestRetries >= 3) {
                    throw new Exception('Could not complete request. Exiting loop.');
                }
                
                // Force retrieval of new FN
                self::_getFn(true);
                
                // Change the URL
                curl_setopt($ch, CURLOPT_URL, $newUrl = self::_getUrl($value, $from, $to));
                
                // Sleep time in seconds
                $sleepTime = mt_rand(1, 3);

                // Log this
                Log::check(Log::LEVEL_DEBUG) && Log::debug($value);
                Log::check(Log::LEVEL_WARNING) && Log::warning('Could not complete request. Sleeping ' . $sleepTime . ' second' . (1 == $sleepTime ? '' : 's'));
                sleep($sleepTime);
                
                // Increment the counter
                $requestRetries++;
            } while (true);

            // Close
            curl_close($ch);

            // Sanitize the JSON
            $json = trim(str_replace("\n", "", preg_replace('%\,{2,}%', ',', $json)));
            $json = str_replace(array('[,', ',]'), array('[', ']'), $json);
            
            // Invalid JSON
            if (null == $json = @json_decode($json, true)) {
                throw new Exception('Google result is invalid: ' . print_r(array('value' => $result, 'lang' => $to, 'response' => $json), true));
            }

            // Get the translation
            if (isset($json[0]) && is_array($json[0]) && isset($json[0][0]) && isset($json[0][0][0])) {
                // Prepare the result
                $result = '';
                
                // Get the translation
                foreach ($json[0] as $firstLineResults) {
                    if (is_array($firstLineResults) && count($firstLineResults) > 1 && isset($firstLineResults[0])) {
                        $result .= $firstLineResults[0];
                    }
                }
                
                // Clean non-utf8 characters
                $regex = <<<'END'
/
(
(?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
|   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
|   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
|   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
){1,100}                        # ...one or more times
)
| .                                 # anything else
/x
END;
                $result = preg_replace($regex, '$1', $result);
                
                // Sanitize the result
                $result = preg_replace('%\s([\.\,\;\:\!\?])%i', '$1', $result);
                $result = preg_replace('% {2,}%i', ' ', $result);
                $result = preg_replace('%\t{2,}%i', '    ', $result);
                $result = preg_replace('%\r\n%i', "\n", $result);

                // Correct quotes
                $result = trim(preg_replace('/"\s*(.*?)\s*"/u', '"$1"', $result));
                
                // Correct %s and %d
                $result = preg_replace('/% +([sd])\b/', ' %$1', $result);
                
                // Need to uppercase the word
                if ($value[0] == strtoupper($value[0])) {
                    $result = TwString::mbUcfirst($result);
                }
                
                // Log this
                Log::check(Log::LEVEL_DEBUG) && Log::debug('<translation_result from="' . $from . '" to="' . $to . '">' . PHP_EOL . $result . PHP_EOL . '</translation_result>');
                
                // Return the result
                return $result;
            } else {
                throw new Exception('Google result is not formated as expected: ' . print_r($json, true));
            }
        } catch (Exception $exception) {
            Log::check(Log::LEVEL_ERROR) && Log::error($exception->getMessage(), $exception->getFile(), $exception->getLine());
        }

        // Nothing returned
        return '';
    }

    /**
     * Get the Google Translate URL
     * 
     * @param string $value Text to translate
     * @param string $from
     * @param string $to
     * @return string
     */
    protected static function _getUrl($value, $from, $to) {
        $result = 'https://translate.google.com/translate_a/single?client=webapp'
            . '&sl=' . $from 
            . '&tl=' . $to 
            . '&hl=' . $from 
            . '&dt=at&dt=bd&dt=ex&dt=ld&dt=md&dt=qca&dt=rw&dt=rm&dt=sos&dt=ss&dt=t&otf=1&pc=1&ie=UTF-8&oe=UTF-8&source=btn&ssel=0&tsel=0&kc=2'
            . '&tk=' . self::_getGToken($value, $to);
        Log::check(Log::LEVEL_DEBUG) && Log::debug('Google Translate URL: ' . $result);
        return $result;
    }
    
    /**
     * Get the translation token
     * 
     * @param boolean $forced Force new request or get from cache
     * @return string
     */
    protected static function _getFn($forced = false) {
        // Token already stored
        if (!$forced && isset(self::$_fn)) {
            return self::$_fn;
        }
        
        // User agent
        $userAgent = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0';

        // Get the number of attempts
        $attempts = 0;
        do {
            // Prepare the CURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://translate.google.com/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_REDIR_PROTOCOLS, CURLPROTO_ALL);
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            // Get the contents
            $gtContents = curl_exec($ch);
            curl_close($ch);
            
            // Valid result
            if (false !== $gtContents || $attempts >= 5) {
                break;
            }
            
            // Increment the attempts
            $attempts++;
            
            // Log the attempt
            Log::check(Log::LEVEL_WARNING) && Log::warning('Failed attempt #' . $attempts . ' at getting translate.google.com');
        } while (true);
        
        // Get the session token
        if (preg_match('%TKK\s*=\s*eval\s*\(\'([^\']+)\'\)%', $gtContents, $matches)) {
            // Get A
            $tkkPartA = preg_replace('%.*?var\s+a.*?3d(\-?\d+)\s*\;.*%', '${1}', $matches[1]);

            // Get B
            $tkkPartB = preg_replace('%.*?var\s+b.*?3d(\-?\d+)\s*\;.*%', '${1}', $matches[1]);

            // Get result
            $tkkPartR = preg_replace('%.*?return\s+(\d+).*%', '${1}', $matches[1]);

            // Prepare the quantifier
            self::$_fn = $tkkPartR . '.' . ($tkkPartA + $tkkPartB);
        } elseif(preg_match('%\btkk\s*[=:]\s*([\'"])(\d+\.\d+)\1\s*[;,]%i', $gtContents, $matches)) {
            // Store the result
            self::$_fn = $matches[2];
        } else {
            return null;
        }
        
        // Log this information
        Log::check(Log::LEVEL_INFO) && Log::info('Computed new Google Translate Token Key (' . self::$_langDuo . '): ' . self::$_fn);
        
        // All done
        return self::$_fn;
    }
    
    /**
     * Compute the Google Translate request token
     * 
     * @param string $text
     * @return string Google Translate request token
     */
    protected static function _getGToken($input) {
        // Clean-up the input
        $input = str_replace("\r\n", "\n", $input);
                
        // Get the quantifier
        $fn = self::_getFn();
        
        // No valid quantifier found
        if (null === $fn) {
            return null;
        }
        
        // Get the quantifier parts
        list($fnA, $fnB) = array_map('floatval', explode('.', $fn));

        // Prepare the result
        $results = [];

        // Get the index
        $index = 0;
        
        // Go through the string
        for ($i = 0; $i < strlen($input); $i++) {
            // Get the charcode
            list(, $v) = @unpack('N', mb_convert_encoding($input[$i], 'UCS-4BE', 'UTF-8'));
            
            // Lower than 128
            if (128 > $v) {
                $results[$index++] = $v;
            } else {
                if (2048 > $v) {
                    $results[$index++] = $v >> 6 | 192;
                } else {
                    list(, $vP) = unpack('N', mb_convert_encoding($input[$i + 1], 'UCS-4BE', 'UTF-8'));
                    if (55296 == ($v & 64512) && ($i + 1 < strlen($input) && 56320 == ($vP & 64512))) {
                        // Increment
                        $i++;
                        
                        // Set the new value
                        $v = 65536 + (($v & 1023) << 10) + ($vP & 1023);
                        
                        // Set the next step
                        $results[$index++] = $v >> 18 | 240;
                        
                        // Set the next step
                        $results[$index++] = $v >> 12 & 63 | 128;
                    } else {
                        // Set the next step
                        $results[$index++] = $v >> 12 | 224;
                    }
                    
                    // Set the next step
                    $results[$index++] = $v >> 6 & 63 | 128;
                }
                // Set the next step
                $results[$index++] = $v & 63 | 128;
            }
        }

        // Get the int value
        $input = $fnA;

        // Perform magical transformations
        foreach ($results as $res) {
            $input += $res;
            $input = self::_getGTokenHelper($input, "+-a^+6");
        }
        
        // Transform the result once more
        $input = self::_getGTokenHelper($input, "+-3^+b+-f");
        
        // Filter the second part
        $input ^= $fnB;
        
        // Negative value
        if (0 > $input) {
            // Eight Mersenne prime
            $input = ($input & 2147483647) + 2147483648;
        }
        
        // Get the modulus
        $result = fmod($input, 1000000);

        // All done
        $finalToken = strval($result) . "." . ($result ^ $fnA);
        
        // Log this
        Log::check(Log::LEVEL_INFO) && Log::info('Computed new Google Translate Final Token (' . self::$_langDuo . '): ' . $finalToken);
        
        // All done
        return $finalToken;
    }

    /**
     * Google Translate request token helper
     * 
     * @param int    $x       Character value
     * @param string $message Conversion message
     * @return int Converted character
     */
    protected static function _getGTokenHelper($x, $message) {
        // Interpret the message
        for ($step = 0; $step < strlen($message) - 2; $step += 3) {
            // Get the s + 1
            $y = $message[$step + 2];
            
            // Get the characte code
            list(, $charCode) = unpack('N', mb_convert_encoding($y[0], 'UCS-4BE', 'UTF-8'));
            
            // Get the new value
            $y = $y >= "a" ? $charCode - 87 : floatval($y);
            
            // Some bitwise shifting
            $y = $message[$step + 1] == "+" ? (($x >= 0) ? ($x >> $y) : (($x & 0x7fffffff) >> $y) | (0x40000000 >> ($y - 1))) : $x << $y;
            
            // Five Fermat primes produce
            $x = $message[$step] == "+" ? $x + $y & 4294967295 : $x ^ $y;
        }

        // All done
        return $x;
    }
    
    /**
     * Translate texts in batches; supports translation of sprintf() parameters.<br/>
     * Each failed translation is replaced with an empty string.
     * 
     * @param array   $batch          Texts to translate
     * @param string  $from           Original language
     * @param string  $to             Translation language
     * @param boolean $ignoreTextSize (optional) Ignore the text size; default <b>false</b>
     * @param int     $splitSize      (optional) Batch chunk size, [1,500]; default <b>50</b>
     * @return array Associative array of translated values
     */
    public static function getBatch(Array $batch, $from, $to, $ignoreTextSize = false, $splitSize = 10) {
        // Don't work form empty arrays
        if (!count($batch)) {
            return array();
        }
        
        // Validate the split size
        if (!is_int($splitSize) && false !== $splitSize) {
            $splitSize = 10;
        }

        // First run - split the task into smaller batches
        if (is_int($splitSize)) {
            // More than 1
            if ($splitSize < 1) {
                $splitSize = 1;
            }
            
            // Less than 500
            if ($splitSize > 500) {
                $splitSize = 500;
            }
            
            // Get the number of batches
            $splitNumber = intval(count($batch) / $splitSize) + 1;
            
            // Go through each batch
            for ($i = 1; $i <= $splitNumber; $i++) {
                // Prepare the batch keys
                $batchKeys = array_slice(array_keys($batch), ($i - 1) * $splitSize, $splitSize);
                
                // Prepare the batch fragment
                $batchFragment = array();
                foreach ($batchKeys as $batchKey) {
                    $batchFragment[$batchKey] = $batch[$batchKey];
                }

                // Translate the values
                $batchFragment = self::getBatch($batchFragment, $from, $to, $ignoreTextSize, false);

                // Store the translated values back
                foreach ($batchFragment as $batchKey => $batchValue) {
                    $batch[$batchKey] = $batchValue;
                }
                
                // Sleep between 200ms and 400ms
                usleep(mt_rand(200, 400) * 1000);
            }
            
            // Translated by batches
            return $batch;
        }
        
        // Get the combined text
        $originalCombined = implode(PHP_EOL . '---' . PHP_EOL, array_values($batch));
        
        // Prepare the placeholders
        $placeholders = array();
        
        // Replace the sprintf() placeholders with tags
        $originalCombined = preg_replace_callback(
            WordPress_Pot_Translations_Entry::REGEX_SPRINTF_PLACEHOLDERS, 
            function($item) use(&$placeholders) {
                // Prepare the key
                $key = count($placeholders);
                
                // Store the value
                $placeholders[$key] = $item[0];
                
                // Set the placeholder
                return '__' . ($key + 1) . '__';
            }, 
            $originalCombined
        );
        
        // Translate
        $translatedCombined = self::get($originalCombined, $from, $to, $ignoreTextSize);

        // Get the values back
        $translatedValues = array_map('trim', preg_split('%\s*-{2}\s?-(?:\s*-{2}\s?-\s*\.)?%iums', $translatedCombined));

        // Double translation
        if (count($batch) < count($translatedValues)) {
            $translatedValues = array_slice($translatedValues, 0, count($batch) - 1);
        }
        
        // Valid reconstruction
        if (count($batch) == count($translatedValues)) {
            foreach ($batch as $key => $value) {
                // Get the translated value
                $translation = array_shift($translatedValues);
                
                // Need to uppercase
                if ($value[0] == strtoupper($value[0]) && preg_match('%^[a-z]%i', $value)) {
                    // Multi-byte uppercase
                    $translation = TwString::mbUcfirst($translation);
                }
                
                // Replace the tags with sprintf() placeholders
                try {
                    $translation = preg_replace_callback(
                        '%\b__\s*(\d+)(?:\s*__)?\b%i', 
                        function($item) use($placeholders) {
                            // Prepare the key
                            $key = intval($item[1]) - 1;

                            // The key was not defined
                            if (!isset($placeholders[$key])) {
                                throw new Exception('Key ' . $key . ' not defined');
                            }

                            // Set the replacement
                            return $placeholders[$key];
                        }, 
                        $translation
                    );
                } catch (Exception $exc) {
                    // Failed translation
                    $translation = '';
                    
                    // Log the event
                    Log::check(Log::LEVEL_ERROR) && Log::error($exc->getMessage(), $exc->getFile(), $exc->getLine());
                }
                
                // Save the translation
                $batch[$key] = $translation;
            }
        } else {
            // Invalidate the entire batch
            foreach (array_keys($batch) as $key) {
                $batch[$key] = '';
            }
        }

        // All done
        return $batch;
    }
    
    /**
     * Split a text into sentences this length
     * 
     * @param string $text   Text
     * @param int    $length Chunk size
     * @return string 
     */
    protected static function _textSplit($text = '', $length = 2000) {
        // Prepare the result
        $result = array();

        // Get the sentences
        preg_match_all('%(\S.+?[\.!?](?:[\s\n]+|$))%sim', $text, $matches);

        // Prepare the sentence
        $sentence = '';
        foreach ($matches[0] as $s) {
            if (strlen($s) + strlen($sentence) > $length) {
                $result[] = $sentence;
                $sentence = $s;
            } else {
                $sentence .= $s;
            }
        }

        // Append the last created sentence
        $result[] = $sentence;

        // Return the result
        return $result;
    }
    
    /**
     * Count the apparition of a text at the end of a string
     * 
     * @param string $text Text
     * @param string $char Final character(s) to count
     * @return int
     */
    protected static function _countFinalChars($text, $char) {
        $length = (strlen($text) - strlen(rtrim($text, $char))) / strlen($char);
        return $length >= 0 ? (int) $length : 0;
    }
    
}

/*EOF*/