<?php
/**
 * Theme Warlock - Whisper_Inspiration
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Whisper_Inspiration {

    // Quotes contents
    const Q_AUTHOR = 'author';
    const Q_TEXT   = 'text';
    const Q_ID     = 'id';
    
    /**
     * Quotes database (author => [quotes,...])
     * 
     * @var array
     */
    protected static $_data = array();
    
    /**
     * Word to get the quotes for
     * 
     * @param string $word Word
     * @param int    $max  Maximum number of results
     * @return string[] Quotes array as (author, quote)
     */
    public static function getQuotes($word, $max = 10) {
        // Initialize
        self::_init();
        
        // Prepare the result
        $result = array();
        
        // Go through each quote
        foreach (self::$_data as $quote) {
            // Found the key word
            if (preg_match('%\b' . preg_quote($word) . '\b%i', $quote[self::Q_TEXT])) {
                $result[] = $quote;
            }

            // Stop here
            if (count($result) >= $max) {
                break;
            }
        }
        
        // All done
        return array_values(array_slice($result, 0, $max));
    }
    
    /**
     * Get a single quote
     * 
     * @param type $word
     * @return array Quote as {Whisper_Inspiration::Q_AUTHOR =>, Whisper_Inspiration::Q_TEXT =>}
     */
    public static function getQuote($word = null) {
        // Get the quotes
        $quotes = self::getQuotes($word);
        
        // No item found
        if (!count($quotes)) {
            return array();
        }
        
        // Get the first quote
        return $quotes[mt_rand(0, count($quotes) - 1)];
    }
    
    /**
     * Remove a quote from the database
     * 
     * @param array $quote Quote as {Whisper_Inspiration::Q_AUTHOR =>, Whisper_Inspiration::Q_TEXT =>}
     * @return boolean
     */
    public static function removeQuote(Array $quote) {
        // ID not set
        if (!isset($quote[self::Q_ID])) {
            return false;
        }
        
        // Initialize
        self::_init();

        // Remove the item
        self::$_data = array_values(array_filter(self::$_data, function($item) use ($quote) {
            // Remove the selected quote by ID
            if ($quote[self::Q_ID] == $item[self::Q_ID]) {
                return false;
            }
            return true;
        }));
        
        // Prepare the authors
        $quotesDb = array();
        
        // Go through the quotes
        foreach (self::$_data as $quote) {
            // Prepare the author DB
            if (!isset($quotesDb[$quote[self::Q_AUTHOR]])) {
                $quotesDb[$quote[self::Q_AUTHOR]] = array();
            }
            
            // Append the quote
            $quotesDb[$quote[self::Q_AUTHOR]][] = $quote[self::Q_TEXT];
        }
        
        // Save the data
        file_put_contents(ROOT . '/web/resources/whisper/data/quotes.json', json_encode($quotesDb));

        // All done
        return true;
    }

    /**
     * Initialize the data store
     * 
     * @return null
     */
    protected static function _init() {
        // Get the data
        if (!count(self::$_data)) {
            // Get the JSON Information
            $data = json_decode(file_get_contents(ROOT . '/web/resources/whisper/data/quotes.json'), true);
            
            // Prepare the data array
            self::$_data = array();
            
            // Parse the stored info
            foreach ($data as $quoteAuthor => $quoteTexts) {
                foreach ($quoteTexts as $quoteText) {
                    // Get the ID
                    $id = count(self::$_data);
                    
                    // Append the information
                    self::$_data[] = array(
                        self::Q_AUTHOR => $quoteAuthor,
                        self::Q_TEXT   => $quoteText,
                        self::Q_ID     => $id,
                    );
                }
            }
        }

        // Shuffle the quotes
        shuffle(self::$_data);
    }
}

/* EOF */