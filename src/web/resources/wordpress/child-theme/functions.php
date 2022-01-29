<?php
/**
 * {project.destProjectName} - Child Theme
 *
 * @link https://developer.wordpress.org/themes/advanced-topics/child-themes/
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Enqueue child theme scripts and styles
 */
function {project.prefix}_child_scripts() {
    wp_enqueue_style('{project.destDir}-style', get_parent_theme_file_uri() . '/style.css', array(), '{project.versionVerbose}');
    wp_enqueue_style('{project.destDir}-child-style', get_stylesheet_directory_uri() . '/style.css', array('{project.destDir}-style'), '{project.versionVerbose}');
}
add_action('wp_enqueue_scripts', '{project.prefix}_child_scripts');

/**
 * Add your own functionality below this line
 */

    

/*EOF*/