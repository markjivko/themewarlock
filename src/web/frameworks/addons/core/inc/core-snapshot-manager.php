<?php
/**
 * Snaphsot Manager
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

if (!class_exists('St_SnapshotManager')) {
    
    /**
     * Snapshot information
     */
    class St_Snapshot {
        
        /**
         * Database increment amount for insert queries.<br/>
         * This prevents accidental insert collisions during snapshot deployment.
         * 
         * @var int
         */
        const DB_INCREMENT = 1{else.core.staging}00{/else.core.staging};
        
        /**
         * Snapshots relative path
         */
        const PATH = '{project.destDir}/snapshots';

        /**
         * Files and Folders
         */
        const FILE_INFO           = 'info.php';
        const FILE_ACTIONS        = 'actions.php';
        const FILE_REVERT         = 'revert.php';
        const FILE_PREVIEW        = 'preview.png';
        const FILE_STATUS         = 'status.json';
        const FOLDER_FILES        = 'files';
        const FOLDER_FILES_REVERT = 'files-revert';
        const FOLDER_TEMPORARY    = 'temporary';
        
        /**
         * Keys
         */
        const KEY_ID                    = 'id';
        const KEY_TITLE                 = 'title';
        const KEY_DESCRIPTION           = 'description';
        const KEY_COLORS                = 'colors';
        const KEY_PREVIEW               = 'preview';
        const KEY_INSTALLED             = 'installed';
        const KEY_ACTIONS_FS            = 'actions-fs';
        const KEY_ACTIONS_DB            = 'actions-db';
        
        /**
         * Export data keys
         */
        const DATA_KEY_CUSTOMIZER          = 'customizer';
        const DATA_KEY_CUSTOMIZER_EXTENDED = 'customizer-extended';
        const DATA_KEY_WIDGETS             = 'widgets';
        const DATA_KEY_REV_SLIDER          = 'rev-slider';
        const DATA_KEY_REV_SLIDER_TABLES   = 'rev-slider-tables';
        const DATA_KEY_CONTENT             = 'content';
        const DATA_KEY_CONTENT_POSTS       = 'content-posts';
        const DATA_KEY_CONTENT_TERMS       = 'content-terms';
        
        /**
         * Export data: Term keys
         */
        const DATA_TERM_ID          = 'id';
        const DATA_TERM_SLUG        = 'slug';
        const DATA_TERM_TAXONOMY    = 'taxonomy';
        const DATA_TERM_TAXONOMY_ID = 'taxonomy_id';
        const DATA_TERM_PARENT      = 'parent';
        const DATA_TERM_NAME        = 'name';
        const DATA_TERM_GROUP       = 'group';
        const DATA_TERM_COUNT       = 'count';
        const DATA_TERM_DESCRIPTION = 'description';

        /**
         *  Export data: Post keys
         */
        const DATA_POST_ID             = 'ID';
        const DATA_POST_TITLE          = 'post_title';
        const DATA_POST_CONTENT        = 'post_content';
        const DATA_POST_EXCERPT        = 'post_excerpt';
        const DATA_POST_DATE           = 'post_date';
        const DATA_POST_DATE_GMT       = 'post_date_gmt';
        const DATA_POST_MODIFIED       = 'post_modified';
        const DATA_POST_MODIFIED_GMT   = 'post_modified_gmt';
        const DATA_POST_STATUS         = 'post_status';
        const DATA_POST_NAME           = 'post_name';
        const DATA_POST_TYPE           = 'post_type';
        const DATA_POST_MIME_TYPE      = 'post_mime_type';
        const DATA_POST_PASSWORD       = 'post_password';
        const DATA_POST_MENU_ORDER     = 'menu_order';
        const DATA_POST_PARENT         = 'post_parent';
        const DATA_POST_COMMENT_STATUS = 'comment_status';
        const DATA_POST_COMMENT_COUNT  = 'comment_count';
        const DATA_POST_PING_STATUS    = 'ping_status';
        const DATA_POST_GUID           = 'guid';
        const DATA_POST_AUTHOR         = 'post_author';

        /**
         *  Export data: Comments keys
         */
        const DATA_COMMENT_ID           = 'comment_ID';
        const DATA_COMMENT_POST_ID      = 'comment_post_ID';
        const DATA_COMMENT_AUTHOR       = 'comment_author';
        const DATA_COMMENT_AUTHOR_EMAIL = 'comment_author_email';
        const DATA_COMMENT_AUTHOR_URL   = 'comment_author_url';
        const DATA_COMMENT_AUTHOR_IP    = 'comment_author_IP';
        const DATA_COMMENT_DATE         = 'comment_date';
        const DATA_COMMENT_DATE_GMT     = 'comment_date_gmt';
        const DATA_COMMENT_CONTENT      = 'comment_content';
        const DATA_COMMENT_APPROVED     = 'comment_approved';
        const DATA_COMMENT_TYPE         = 'comment_type';
        const DATA_COMMENT_PARENT       = 'comment_parent';
        const DATA_COMMENT_USER_ID      = 'user_id';

        /**
         *  Export data: Extra keys
         */
        const DATA_EXTRA_META       = 'meta';
        const DATA_EXTRA_IS_STICKY  = 'is_sticky';
        const DATA_EXTRA_TAXONOMIES = 'taxonomies';
        const DATA_EXTRA_COMMENTS   = 'comments';
        
        /**
         * Error codes
         */
        const ERROR_INVALID_ID           = 'snapshot_invalid_id';
        const ERROR_INFO_NOT_ARRAY       = 'snapshot_info_not_array';
        const ERROR_ACTIONS_NOT_ARRAY    = 'snapshot_actions_not_array';
        const ERROR_ACTIONS_NOT_FOUND    = 'snapshot_actions_not_found';
        const ERROR_NO_COLORS            = 'snapshot_no_colors';
        const ERROR_PREVIEW_MISSING      = 'snapshot_preview_missing';
        const ERROR_ACTIONS_MISSING      = 'snapshot_actions_missing';
        const ERROR_FILES_FOLDER_MISSING = 'snapshot_files_folder_missing';
        const ERROR_TASK_NOT_ALLOWED     = 'snapshot_task_not_allowed';
        const ERROR_DELETE_NOT_ALLOWED   = 'snapshot_delete_not_allowed';
        const ERROR_DELETE_FAILED        = 'snapshot_delete_failed';
        const ERROR_INSTALL_PLUGINS      = 'snapshot_install_plugins';
        
        /**
         * ID
         * 
         * @var int
         */
        protected $_id;
        
        /**
         * Test Mode
         * 
         * @var boolean
         */
        protected $_testMode = false;
        
        /**
         * Title
         * 
         * @var string
         */
        protected $_title;
        
        /**
         * Description
         * 
         * @var string
         */
        protected $_description;
        
        /**
         * Colors
         * 
         * @var string[]
         */
        protected $_colors;
        
        /**
         * Installed
         * 
         * @var boolean
         */
        protected $_installed;
        
        /**
         * CLI mode
         * 
         * @var boolean
         */
        protected $_cliMode = false;
        
        /**
         * Store the active plugins
         * 
         * @var array
         */
        protected $_activePlugins = array();
        
        /**
         * FileSystem actions
         * 
         * @see WP_Filesystem
         * @var array
         */
        protected $_actionsFs = null;
        
        /**
         * DataBase actions
         * 
         * @var array
         */
        protected $_actionsDb = null;
        
        /**
         * Store diff between the current website layout and our current snapshot
         * after the installation
         * 
         * @var array
         */
        protected $_diff = array();
        
        /**
         * Set which file to also write to during a purge
         */
        protected $_parentStatusId = null;
        
        /**
         * Store the placeholders
         */
        protected static $_placeholders = null;
        
        /**
         * Store the upload directory information
         */
        protected static $_uploadDirInfo = null;
        
        /**
         * Snapshot constructor. <br/>
         * Read-only filesystem access, <b>WP_Filesystem</b> not required!
         * 
         * @param int     $id           Snapshot ID
         * @param array   $snapshotInfo Snapshot Info - defined in the 
         * <b>St_Snapshot::FILE_INFO</b> file
         * @param boolean $testMode     (optional) Whether to validate a snapshot 
         * in the temporary path after extraction; default <b>false</b>
         * @throws St_Exception
         */
        public function __construct($id, $snapshotInfo, $testMode = false) {
            // Set the id
            $this->_id = intval($id);
            
            // Invalid snapshot ID
            if ($this->_id <= 0) {
                throw new St_Exception(
                    __('Invalid snapshot ID', '{project.destDir}'),
                    self::ERROR_INVALID_ID
                );
            }
            
            // Store the test mode
            $this->_testMode = (true === $testMode);
            
            // Validate snapshot info
            if (!is_array($snapshotInfo)) {
                throw new St_Exception(
                    __('Snapshot Info must be an array', '{project.destDir}'),
                    self::ERROR_INFO_NOT_ARRAY,
                    $this->_id
                );
            }
            
            // The St_Snapshot::FILE_ACTIONS file is missing
            if (!is_file($this->_getActionsPath())) {
                throw new St_Exception(
                    sprintf(__('The "%s" file is missing', '{project.destDir}'), self::FILE_ACTIONS),
                    self::ERROR_ACTIONS_MISSING,
                    $this->_id
                );
            }
            
            // The St_Snapshot::FILE_PREVIEW file is missing
            if (!is_file($this->_getPreviewPath())) {
                throw new St_Exception(
                    sprintf(__('The "%s" file is missing', '{project.destDir}'), self::FILE_PREVIEW),
                    self::ERROR_PREVIEW_MISSING,
                    $this->_id
                );
            }
            
            // The St_Snapshot::FOLDER_FILES folder is missing
            if (!is_dir($this->_getFilesFolderPath())) {
                throw new St_Exception(
                    sprintf(__('The "%s" folder is missing', '{project.destDir}'), self::FOLDER_FILES),
                    self::ERROR_FILES_FOLDER_MISSING,
                    $this->_id
                );
            }
            
            // Set the title
            $this->_title = isset($snapshotInfo[self::KEY_TITLE]) ? $snapshotInfo[self::KEY_TITLE] : sprintf(__('Snapshot number %s', '{project.destDir}'), $this->_id);
            
            // Set the description. Default to ellipsis ("...")
            $this->_description = isset($snapshotInfo[self::KEY_DESCRIPTION]) ? $snapshotInfo[self::KEY_DESCRIPTION] : '&#8230;';
            
            // Set the colors
            $this->_colors = isset($snapshotInfo[self::KEY_COLORS]) && is_array($snapshotInfo[self::KEY_COLORS]) ? $snapshotInfo[self::KEY_COLORS] : array();
            
            // Filter-out invalid colors
            $this->_colors = array_filter($this->_colors, function($item) {
                return preg_match('%^\#[\da-f]{6}$%i', $item);
            });

            // Colors are mandatory
            if (!count($this->_colors)) {
                throw new St_Exception(
                    __('Colors were not correctly defined', '{project.destDir}'),
                    self::ERROR_NO_COLORS,
                    $this->_id
                );
            }
            
            // Store the installed state
            $this->_installed = is_file($this->_getRevertPath());
        }
        
        /**
         * Set the test mode
         * 
         * @param boolean $testMode     (optional) Whether to validate a snapshot 
         * in the temporary path after extraction; default <b>false</b>
         * @return St_Snapshot
         */
        public function setTestMode($testMode = false) {
            // Implicit cast to boolean
            $this->_testMode = (true === $testMode);
            
            // All done
            return $this;
        }
        
        /**
         * Get the snapshot directory information
         * 
         * @param string  $key      Upload Dir Key, one of <ul>
         * <li>'basedir' (default)</li>
         * <li>'baseurl'</li>
         * <li>'path'</li>
         * <li>'url'</li>
         * <li>'subdir'</li>
         * </ul>
         * @param boolean $testMode (optional) Whether to reference a snapshot 
         * in the temporary path after extraction; default <b>false</b>
         * @see https://developer.wordpress.org/reference/functions/wp_upload_dir/
         * @return string
         */
        public static function getSnapshotDir($key = null, $testMode = false) {
            // Get the upload directory
            if (null === self::$_uploadDirInfo) {
                self::$_uploadDirInfo = wp_upload_dir(null, false);
            }
            
            // Invalid key
            if (null === $key || !isset(self::$_uploadDirInfo[$key])) {
                $key = 'basedir';
            }
            
            // All done
            return trailingslashit(self::$_uploadDirInfo[$key]) . self::PATH . ($testMode ? ('/' . self::FOLDER_TEMPORARY) : '');
        }
        
        /**
         * Convert the Snapshot object into an array.<br/>
         * Based entirely on the data found in <b>St_Snapshot::FILE_INFO</b>
         * 
         * @return Associative array
         */
        public function getPublicInfo() {
            // Set the data
            return array(
                // Snapshot ID
                self::KEY_ID          => $this->_id,
                
                // Snapshot Title (St_Snapshot::FILE_INFO)
                self::KEY_TITLE       => $this->_title,
                
                // Snapshot Description (St_Snapshot::FILE_INFO)
                self::KEY_DESCRIPTION => $this->_description,
                
                // Snapshot Colors (St_Snapshot::FILE_INFO)
                self::KEY_COLORS      => $this->_colors,
                
                // Snapshot preview URL
                self::KEY_PREVIEW     => self::getSnapshotDir('baseurl', $this->_testMode) . '/' . $this->_id . '/' . self::FILE_PREVIEW,
                    
                // Whether this snapshot was marked as installed
                self::KEY_INSTALLED   => $this->_installed,
            );
        }
        
        /**
         * Get whether or not this snapshot was installed
         * 
         * @return boolean
         */
        public function isInstalled() {
            return $this->_installed;
        }
        
        /**
         * Set a parent log for this task
         * 
         * @param int $snapshotId Parent snapshot ID
         * @return St_Snapshot
         */
        public function setParentLog($snapshotId = null) {
            global $wp_filesystem;
            
            // Validate the snapshot ID
            if (null === $snapshotId || (is_int($snapshotId) && $wp_filesystem->is_file($this->_getStatusPath($snapshotId)))) {
                $this->_parentStatusId = $snapshotId;
            }
            
            // All done
            return $this;
        }
        
        /**
         * Install the snapshot. <br/>
         * Read/write access to filesystem, <b>WP_Filesystem</b> is required!
         * 
         * @throws St_Exception
         * @return St_Snapshot
         */
        public function install($cliMode = false) {
            // Set the CLI mode
            $this->_cliMode = (boolean) $cliMode;
            
            // Check the task is allowed
            $this->_checkTaskAllowed();
            
            try {
                // Log the event
                $this->_log(__('Installing snapshot', '{project.destDir}'));

                // Gracefully uninstall all other snapshots
                $this->_prune();

                // Initialize the actions and files list
                $this->_actionsLoad();

                // Install the files
                $this->_installFs();

                // Install the DataBase modifications
                $this->_installDb();

                // Store the diff/revert information
                $this->_diffSave();
                
            } catch (Exception $exc) {
                
                // Task done (finally is PHP5.5+)
                $this->_taskDone();
                
                // Re-throw the exception
                throw $exc;
            }
            
            // Task done
            $this->_taskDone();
            
            // All done
            return $this;
        }
        
        /**
         * Greacefully uninstall the snapshot.<br/>
         * Read/write access to filesystem, <b>WP_Filesystem</b> is required!
         * 
         * @param boolean $checkOtherTasks (optional) Whether or not to prevent 
         * this task if others are already running; default <b>true</b>
         * @throws St_Exception
         * @return St_Snapshot
         */
        public function uninstall($checkOtherTasks = true) {
            // Check the task is allowed
            $checkOtherTasks && $this->_checkTaskAllowed();
            
            try {
                // Log the event
                $this->_log(__('Uninstalling snapshot', '{project.destDir}'));
                
                // Load the diff/revert information
                $this->_diffLoad();
                
                // Uninstall the files
                $this->_uninstallFs();
                
                // Uninstall the DataBase modifications
                $this->_uninstallDb();
                
                // Remove the diff/revert information
                $this->_diffRemove();
                
            } catch (Exception $exc) {
                
                // Task done (finally is PHP5.5+)
                $this->_taskDone();
                
                // Re-throw the exception
                throw $exc;
            }
            
            // Task done
            $this->_taskDone();
            
            // All done
            return $this;
        }
        
        /**
         * Remove this snapshot entirely.<br/>
         * <b>Do NOT</b> perform any other tasks after this!
         * 
         * @throws St_Exception
         */
        public function delete() {
            // Check the task is allowed
            $this->_checkTaskAllowed();
            
            try {
                // Log the event
                $this->_log(__('Deleting snapshot', '{project.destDir}'));

                // Delete the snapshot
                $this->_delete();
                
            } catch (Exception $exc) {
                // Task done (finally is PHP5.5+)
                $this->_taskDone();
                
                // Re-throw the exception
                throw $exc;
            }
            
            // Task done
            $this->_taskDone();
        }
        
        /**
         * Get the current task's status.<br/>
         * Read only access to filesystem, <b>WP_Filesystem</b> is not required
         * 
         * @return array|null Array of {"message", boolean} or NULL if the no status available
         */
        public function getStatus() {
            // Check the file exists
            if (is_file($statusFilePath = $this->_getStatusPath())) {
                // Prepare the function name
                $functionName = implode('_', array('file', 'get', 'contents'));
            
                // Get the array
                return json_decode(
                    call_user_func(
                        $functionName, 
                        $statusFilePath
                    ), 
                    true
                );
            }
            
            // Nothing found
            return null;
        }
        
        /**
         * Delete the current snapshot
         */
        protected function _delete() {
            global $wp_filesystem;
            
            // Snapshot is installed, cannot be removed
            if ($this->_installed) {
                throw new St_Exception(
                    __('Uninstall the snapshot before deleting it', '{project.destDir}'),
                    self::ERROR_DELETE_NOT_ALLOWED,
                    $this->_id
                );
            }
            
            // Remove the files
            if (!$wp_filesystem->delete(self::getSnapshotDir(null, $this->_testMode) . '/' . $this->_id, true, 'd')) {
                throw new St_Exception(
                    __('Could not delete the snapshot, check your filesystem settings', '{project.destDir}'),
                    self::ERROR_DELETE_FAILED,
                    $this->_id
                );
            }
        }
        
        /**
         * Get the Snapshot placeholder tags
         */
        protected static function _getPlaceholders() {
            // Store the placeholders
            if (null === self::$_placeholders) {
                // Get the upload directory information
                $uploadDir = wp_upload_dir(null, false);
        
                // Set the placeholders
                self::$_placeholders= array(
                    '{{__UPLOAD_URL__}}' => $uploadDir['baseurl'],
                    '{{__SITE_URL__}}'   => get_site_url(),
                );
            }
            
            // All done
            return self::$_placeholders;
        }
        
        /**
         * Log the current action in the status file
         * 
         * @param string  $message    Message to log
         * @param boolean $success    Message type
         * @param boolean $showToUser Public-facing messages
         */
        protected function _log($message, $success = true, $showToUser = true) {
            global $wp_filesystem;
{if.core.staging}
            // Prepare the log path
            $logPath = self::getSnapshotDir(null, $this->_testMode) . '/log.txt';
            
            // Write to file (Append Mode)
            $wp_filesystem->put_contents(
                $logPath, 
                $wp_filesystem->get_contents($logPath) . ' ' . implode(' | ', array(
                    date('d.m H:i:s'), 
                    $this->_id,
                    var_export($success, true),
                    is_string($message) ? $message : var_export($message, true),
                )) . PHP_EOL
            );
{/if.core.staging}
            // Public logs
            if ($showToUser) {
                // Write to file
                $wp_filesystem->put_contents(
                    $this->_getStatusPath(), 
                    json_encode(
                        array(
                            $message,
                            $success
                        )
                    )
                );
                
                // Also write to parent
                if (is_int($this->_parentStatusId)) {
                    $wp_filesystem->put_contents(
                        $this->_getStatusPath($this->_parentStatusId), 
                        json_encode(
                            array(
                                '#' . $this->_id . ': ' . $message,
                                $success
                            )
                        )
                    );
                }
                
                // Carpe diem
                if (!$this->_cliMode) {
                    usleep(600000);
                }
            }
        }
        
        /**
         * Get the path to the current snapshot's preview file
         */
        protected function _getPreviewPath() {
            return self::getSnapshotDir(null, $this->_testMode) . '/' . $this->_id . '/' . self::FILE_PREVIEW;
        }
        
        /**
         * Get the path to the current snapshot's status file
         */
        protected function _getStatusPath($snapshotId = null) {
            // Set the default
            if (null === $snapshotId) {
                $snapshotId = $this->_id;
            }
            
            // All done
            return self::getSnapshotDir(null, $this->_testMode) . '/' . $snapshotId . '/' . self::FILE_STATUS;
        }
        
        /**
         * Get the path to the current snapshot's actions file
         */
        protected function _getActionsPath() {
            return self::getSnapshotDir(null, $this->_testMode) . '/' . $this->_id . '/' . self::FILE_ACTIONS;
        }
        
        /**
         * Get the path to the current snapshot's revert file
         */
        protected function _getRevertPath() {
            return self::getSnapshotDir(null, $this->_testMode) . '/' . $this->_id . '/' . self::FILE_REVERT;
        }
        
        /**
         * Get the path to the current snapshot's revert folder
         */
        protected function _getRevertFolderPath() {
            return self::getSnapshotDir(null, $this->_testMode) . '/' . $this->_id . '/' . self::FOLDER_FILES_REVERT;
        }
        
        /**
         * Get the path to the current snapshot's files folder
         */
        protected function _getFilesFolderPath() {
            return self::getSnapshotDir(null, $this->_testMode) . '/' . $this->_id . '/' . self::FOLDER_FILES;
        }
        
        /**
         * Mark the current task as done
         */
        protected function _taskDone() {
            global $wp_filesystem;
            
            // Remove the status file
            if ($wp_filesystem->is_file($statusFilePath = $this->_getStatusPath())) {
                $wp_filesystem->delete($statusFilePath);
            }
        }

        /**
         * Check whether this task is allowed; throws an exception otherwise
         * 
         * @throws St_Exception
         * @return boolean
         */
        protected function _checkTaskAllowed() {
            global $wp_filesystem;
            
            // TGMPA Register
            ob_start();
            do_action('tgmpa_register');
            ob_end_clean();
            
            // Get the Inactive Plugins list
            $inactivePlugins = array();
            $activePlugins = array();
            
            // Go through the registered plugins list
            foreach(TGM_Plugin_Activation::get_instance()->plugins as $pluginData) {
                // Invalid data
                if (!is_array($pluginData)) {
                    continue;
                }
                
                // Keys not found
                if (!isset($pluginData['file_path']) || !isset($pluginData['name'])) {
                    continue;
                }
                
                // Check if plugin is active
                if(!call_user_func(implode('_', array('is', 'plugin', 'active')), $pluginData['file_path'])) {
                    $inactivePlugins[] = $pluginData['name'];
                } else {
                    $activePlugins[] = $pluginData['name'];
                }
            }
            
            // Store the active plugins
            $this->_activePlugins = $activePlugins;
            
            // Cannot continue if not all plugins were installed
            if (count($inactivePlugins)) {
                throw new St_Exception(
                    count($inactivePlugins) == 1 ? 
                        sprintf(__('Please install the "%s" plugin first', '{project.destDir}'), current($inactivePlugins))
                        :
                        sprintf(__('Please install the following plugins first: %s', '{project.destDir}'), implode(',', $inactivePlugins)),
                    self::ERROR_INSTALL_PLUGINS,
                    $this->_id
                );
            }
            
            // Go through the public information
            foreach (St_SnapshotManager::getInstance()->getPublicInfo() as $snapshotData) {
                // Prepare the status file path
                $statusFilePath = $this->_getStatusPath($snapshotData[self::KEY_ID]);
                
                // Another task is executing
                if ($wp_filesystem->is_file($statusFilePath)) {
                    // Less than 3 minutes
                    if (time() - $wp_filesystem->atime($statusFilePath) <= 180) {
                        throw new St_Exception(
                            __('Task not allowed', '{project.destDir}'),
                            self::ERROR_TASK_NOT_ALLOWED,
                            $this->_id
                        );
                    } else {
                        // Kill most likely zombie status file
                        $wp_filesystem->delete($statusFilePath);
                    }
                }
            }
            return true;
        }
        
        /**
         * Load the Diff information
         */
        protected function _diffLoad() {
            do {
                // Actions definition not found
                if (!is_file($revertPath = $this->_getRevertPath())) {
                    break;
                }

                // Prepare the revert actions
                $diffActions = null;

                // Load the actions
                require $revertPath;

                // Validate snapshot actions
                if (!is_array($diffActions)) {
                    break;
                }
                
                // Store the revert data
                $this->_diff = $diffActions;
            } while (false);
        }
        
        /**
         * Perform final uninstall clean-up
         */
        protected function _diffRemove() {
            global $wp_filesystem;
            
            // Log the event
            $this->_log(__('Performing final cleanup', '{project.destDir}'));
            
            // Clean-up previous file diffs
            if ($wp_filesystem->is_dir($revertFolderPath = $this->_getRevertFolderPath())) {
                $wp_filesystem->rmdir($revertFolderPath, true);
            }
            
            // Remove known DataBase and FileSystem changes
            if ($wp_filesystem->is_file($revertFilePath = $this->_getRevertPath())) {
                $wp_filesystem->delete($revertFilePath);
            }
            
            // Update the flag
            $this->_installed = false;
        }
        
        /**
         * Initialize the FileSystem and DataBase actions
         * 
         * @throws St_Exception
         */
        protected function _actionsLoad() {
            // Log the event
            $this->_log(__('Initializing actions', '{project.destDir}'));
            
            // FileSystem or DataBase actions not initialized
            if (null === $this->_actionsFs || null === $this->_actionsDb) {
                // Actions definition not found
                if (!is_file($actionsPath = $this->_getActionsPath())) {
                    throw new St_Exception(
                        __('Snapshot Actions not found', '{project.destDir}'),
                        self::ERROR_ACTIONS_NOT_FOUND,
                        $this->_id
                    );
                }
                
                // Prepare the snapshot actions
                $snapshotActions = null;

                // Load the actions
                require $actionsPath;
                
                // Validate snapshot actions
                if (!is_array($snapshotActions)) {
                    throw new St_Exception(
                        __('Snapshot Actions must be an array', '{project.destDir}'),
                        self::ERROR_ACTIONS_NOT_ARRAY,
                        $this->_id
                    );
                }
                
                // Set the FileSystem actions
                $this->_actionsFs = isset($snapshotActions[self::KEY_ACTIONS_FS]) && is_array($snapshotActions[self::KEY_ACTIONS_FS]) ? $snapshotActions[self::KEY_ACTIONS_FS] : array();

                // Set the DataBase actions
                $this->_actionsDb = isset($snapshotActions[self::KEY_ACTIONS_DB]) && is_array($snapshotActions[self::KEY_ACTIONS_DB]) ? $snapshotActions[self::KEY_ACTIONS_DB] : array();
            }
        }
        
        /**
         * Gracefully remove all other installations (if any)
         * 
         * @throws St_Exception
         */
        protected function _prune() {
            // Go through the public information
            foreach (St_SnapshotManager::getInstance()->getPublicInfo() as $snapshotData) {
                // Snapshot is installed
                if ($snapshotData[self::KEY_INSTALLED]) {
                    St_SnapshotManager::getInstance()
                        // Get the snapshot by ID
                        ->getById($snapshotData[self::KEY_ID])
                        // Save the uninstall log in the parent task as well
                        ->setParentLog($this->_id)
                        // Uninstall the snapshot, without checking for other tasks
                        ->uninstall(false)
                        // Reset the parent log flag
                        ->setParentLog();
                }
            }
        }
        
        /**
         * Store the original FileSystem and DataBase values
         */
        protected function _diffSave() {
            global $wp_filesystem;
            
            // Prepare the file contents
            $fileContents = <<<'PHP'
<?php
/**
 * Snapshot #__ID__ diff
 */
if (!defined('WPINC')) {die;}

__CODE__
    
/*EOF*/
PHP;
            
            // Prepare the code
            $code = '$diffActions = ' . var_export($this->_diff, true) . ';';
            
            // Create the file
            $wp_filesystem->put_contents(
                $this->_getRevertPath(),
                str_replace(
                    array(
                        '__ID__',
                        '__CODE__',
                    ),
                    array(
                        $this->_id,
                        $code,
                    ),
                    $fileContents
                )
            );
            
            // Update the flag
            $this->_installed = true;
        }
        
        /**
         * Store the changes to the DataBase during an install procedure
         * 
         * @param string $actionType DataBase action type
         * @param string $key        Action key
         * @param mixed  $value      Action value
         */
        protected function _diffDb($actionType, $key, $value) {
            // DB not initialized
            if (!isset($this->_diff[self::KEY_ACTIONS_DB])) {
                $this->_diff[self::KEY_ACTIONS_DB] = array();
            }
            
            // DB/ActionType not initialized
            if (!isset($this->_diff[self::KEY_ACTIONS_DB][$actionType])) {
                $this->_diff[self::KEY_ACTIONS_DB][$actionType] = array();
            }
            
            // Store the key-value pair
            $this->_diff[self::KEY_ACTIONS_DB][$actionType][$key] = $value;
        }
        
        /**
         * Store the changes to the FileSystem during an install procedure
         * 
         * @param string $filePath Path to file
         */
        protected function _diffFs($filePath) {
            global $wp_filesystem;
            
            // DB not initialized
            if (!isset($this->_diff[self::KEY_ACTIONS_FS])) {
                $this->_diff[self::KEY_ACTIONS_FS] = array();
            }
            
            // Is this file about to be modified?
            $fileExists = $wp_filesystem->is_file($filePath);
            
            // Prepare the file key
            $fileKey = md5($filePath);
            
            // Store the file information
            $this->_diff[self::KEY_ACTIONS_FS][$fileKey] = array($filePath,  $fileExists);
            
            // The file is about to be modified, store the original
            if ($fileExists) {
                // Store the file
                $wp_filesystem->copy(
                    $filePath, 
                    $this->_getRevertFolderPath() . '/' . $fileKey . '._st',
                    true
                );
            }
        }
        
        /**
         * An extended version of <b>array_walk_recursive</b> that goes through leafs and nodes
         * 
         * @see http://php.net/manual/en/function.array-walk-recursive.php
         * @param array    $input        Array
         * @param callable $userFunction Callable
         * @param mixed    $userData     (optional) User Data; default <b>null</b>
         */
        protected function _walkRecursive(&$input, $userFunction, $userData = null) {
            // Invalid arguments
            if (!is_array($input) || !is_callable($userFunction)) {
                return;
            }

            // Go through the nodes
            foreach ($input as $key => $value) { 
                // Call the user function and pass all the arguments 
                call_user_func_array($userFunction, array(&$value, $key, $userData));

                // Go through to the next level
                if (is_array($value)) { 
                    $this->_walkRecursive($value, $userFunction, $userData); 
                }

                // Store all user changes
                $input[$key] = $value;
            } 
        }
        
        /**
         * Replace the placeholders in the provided string
         * 
         * @param string $string
         * @return string
         */
        protected function _replacePlaceholders($string) {
            // Not a string, skip it
            if (!is_string($string) || !count(self::_getPlaceholders())) {
                return $string;
            }
            
            // Replace the placeholders
            return str_replace(
                array_keys(self::_getPlaceholders()), 
                array_values(self::_getPlaceholders()), 
                $string
            );
        }
        
        /**
         * Decode content, replacing the placeholders as well
         * 
         * @param string $string Base 64 Encoded string
         */
        protected function _decode($string) {
            return $this->_replacePlaceholders(call_user_func(implode('_', array('base64', 'decode')), $string));
        }
        
        /**
         * Multi-option helper for deep placeholder replacement; auto-increments post IDs
         * 
         * @param string  $string     Multi-option string to perform replacements on
         * @param int     $increment  Post ID increment
         * @param boolean $urlEncoded (optional) Whether to raw url encode/decode the string; default <b>true</b>
         * @return string
         */
        protected function _fixContentMO($string, $increment, $urlEncoded = true) {
            // Prepare the options
            $options = array();

            // Go through each detail
            foreach(explode('|', $string) as $option) {
                // Not a key-value pair
                if (false === strpos($option, ':')) {
                    $options[] = '';
                    continue;
                }

                // Get the key and value
                list($optionKey, $optionValue) = explode(':', $option);

                // Encoded
                if ($urlEncoded) {
                    $optionValue = rawurldecode($optionValue);
                }
                
                // Post reference
                if (is_numeric($optionValue) && preg_match('%(?:background|image)$%i', $optionKey)) {
                    $optionValue += $increment;
                } else {
                    // Parse the value
                    $optionValue = self::_replacePlaceholders($optionValue);
                }
                
                // Store in the final array
                $options[] = $optionKey . ':' . ($urlEncoded ? rawurlencode($optionValue) : $optionValue);
            }

            // Store the value
            return implode('|', $options);
        }
        
        /**
         * Fix shotcode definitions in post content (incrementing Post IDs) and 
         * replace placeholders. Content should retain original JSON and Urlencoding.
         * 
         * @param string  $contents     Post content
         * @param int     $increment    Post ID increment
         * @param boolean $base64Decode (optional) Decode the post content from base 64; default <b>false</b>
         */
        protected function _fixContent($contents, $increment = 0, $base64Decode = false) {
            // The increment is an integer
            $increment = intval($increment);
            
            // Invalid arguments
            if (!is_string($contents) || $increment <= 0) {
                return $contents;
            }
            
            // Decode the contents
            if ($base64Decode) {
                $contents = call_user_func(implode('_', array('base64', 'decode')), $contents);
            }
            
            // Remove slider references
            if (!class_exists('RevSliderGlobals')) {
                $contents = preg_replace('%\[rev_slider[^\]]*?\]%ims', '', $contents);
            }
            
            // Replace the post IDs
            $contents = preg_replace_callback(
                '%\[(\w+)\s*([^\]]*?)\]%ims', 
                function($item) use ($increment) {
                    // Get the shortcode details
                    $shortCodeType = $item[1];
                    $shortCodeContent = $item[2];

                    // Replace the content details
                    $shortCodeContent = preg_replace_callback(
                        '%(\s*)(\w+)\s*=\s*"([^"]*?)"(\s*)%ims', 
                        function($item) use ($increment) {
                            // Store the spaces
                            $spaceBefore = $item[1];
                            $spaceAfter = $item[4];

                            // Get the attribute name
                            $attrName = $item[2];

                            // Is this a multi-option?
                            if (preg_match('%url$%', $attrName) && preg_match('%^\w+\:.*?\|%', $item[3])) {
                                // Plain text
                                $attrValueEncoded = false;

                                // Store the new value
                                $attrValue = $this->_fixContentMO($item[3], $increment);
                            } else {
                                // Get the attribute value
                                $attrValue = rawurldecode($item[3]);

                                // Store whether or not this was encoded
                                $attrValueEncoded = ($attrValue !== $item[3]);

                                // Attempt JSON decode
                                do {
                                    if (!is_numeric($attrValue) && 'null' !== $attrValue) {
                                        // Attempt decoding
                                        $attrValueArray = @json_decode($attrValue, true);

                                        // Valid array
                                        if (is_array($attrValueArray)) {

                                            // Go through the array
                                            foreach ($attrValueArray as $attrValueArrayKey => $attrValueArrayValues) {

                                                // Valid array
                                                if (is_array($attrValueArrayValues)) {
                                                    // Go through the array
                                                    foreach ($attrValueArrayValues as $key => $originalValue) {
                                                        // Is this a multi-option?
                                                        if (preg_match('%url$%', $key) && preg_match('%^\w+\:.*?\|%', $originalValue)) {
                                                            // Store the new value
                                                            $value = $this->_fixContentMO($originalValue, $increment);
                                                            
                                                            // Plain text
                                                            $valueEncoded = false;
                                                        } else {
                                                            // Get the decoded value
                                                            $value = rawurldecode($originalValue);
                                                            
                                                            // Store the encoded flag
                                                            $valueEncoded = ($value !== $originalValue);

                                                            // Final nested layer
                                                            if (!is_numeric($value) && 'null' !== $value) {

                                                                // Get the final layer
                                                                $finalLayerArray = @json_decode($value, true);

                                                                // Valid definitions
                                                                if (is_array($finalLayerArray)) {

                                                                    // Go through the array
                                                                    foreach ($finalLayerArray as $finalLayerKey => $finalLayerValues) {
                                                                        if (is_array($finalLayerValues)) {
                                                                            foreach ($finalLayerValues as $fKey => $fValue) {
                                                                                // Is this a multi-option?
                                                                                if (preg_match('%url$%', $fKey) && preg_match('%^\w+\:.*?\|%', $fValue)) {
                                                                                    // Store the new value
                                                                                    $finalLayerArray[$finalLayerKey][$fKey] = $this->_fixContentMO($fValue, $increment, false);
                                                                                } else {
                                                                                    // Post reference
                                                                                    if (is_numeric($fValue) && preg_match('%(?:background|image)$%i', $fKey)) {
                                                                                        $fValue += $increment;
                                                                                    } else {
                                                                                        $fValue = $this->_replacePlaceholders($fValue);
                                                                                    }
                                                                                    
                                                                                    // Re-encode the final value
                                                                                    $finalLayerArray[$finalLayerKey][$fKey] = $fValue;
                                                                                }
                                                                            }
                                                                        }
                                                                    }

                                                                    // Restore the value
                                                                    $value = json_encode($finalLayerArray);
                                                                }
                                                            }
                                                            
                                                            // Post reference
                                                            if (is_numeric($value) && preg_match('%(?:background|image)$%i', $key)) {
                                                                $value += $increment;
                                                            } else {
                                                                $value = $this->_replacePlaceholders($value);
                                                            }
                                                            
                                                        }

                                                        // Re-encode the value
                                                        $attrValueArray[$attrValueArrayKey][$key] = $valueEncoded ? rawurlencode($value) : $value;
                                                    }
                                                }
                                            }

                                            // Store the array back
                                            $attrValue = json_encode($attrValueArray);
                                            break;
                                        }
                                    }

                                    // Post reference
                                    if (is_numeric($attrValue) && preg_match('%(?:background|image)$%i', $attrName)) {
                                        $attrValue += $increment;
                                    } else {
                                        $attrValue = $this->_replacePlaceholders($attrValue);
                                    }
                                } while (false);
                                
                            }
                            
                            // Re-encode the value
                            if ($attrValueEncoded) {
                                $attrValue = rawurlencode($attrValue);
                            }
                            
                            // All done
                            return "{$spaceBefore}{$attrName}=\"{$attrValue}\"{$spaceAfter}";
                        }, 
                        $shortCodeContent
                    );

                    // Prepare the separator
                    $separator = '';
                    if (strlen($shortCodeContent)) {
                        $separator = ' ';
                    }

                    // All done
                    return "[{$shortCodeType}{$separator}{$shortCodeContent}]";
                }, $contents
            );
                
            // Final pass
            return $this->_replacePlaceholders($contents);
        }
        
        /**
         * Install the DataBase modifications 
         */
        protected function _installDb() {
            global $wpdb;
            
            // Log the event
            $this->_log(__('Performing DataBase changes', '{project.destDir}'));
{if.core.useWidgetBlocks}
            // WPBakery Page Builder integration
            if (function_exists('vc_editor_set_post_types')) {
                // Get the enabled post tyles
                $postTypes = vc_editor_post_types();

                // Append our own post type
                if (!in_array('st_widget_block', $postTypes)) {
                    $postTypes[] = 'st_widget_block';
                }

                // Update the post types
                vc_editor_set_post_types($postTypes);
            }
{/if.core.useWidgetBlocks}
            // Get the increments
            $incrementPosts = $wpdb->get_var("SELECT MAX(ID) FROM {$wpdb->posts}") + self::DB_INCREMENT;
            $incrementTerms = $wpdb->get_var("SELECT MAX(term_id) FROM {$wpdb->terms}") + self::DB_INCREMENT;
            $incrementComments = $wpdb->get_var("SELECT MAX(comment_ID) FROM {$wpdb->comments}") + self::DB_INCREMENT;
            
            // Go through the DataBase actions
            foreach ($this->_actionsDb as $actionType => $payload) {
                switch ($actionType) {
                    // Theme Modifications
                    case self::DATA_KEY_CUSTOMIZER:
                        // Log the event
                        $this->_log(__('Installing Theme Modifications', '{project.destDir}'));
                        
                        // Go through each Theme Mod
                        foreach ($payload as $payloadKey => $payloadValue) {
                            // Custom Logo
                            if (in_array($payloadKey, array('custom_logo'), true)) {
                                // Do not replace the client's logo
                                if (!$this->_cliMode) {
                                    continue;
                                }
                                
                                // Increment the ID
                                $payloadValue = intval($payloadValue) + $incrementPosts;
                            }
                            
                            // Custom menu
                            if (in_array($payloadKey, array('nav_menu_locations'), true)) {
                                if (is_array($payloadValue)) {
                                    foreach ($payloadValue as $payloadValueK => $payloadValueV) {
                                        $payloadValue[$payloadValueK] = intval($payloadValueV) + $incrementTerms;
                                    }
                                }
                            }
                            
                            // Store the revert value
                            $this->_diffDb(
                                $actionType, 
                                $payloadKey, 
                                get_theme_mod($payloadKey, false)
                            );
            
                            // Get the final payload value
                            $payloadValueFinal = $this->_replacePlaceholders($payloadValue);
                            
                            // Set our custom theme modification
                            set_theme_mod($payloadKey, $payloadValueFinal);
                        }
                        break;
                        
                    // Theme Modifications - Extended
                    case self::DATA_KEY_CUSTOMIZER_EXTENDED:
                        // Log the event
                        $this->_log(__('Installing Customizer data', '{project.destDir}'));
                        
                        // Go through each Theme Mod
                        foreach ($payload as $payloadKey => $payloadValue) {
                            // Store the revert value
                            $this->_diffDb(
                                $actionType, 
                                $payloadKey, 
                                get_option($payloadKey, false)
                            );
                            
                            // Increment the page value
                            if (preg_match('%^page_%', $payloadKey) && is_numeric($payloadValue)) {
                                $payloadValue += $incrementPosts;
                            } elseif ('nav_menus_created_posts' === $payloadKey && is_array($payloadValue) && count($payloadValue)) {
                                // Increment each post ID
                                foreach (array_keys($payloadValue) as $plKey) {
                                    $payloadValue[$plKey] += $incrementPosts;
                                }
                            } else {
                                // Get the final payload value
                                $payloadValue = $this->_replacePlaceholders($payloadValue);
                            }
                            
                            // Set our custom theme modification
                            update_option($payloadKey, $payloadValue);
                        }
                        break;
                        
                    // Widgets/Sidebars
                    case self::DATA_KEY_WIDGETS:
                        // Log the event
                        $this->_log(__('Installing Widgets', '{project.destDir}'));
                        
                        // Go through each widget/sidebar option
                        foreach ($payload as $payloadKey => $payloadValue) {
                            // Store the revert value
                            $this->_diffDb(
                                $actionType, 
                                $payloadKey, 
                                get_option($payloadKey, false)
                            );
                            
                            // Increment the page value
                            if (is_array($payloadValue)) {
                                foreach ($payloadValue as $pvKey => $pvValue) {
                                    if (is_array($pvValue)) {
                                        // Go through the widget arguments
                                        foreach ($pvValue as $pvValueK => $pvValueV) {
{if.core.useWidgetBlocks}
                                            // Widget blocks
                                            if ('widget_st_widget_block_wrapper' === $payloadKey) {
                                                if (class_exists('st_widget_block_wrapper')) {
                                                    if ($pvValueK === st_widget_block_wrapper::FIELD_CONTENT_TYPE_WIDGET_ID) {
                                                        $pvValue[$pvValueK] = intval($pvValueV) + $incrementPosts;
                                                    }
                                                }
                                            }
{/if.core.useWidgetBlocks}                                            
                                            // Replace the placeholders
                                            $pvValue[$pvValueK] = $this->_replacePlaceholders($pvValueV);
                                        }
                                        
                                        // No other placeholders to replace
                                        $payloadValue[$pvKey] = $pvValue;
                                    } else {
                                        $payloadValue[$pvKey] = $this->_replacePlaceholders($pvValue);
                                    }
                                }
                            } else {
                                // Get the final payload value
                                $payloadValue = $this->_replacePlaceholders($payloadValue);
                            }
                            
                            // Set our custom theme modification
                            update_option($payloadKey, $payloadValue);
                        }
                        break;
                    
                    // Revolution Slider
                    case self::DATA_KEY_REV_SLIDER:
                        // Revolution slider not installed, skip the task
                        if (!class_exists('RevSliderGlobals')) {
                            break;
                        }
                        
                        // Log the event
                        $this->_log(__('Installing Sliders', '{project.destDir}'));
                        
                        // Prepare the table names
                        $tablesList = array(
                            RevSliderGlobals::TABLE_SLIDERS_NAME       => RevSliderGlobals::$table_sliders,
                            RevSliderGlobals::TABLE_SLIDES_NAME        => RevSliderGlobals::$table_slides,
                            RevSliderGlobals::TABLE_STATIC_SLIDES_NAME => RevSliderGlobals::$table_static_slides,
                            RevSliderGlobals::TABLE_LAYER_ANIMS_NAME   => RevSliderGlobals::$table_layer_anims,
                            RevSliderGlobals::TABLE_NAVIGATION_NAME    => RevSliderGlobals::$table_navigation,
                        );
        
                        // Go through each widget/sidebar option
                        foreach ($payload as $payloadKey => $payloadValue) {
                            switch ($payloadKey) {
                                case self::DATA_KEY_REV_SLIDER_TABLES:
                                    // Prepare the tables diff
                                    $tablesDiff = array();
                                    
                                    // Go through the tables
                                    foreach ($payloadValue as $tableKey => $tableRows) {
                                        // Invalid table key
                                        if (!isset($tablesList[$tableKey])) {
                                            continue;
                                        }
                                        
                                        // Prepare the table name
                                        $tableName = $tablesList[$tableKey];
                                        
                                        // Initiate the diff store for this table
                                        $tablesDiff[$tableName] = array();
                                        
                                        // Go through each row
                                        foreach ($tableRows as $rowId => $rowData) {
                                            // Parse the entry
                                            foreach ($rowData as $colName => $colValue) {
                                                // JSON-serialized data
                                                if (in_array($colName, array('params', 'layers', 'settings'))) {
                                                    // Deserialize the value
                                                    $colValueDecoded = @json_decode($colValue, true);
                                                    
                                                    // Successful decoding
                                                    if (null !== $colValueDecoded) {
                                                        if (is_array($colValueDecoded)) {
                                                            $this->_walkRecursive($colValueDecoded, function(&$item, $key) {
                                                                // @FIXME - Handle Post IDs ($incrementPosts)
                                                                if (is_string($item)) {
                                                                    $item = $this->_replacePlaceholders($item);
                                                                }
                                                            });
                                                        } elseif (is_string($colValueDecoded)) {
                                                            $colValueDecoded = $this->_replacePlaceholders($colValueDecoded);
                                                        }

                                                        // Re-serialize and save the value
                                                        $rowData[$colName] = json_encode($colValueDecoded);
                                                    }
                                                }
                                            }
                                        
                                            // Get the old row
                                            $oldRowData = $wpdb->get_row(
                                                $wpdb->prepare(
                                                    "SELECT * FROM {$tableName} WHERE id = %s", 
                                                    $rowId
                                                ), 
                                                ARRAY_A
                                            );
                                                    
                                            // Update
                                            if (is_array($oldRowData)) {
                                                // Remove the ID for the update
                                                unset($rowData['id']);
                                                
                                                // Update the row
                                                $wpdb->update(
                                                    $tableName,
                                                    $rowData,
                                                    array(
                                                        'id' => $rowId
                                                    )
                                                );
                                            } else {
                                                // Insert the row
                                                $wpdb->insert($tableName, $rowData);
                                            }
                                            
                                            // Store the diff
                                            $tablesDiff[$tableName][$rowId] = is_array($oldRowData) ? $oldRowData : false;
                                        }
                                    }
                                    
                                    // Store the revert value
                                    $this->_diffDb(
                                        $actionType, 
                                        $payloadKey, 
                                        $tablesDiff
                                    );
                                    break;
                            }
                        }
                        break;
                    
                    // Content
                    case self::DATA_KEY_CONTENT:
                        // Get the current user ID
                        $currentUser = wp_get_current_user();
                        
                        // Go through the post types
                        foreach ($payload as $payloadKey => $payloadValue) {
                            switch ($payloadKey) {
                                // Terms
                                case self::DATA_KEY_CONTENT_TERMS:
                                    // Log the event
                                    $this->_log(__('Creating terms', '{project.destDir}'));
                                    
                                    // Prepare the term IDs to remove
                                    $termIds = array(
                                        'remove'  => array(),
                                        'restore' => array(),
                                    );
                                    
                                    // Go through each post
                                    foreach ($payloadValue as $termData) {
                                        // Set the new term ID
                                        $termData[self::DATA_TERM_ID] += $incrementTerms;
                                        $termData[self::DATA_TERM_TAXONOMY_ID] += $incrementTerms;
                                        
                                        // Set the new parent ID
                                        if ($termData[self::DATA_TERM_PARENT] > 0) {
                                            $termData[self::DATA_TERM_PARENT] += $incrementTerms;
                                        }
                                        
                                        // Restore the description
                                        $termData[self::DATA_TERM_DESCRIPTION] = $this->_decode($termData[self::DATA_TERM_DESCRIPTION]);
                                        
                                        // Avoid name or slug collisions
                                        $collidingTerms = $wpdb->get_col(
                                            $wpdb->prepare( 
                                                "SELECT term_id FROM {$wpdb->terms} WHERE name = %s OR slug = %s", 
                                                $termData[self::DATA_TERM_NAME], 
                                                $termData[self::DATA_TERM_SLUG] 
                                            )
                                        );
                                        if (is_array($collidingTerms)) {
                                            foreach ($collidingTerms as $colidingTermId) {
                                                // Get the row data
                                                $collidingRowData = $wpdb->get_row(
                                                    $wpdb->prepare(
                                                        "SELECT * FROM {$wpdb->terms} WHERE term_id = %d", 
                                                        $colidingTermId
                                                    )
                                                );
                                                        
                                                // Store the restoration data
                                                $termIds['restore'][] = array(
                                                    $colidingTermId, 
                                                    $collidingRowData->name, 
                                                    $collidingRowData->slug
                                                );
                                                
                                                // Update the colliding row
                                                $wpdb->update(
                                                    $wpdb->terms, 
                                                    array(
                                                        'name' => $collidingRowData->name . ' (Old)',
                                                        'slug' => $collidingRowData->slug . '-st-old',
                                                    ), 
                                                    array(
                                                        'term_id' => $colidingTermId,
                                                    )
                                                );
                                            }
                                        }
                                        
                                        // Create the term       
                                        $wpdb->insert(
                                            $wpdb->terms, 
                                            array(
                                                'term_id'    => $termData[self::DATA_TERM_ID], 
                                                'name'       => $termData[self::DATA_TERM_NAME], 
                                                'slug'       => $termData[self::DATA_TERM_SLUG], 
                                                'term_group' => $termData[self::DATA_TERM_GROUP],
                                            )
                                        );
                                        
                                        // Update the taxonomy
                                        $wpdb->insert(
                                            $wpdb->term_taxonomy,
                                            array(
                                                'term_taxonomy_id' => $termData[self::DATA_TERM_TAXONOMY_ID],
                                                'term_id'          => $termData[self::DATA_TERM_ID], 
                                                'taxonomy'         => $termData[self::DATA_TERM_TAXONOMY], 
                                                'description'      => $termData[self::DATA_TERM_DESCRIPTION],
                                                'parent'           => $termData[self::DATA_TERM_PARENT], 
                                                'count'            => $termData[self::DATA_TERM_COUNT],
                                            )
                                        );
                                                
                                        // Update term meta
                                        foreach ($termData[self::DATA_EXTRA_META] as $metaKey => $metaValue) {
                                            update_term_meta(
                                                $termData[self::DATA_TERM_ID], 
                                                $metaKey, 
                                                $this->_replacePlaceholders($metaValue)
                                            );
                                        }

                                        // Store the term for later removal
                                        $termIds['remove'][] = array($termData[self::DATA_TERM_ID], $termData[self::DATA_TERM_TAXONOMY_ID]);
                                    }
                                    
                                    // Store the revert value
                                    $this->_diffDb(
                                        $actionType, 
                                        $payloadKey, 
                                        $termIds
                                    );
                                    break;
                                
                                // Posts
                                case self::DATA_KEY_CONTENT_POSTS:
                                    // Log the event
                                    $this->_log(__('Creating posts', '{project.destDir}'));
                                    
                                    // Collect the sticky posts
                                    $stickyPosts = get_option('sticky_posts');
                                    
                                    // Prepare the diff data
                                    $diffData = array(
                                        'posts'      => array(),
                                        'stickies'   => $stickyPosts,
                                        'taxonomies' => array(),
                                        'comments'   => array(),
                                    );
                                    
                                    // Get the current time
                                    $time = time();
                                    
                                    // Get the post date
                                    $postDate = gmdate("Y-m-d H:i:s", $time);
                                    
                                    // Get the post date GMT
                                    $postDateGMT = gmdate("Y-m-d H:i:s", ($time + get_option('gmt_offset') * HOUR_IN_SECONDS));
                                    
                                    // Go through each post
                                    foreach ($payloadValue as $postData) {
                                        // Set the new post ID
                                        $postData[self::DATA_POST_ID] += $incrementPosts;
                                        
                                        // Navigation Menu Item, increment the name as well
                                        if ('nav_menu_item' === $postData[self::DATA_POST_TYPE]) {
                                            $postData[self::DATA_POST_NAME] = intval($postData[self::DATA_POST_NAME]) + $incrementPosts;
                                        }
                                        
                                        // Set the parent
                                        if ($postData[self::DATA_POST_PARENT] > 0) {
                                            $postData[self::DATA_POST_PARENT]+= $incrementPosts;
                                        }
                                        
                                        // Restore the content
                                        $postData[self::DATA_POST_CONTENT] = $this->_fixContent($postData[self::DATA_POST_CONTENT], $incrementPosts, true);
                                        
                                        // Restore the excerpt
                                        $postData[self::DATA_POST_EXCERPT] = $this->_decode($postData[self::DATA_POST_EXCERPT]);
                                        
                                        // Replace the placeholders
                                        $postData[self::DATA_POST_GUID] = $this->_replacePlaceholders($postData[self::DATA_POST_GUID]);
                                        
                                        // Set the dates
                                        $postData[self::DATA_POST_DATE]         = $postDate;
                                        $postData[self::DATA_POST_DATE_GMT]     = $postDateGMT;
                                        $postData[self::DATA_POST_MODIFIED]     = $postDate;
                                        $postData[self::DATA_POST_MODIFIED_GMT] = $postDateGMT;
                                            
                                        // Prepare the insert values
                                        $insertArguments = $postData;
                                        
                                        // Remove extra arguments
                                        unset($insertArguments[self::DATA_EXTRA_IS_STICKY]);
                                        unset($insertArguments[self::DATA_EXTRA_TAXONOMIES]);
                                        unset($insertArguments[self::DATA_EXTRA_META]);
                                        unset($insertArguments[self::DATA_EXTRA_COMMENTS]);
                                        
                                        // Set the author ID
                                        $insertArguments[self::DATA_POST_AUTHOR] = $currentUser->ID;
                                        
                                        // Make sure the post slug is unique
                                        $insertArguments[self::DATA_POST_NAME] = wp_unique_post_slug(
                                            $postData[self::DATA_POST_NAME], 
                                            $postData[self::DATA_POST_ID], 
                                            $postData[self::DATA_POST_STATUS], 
                                            $postData[self::DATA_POST_TYPE], 
                                            $postData[self::DATA_POST_PARENT] 
                                        );
                                        
                                        // Create the post       
                                        $wpdb->insert($wpdb->posts, $insertArguments);
                                        
                                        // Store the new posts
                                        $diffData['posts'][] = $postData[self::DATA_POST_ID];
                                        
                                        // Mark this sticky post
                                        if ($postData[self::DATA_EXTRA_IS_STICKY]) {
                                            $stickyPosts[] = $postData[self::DATA_POST_ID];
                                        }
                                        
                                        // Set the taxonomies
                                        foreach ($postData[self::DATA_EXTRA_TAXONOMIES] as $objectTaxonomy => $objectTermIds) {
                                            // Increment each term
                                            $objectTermIds = array_map(
                                                function($item) use ($incrementTerms) {
                                                    return $item + $incrementTerms;
                                                }, 
                                                $objectTermIds
                                            );
                                                
                                            // Set the object terms
                                            wp_set_object_terms($postData[self::DATA_POST_ID], $objectTermIds, $objectTaxonomy, false);
                                            
                                            // Store the new object relationships
                                            $diffData['taxonomies'][] = $postData[self::DATA_POST_ID];
                                        }
                                        
                                        // Go through the meta
                                        foreach ($postData[self::DATA_EXTRA_META] as $metaKey => $metaValue) {
                                            // Replace placeholders flag
                                            $replacePlaceholders = true;

                                            // Metadata fix
                                            if ('_wp_attachment_metadata' === $metaKey) {
                                                $replacePlaceholders = false;
                                            }
                                            
                                            // Replace the placeholders
                                            $metaValue = $replacePlaceholders ? $this->_replacePlaceholders($metaValue) : $metaValue;
                                            
                                            // Increment the IDs
                                            if (preg_match('%\_id$%', $metaKey)) {
                                                $metaValue += $incrementPosts;
                                            }
                                            
                                            // Insert the post Meta information
                                            $wpdb->insert(
                                                $wpdb->postmeta, 
                                                array(
                                                    'post_id'    => $postData[self::DATA_POST_ID], 
                                                    'meta_key'   => $metaKey,
                                                    'meta_value' => $metaValue,
                                                )
                                            );
                                        }
                                        
                                        // Add the comments
                                        foreach ($postData[self::DATA_EXTRA_COMMENTS] as $commentData) {
                                            // Set the new comment ID
                                            $commentData[self::DATA_COMMENT_ID] += $incrementComments;
                                            $commentData[self::DATA_COMMENT_POST_ID] += $incrementPosts;
                                            
                                            // Replace the placeholders
                                            $commentData[self::DATA_COMMENT_AUTHOR_URL] = $this->_replacePlaceholders($commentData[self::DATA_COMMENT_AUTHOR_URL]);
                                            $commentData[self::DATA_COMMENT_CONTENT] = $this->_decode($commentData[self::DATA_COMMENT_CONTENT]);
                                            
                                            // Replace all e-mail addresses
                                            $commentData[self::DATA_COMMENT_AUTHOR_EMAIL] = $currentUser->user_email;
                                            
                                            // Set the user ID
                                            $commentData[self::DATA_COMMENT_USER_ID] = (1 == $commentData[self::DATA_COMMENT_USER_ID] ? $currentUser->ID : $commentData[self::DATA_COMMENT_USER_ID]);
                                                
                                            // Prepare the insert comment data
                                            $insertCommentArguments = $commentData;
                                            
                                            // Remove extra arguments
                                            unset($insertCommentArguments[self::DATA_EXTRA_META]);
                                            
                                            // Create the comment
                                            $wpdb->insert($wpdb->comments, $insertCommentArguments);
                                            
                                            // Go through the meta
                                            foreach ($commentData[self::DATA_EXTRA_META] as $metaKey => $metaValue) {
                                                // Replace the placeholders
                                                $metaValue = $this->_replacePlaceholders($metaValue);

                                                // Insert the Comment Meta information
                                                $wpdb->insert(
                                                    $wpdb->commentmeta, 
                                                    array(
                                                        'comment_id' => $commentData[self::DATA_COMMENT_ID], 
                                                        'meta_key'   => $metaKey,
                                                        'meta_value' => $metaValue,
                                                    )
                                                );
                                            }
                                            
                                            // Store the comments to remove
                                            $diffData['comments'][] = $commentData[self::DATA_COMMENT_ID];
                                        }
                                    }
                                    
                                    // Mark the sticky posts
                                    update_option('sticky_posts', $stickyPosts);
                                    
                                    // Store the revert value
                                    $this->_diffDb(
                                        $actionType, 
                                        $payloadKey, 
                                        $diffData
                                    );
                                    break;
                            }
                        }
                        break;
                }
            }
        }
        
        /**
         * Uninstall the DataBase modifications
         */
        protected function _uninstallDb() {
            global $wpdb;
            
            // Log the event
            $this->_log(__('Reverting DataBase changes', '{project.destDir}'));
            
            if (isset($this->_diff[self::KEY_ACTIONS_DB])) {
                foreach ($this->_diff[self::KEY_ACTIONS_DB] as $actionType => $payload) {
                    switch ($actionType) {
                        // Theme Modifications
                        case self::DATA_KEY_CUSTOMIZER:
                            // Log the event
                            $this->_log(__('Reverting Theme Modifications', '{project.destDir}'));
                            
                            // Go through each Theme Mod
                            foreach ($payload as $payloadKey => $payloadValue) {
                                // Newly added key
                                if (false === $payloadValue) {
                                    // Remove it
                                    remove_theme_mod($payloadKey);
                                } else {
                                    {if.core.staging}$this->_log(array(
                                        '_uninstallDb::set_theme_mod',
                                        $payloadKey,
                                        $payloadValue
                                    ), true, false);{/if.core.staging}
                            
                                    // Revert to the old value
                                    set_theme_mod($payloadKey, $payloadValue);
                                }
                            }
                            break;
                        
                        // Theme Modifications - Extended
                        case self::DATA_KEY_CUSTOMIZER_EXTENDED:
                            // Log the event
                            $this->_log(__('Reverting Customizer data', '{project.destDir}'));
                            
                            // Go through each Customizer option
                            foreach ($payload as $payloadKey => $payloadValue) {
                                // Newly added key
                                if (false === $payloadValue) {
                                    // Remove it
                                    delete_option($payloadKey);
                                } else {
                                    // Revert to the old value
                                    update_option($payloadKey, $payloadValue);
                                }
                            }
                            break;
                        
                        // Widgets/Sidebars
                        case self::DATA_KEY_WIDGETS:
                            // Log the event
                            $this->_log(__('Uninstalling Widgets', '{project.destDir}'));
                            
                            // Go through each Customizer option
                            foreach ($payload as $payloadKey => $payloadValue) {
                                // Newly added key
                                if (false === $payloadValue) {
                                    // Remove it
                                    delete_option($payloadKey);
                                } else {
                                    // Revert to the old value
                                    update_option($payloadKey, $payloadValue);
                                }
                            }
                            break;
                        
                        // Revolution Slider
                        case self::DATA_KEY_REV_SLIDER:
                            // Revolution slider not installed, skip the task
                            if (!class_exists('RevSliderGlobals')) {
                                break;
                            }
                            
                            // Log the event
                            $this->_log(__('Uninstalling Sliders', '{project.destDir}'));
                            
                            // Go through each widget/sidebar option
                            foreach ($payload as $payloadKey => $payloadValue) {
                                switch ($payloadKey) {
                                    case self::DATA_KEY_REV_SLIDER_TABLES:
                                        // Go through the tables diff
                                        foreach ($payloadValue as $tableName => $tableRows) {
                                            foreach ($tableRows as $rowId => $rowData) {
                                                if (false === $rowData) {
                                                    $wpdb->delete(
                                                        $tableName,
                                                        array(
                                                            'id' => $rowId,
                                                        )
                                                    );
                                                } else {
                                                    // Revert the row
                                                    $wpdb->update(
                                                        $tableName,
                                                        $rowData,
                                                        array(
                                                            'id' => $rowId
                                                        )
                                                    );
                                                }
                                            }
                                        }
                                        break;
                                }
                            }
                            break;
                        
                        // Content
                        case self::DATA_KEY_CONTENT:
                            // Go through each content type
                            foreach ($payload as $payloadKey => $payloadValue) {
                                switch ($payloadKey) {
                                    // Terms
                                    case self::DATA_KEY_CONTENT_TERMS:
                                        // Log the event
                                        $this->_log(__('Removing terms', '{project.destDir}'));
                                        
                                        // Prepare the term IDs
                                        $termIdsToRemove = array();
                                        
                                        // Prepare the pair where statement
                                        $whereStatementArray = array();
                                        
                                        // Remove our custom terms
                                        foreach ($payloadValue['remove'] as $termData) {
                                            // Store the IDs to remove
                                            $termIdsToRemove[] = $termData[0];
                                            
                                            // Append to the composite where statement
                                            $whereStatementArray[] = '( `term_id` = ' . $termData[0] . ' AND `term_taxonomy_id` = ' . $termData[1] . ' )';
                                        }
                                        
                                        // Remove our custom terms' meta information
                                        $wpdb->query("DELETE FROM {$wpdb->termmeta} WHERE `term_id` IN ( " . implode(', ', $termIdsToRemove) . " )");
                                        
                                        // Remove our custom terms
                                        $wpdb->query("DELETE FROM {$wpdb->terms} WHERE `term_id` IN ( " . implode(', ', $termIdsToRemove) . " )");
                                        
                                        // Remove taxonomy-term relationships
                                        $wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE ( " . implode(' OR ', $whereStatementArray) . " )");
                                        
                                        // Restore the old terms
                                        foreach ($payloadValue['restore'] as $termData) {
                                            // Update the colliding row
                                            $wpdb->update(
                                                $wpdb->terms, 
                                                array(
                                                    'name' => $termData[1],
                                                    'slug' => $termData[2],
                                                ), 
                                                array(
                                                    'term_id' => $termData[0],
                                                )
                                            );
                                        }
                                        break;
                                    
                                    // Posts
                                    case self::DATA_KEY_CONTENT_POSTS:
                                        // Log the event
                                        $this->_log(__('Removing posts', '{project.destDir}'));
                                        
                                        // Remove our custom posts
                                        $wpdb->query("DELETE FROM {$wpdb->posts} WHERE `ID` IN ( " . implode(', ', $payloadValue['posts']) . " )");
                                        
                                        // Remove our custom posts metadata
                                        $wpdb->query("DELETE FROM {$wpdb->postmeta} WHERE `post_id` IN ( " . implode(', ', $payloadValue['posts']) . " )");
                                        
                                        // Remove our custom comments
                                        $wpdb->query("DELETE FROM {$wpdb->comments} WHERE `comment_ID` IN ( " . implode(', ', $payloadValue['comments']) . " )");
                                        
                                        // Remove our custom comments metadata
                                        $wpdb->query("DELETE FROM {$wpdb->commentmeta} WHERE `comment_ID` IN ( " . implode(', ', $payloadValue['comments']) . " )");
                                        
                                        // Remove object terms
                                        $wpdb->query("DELETE FROM {$wpdb->term_relationships} WHERE `object_id` IN ( " . implode(', ', $payloadValue['taxonomies']) . " )");
                                        
                                        // Set the stickies back
                                        update_option('sticky_posts', $payloadValue['stickies']);
                                    break;
                                }
                            }
                            break;
                    }
                }
            }
        }
        
        /**
         * Install the snapshot Files
         */
        protected function _installFs() {
            global $wp_filesystem;
            
            // Log the event
            $this->_log(__('Performing FileSystem changes', '{project.destDir}'));
            
            // Create the St_Snapshot::FOLDER_FILES_REVERT folder
            if (!$wp_filesystem->is_dir($revertFolderPath = $this->_getRevertFolderPath())) {
                // Create the folder
                call_user_func(array($wp_filesystem, 'mkdir'), $revertFolderPath);

                // Add an index
                $wp_filesystem->put_contents($revertFolderPath . '/index.html', '');
            }
            
            // Prepare the source path
            $filesPath = $this->_getFilesFolderPath();
            
            // Get the upload folder
            $uploadDirInfo = wp_upload_dir(null, false);
            
            // Go through the files
            foreach ($this->_actionsFs as $fileKey => $relativePath) {
                // Prepare the source path
                $sourcePath = $filesPath . '/' . $fileKey . '._st';
                
                // Prepare the destination path
                $destPath = rtrim($uploadDirInfo['basedir'], '\\/') . '/' . $relativePath;
                
                // File found
                if ($wp_filesystem->is_file($sourcePath)) {
                    // Store the revert value
                    $this->_diffFs($destPath);
                    
                    // Assuming write access granted to uploads folder
                    if (self::fsMkdirRecursive(dirname($destPath))) {
                        // Copy the file
                        $wp_filesystem->copy($sourcePath, $destPath, true);
                    } else {
                        $this->_log(
                            sprintf(__('Could not create file %s', '{project.destDir}'), $relativePath), 
                            false
                        );
                    }
                }
            }
        }
        
        /**
         * Create a directory recursively using WP_FileSystem.
         * 
         * @param string $relativeFilePath Relative file path
         * @return boolean True on success, false on failure
         */
        public static function fsMkdirRecursive($dirPath) {
            global $wp_filesystem;
            
            // Directory structure already created
            if ($wp_filesystem->is_dir($dirPath)) {
                return true;
            }
            
            // Prepare the relative path
            $relativePath = preg_replace('%^' . preg_quote(ABSPATH) . '%', '', $dirPath);
            
            // Break the relative file path apart
            $tree = explode('/', $relativePath);
            
            // Valid tree
            $treeSize = count($tree);
            if ($treeSize) {
                for ($branchLength = 1; $branchLength <= $treeSize; $branchLength++) {
                    // Get the subpath
                    $subPath = ABSPATH . implode('/', array_slice($tree, 0, $branchLength));
                    
                    // Directory not found
                    if (!$wp_filesystem->is_dir($subPath)) {
                        // Attempt to create it
                        if (!call_user_func(array($wp_filesystem, 'mkdir'), $subPath)) {
                            return false;
                        }
                    }
                }
            }
            
            // All went well
            return true;
        }
        
        /**
         * Uninstall the snapshot Files
         */
        protected function _uninstallFs() {
            global $wp_filesystem;
            
            // Log the event
            $this->_log(__('Reverting FileSystem changes', '{project.destDir}'));
            
            // FileSystem actions defined
            if (isset($this->_diff[self::KEY_ACTIONS_FS]) && is_array($this->_diff[self::KEY_ACTIONS_FS])) {
                // Go through each file
                foreach ($this->_diff[self::KEY_ACTIONS_FS] as $fileKey => $fileData) {
                    // Get the information
                    list($fullPath, $fileExists) = $fileData;
                    
                    // Remove the data
                    $wp_filesystem->delete($fullPath);
                    
                    // Restore the original
                    if ($fileExists) {
                        // Prepare the revert file path
                        $revertPath = $this->_getRevertFolderPath() . '/' . $fileKey . '._st';
                        
                        // Revert file stored
                        if ($wp_filesystem->is_file($revertPath)) {
                            $wp_filesystem->copy($revertPath, $fullPath, true);
                        }
                    }
                }
            }
        }
    }
    
    /**
     * Snapshot manager
     */
    class St_SnapshotManager {
        
        /**
         * Error codes
         */
        const ERROR_NO_SNAPSHOTS            = 'no_snapshots';
        const ERROR_SNAPSHOT_ID_NOT_NUMERIC = 'snapshot_id_not_numeric';
        const ERROR_SNAPSHOT_NOT_FOUND      = 'snapshot_not_found';
        const ERROR_SNAPSHOT_INCOMPLETE     = 'snapshot_incomplete';
        const ERROR_UPLOAD_NO_ARCHIVE       = 'upload_no_archive';
        const ERROR_UPLOAD_CANNOT_OVERRIDE  = 'upload_cannot_override';
        const ERROR_UPLOAD_NO_DIR           = 'upload_no_dir';
        const ERROR_UPLOAD_INVALID_ARCHIVE  = 'upload_invalid_archive';
        
        /**
         * Singleton instance of St_SnapshotManager
         * 
         * @var St_SnapshotManager
         */
        protected static $_instance = null;
        
        /**
         * Snapshot list
         * 
         * @var St_Snapshot[]
         */
        protected $_snapshots = array();
        
        /**
         * Snapshot Manager
         * 
         * @return St_SnapshotManager
         */
        protected function __construct() {
            // Get all snapshots from the folder
            foreach (glob(St_Snapshot::getSnapshotDir() . '/*', GLOB_ONLYDIR) as $snapshotPath) {
                // Prepare the snapshot ID
                $snapshotId = basename($snapshotPath);
                
                // It must be a number
                if (!is_numeric($snapshotId)) {
                    continue;
                }
                
                // The snapsot ID is an integer
                $snapshotId = intval($snapshotId);
                
                // Attempt to store the snapshot definition
                try {
                    // Info and Actions are mandatory
                    if (!is_file($infoPath = $snapshotPath . '/' . St_Snapshot::FILE_INFO)) {
                        throw new St_Exception(
                            __('Snapshot definition is incomplete', '{project.destDir}'),
                            self::ERROR_SNAPSHOT_INCOMPLETE,
                            $snapshotId
                        );
                    }

                    // Prepare the snapshot info only - optimize memory for large number of snapshots
                    $snapshotInfo = null;

                    // Load the info
                    require $infoPath;

                    // Store the snapshot object
                    $this->_snapshots[$snapshotId] = new St_Snapshot($snapshotId, $snapshotInfo);
                } catch (Exception $exc) {
                    // Invalid Snapshot definition, log the exception and skip it
                    St_Ajax::storeCaughtException($exc);
                }
            }
        }
        
        /**
         * Get a Singleton instance of St_SnapshotManager
         * 
         * @return St_SnapshotManager
         */
        public static function getInstance() {
            if (null === self::$_instance) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        /**
         * Get the public information for all snapshots
         * 
         * @return array Numeric array of Snapshot definitions
         * @throws St_Exception
         */
        public function getPublicInfo() {
            // No snapshots found
            if (!count($this->_snapshots)) {
                throw new St_Exception(
                    __('There are no snapshots available', '{project.destDir}'),
                    self::ERROR_NO_SNAPSHOTS
                );
            }

            // Convert Snapshots to arrays
            return array_map(
                function(/*@var $item St_Snapshot*/ $item) {
                    return $item->getPublicInfo();
                },
                // Convert to numeric array
                array_values($this->_snapshots)
            );
        }
        
        /**
         * Get the snapshots list
         * 
         * @return St_Snapshot[]
         */
        public function getAll() {
            return $this->_snapshots;
        }
        
        /**
         * Upload a snapshot archive
         * 
         * @return int New snapshot ID
         */
        public function upload() {
            global $wp_filesystem;
            
            // Invalid file specified
            if (!isset($_FILES) || !isset($_FILES['file']) || !preg_match('%^\d+\.zip$%i', $_FILES['file']['name'])) {
                throw new St_Exception(
                    __('Please upload a valid snapshot archive provided with this theme', '{project.destDir}'),
                    self::ERROR_UPLOAD_NO_ARCHIVE
                );
            }
            
            // Prepare the snapshot ID
            $snapshotId = intval(preg_replace('%\.zip$%i', '', $_FILES['file']['name']));
            
            // Cannot write over a snapshot that is already installed
            if ($wp_filesystem->is_file(St_Snapshot::getSnapshotDir() . '/' . $snapshotId . '/' . St_Snapshot::FILE_REVERT)) {
                throw new St_Exception(
                    __('Cannot write over installed snapshots', '{project.destDir}'),
                    self::ERROR_UPLOAD_CANNOT_OVERRIDE
                );
            }
            
            // Prepare the upload path
            $uploadPathTemporary = St_Snapshot::getSnapshotDir(null, true);

            // Clean-up failed attempts
            if ($wp_filesystem->is_dir($uploadPathTemporary)) {
                $wp_filesystem->delete($uploadPathTemporary, true, 'd');
            }
            
            // (Re)Create the upload path
            if (!St_Snapshot::fsMkdirRecursive($uploadPathTemporary)) {
                throw new St_Exception(
                    __('Could not create directory structure', '{project.destDir}'),
                    self::ERROR_UPLOAD_NO_DIR
                );
            }
            
            // Make it accessible to everybody temporarily
	    @chmod($_FILES['file']['tmp_name'], 0777);

            // Extract the archive
            $unzipResult = unzip_file($_FILES['file']['tmp_name'], $uploadPathTemporary);
            
            // Could not extract the archive
            if ($unzipResult instanceof WP_Error) {
                throw new St_Exception(
                    $unzipResult->get_error_message(),
                    $unzipResult->get_error_code()
                );
            }
            
            // Validate folder structure
            do {
                // The snapshot ID folder was found
                if ($wp_filesystem->is_dir($snapshotPath = $uploadPathTemporary . '/' . $snapshotId)) {
                    // The files are there
                    if ($wp_filesystem->is_file($infoPath = $snapshotPath . '/' . St_Snapshot::FILE_INFO)) {
                        // Prepare the snapshot info only - optimize memory for large number of snapshots
                        $snapshotInfo = null;

                        // Load the info
                        require $infoPath;

                        // Attempt to create a new snapshot object, validating the package in the process
                        $this->_snapshots[$snapshotId] = new St_Snapshot($snapshotId, $snapshotInfo, true);

                        // Everything seems OK
                        break;
                    }
                }
                
                // Exception clean-up of the temporary path
                $wp_filesystem->delete($uploadPathTemporary, true, 'd');
                
                // Invalid archive uploaded
                throw new St_Exception(
                    __('Invalid snapshot archive', '{project.destDir}'),
                    self::ERROR_UPLOAD_INVALID_ARCHIVE
                );
            } while (false);
            
            // Prepare the final path
            if ($wp_filesystem->is_dir($finalSnapshotPath = St_Snapshot::getSnapshotDir() . '/' . $snapshotId)) {
                // Remove older versions
                $wp_filesystem->delete($finalSnapshotPath, true, 'd');
            }
            
            // Re-create the snapshot folder
            St_Snapshot::fsMkdirRecursive($finalSnapshotPath);
            
            // Copy the files recursively
            copy_dir($uploadPathTemporary . '/' . $snapshotId, $finalSnapshotPath);
            
            // Final clean-up of the temporary path
            $wp_filesystem->delete($uploadPathTemporary, true, 'd');
            
            // Switch the snapshot to live mode
            $this->_snapshots[$snapshotId]->setTestMode(false);
            
            // All done
            return $snapshotId;
        }
        
        /**
         * Delete a snapshot by ID
         * 
         * @param int $snapshotId Snapshot ID
         * @return St_Snapshot
         * @throws St_Exception
         */
        public function deleteById($snapshotId = null) {
            // Delete the snapshot
            $this->getById($snapshotId)->delete();
            
            // The snapshot ID is numeric
            $snapshotId = intval($snapshotId);
            
            // Update internal list
            unset($this->_snapshots[$snapshotId]);
        }
        
        /**
         * Get a snapshot by ID
         * 
         * @param int $snapshotId Snapshot ID
         * @return St_Snapshot
         * @throws St_Exception
         */
        public function getById($snapshotId = null) {
            // No snapshots found
            if (!count($this->_snapshots)) {
                throw new St_Exception(
                    __('There are no snapshots available', '{project.destDir}'),
                    self::ERROR_NO_SNAPSHOTS
                );
            }
            
            // Validate the snapshot ID
            if (!is_numeric($snapshotId)) {
                throw new St_Exception(
                    __('The snapshot ID must be a number', '{project.destDir}'),
                    self::ERROR_SNAPSHOT_ID_NOT_NUMERIC
                );
            }
            
            // Convert to integer
            $snapshotId = intval($snapshotId);
            
            // Snapshot not set
            if (!isset($this->_snapshots[$snapshotId])) {
                throw new St_Exception(
                    __('Snapshot does not exist', '{project.destDir}'),
                    self::ERROR_SNAPSHOT_NOT_FOUND
                );
            }
            
            // All done
            return $this->_snapshots[$snapshotId];
        }
    }
}

/*EOF*/