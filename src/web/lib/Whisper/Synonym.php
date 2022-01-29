<?php
/**
 * Theme Warlock - Whisper_Synonym
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Whisper_Synonym {

    // Data stores
    const DATA_WORDS    = 'words';
    const DATA_SYNONYMS = 'synonyms';
    const DATA_ADJ      = 'adj';
    const DATA_ADV      = 'adv';
    const DATA_NOUN     = 'noun';
    
    /**
     * Synonyms data
     * 
     * @var array
     */
    protected static $_data;
    
    /**
     * Get the synonyms list for a given word
     * 
     * @param string $word   Word
     * @param int    $max    Max number of results
     * @param int    $levels Levels
     * @return string[]
     */
    public static function getArray($word = null, $max = 10, $levels = 1) {
        // Initialize
        self::_init();
        
        // No word defined
        if (null === $word) {
            // Get a random noun id
            $wordId = self::$_data[self::DATA_NOUN][mt_rand(0, count(self::$_data[self::DATA_NOUN]) - 1)];
            
            // Get the word as string
            $word = self::$_data[self::DATA_WORDS][$wordId];
        }
        
        // Trim the word
        $word = trim($word);
        
        // Store word is ucfirst
        $capitalize = (strtoupper($word[0]) === $word[0]);
        
        // Get the closest word ID
        if (null === $closestIds = self::_closestIds($word)) {
            return array($word);
        }
        
        // Perform a synonym search
        $wordIds = self::_search($closestIds, $max, $levels);
        
        // Get the actual words
        return self::_idTranslate($wordIds, $capitalize);
    }
    
    /**
     * Convert word IDs into word strings
     * 
     * @param int[]   $ids        Ids list
     * @param boolean $capitalize Capitalize the words
     * @return string[]
     */
    protected static function _idTranslate($ids, $capitalize = false) {
        // Prepare the result
        return array_filter(array_map(function($item) use ($capitalize) {
            return isset(Whisper_Synonym::$_data[Whisper_Synonym::DATA_WORDS][$item]) ? ($capitalize ? ucfirst(Whisper_Synonym::$_data[Whisper_Synonym::DATA_WORDS][$item]) : Whisper_Synonym::$_data[Whisper_Synonym::DATA_WORDS][$item]) : null;
        }, $ids));
    }
    
    /**
     * Search through the database
     * 
     * @param int[] $closestIds Closest IDs
     * @param int   $max        Max number of results
     * @param int   $levels     Search levels
     * @return type
     */
    protected static function _search($closestIds, $max, $levels) {
        // Prepare the exact matches result
        $resultMatch = array();
        
        // Prepare the siblings result
        $resultSiblings = array();
        
        // Final result
        $result = array();
        
        // Go through the ids
        foreach (array_keys($closestIds) as $id) {
            // Prepare the word list
            $wordList = array($id);
            
            // Prepare the level
            $currentLevel = 1;
            
            while(true) {
                // Go through the data
                foreach ($wordList as $wordListKey => $wordToFind) {
                    // Get the word type
                    $wordToFindType = self::_wordTypeById($wordToFind);
                    
                    // Remove the current item
                    unset($wordList[$wordListKey]);
                    
                    // Found an exact match
                    if (isset(self::$_data[self::DATA_SYNONYMS][$wordToFind])) {
                        foreach (self::$_data[self::DATA_SYNONYMS][$wordToFind] as $val) {
                            if (self::_wordTypeById($val) == $wordToFindType) {
                                $resultMatch[] = $val;
                            }
                        }
                    }

                    // Move on
                    foreach(self::$_data[self::DATA_SYNONYMS] as $key => $values) {
                        // New words to find
                        if (in_array($wordToFind, $values)) {
                            foreach ($values as $val) {
                                if (self::_wordTypeById($val) == $wordToFindType) {
                                    $resultSiblings[] = $val;
                                    if ($wordToFind != $val) {
                                        $wordList[] = $val;
                                    }
                                }
                            }
                            $wordList = array_unique($wordList);
                        }
                        
                        // Get the result
                        $result = array_unique(array_merge($resultMatch, $resultSiblings));
                        
                        // All done
                        if (count($result) >= $max) {
                            break 4;
                        }
                    }
                }

                // Increment the level
                $currentLevel++;
                
                // Off to the next level
                if ($currentLevel > $levels) {
                    break;
                }
            }
        }
        
        // Prepare the final result
        $finalResult = array_unique(array_merge($resultMatch, $resultSiblings));
        
        // Shuffle the result
        shuffle($finalResult);
        
        // All done
        return array_slice($finalResult, 0, $max);
    }
    
    /**
     * Get the word type by the word id
     * 
     * @param int $wordId Word ID
     * @return string Word type, one of <ul>
     * <li>Whisper_Synonym::DATA_ADJ</li>
     * <li>Whisper_Synonym::DATA_ADV</li>
     * <li>Whisper_Synonym::DATA_NOUN</li>
     * </ul>
     */
    protected static function _wordTypeById($wordId) {
        // Try to find the word type
        foreach (array(self::DATA_ADJ, self::DATA_ADV, self::DATA_NOUN) as $dataType) {
            if (in_array($wordId, self::$_data[$dataType])) {
                return $dataType;
            }
        }
        
        // Default to noun
        return self::DATA_NOUN;
    }
    
    /**
     * Get the closest word matches
     * 
     * @param string $word Word to search for in the database
     * @return int[] Word IDs
     */
    protected static function _closestIds($word) {
        // Make the word case insensitive
        $word = strtolower($word);
        
        // Find similar words
        $similarWords = array();

        // Go through the words
        foreach (self::$_data[self::DATA_WORDS] as $wordId => $wordValue) {
            // Get the similarity percent
            similar_text($wordValue, $word, $percent);
            
            // Similar word
            if ($percent >= 90) {
                $similarWords[$wordId] = $percent;
            }
        }
        
        // Found similar words
        if (count($similarWords)) {
            // Arrange the array in descending order of match
            arsort($similarWords);

            // All done
            return $similarWords;
        }
        
        // Found nothing
        return null;
    }
    
    /**
     * Get a random synonym for the given word
     * 
     * @param string $word Word
     * @return string
     */
    public static function getRandom($word = null) {
        // Just get a random noun
        if (null === $word) {
            // Initialize
            self::_init();
            
            // Get a random noun id
            $wordId = self::$_data[self::DATA_NOUN][mt_rand(0, count(self::$_data[self::DATA_NOUN]) - 1)];
            
            // Get the word as string
            return self::$_data[self::DATA_WORDS][$wordId];
        }
        
        // Get an array
        $array = self::getArray($word);
        
        // Return the first item
        return $array[mt_rand(0, count($array) - 1)];
    }
    
    /**
     * Initialize the data store
     * 
     * @return null
     */
    protected static function _init() {
        // Get the data
        if (!isset(self::$_data)) {
            self::$_data = json_decode(file_get_contents(ROOT . '/web/resources/whisper/data/synonyms.json'), true);
        }

        // Get the synonyms keys
        $synonymKeys = array_keys(self::$_data[self::DATA_SYNONYMS]);
        
        // Shuffle them
        shuffle($synonymKeys);
        
        // Prepare the new synonyms
        $newSynonyms = array();
        
        // Re-create the original array
        foreach ($synonymKeys as $key) {
            $newSynonyms[$key] = self::$_data[self::DATA_SYNONYMS][$key];
        }
        
        // Rewrite the data
        self::$_data[self::DATA_SYNONYMS] = $newSynonyms;
    }
}

/* EOF */