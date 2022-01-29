<?php
/**
 * {project.destProjectName} Theme Customizer
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Add postMessage support for site title and description for the Theme Customizer.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function {project.prefix}_customize_register($wp_customize) {
    $wp_customize->get_setting('blogname')->transport         = 'postMessage';
    $wp_customize->get_setting('blogdescription')->transport  = 'postMessage';
    $wp_customize->get_setting('header_textcolor')->transport = 'postMessage';
}
add_action('customize_register', '{project.prefix}_customize_register');

/**
 * Binds JS handlers to make Theme Customizer preview reload changes asynchronously.
 */
function {project.prefix}_customize_preview_js() {
    wp_enqueue_script(
        '{project.prefix}_customizer', 
        St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/js/customizer.js', 
        array('customize-preview'), 
        '{project.versionVerbose}', 
        true
    );
}
add_action('customize_preview_init', '{project.prefix}_customize_preview_js' );

/*EOF*/