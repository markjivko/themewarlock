<?php
/**
 * Custom menu helper functions
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

if (!function_exists('{project.prefix}_custom_menu_floating_class')) {
    /**
     * Generate the main-navigation class
     * 
     * @return string
     */
    function {project.prefix}_custom_menu_floating_class() {
        // Prepare the result
        $result = 'main-navigation';

        {call.core-custom-menu.customizer.floating_menu}

        // Full-width container
        if('on' === {call.core-custom-menu.customizer.floating_menu.exportVarName}) {
            $result .= ' is-floating';
        }

        // All done
        return $result;
    }
}
{if.core.useStoryline}
if (!function_exists('{project.prefix}_custom_menu_dynamic')) {
    /**
     * Generate the main-navigation class
     * 
     * @return string
     */
    function {project.prefix}_custom_menu_dynamic() {
        // Prepare the result
        $result = false;

        {call.core-custom-menu.customizer.dynamic_menu}

        // Build the floating menu at runtime
        if('on' === {call.core-custom-menu.customizer.dynamic_menu.exportVarName}) {
            $result = true;
        }

        // All done
        return $result;
    }
}
{/if.core.useStoryline}
/**
 * Register the custom layout controls
 * 
 * @param WP_Customize_Manager $wp_customize WordPress Customize Manager
 */
function {project.prefix}_customize_custom_menu_register($wp_customize) {

    {call.core-custom-menu.customizer._register}

}
add_action('customize_register', '{project.prefix}_customize_custom_menu_register');

/*EOF*/