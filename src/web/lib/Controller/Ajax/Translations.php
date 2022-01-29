<?php
/**
 * Theme Warlock - Controller_Ajax_Translations
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax_Translations extends Controller_Ajax {

    /**
     * Update a Translations Cache Entry
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function update($translationLang = WordPress_Pot_Translations_Cache::LANG_RO) {
        // Cet the translation cache
        $wpTranslationsCache = WordPress_Pot_Translations_Cache::getInstance($translationLang);
        
        // Get the translation key; need entity decode because of the AJAX handler
        $translationKey = html_entity_decode(trim(Input::getInstance()->postRequest('key')), ENT_QUOTES, "UTF-8");
        
        // Get the translation index
        $translationIndex = intval(trim(Input::getInstance()->postRequest('index')));

        // Get the translation value; need entity decode because of the AJAX handler
        $translationValue = html_entity_decode(trim(Input::getInstance()->postRequest('value')), ENT_QUOTES, "UTF-8");

        // Save the entry
        return $wpTranslationsCache->setEntryTranslation($translationKey, $translationIndex, $translationValue);
    }
    
    /**
     * Delete a Translations Cache Entry
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function delete() {
        foreach (WordPress_Pot_Translations_Cache::getLanguages() as $translationLang) {
            // Cet the translation cache
            $wpTranslationsCache = WordPress_Pot_Translations_Cache::getInstance($translationLang);

            // Get the translation key; need entity decode because of the AJAX handler
            $translationKey = html_entity_decode(trim(Input::getInstance()->postRequest('key')), ENT_QUOTES, "UTF-8");

            // Delete the entry
            $wpTranslationsCache->deleteEntry($translationKey);
        }
    }

}

/* EOF */
