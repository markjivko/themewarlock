<?php
/**
 * {project.destProjectName} Theme Manager
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Theme Manager
 */
class St_ThemeManager {
    
    /**
     * Singleton instance of St_ThemeManager
     * 
     * @var St_ThemeManager
     */
    protected static $_instance = null;
    
    /**
     * Get a Singleton instance of St_ThemeManager
     * 
     * @return St_ThemeManager
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Theme Manager
     */
    protected function __construct() {
        // Singleton
    }
    
    /**
     * Show the Theme Manager
     */
    public function display() {
        // Add the style
        wp_enqueue_style(
            '{project.prefix}_theme_manager_bootstrap', 
            St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/admin/css/bootstrap.css',
            array(),
            '{project.versionVerbose}'
        );
        wp_enqueue_style(
            '{project.prefix}_theme_manager', 
            St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/admin/css/style.css',
            array(),
            '{project.versionVerbose}'
        );

        // Add the JS functionality
        wp_enqueue_script(
            '{project.prefix}_tools', 
            St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/admin/js/tools.js',
            array(),
            '{project.versionVerbose}',
            true
        );
        wp_localize_script(
            '{project.prefix}_tools', 
            '{project.prefix}',
            array(
                'ajax_url'                  => admin_url('admin-ajax.php'),
                'ajax_nonce'                => wp_create_nonce('{project.prefix}'),
                'text_install'              => __('Install', '{project.destDir}'),
                'text_uninstall'            => __('Uninstall', '{project.destDir}'),
                'text_working'              => __('Working', '{project.destDir}'),
                'text_uploading'            => __('Uploading', '{project.destDir}'),
                'text_upload_failed'        => __('Upload failed. Double check your Internet Connection or the "upload_max_filesize" setting in php.ini', '{project.destDir}'),
                'text_deleting'             => __('Deleting', '{project.destDir}'),
                'text_confirm_delete'       => __('Are you sure you want to delete this snapshot?', '{project.destDir}'),
                'text_no_snapshots'         => __('No snapshots', '{project.destDir}'),
                'text_no_snapshots_details' => __('Add your first snapshot by clicking the icon to the left', '{project.destDir}'),
            )
        );
        wp_enqueue_script(
            '{project.prefix}_theme_manager', 
            St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/admin/js/functions.js',
            array(),
            '{project.versionVerbose}',
            true
        );
        
        // Load the main template
        require_once dirname(__FILE__) . '/tpl/main.php';
    }
    
    /**
     * Handle AJAX Requests
     */
    public function ajax() {
        St_Ajax::run($this);
    }
    
    /**
     * Initialize the Theme Manager
     */
    public function init() {
        // Add the "Install Demo Content" button to the theme preview
        add_theme_page(
            // Page Title
            __('Theme Manager', '{project.destDir}'),
            
            // Menu title
            '<img src="' . St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY_URI) . '/admin/img/st_icon_20_white.svg" style="height: 20px;vertical-align: text-top; margin-right: 5px;" />' . 
                __('Demo Content', '{project.destDir}'),
            
            // Capabilities
            'manage_options',
            
            // Menu slug
            '{project.prefix}_theme_manager',
            
            // Output method
            array($this, 'display')
        );
    }
    
    /**
     * Get a list of all available snapshots
     * 
     * @validateFs false
     * @return array Array of {"info":{}, "errors": []}
     */
    public function ajaxGetSnapshots() {
        // All done
        return St_SnapshotManager::getInstance()->getPublicInfo();
    }
    
    /**
     * Get the latest status message for the current install/uninstall task
     * 
     * @validateFs false
     * @return array|null Array of {"message", boolean} or NULL if the no status available
     */
    public function ajaxGetStatus($snapshotId) {
        return St_SnapshotManager::getInstance()
            ->getById($snapshotId)
            ->getStatus();
    }
    
    /**
     * Install a snapshot
     * 
     * @validateFs true
     * @return boolean Installation status
     */
    public function ajaxInstallSnapshot($snapshotId) {
        // Install
        St_SnapshotManager::getInstance()
            ->getById($snapshotId)
            ->install();
        
        // All went well
        return __('Snapshot installed successfuly!', '{project.destDir}');
    }
    
    /**
     * Pre-upload filesystem check
     * 
     * @validateFs true
     * @return boolean True
     */
    public function ajaxPreUploadCheck() {
        // FileSystem checks were made before this call
        return true;
    }
    
    /**
     * Snapshot upload
     * 
     * @validateFs true
     * @return boolean Snapshot upload status
     */
    public function ajaxUploadSnapshot() {
        // Upload
        St_SnapshotManager::getInstance()->upload();
        
        // Get the snapshots list
        return array_map(
            function(/*@var $item St_Snapshot*/ $item) {
                return $item->getPublicInfo();
            },
            // Convert to numeric array
            array_values(St_SnapshotManager::getInstance()->getAll())
        );
    }
    
    /**
     * Delete a snapshot
     * 
     * @validateFs true
     * @return boolean Installation status
     */
    public function ajaxDeleteSnapshot($snapshotId) {
        // Delete
        St_SnapshotManager::getInstance()
            ->deleteById($snapshotId);
        
        // Get the snapshots list
        return array_map(
            function(/*@var $item St_Snapshot*/ $item) {
                return $item->getPublicInfo();
            },
            // Convert to numeric array
            array_values(St_SnapshotManager::getInstance()->getAll())
        );
    }
    
    /**
     * Uninstall a snapshot
     * 
     * @validateFs true
     * @return boolean Installation status
     */
    public function ajaxUninstallSnapshot($snapshotId) {
        // Uninstall
        St_SnapshotManager::getInstance()
            ->getById($snapshotId)
            ->uninstall();
        
        // All went well
        return __('Snapshot uninstalled successfuly!', '{project.destDir}');
    }
}

add_action('admin_menu', array(St_ThemeManager::getInstance(), 'init'));
add_action('wp_ajax_{project.prefix}_action', array(St_ThemeManager::getInstance(), 'ajax'));

/* EOF */