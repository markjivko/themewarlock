<?php
/**
 * Add support for custom layout controls
 * 
 * @link https://developer.wordpress.org/themes/customize-api/
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Register the custom layout controls
 * 
 * @param WP_Customize_Manager $wp_customize WordPress Customize Manager
 */
function {project.prefix}_customize_onepage_layout_register($wp_customize) {

    {call.onepage.customizer._register}

}
add_action('customize_register', '{project.prefix}_customize_onepage_layout_register');

/**
 * Generate the custom CSS
 * 
 * @return string
 */
function {project.prefix}_customize_onepage_layout_css() {
    // Prepare the result
    $result = '';

    {call.onepage.customizer._stylize}

    return $result;
}

/**
 * Enqueue the custom layout CSS
 * 
 * @uses {project.prefix}_customize_onepage_layout_css()
 */
function {project.prefix}_customize_onepage_layout_enqueue_styles() {
    wp_add_inline_style('{project.destDir}-style', {project.prefix}_customize_onepage_layout_css());
}
add_action('wp_enqueue_scripts', '{project.prefix}_customize_onepage_layout_enqueue_styles', 1000);

{if.onepage.layoutToggle}
/**
 * Generate the custom CSS
 * 
 * @return string
 */
function {project.prefix}_get_page_class() {
    // Prepare the result
    $result = 'container';

    {call.onepage.customizer.layout_toggle}
    
    // Full-width container
    if('full-width' === {call.onepage.customizer.layout_toggle.exportVarName}) {
        $result = 'container-fluid';
    }
    
    // All done
    return $result;
}
{/if.onepage.layoutToggle}

/**
 * Generate the custom CSS
 * 
 * @return string
 */
function {project.prefix}_get_rows_class() {
    {if.onepage.rowGuttersToggle}
    // Prepare the result
    $result = 'row';

    {call.onepage.customizer.gutters_rows_toggle}
    
    // Front-page only. Secondary pages must have row gutters for sidebar/content separation
    if ('on' !== {call.onepage.customizer.gutters_rows_toggle.exportVarName}) {
        $result .= ' no-gutters';
    }
    
    // All done
    return $result;
    {/if.onepage.rowGuttersToggle}{else.onepage.rowGuttersToggle}
    // By default, all rows have gutters
    return 'row';
    {/else.onepage.rowGuttersToggle}
}

/*EOF*/