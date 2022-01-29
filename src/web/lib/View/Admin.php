<?php
/**
 * Theme Warlock - View_Admin
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class View_Admin extends WebView {
    
    // Parts
    const PART_ADMIN_INDEX   = 'admin/index';
    const PART_ADMIN_PROJECT = 'admin/project';
    const PART_ADMIN_TRANSLATIONS = 'admin/translations';
    
    // Placeholders
    const PH_PROJECT      = 'project';
    const PH_PAGINATION   = 'pagination';
    const PH_CURRENT_USER = 'currentUser';
    const PH_QUERY_USER   = 'queryUser';
    const PH_ERROR        = 'error';
    
    const PH_TR_LANGUAGES = 'trLanguages';
    const PH_TR_ENTRIES   = 'trEntries';
    const PH_TR_NUMBER    = 'trNumberOfTranslations';
    const PH_TR_LANGUAGE  = 'trCurrentLanguage';
    const PH_TR_ERRORS    = 'trErrors';
}

/*EOF*/