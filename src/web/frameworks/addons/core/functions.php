<?php
/**
 * {project.destProjectName} functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

// WordPress functions caching
require_once get_template_directory() . '/inc/core-cache.php';

// Utilities
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-utils.php';

// #Dependencies#
// Snapshot Manager
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-snapshot-manager.php';

// Custom Fonts
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-custom-fonts.php';

// Custom Colors
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-custom-colors.php';

// Custom template tags
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-template-tags.php';

// Additional features to allow styling of the templates
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-template-functions.php';

// Customizer additions
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-customizer.php';

// Jetpack compatibility
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-jetpack.php';

// Plugin activation using TGM
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/class.tgm-plugin-activation.php';

// Register the required plugins
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-plugins.php';

// Admin Page
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/admin/manager.php';

// Shim for wp_body_open, ensuring backward compatibility with versions of WordPress older than 5.2.
if (!function_exists('wp_body_open')) {
    function wp_body_open() {
        do_action('wp_body_open');
    }
}

// Set-up the theme
if (!function_exists('{project.prefix}_setup')) {
    /**
     * Sets up theme defaults and registers support for various WordPress features.
     *
     * Note that this function is hooked into the after_setup_theme hook, which
     * runs before the init hook. The init hook is too late for some features, such
     * as indicating support for post thumbnails.
     */
    function {project.prefix}_setup() {
        load_theme_textdomain('{project.destDir}', St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/languages');
        
        // Add default posts and comments RSS feed links to head.
        add_theme_support( 'automatic-feed-links' );

        // Hide the Admin Bar
        add_action('wp', function() {show_admin_bar(false);});
        
        /*
         * Let WordPress manage the document title.
         * By adding theme support, we declare that this theme does not use a
         * hard-coded <title> tag in the document head, and expect WordPress to
         * provide it for us.
         */
        add_theme_support('title-tag');

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
         */
        add_theme_support('post-thumbnails');

        /*
         * Switch default markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
        ) );

        // Set up the WordPress custom background feature.
        add_theme_support( 'custom-background', apply_filters( '{project.prefix}_custom_background_args', array(
            'default-color' => 'ffffff',
            'default-image' => '',
        ) ) );

        // Add theme support for selective refresh for widgets.
        add_theme_support( 'customize-selective-refresh-widgets' );
    }
}
add_action('after_setup_theme', '{project.prefix}_setup');

if (!function_exists('{project.prefix}_content_width')) {
    /**
     * Set the content width in pixels, based on the theme's design and stylesheet
     */
    function {project.prefix}_content_width() {
        $GLOBALS['content_width'] = apply_filters('{project.prefix}_content_width', 1140);
    }
}
add_action('after_setup_theme', '{project.prefix}_content_width', 0);

if (!function_exists('{project.prefix}_widgets_init')) {
    /**
     * Register widget areas
     */
    function {project.prefix}_widgets_init() {
        
    }
}
add_action('widgets_init', '{project.prefix}_widgets_init');

if (!function_exists('{project.prefix}_scripts')) {
    /**
     * Enqueue scripts and styles
     */
    function {project.prefix}_scripts() {
        {utils.common.enqueueScripts}

        // Enqueue the main stylesheet
        wp_enqueue_style(
            '{project.destDir}-style', 
            get_stylesheet_directory_uri() . '/' . (is_rtl() ? 'style-rtl.css' : 'style.css'), 
            array(), 
            '{project.versionVerbose}'
        );

        // Enqueue the navigation js script
        wp_enqueue_script(
            '{project.destDir}-navigation', 
            St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/js/navigation.js', 
            array(), 
            '{project.versionVerbose}', 
            true
        );

        // Enqueue the skip link focus fix js script
        wp_enqueue_script(
            '{project.destDir}-skip-link-focus-fix', 
            St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/js/skip-link-focus-fix.js', 
            array(), 
            '{project.versionVerbose}', 
            true
        );

        // Finally, enqueue the main functions
        wp_enqueue_script(
            '{project.destDir}-functions', 
            St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/js/functions.js', 
            array('jquery'), 
            '{project.versionVerbose}', 
            true
        );
        wp_localize_script(
            '{project.destDir}-functions', 
            '{project.prefix}',
            array(
{if.core.addonEnabled.core-custom-menu}{if.core.useStoryline}
                'storyline_build_menu' => St_CoreCache::get(St_CoreCache::ST_CUSTOM_MENU_DYNAMIC),
{/if.core.useStoryline}{/if.core.addonEnabled.core-custom-menu}
            )
        );

        if (is_singular() && comments_open() && get_option('thread_comments')) {
            wp_enqueue_script('comment-reply');
        }
    }
}
add_action('wp_enqueue_scripts', '{project.prefix}_scripts', 999);

if (!function_exists('{project.prefix}_pingback_header')) {
    /**
     * Add a pingback url auto-discovery header for singularly identifiable articles.
     */
    function {project.prefix}_pingback_header() {
        if (is_singular() && pings_open()) {
            echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
        }
    }
}
add_action('wp_head', '{project.prefix}_pingback_header');

// Allow user edits
add_editor_style(array("style.css"));

/*EOF*/