<?php
/**
 * Register all required plugins for this theme
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

if (!function_exists('{project.prefix}_register_required_plugins')) {
    /**
     * TGM Plugin Activation
     */
    function {project.prefix}_register_required_plugins() {
        
        // Prepare the plugins path
        $pluginsPath = WP_CONTENT_DIR . '/themes/{project.destDir}/plugins';
        
        /**
         * Available plugins
         */
        $plugins = array(

        );

        /**
         * Plugin activation settings
         */
        $config = array(
            'id'           => '{project.destDir}',     // Unique ID for hashing notices for multiple instances of TGMPA.
            'default_path' => '',                      // Default absolute path to bundled plugins.
            'menu'         => 'tgmpa-install-plugins', // Menu slug.
            'has_notices'  => true,                    // Show admin notices or not.
            'dismissable'  => false,                   // If false, a user cannot dismiss the nag message.
            'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
            'is_automatic' => true,                    // Automatically activate plugins after installation or not.
            'message'      => '',                      // Message to output right before the plugins table.
        );

        // Run the tool
        tgmpa($plugins, $config);
    }
}
add_action('tgmpa_register', '{project.prefix}_register_required_plugins');

/*EOF*/