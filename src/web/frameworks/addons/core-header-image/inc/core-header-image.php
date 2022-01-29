<?php
/**
 * Add support for custom header image and colors
 * 
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Set up the WordPress custom header feature.
 *
 * @uses {project.prefix}_header_style()
 */
function {project.prefix}_custom_header_setup() {
    add_theme_support('custom-header', 
        apply_filters(
            '{project.prefix}_custom_header_args', 
            array(
                'default-image'          => '',
                'default-text-color'     => {utils.color.wp.textColor},
                'width'                  => {addon.width},
                'height'                 => {addon.height},
                'flex-height'            => {addon.flexHeight},
                'flex-width'             => {addon.flexWidth},
                'wp-head-callback'       => '{project.prefix}_header_style',
            )
        )
    );
}
add_action('after_setup_theme', '{project.prefix}_custom_header_setup');

if (!function_exists('{project.prefix}_header_style')) {
    /**
     * Styles the header image and text displayed on the blog.
     *
     * @see {project.prefix}_custom_header_setup().
     */
    function {project.prefix}_header_style() {
        // Get the header text color
        $headerTextColor = get_header_textcolor();

        /*
         * If no custom options for text are set, let's bail.
         * get_header_textcolor() options: Any hex value, 'blank' to hide text. Default: add_theme_support( 'custom-header' ).
         */
        if (get_theme_support('custom-header', 'default-text-color') === $headerTextColor ) {
            return;
        }

        // If we got this far, we have custom styles
        echo '<style type="text/css">';
        
        // Has the text been hidden?
        if (!display_header_text()) {
            echo '.site-title, .site-description {position: absolute; clip: rect(1px, 1px, 1px, 1px);}';
        } else {
            echo '.site-title a, .site-description {color: #' . esc_attr($headerTextColor) . ';}';
        }
        
        // Close the style tag
        echo '</style>';
    }
}

/*EOF*/