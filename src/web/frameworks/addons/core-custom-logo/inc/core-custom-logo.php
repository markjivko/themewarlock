<?php
/**
 * Add support for custom logo
 * 
 * @link https://developer.wordpress.org/themes/functionality/custom-logo/
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

function {project.prefix}_custom_logo_setup() {
    $defaults = array(
        'width'       => {addon.width},
        'height'      => {addon.height},
        'flex-height' => {addon.flexHeight},
        'flex-width'  => {addon.flexWidth},
        'header-text' => array('site-title', 'site-description'),
    );
    add_theme_support('custom-logo', $defaults);
}
add_action('after_setup_theme', '{project.prefix}_custom_logo_setup');

/*EOF*/