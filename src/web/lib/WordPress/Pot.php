<?php
/**
 * Theme Warlock - WordPress_Pot
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */
class WordPress_Pot {

    /**
     * WordPress Internationalization functions
     */
    const I18N_U                    = '_';
    const I18N_U2                   = '__';
    const I18N_U_E                  = '_e';
    const I18N_U_C                  = '_c';
    const I18N_U_N                  = '_n';
    const I18N_U_N_NOOP             = '_n_noop';
    const I18N_U_NC                 = '_nc';
    const I18N_U2_NGETTEXT          = '__ngettext';
    const I18N_U2_NGETTEXT_NOOP     = '__ngettext_noop';
    const I18N_U_X                  = '_x';
    const I18N_U_EX                 = '_ex';
    const I18N_U_NX                 = '_nx';
    const I18N_U_NX_NOOP            = '_nx_noop';
    const I18N_U_N_JS               = '_n_js';
    const I18N_U_NX_JS              = '_nx_js';
    const I18N_ESC_ATTR_U2          = 'esc_attr__';
    const I18N_ESC_HTML_U2          = 'esc_html__';
    const I18N_ESC_ATTR_E           = 'esc_attr_e';
    const I18N_ESC_HTML_E           = 'esc_html_e';
    const I18N_ESC_ATTR_X           = 'esc_attr_x';
    const I18N_ESC_HTML_X           = 'esc_html_x';
    const I18N_COMMENTS_NUMBER_LINK = 'comments_number_link';

    /**
     * Known .pot headers
     */
    const HEADER_PLURAL                    = 'Plural-Forms';
    const HEADER_PROJECT_ID_VERSION        = 'Project-Id-Version';
    const HEADER_REPORT_MSGID_BUGS_TO      = 'Report-Msgid-Bugs-To';
    const HEADER_POT_CREATION_DATE         = 'POT-Creation-Date';
    const HEADER_MIME_VERSION              = 'MIME-Version';
    const HEADER_CONTENT_TYPE              = 'Content-Type';
    const HEADER_CONTENT_TRANSFER_ENCODING = 'Content-Transfer-Encoding';
    const HEADER_PO_REVISION_DATE          = 'PO-Revision-Date';
    const HEADER_LAST_TRANSLATOR           = 'Last-Translator';
    const HEADER_LANGUAGE                  = 'Language';
    const HEADER_LANGUAGE_TEAM             = 'Language-Team';
    const HEADER_X_GENERATOR               = 'X-Generator';
    
