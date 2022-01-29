<?php
/**
 * VC - Revolution slider tweaks
 * 
 * @link https://developer.wordpress.org/themes/functionality/custom-headers/
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Set up the VC - Revolution slider wrapper
 */
if (!function_exists('{project.prefix}_vc_vendor_revslider_setup')) {
    function {project.prefix}_vc_vendor_revslider_setup($output) {
        // Wrap the slider
        return '<div class="st-vc-vendor-revslider-wrapper">' . 
            $output .
        '</div>';
    }
}
add_filter('vc_revslider_shortcode', '{project.prefix}_vc_vendor_revslider_setup');

/* EOF */