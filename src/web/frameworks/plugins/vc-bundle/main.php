<?php 
/**
 * Plugin Name: Add-Ons Bundle for {project.destProjectName}
 * Plugin URI: {utils.common.themeUrl}
 * Description: A collection of {Utils.parse.count.core.getVcBundleAddons} WPBakery Page Builder Add-Ons: {foreach.core.getVcBundleAddons}"{@Value.getParentAddon.addonData.title}", {/foreach.core.getVcBundleAddons} and other features for theme {project.destProjectName}.{if.core.useWidgetBlocks} Enables the use of "Widget Blocks".{/if.core.useWidgetBlocks}
 * Author: {config.authorName}
 * Author URI: {config.authorUrl}
 * Version: {project.versionVerbose}
 * License: GPLv2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('WPINC')) {die;}

// Prepare the WPBakery Page Builder actions
if (!function_exists('{project.prefix}_{Call.core.getVcBundleNameVar}_actions')) {
    function {project.prefix}_{Call.core.getVcBundleNameVar}_actions() {
{foreach.core.getVcBundleAddons}
        /**
         * Load the entry point for the "{@Value.getParentAddon.addonData.title}" ({@Value.getName}) Add-On, version {@Value.getVersion}
         * 
         * - {Utils.parse.string.stripNl}{@Value.getParentAddon.addonData.description}{/Utils.parse.string.stripNl}
         */
        require_once(dirname(__FILE__) . '/{@Value.getSlug}/inc/{@Value.getNameClass}.php');
{/foreach.core.getVcBundleAddons}
    }
}
add_action('vc_before_init', '{project.prefix}_{Call.core.getVcBundleNameVar}_actions');

// Prepare the common scripts
if (!function_exists('{project.prefix}_{Call.core.getVcBundleNameVar}_scripts')) {
    function {project.prefix}_{Call.core.getVcBundleNameVar}_scripts() {
        // Main theme not active
        if (!function_exists('{project.prefix}_setup')) {
            // Ensure that the layout does not break, include a Bootstrap theme
            wp_enqueue_style(
                '{project.destDir}-bootstrap-{Options.projectUiSet}', 
                plugins_url() . '/{Call.core.getVcBundleName}/css/bootstrap.css', 
                array(), 
                {plugin.getBootstrapVersion}
            );
        }
    }
}
add_action('wp_enqueue_scripts', '{project.prefix}_{Call.core.getVcBundleNameVar}_scripts');
{if.core.useWidgetBlocks}
/**
 * Load the entry point for the Widget Blocks Content type
 */
require_once(dirname(__FILE__) . '/st-content-type-widget-block/inc/StContentTypeWidgetBlock.php');
        
// Widget Blocks Content type - Plugin activation hook
register_activation_hook(__FILE__, 'st_content_type_widget_block_activated');
{/if.core.useWidgetBlocks}
/*EOF*/