    /**
     * WordPress i18n methods and their extraction rules
     * 
     * @var array
     */
    protected $_rules = array(
        self::I18N_U                    => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_U2                   => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_U_E                  => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_U_C                  => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_U_N                  => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL),
        self::I18N_U_N_NOOP             => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL),
        self::I18N_U_NC                 => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL),
        self::I18N_U2_NGETTEXT          => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL),
        self::I18N_U2_NGETTEXT_NOOP     => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL),
        self::I18N_U_X                  => array(WordPress_Pot_Translations_Entry::ARG_STRING, WordPress_Pot_Translations_Entry::ARG_CONTEXT),
        self::I18N_U_EX                 => array(WordPress_Pot_Translations_Entry::ARG_STRING, WordPress_Pot_Translations_Entry::ARG_CONTEXT),
        self::I18N_U_NX                 => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL, null, WordPress_Pot_Translations_Entry::ARG_CONTEXT),
        self::I18N_U_NX_NOOP            => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL, WordPress_Pot_Translations_Entry::ARG_CONTEXT),
        self::I18N_U_N_JS               => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL),
        self::I18N_U_NX_JS              => array(WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL, WordPress_Pot_Translations_Entry::ARG_CONTEXT),
        self::I18N_ESC_ATTR_U2          => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_ESC_HTML_U2          => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_ESC_ATTR_E           => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_ESC_HTML_E           => array(WordPress_Pot_Translations_Entry::ARG_STRING),
        self::I18N_ESC_ATTR_X           => array(WordPress_Pot_Translations_Entry::ARG_STRING, WordPress_Pot_Translations_Entry::ARG_CONTEXT),
        self::I18N_ESC_HTML_X           => array(WordPress_Pot_Translations_Entry::ARG_STRING, WordPress_Pot_Translations_Entry::ARG_CONTEXT),
        self::I18N_COMMENTS_NUMBER_LINK => array(WordPress_Pot_Translations_Entry::ARG_STRING, WordPress_Pot_Translations_Entry::ARG_SINGULAR, WordPress_Pot_Translations_Entry::ARG_PLURAL),
    );
    
    /**
     * Extractor
     * 
     * @var WordPress_Pot_StringExtractor
     */
    protected $_extractor = null;
    
    /**
     * WordPress POT handler
     */
    public function __construct() {
        // Set the Extractor
        $this->_extractor = new WordPress_Pot_StringExtractor(
            $this->_rules 
        );
    }

    /**
     * Extract all the WordPress gettext values
     * 
     * @param string $pathToTheme Path to the WordPress theme directory
     * @param string $textDomain  Text Domain
     * @param string $potFilePath (optional) Path to the final ".pot" file
     * @param string $fileVersion (optional) File version
     * @return WordPress_Pot_Translations_Po Extracted Translations object
     */
    public function extract($pathToTheme, $textDomain, $potFilePath = null, $fileVersion = null) {
        // Set the text domain restriction
        $this->_extractor->setTextDomain($textDomain);

        // Extract the original entries
        $originals = $this->_extractor->extractFromDirectory($pathToTheme);

        // Prepare a new translator object
        $translationsPo = new WordPress_Pot_Translations_Po($originals->entries);

        // Prepare the copyright header
        $copyright = 'Copyright (c) ' . date('Y') . ' ' . Config::get()->authorName . PHP_EOL . PHP_EOL
            . Addons_Utils::getInstance(Model_Project_Config::CATEGORY_CORE)->common(Addons_Utils::COMMON_QUOTE) . PHP_EOL . PHP_EOL
            . 'Distributed under the GNU General Public License v3 or later.';
        
        // Prepare the theme name
        $themeName = ucwords(preg_replace('%\W+%', ' ', $textDomain));
        
        // Prepare the theme version
        if (null === $fileVersion) {
            $fileVersion = '1.0.0';
            if (isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_VERSION])) {
                $fileVersion = Tasks_1NewProject::getVerboseVersion();
            }
        }
        
        // Set the headers
        $translationsPo->setHeaders(array(
            self::HEADER_PROJECT_ID_VERSION        => $themeName . ' v.' . $fileVersion,
            self::HEADER_POT_CREATION_DATE         => gmdate('Y-m-d H:i:s+00:00'),
            self::HEADER_PO_REVISION_DATE          => gmdate('Y-m-d H:i:s+00:00'),
            self::HEADER_LAST_TRANSLATOR           => Config::get()->authorName,
            self::HEADER_LANGUAGE_TEAM             => Config::get()->authorName,
            self::HEADER_MIME_VERSION              => '1.0',
            self::HEADER_CONTENT_TYPE              => 'text/plain; charset=UTF-8',
            self::HEADER_CONTENT_TRANSFER_ENCODING => '8bit',
            self::HEADER_REPORT_MSGID_BUGS_TO      => Config::get()->authorUrl,
            self::HEADER_X_GENERATOR               => $textDomain,
            self::HEADER_PLURAL                    => 'nplurals=NUMBER; plural=EXPRESSION;'
        ));
        
        // Set the comments
        $translationsPo->setCommentBeforeHeaders($copyright);
        
        // Set the textdomain
        $translationsPo->setTextDomain($textDomain);

        // Export to .pot file
        if (null !== $potFilePath && strlen($potFilePath) && preg_match('%\.pot$%', $potFilePath) && is_dir(dirname($potFilePath))) {
            $translationsPo->exportToFile($potFilePath);
        }
        
        // Get the object
        return $translationsPo;
    }
}

/* EOF */