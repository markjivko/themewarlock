<?php
/**
 * Theme Warlock - WordPress_Tags
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Tags {

    /**
     * Subject tags
     */
    const TAG_SUBJECT_BLOG           = 'blog';
    const TAG_SUBJECT_E_COMMERCE     = 'e-commerce';
    const TAG_SUBJECT_EDUCATION      = 'education';
    const TAG_SUBJECT_ENTERTAINMENT  = 'entertainment';
    const TAG_SUBJECT_FOOD_AND_DRINK = 'food-and-drink';
    const TAG_SUBJECT_HOLIDAY        = 'holiday';
    const TAG_SUBJECT_NEWS           = 'news';
    const TAG_SUBJECT_PHOTOGRAPHY    = 'photography';
    const TAG_SUBJECT_PORTFOLIO      = 'portfolio';

    /**
     * Describe the available subject tags
     * 
     * @var array
     * @link https://make.wordpress.org/themes/handbook/review/required/theme-tags/
     */
    protected static $_subjectTags = array(
        self::TAG_SUBJECT_BLOG => array(
            "Classic blog",
            "Theme designed primarily for use on blogs.",
        ),
        self::TAG_SUBJECT_E_COMMERCE => array(
            "E-Commerce",
            "Theme designed for use on e-commerce sites. Should most likely integrate with an e-commerce plugin.",
        ),
        self::TAG_SUBJECT_EDUCATION => array(
            "Education / E-Learning",
            "Theme designed for the use on education-related sites.",
        ),
        self::TAG_SUBJECT_ENTERTAINMENT => array(
            "Entertainment",
            "Theme designed for use on entertainment-related sites (e.g., movies, music, gaming).",
        ),
        self::TAG_SUBJECT_FOOD_AND_DRINK => array(
            "Food and drink (HoReCa)",
            "Themes geared toward food-related web sites, such as restaurants, bars, etc.",
        ),
        self::TAG_SUBJECT_HOLIDAY => array(
            "Holiday",
            "Themes built for seasonal or religious holidays.",
        ),
        self::TAG_SUBJECT_NEWS => array(
            "News",
            "Themes built for the use on news sites.",
        ),
        self::TAG_SUBJECT_PHOTOGRAPHY => array(
            "Photography",
            "Themes built for photobloggers and photographers.",
        ),
        self::TAG_SUBJECT_PORTFOLIO => array(
            "Portfolio",
            "Themes meant for showing off portfolios.",
        ),
    );

    /**
     * Layout with two columns <br/>
     * e.g. Custom page template with content and sidebar
     */
    const TAG_CORE_TWO_COLUMNS           = 'two-columns';

    /**
     * Layout with three columns <br/>
     * e.g. Custom page template with content and two sidebars
     */
    const TAG_CORE_THREE_COLUMNS         = 'three-columns';

    /**
     * Layout with four columns <br/>
     * e.g. Custom page template with content and three sidebars
     */
    const TAG_CORE_FOUR_COLUMNS          = 'four-columns';

    /**
     * Has a left sidebar
     */
    const TAG_CORE_LEFT_SIDEBAR          = 'left-sidebar';

    /**
     * Has a right sidebar
     */
    const TAG_CORE_RIGHT_SIDEBAR         = 'right-sidebar';

    /**
     * Theme has a layout such as Masonry or tiles.
     */
    const TAG_CORE_GRID_LAYOUT           = 'grid-layout';

    /**
     * Uses 'flex-height' and/or 'flex-width' parameter of <b>add_theme_support('custom-header');</b>
     */
    const TAG_CORE_FLEXIBLE_HEADER       = 'flexible-header';

    /**
     * Complies to the accessibility-ready requirements.
     */
    const TAG_CORE_ACCESSIBILITY_READY   = 'accessibility-ready';

    /**
     * BuddyPress elements are properly integrated in the design <br/>
     * @see http://codex.buddypress.org/theme-compatibility/
     */
    const TAG_CORE_BUDDYPRESS            = 'buddypress';

    /**
     * Ability to change background image and color uses <b>add_theme_support('custom-background');</b>
     */
    const TAG_CORE_CUSTOM_BACKGROUND     = 'custom-background';

    /**
     * Ability to customize colors from theme options (customizer) <br/>
     * Important, the color customization must be something different from custom background implementation.
     */
    const TAG_CORE_CUSTOM_COLORS         = 'custom-colors';

    /**
     * Ability to change header image, uses <b>add_theme_support('custom-header');</b>
     */
    const TAG_CORE_CUSTOM_HEADER         = 'custom-header';

    /**
     * Support custom menus, use <b>register_nav_menu()</b>/<b>register_nav_menus()</b>, and <b>wp_nav_menu()</b>.
     */
    const TAG_CORE_CUSTOM_MENU           = 'custom-menu';

    /**
     * Support custom logos, use <b>add_theme_support('custom-logo');</b>.
     */
    const TAG_CORE_CUSTOM_LOGO           = 'custom-logo';

    /**
     * Supports editor style in page & post editor backend, use <b>add_editor_style();</b>
     */
    const TAG_CORE_EDITOR_STYLE          = 'editor-style';

    /**
     * Outputs a featured image, via <b>add_theme_support('post-thumbnails')</b>, <br/>
     * in place of the custom header image via <b>add_theme_support('custom-header')</b>, on single-post view.
     */
    const TAG_CORE_FEATURED_IMAGE_HEADER = 'featured-image-header';

    /**
     * Support feature images on post in blog, use <b>add_theme_support('post-thumbnails')</b>
     */
    const TAG_CORE_FEATURED_IMAGES       = 'featured-images';

    /**
     * Theme supports one or more dynamic sidebars in the footer.
     */
    const TAG_CORE_FOOTER_WIDGETS        = 'footer-widgets';

    /**
     * Ability to add new posts from the site front-end (reference P2 Theme)
     */
    const TAG_CORE_FRONT_PAGE_POST_FORM  = 'front-page-post-form';

    /**
     * Custom page template that uses a one-column design
     */
    const TAG_CORE_FULL_WIDTH_TEMPLATE   = 'full-width-template';

    /**
     * Microformats must be included and be validated.
     */
    const TAG_CORE_MICROFORMATS          = 'microformats';

    /**
     * Support a post formats with clear visual distinction, use <b>add_theme_support('post-formats')</b>
     */
    const TAG_CORE_POST_FORMATS          = 'post-formats';

    /**
     * No visual issues in rtl mode. RTL Tester is used for testing
     */
    const TAG_CORE_RTL_LANGUAGE_SUPPORT  = 'rtl-language-support';

    /**
     * Visually distinctive style for sticky posts.
     */
    const TAG_CORE_STICKY_POST           = 'sticky-post';

    /**
     * Has theme options (customizer).
     */
    const TAG_CORE_THEME_OPTIONS         = 'theme-options';

    /**
     * Supports threaded comments.
     */
    const TAG_CORE_THREADED_COMMENTS     = 'threaded-comments';

    /**
     * All visible texts (front and backend) are internationalized. <br/>
     * The text domain is defined via <b>load_theme_textdomain()</b> and the language folder is added. <br/>
     * The "Text Domain" tag is defined in the header of the style.css
     */
    const TAG_CORE_TRANSLATION_READY     = 'translation-ready';
    
    /**
     * Core Tags
     * 
     * @var string[]
     */
    protected static $_coreTags = null;
    
    /**
     * WordPress_Tags Reflection Class
     * 
     * @var ReflectionClass
     */
    protected static $_reflection = null;
    
    /**
     * Get all available WordPress subject tags
     * 
     * @return string[]
     * @link https://make.wordpress.org/themes/handbook/review/required/theme-tags/
     */
    public static function getSubjectTags() {
        return self::$_subjectTags;
    }
    
    /**
     * Get all available WordPress Core tags
     * 
     * @return string[]
     */
    public static function getCoreTags() {
        // Initialize
        self::_init();
        
        // Core tags not initialized
        if (null === self::$_coreTags) {
            self::$_coreTags = array();
            foreach (self::$_reflection->getConstants() as $constantName => $constantValue) {
                if (preg_match('%^TAG_CORE_%', $constantName)) {
                    self::$_coreTags[] = $constantValue;
                }
            }
        }
        
        // All done
        return self::$_coreTags;
    }
    
    /**
     * Initialization
     */
    protected static function _init() {
        // Prepare the reflection
        if (null == self::$_reflection) {
            self::$_reflection = new ReflectionClass(__CLASS__);
        }
    }
}

/* EOF */