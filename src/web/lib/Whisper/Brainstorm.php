<?php
/**
 * Theme Warlock - Whisper_Brainstorm
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Whisper_Brainstorm {
    
    const WORD_TYPE_NOUN = 'noun';
    const WORD_TYPE_ADJECTIVE = 'adjective';
    const WORD_TYPE_PHRASE = 'phrase';
    
    // Color indexes
    const COLOR_RED    = 1;
    const COLOR_BROWN  = 2;
    const COLOR_ORANGE = 3;
    const COLOR_YELLOW = 4;
    const COLOR_GREEN  = 5;
    const COLOR_BLUE   = 6;
    const COLOR_VIOLET = 7;
    const COLOR_PINK   = 8;
    const COLOR_WHITE  = 9;
    const COLOR_GREY   = 10;
    const COLOR_BLACK  = 11;
    
    /**
     * Allowed word types
     *
     * @var string[]
     */
    public static $wordTypes = array(
        self::WORD_TYPE_NOUN,
        self::WORD_TYPE_ADJECTIVE,
        self::WORD_TYPE_PHRASE,
    );
    
    /**
     * Color names
     * 
     * @var array
     */
    public static $colorNames = array(
        self::COLOR_RED    => 'red',
        self::COLOR_BROWN  => 'brown',
        self::COLOR_ORANGE => 'orange',
        self::COLOR_YELLOW => 'yellow',
        self::COLOR_GREEN  => 'green',
        self::COLOR_BLUE   => 'blue',
        self::COLOR_VIOLET => 'violet',
        self::COLOR_PINK   => 'pink',
        self::COLOR_WHITE  => 'white',
        self::COLOR_GREY   => 'grey',
        self::COLOR_BLACK  => 'black',
    );
    
    /**
     * Color definitions
     * 
     * @var array
     */
    public static $colorDefinitionsHsl = array (
        self::COLOR_RED    => array(array(335, 25), array(0.4, 1), array(0.4, 0.6)),
        self::COLOR_BROWN  => array(array(15, 35), array(0.4, 1), array(0.2, 0.5)),
        self::COLOR_ORANGE => array(array(15, 50), array(0.4, 1), array(0.4, 0.7)),
        self::COLOR_YELLOW => array(array(50, 65), array(0.4, 1), array(0.5, 1)),
        self::COLOR_GREEN  => array(array(65, 175), array(0.4, 1), array(0.1, 1)),
        self::COLOR_BLUE   => array(array(175, 275), array(0.4, 1), array(0.1, 1)),
        self::COLOR_VIOLET => array(array(275, 335), array(0.4, 1), array(0.1, 0.7)),
        self::COLOR_PINK   => array(array(325, 335), array(0.4, 1), array(0.7, 1)),
        self::COLOR_WHITE  => array(array(0, 360), array(0, 0.4), array(0.9, 1)),
        self::COLOR_GREY   => array(array(0, 360), array(0, 0.4), array(0.2, 0.9)),
        self::COLOR_BLACK  => array(array(0, 360), array(0, 0.4), array(0, 0.2)),
    );
    
    /**
     * Whisper Builder instance
     *
     * @var Whisper_Builder
     */
    protected static $_instance;
    
    /**
     * Image instance
     * 
     * @var Image
     */
    protected static $_image;
    
    /**
     * Nouns
     * 
     * @var array
     */
    protected static $_nouns = array();
    
    /**
     * Priority Nouns
     * 
     * @var array
     */
    protected static $_nounsPriority = array();
    
    /**
     * Adjectives
     * 
     * @var array
     */
    protected static $_adjectives = array();
    
    /**
     * Priority Adjectives
     * 
     * @var array
     */
    protected static $_adjectivesPriority = array();
    
    /**
     * Phrases
     *
     * @var array 
     */
    protected static $_phrases = array();
    
    /**
     * Whisper Brainstorm
     */
    protected function __construct() {
        // Get the image instance
        if (!isset(self::$_image)) {
            self::$_image = new Image();
        }
        
        // Get the words
        foreach (array('nouns', 'adjectives') as $wordType) {
            // Get the default list
            self::${'_' . $wordType} = json_decode(file_get_contents(ROOT . '/web/resources/whisper/data/' . $wordType . '.json'), true);
            
            // Get the priority list
            self::${'_' . $wordType . 'Priority'} = json_decode(file_get_contents(ROOT . '/web/resources/whisper/data/priority_' . $wordType . '.json'), true);
        }
        
        // Store the phrases
        self::$_phrases = json_decode(file_get_contents(ROOT . '/web/resources/whisper/data/phrases.json'), true);
    }
    
    /**
     * Whisper Brainstorm
     * 
     * @return Whisper_Brainstorm
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        
        return self::$_instance;
    }
    
    /**
     * Remove a word from the database
     * 
     * @param string $word Word
     * @throws Exception
     */
    public function remove($word) {
        // Word not defined
        if (!isset(self::$_nouns[$word]) && !isset(self::$_adjectives[$word]) && !isset(self::$_nounsPriority[$word]) && !isset(self::$_adjectivesPriority[$word])&& !isset(self::$_phrases[$word])) {
            throw new Exception('Word "' . $word . '" not found');
        }
        
        // Unset
        unset(self::$_nouns[$word]);
        unset(self::$_adjectives[$word]);
        unset(self::$_nounsPriority[$word]);
        unset(self::$_adjectivesPriority[$word]);
        unset(self::$_phrases[$word]);
        
        // Save the data
        $this->_save();
    }
    
    /**
     * Check if word already defined
     * 
     * @param string $word Word/idiom
     * @return array Array of (array types, boolean priority, array colors)
     */
    public function check($word) {
        // Get the priority flag
        $priority = false;
        
        // Get the type
        $types = array();
        
        // Get the colors
        $colors = array();
        
        do {
            // Defined as a phrase
            if (isset(self::$_phrases[$word])) {
                // Set the type
                $types[] = self::WORD_TYPE_PHRASE;

                // Set the colors
                $colors = self::$_phrases[$word]['c'];
                
                // Don't go further
                break;
            }

            // Defined as a noun
            if (isset(self::$_nouns[$word])) {
                // Set the type
                $types[] = self::WORD_TYPE_NOUN;
                
                // Set the colors
                $colors = self::$_nouns[$word]['c'];
            }
            
            // Defined as a priority noun
            if (isset(self::$_nounsPriority[$word])) {
                // Set the priority
                $priority = true;
                
                // Set the type
                $types[] = self::WORD_TYPE_NOUN;
                
                // Set the colors
                $colors = self::$_nounsPriority[$word]['c'];
            }

            // Defined as an adjective
            if (isset(self::$_adjectives[$word])) {
                // Set the type
                $types[] = self::WORD_TYPE_ADJECTIVE;
                
                // Set the colors
                $colors = self::$_adjectives[$word]['c'];
            }
            
            // Defined as a priority adjective
            if (isset(self::$_adjectivesPriority[$word])) {
                // Set the priority
                $priority = true;
                
                // Set the type
                $types[] = self::WORD_TYPE_ADJECTIVE;
                
                // Set the colors
                $colors = self::$_adjectivesPriority[$word]['c'];
            }
            
            // Unique types
            $types = array_values(array_unique($types));
        } while(false);
        
        // Translate the colors
        foreach ($colors as $colorKey => $colorId) {
            $colors[] = self::$colorNames[$colorId];
            unset($colors[$colorKey]);
        }
        
        // All done
        return array(
            $types,
            $priority,
            $colors,
        );
    }
    
    /**
     * Associate a word with a color
     * 
     * @param string   $word     Word
     * @param string[] $colors   Colors
     * @param string[] $types    Word types
     * @param boolean  $priority Word priority
     */
    public function associate($word, Array $colors, Array $types, $priority = false) {
        // Get the color IDs
        $colorIds = array();
        
        // Colors are in $colorNames
        foreach ($colors as $colorName) {
            if (!in_array($colorName, self::$colorNames)) {
                throw new Exception('Invalid color "' . $colorName . '"');
            }
            
            // Get the color ID
            $colorIds[] = array_search($colorName, self::$colorNames);
        }
        
        // Remove old associations
        unset(self::$_nouns[$word]);
        unset(self::$_adjectives[$word]);
        unset(self::$_nounsPriority[$word]);
        unset(self::$_adjectivesPriority[$word]);
        unset(self::$_phrases[$word]);
        
        // Validate the types
        foreach ($types as $type) {
            if (!in_array($type, self::$wordTypes)) {
                throw new Exception('Word type "' . $type . '" not recognized');
            }
            
            switch ($type) {
                case self::WORD_TYPE_NOUN:
                    if ($priority) {
                        self::$_nounsPriority[$word] = array(
                            'c' => $colorIds,
                        );
                    } else {
                        self::$_nouns[$word] = array(
                            'c' => $colorIds,
                        );
                    }
                    break;
                    
                case self::WORD_TYPE_ADJECTIVE:
                    if ($priority) {
                        self::$_adjectivesPriority[$word] = array(
                            'c' => $colorIds,
                        );
                    } else {
                        self::$_adjectives[$word] = array(
                            'c' => $colorIds,
                        );
                    }
                    break;
                    
                case self::WORD_TYPE_PHRASE:
                    self::$_phrases[$word] = array(
                        'c' => $colorIds,
                    );
                    break;
            }
        }
        
        // Save the data
        $this->_save();
    }
    
    /**
     * Get a random nuance as RGB
     * 
     * @param int     $colorId Color ID
     * @param boolean Hex - for HEX color
     * @return array|string RGB or HEX Color
     */
    public function getRandomNuance($colorId, $hex = false) {
        if (!isset(self::$colorNames[$colorId])) {
            return null;
        }
        
        // Get the color defintion
        $colorDefinition = self::$colorDefinitionsHsl[$colorId];
        
        // Get the hue range
        if ($colorDefinition[0][0] > $colorDefinition[0][1]) {
            $rangeH = array_merge(range($colorDefinition[0][0], 360), range(0, $colorDefinition[0][1]));
        } else {
            $rangeH = range($colorDefinition[0][0], $colorDefinition[0][1]);
        }

        // Get the saturation range
        $rangeS = range(intval(100 * $colorDefinition[1][0]), intval(100 * $colorDefinition[1][1]));

        // Get the value range
        $rangeV = range(intval(100 * $colorDefinition[2][0]), intval(100 * $colorDefinition[2][1]));
        
        // Shuffle all the ranges
        shuffle($rangeH);
        shuffle($rangeS);
        shuffle($rangeV);
        
        // Prepare the new color
        $rgbArray = self::$_image->hslToRgb(array(
            array_shift($rangeH),
            array_sum($rangeS) / count($rangeS) / 100,
            array_sum($rangeV) / count($rangeV) / 100,
        ));
        
        // Hex value
        if ($hex) {
            return self::$_image->rgbToHex($rgbArray);
        }
        
        // All done
        return $rgbArray;
    }
    
    /**
     * Get a suggestions array based on the input image
     * 
     * @param string/array $imagePathOrDetails String (image location) or Array([resource, boolean - ninePatch])
     * @param int          $numOfWords         Number of words (1 to 2)
     * @param string       $maxLength          Maximum length of string
     * @param string[]     $avoidList          Names to avoid
     * @param int          $repeats            Number of repeats (final size of the suggestions)
     * @return string[] Suggestion based on the dominant colors in the image
     * @throws Exception
     */
    public function getSuggestions($imagePathOrDetails, $numOfWords = null, $maxLength = 20, Array $avoidList = array(), $repeats = 5) {
        // Validate the words
        $numOfWords = $numOfWords >= 1 && $numOfWords <= 2 ? $numOfWords : null;
        
        // Clean-up the list
        if (count($avoidList)) {
            $avoidList = array_map(function($item){
                return trim(strtolower(preg_replace('%\s+%ims', ' ', $item)));
            }, $avoidList);
        }
        
        // Validate the path
        if (!is_string($imagePathOrDetails)) {
            if (!isset($imagePathOrDetails[0]) || !is_resource($imagePathOrDetails[0])) {
                throw new Exception('The image details must be an array of [resource, boolean - ninePatch]');
            }
        } else {
            if (!file_exists($imagePathOrDetails)) {
                throw new Exception('The image path is not valid');
            }
        }
        
        // Get the colors
        $colorDefs = self::$_image->getColorsHsl($imagePathOrDetails, self::$colorDefinitionsHsl);

        // Prepare random colors if none found
        if (!count($colorDefs)) {
            // Get the color IDs
            $colorIds = array_keys(self::$colorNames);
            
            // Shuffle them
            shuffle($colorIds);
            
            // Extract 5 random IDs
            for ($i = 1; $i <= 5; $i++) {
                $colorDefs[] = array(array_shift($colorIds), 0.2);
            }
        }
        
        // Get the found colors
        $colors = array();
        
        // Store the colors as nouns
        foreach ($colorDefs as $colorDef) {
            $colors[] = self::$colorNames[$colorDef[0]];
        }

        // Prepare the final result
        $finalResult = array();
            
        // How many times?
        for ($repeatsIncrement = 1; $repeatsIncrement <= $repeats;) {
            // Get the final number of words
            $numOfWordsFinal = null === $numOfWords ? $this->_priorityRandomPick(array(2, 2, 1)) : $numOfWords;
        
            do {
                // Prepare the result
                $result = '';

                // Prepare the nouns
                $nouns = array();

                // Prepare the adjectives
                $adjectives = array();

                // Prepare the phrases
                $phrases = array();
                
                // Go through the lists
                foreach (array('nouns', 'adjectives') as $listType) {
                    // Get the data
                    $listData = self::${'_' . $listType};

                    // Get the priority data
                    $listDataPriority = self::${'_' . $listType . 'Priority'};
                    
                    // Get the keys
                    $listDataKeys = array_keys($listData);
                    
                    // Get the priority keys
                    $listDataKeysPriority = array_keys($listDataPriority);

                    // Shuffle it
                    shuffle($listDataKeys);
                    shuffle($listDataKeysPriority);

                    // Go through the IDs
                    foreach ($colorDefs as $colorDef) {
                        // Get the ID
                        list($colorId) = $colorDef;

                        // Prepare the data pool 
                        // TODO set one of the items as "Priority" when the associations are done
                        $pool = $this->_priorityRandomPick(array('', ''));

                        // Add the items
                        foreach (${'listDataKeys' . $pool} as $word) {
                            // Color is defined
                            if (count(${'listData' . $pool}[$word]['c']) && in_array($colorId, ${'listData' . $pool}[$word]['c'])) {
                                // Valid word
                                if (empty($word)) {
                                    continue;
                                }
                                
                                // Randomly pluralize the nouns
                                // 25% chance for titles, 90% chance for the "inspiration" list
                                if ('nouns' == $listType && mt_rand(1, 100) <= ((1 == $repeats) ? 25 : 90)) {
                                    $word = Inflect::pluralize($word);
                                }
                            
                                // Append the word
                                if (!in_array($word, $nouns) && !in_array($word, $adjectives)) {
                                    ${$listType}[] = $word;
                                    break;
                                }
                            }
                        }
                    }
                }
                
                // Get the keys
                $phrasesKeys = array_keys(self::$_phrases);

                // Shuffle it
                shuffle($phrasesKeys);

                // Go through the IDs
                foreach ($colorDefs as $colorDef) {
                    // Get the ID
                    list($colorId) = $colorDef;

                    // Add the items
                    foreach ($phrasesKeys as $phrase) {
                        // Color is defined
                        if (count(self::$_phrases[$phrase]['c']) && in_array($colorId, self::$_phrases[$phrase]['c'])) {
                            // Valid word
                            if (empty($phrase)) {
                                continue;
                            }

                            // Append the word
                            if (!in_array($phrase, $phrases)) {
                                $phrases[] = $phrase;
                                break;
                            }
                        }
                    }
                }

                // Get the probabilities
                switch ($numOfWordsFinal) {
                    case 1:
                        // Prepare the holders and their probability ranges
                        $holders = array(
                            'colors' => array(0, 9), 
                            'nouns'  => array(10, 54), 
                            'adjectives' => array(55, 100)
                        );

                        // Get a number
                        $seed = mt_rand(1, 100);

                        // Get the holder
                        foreach ($holders as $holderName => $holderProbabilityRange) {
                            if ($seed >= $holderProbabilityRange[0] && $seed <= $holderProbabilityRange[1]) {
                                // Get the holder data
                                $holderData = ${$holderName};

                                // Set the result
                                $result = $this->_priorityRandomPick($holderData);

                                // Stop here
                                break;
                            }
                        }
                        break;

                    case 2:
                        // A random noun
                        $randomNoun = $this->_priorityRandomPick($nouns);

                        // Get a random adjective
                        $randomAdjective = $this->_priorityRandomPick(array(
                            $this->_priorityRandomPick($adjectives),
                            $this->_priorityRandomPick($adjectives),
                            $this->_priorityRandomPick($colors)
                        ));

                        // Prepare the result
                        $composedPhrase = $randomAdjective . ' ' . $randomNoun;
                        
                        // Prepare the result
                        $result = $this->_priorityRandomPick(array(
                            $composedPhrase,
                            $composedPhrase,
                            /* $phrases[mt_rand(0, count($phrases) - 1)] TODO, uncomment when idioms are done*/
                        ));
                        break;
                }

                // Validate length
                if (strlen($result) <= $maxLength && !in_array(strtolower($result), $avoidList)) {
                    break;
                }
            } while (true);
            
            // Append to the final result
            if (!in_array($result, $finalResult)) {
                // Append unique result
                $finalResult[] = $result;
                
                // Allow for increment
                $repeatsIncrement++;
            }
        }
        
        // All done
        return $finalResult;
    }
    
    /**
     * Save the current data
     * 
     * @return null
     */
    protected function _save() {
        // Save the words
        foreach (array('nouns', 'adjectives') as $wordType) {
            // Set the data
            file_put_contents(ROOT . '/web/resources/whisper/data/' . $wordType . '.json', json_encode(self::${'_' . $wordType}));
            
            // Set the priority data
            file_put_contents(ROOT . '/web/resources/whisper/data/priority_' . $wordType . '.json', json_encode(self::${'_' . $wordType . 'Priority'}));
        }
        
        // Save the phrases
        file_put_contents(ROOT . '/web/resources/whisper/data/phrases.json', json_encode(self::$_phrases));
    }
    
    /**
     * Get a random value from an array based on the position in the array
     * First item has the highest probability of being picked, the last item the lowest
     * 
     * @param array $data Data
     * @return mixed Random value
     */
    protected function _priorityRandomPick(Array $data = array()) {
        if (!count($data)) {
            return null;
        }
        
        // Get the data length
        $length = count($data);
        
        // Get the total spots
        $total = $length * ($length + 1) / 2;
        
        // Prepare the range start
        $rangeStart = 1;
        
        // Get the seed
        $seed = mt_rand($rangeStart, $total);
        
        // Go through the data
        foreach (array_keys($data) as $i => $key) {
            // Get the range length
            $rangeLength = $length - $i - 1;
            
            // Get the range end
            $rangeEnd = $rangeStart + $rangeLength;
            
            // Value found
            if (in_array($seed, range($rangeStart, $rangeEnd))) {
                return $data[$key];
            }
            
            // Get the range start for the next batch
            $rangeStart = $rangeEnd + 1;
        }
        
        // Not defined
        return null;
    }
}

/* EOF */