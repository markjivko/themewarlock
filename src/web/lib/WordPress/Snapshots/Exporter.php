<?php
/**
 * Theme Warlock - WordPress_Snapshots_Exporter
 * 
 * @title      Snapshots Exporter
 * @desc       Export the current snapshot as a theme-compatible "demo package"
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Snapshots_Exporter {

    /**
     * Export Files and Folders
     */
    const FILE_INFO    = 'info.php';
    const FILE_ACTIONS = 'actions.php';
    const FOLDER_FILES = 'files';
    
    /**
     * Export Keys
     */
    const KEY_TITLE                 = 'title';
    const KEY_DESCRIPTION           = 'description';
    const KEY_COLORS                = 'colors';
    const KEY_ACTIONS_FS            = 'actions-fs';
    const KEY_ACTIONS_DB            = 'actions-db';
    
    /**
     * Export data keys
     */
    const DATA_KEY_FILES               = 'files';
    const DATA_KEY_CUSTOMIZER          = 'customizer';
    const DATA_KEY_CUSTOMIZER_EXTENDED = 'customizer-extended';
    const DATA_KEY_WIDGETS             = 'widgets';
    const DATA_KEY_CONTENT             = 'content';
    const DATA_KEY_CONTENT_POSTS       = 'content-posts';
    const DATA_KEY_CONTENT_CATS        = 'content-cats';
    const DATA_KEY_CONTENT_TAGS        = 'content-tags';
    const DATA_KEY_CONTENT_MENUS       = 'content-menus';
    const DATA_KEY_CONTENT_TERMS       = 'content-terms';
    
    // File actions
    const FILE_ACTION_ORIGINAL = 'original';
    const FILE_ACTION_CLIENT   = 'client';
    
    /**
     * Parent snapshot object
     * 
     * @var WordPress_Snapshots_Snapshot
     */
    protected $_snapshot;
    
    /**
     * Path to the final project's export folder
     * 
     * @example /home/user/projects/tw-projects/u1/1/dist/Snapshots/1
     * @var string
     */
    protected $_exportPath = null;
    
    /**
     * Test Mode
     * 
     * @var boolean
     */
    protected $_testMode = false;
    
    /**
     * Snapshots Exporter - can only be constructed by a WordPress_Snapshots_Snapshot instance
     * 
     * @param WordPress_Snapshots_Snapshot $snapshotObject Snapshot object
     * 
     */
    public function __construct(WordPress_Snapshots_Snapshot $snapshotObject) {
        // Store the parent snapshot
        $this->_snapshot = $snapshotObject;
    }
    
    /**
     * Get the test mode
     * 
     * @return boolean
     */
    public function getTestMode() {
        return $this->_testMode;
    }
    
    /**
     * Set the test mode
     * 
     * @param boolean $testMode (optional) Test Mode - alter the local snapshot instead of the live one; default <b>true</b>
     * @return WordPress_Snapshots_Exporter
     */
    public function setTestMode($testMode = true) {
        // Store the test mode
        $this->_testMode = $testMode;
        
        // All done
        return $this;
    }
    
    /**
     * <b>WARNING</b>: To be called <b>after</b>
     *  WordPress_Snapshots_Snapshot::<b>activate</b>()!<br/>
     * Export the current snapshot to a <b>St_SnapshotManager</b> 
     * compliant package in the DIST folder.<br/>
     * 
     * @return WordPress_Snapshots_Exporter
     * @throws Exception
     */
    public function export() {
        // Live mode
        if (!$this->getTestMode()) {
            // Cannot export in non-project mode
            if (null === Tasks::$project || !strlen(Tasks_1NewProject::$destDir) || !isset(Cli_Run_Integration::$options[Cli_Run_Integration::OPT_PROJECT_VERSION])) {
                throw new Exception('Cannot export outside of Tasks mode!');
            }
        }
        
        // Create the export path
        if (!is_dir($this->_exportPath = $this->_snapshot->getDistPath())) {
            Folder::create($this->_exportPath, 0777, true);
        } else {
            Folder::clean($this->_exportPath);
        }
        
        // Get the export data
        $wpData = WordPress::executeAction(
            WordPress::TOOLS_TW,
            WordPress::TOOL_TW_SNAPSHOT_EXPORT,
            $this->_snapshot->getId()
        );
        
        // Parse the Raw WordPress exported data
        $this->_parse($wpData);
        
        // Save the preview file
        copy($this->_snapshot->getPreviewPath(), $this->_exportPath . '/' . WordPress_Snapshots_Snapshot::PREVIEW_FILE_NAME);
        
        // Prepare the client snapshots
        $this->_prepareClientSnapshots($wpData);
        
        // All done
        return $this;
    }
    
    /**
     * Prepare the client snapshots:<ul>
     * <li>cover images with the chosen pattern</li>
     * <li>store the pruned actions file</li>
     * </ul>
     * 
     * @param array $parseResult
     */
    protected function _prepareClientSnapshots($wpData) {
        // Prepare the actions
        $fileActions = array(
            self::FILE_ACTION_CLIENT   => $this->_exportPath,
            self::FILE_ACTION_ORIGINAL => IO::outputPath() . '/' . Dist::FOLDER_SNAPSHOTS . '/' . $this->_snapshot->getId(),
        );
                
        // Save the original resources
        Folder::copyContents($fileActions[self::FILE_ACTION_CLIENT], $fileActions[self::FILE_ACTION_ORIGINAL]);

        // Re-parse the data, removing unwanted keys
        $parseResult = $this->_parse($wpData, true);
        
        // Prepare the pattern object
        $patternObject = $this->_snapshot->getPatternObject();

        // Files specified with this snapshot
        if (isset($parseResult[self::FILE_ACTIONS]) && isset($parseResult[self::FILE_ACTIONS][self::KEY_ACTIONS_FS])) {
            if (is_array($parseResult[self::FILE_ACTIONS][self::KEY_ACTIONS_FS]) && count($parseResult[self::FILE_ACTIONS][self::KEY_ACTIONS_FS])) {
                // Go through the file actions
                foreach ($fileActions as $fileActionName => $fileActionPath) {
                    // Prepare the files path
                    $filesPath = $fileActionPath . '/' . self::FOLDER_FILES;

                    // Go through each file
                    foreach ($parseResult[self::FILE_ACTIONS][self::KEY_ACTIONS_FS] as $fileKey => $relativePath) {
                        // Get the original file path
                        $origFilePath = $filesPath . '/' . $fileKey . '._st';

                        // Not an image, nothing to optimize/cover
                        if (!preg_match('%\.(?:png|jpe?g|gif)$%i', basename($relativePath))) {
                            continue;
                        }
                        
                        // Get the temporary file path
                        $tempFilePath = $filesPath . '/' . basename($relativePath);

                        // Copy the file
                        copy($origFilePath, $tempFilePath);

                        // Cover the image with a pattern
                        try {
                            // Cover the image in a pattern (client-side only)
                            if (self::FILE_ACTION_CLIENT === $fileActionName) {
                                $patternObject->cover($tempFilePath);
                            }

                            // Optimize the image size on disk
                            Image::optimize($tempFilePath);

                            // Replace the original
                            copy($tempFilePath, $origFilePath);
                        } catch (Exception $exc) {
                            Log::check(Log::LEVEL_WARNING) && Log::warning($exc->getMessage(), $exc->getFile(), $exc->getLine());
                        }

                        // Remove the temporary file
                        @unlink($tempFilePath);
                    }
                }
            }
        }
    }
    
    /**
     * Parse the raw WordPress exported data
     * 
     * @param array   $wpData              Raw WordPress-exported data
     * @param boolean $removeSensitiveData Remove sensitive data from the export
     * @return array Parsed data
     */
    protected function _parse($wpData, $removeSensitiveData = false) {
        return array(
            // Parse the info - general snapshot definition
            self::FILE_INFO    => $this->_parseInfo($wpData, $removeSensitiveData),
            
            // Parse the actions - DataBase & FileSystem changes
            self::FILE_ACTIONS => $this->_parseActions($wpData, $removeSensitiveData),
        );
    }
    
    /**
     * Parse the raw WordPress data into the required WordPress_Snapshots_Exporter::FILE_INFO file,
     * which stores <b>general snapshot definition</b>
     *
     * @param array   $wpData              Raw WordPress data
     * @param boolean $removeSensitiveData Remove sensitive data from the export
     * @throws Exception
     */
    protected function _parseInfo($wpData, $removeSensitiveData = false) {
        // Validate Customizer key
        if (!isset($wpData[self::DATA_KEY_CUSTOMIZER])) {
            throw new Exception('Customizer data not provided');
        }
        
        // Prepare the colors array
        $colorsArray = array();
        foreach ($wpData[self::DATA_KEY_CUSTOMIZER] as $customizerKey => $customizerValue) {
            if (preg_match('%^st_color(\d+)$%', $customizerKey, $matches)) {
                $colorsArray[$matches[1]] = $customizerValue;
            }
        }
        
        // Get the project data
        $projectData = $this->_snapshot->getProject()->getConfig()->getProjectData();
        
        // Go through the items
        foreach ($projectData[Model_Project_Config::CATEGORY_CORE] as $coreConfigKey => $coreConfigValue) {
            if (preg_match('%^projectColor(\d+)Default$%i', $coreConfigKey, $colorMatches)) {
                if ($coreConfigValue instanceof Model_Project_Config_Item_Color) {
                    // Add missing color
                    if (!isset($colorsArray[$colorMatches[1]])) {
                        $colorsArray[$colorMatches[1]] = $coreConfigValue->getWpColor();
                    }
                }
            }
        }
        
        // No colors defined
        if (!count($colorsArray)) {
            throw new Exception('No colors defined');
        }
        ksort($colorsArray);
        
        // Prepare the result
        $result = array(
            self::KEY_TITLE       => $this->_snapshot->getTitle(),
            self::KEY_DESCRIPTION => $this->_snapshot->getDescription(),
            self::KEY_COLORS      => array_values($colorsArray),
        );
        
        // Save the file
        $this->_saveFile($result, self::FILE_INFO);
        
        // All done
        return $result;
    }
    
    /**
     * Parse the raw WordPress data into the require WordPress_Snapshots_Exporter::FILE_ACTIONS file,
     * which stores <b>DataBase & FileSystem changes</b>
     * 
     * @param array   $wpData Raw          WordPress data
     * @param boolean $removeSensitiveData Remove sensitive data from the export
     * @throws Exception
     */
    protected function _parseActions($wpData, $removeSensitiveData = false) {
        // Validate Customizer key
        if (!isset($wpData[self::DATA_KEY_CUSTOMIZER])) {
            throw new Exception('Customizer data not provided');
        }
        
        // Parse the customizer keys
        $wpData[self::DATA_KEY_CUSTOMIZER] = $this->_parseActionDbCustomizer($wpData[self::DATA_KEY_CUSTOMIZER]);
        
        // Remove sensitive data
        if ($removeSensitiveData) {
            // WP Customizer
            if (isset($wpData[self::DATA_KEY_CUSTOMIZER]) && is_array($wpData[self::DATA_KEY_CUSTOMIZER])) {
                foreach ($wpData[self::DATA_KEY_CUSTOMIZER] as $key => $value) {
                    // Remove Twitter Keys
                    if (preg_match('%^st_.*?_twitter_api_%', $key)) {
                        $wpData[self::DATA_KEY_CUSTOMIZER][$key] = '';
                    }
                }
            }
            
            // Widgets
            if (isset($wpData[self::DATA_KEY_WIDGETS]) && is_array($wpData[self::DATA_KEY_WIDGETS])) {
                foreach ($wpData[self::DATA_KEY_WIDGETS] as $widgetName => $widgetData) {
                    if(is_array($widgetData)) {
                        foreach ($widgetData as $widgetKey => $wdInfo) {
                            if (is_array($wdInfo)) {
                                foreach ($wdInfo as $wdKey => $wdValue) {
                                    // Hide the API keys
                                    if (preg_match('%^apiKey%', $wdKey)) {
                                        $wpData[self::DATA_KEY_WIDGETS][$widgetName][$widgetKey][$wdKey] = '';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        // Get the files list
        $filesList = $wpData[self::DATA_KEY_FILES];
        
        // Remove from the final data set
        unset($wpData[self::DATA_KEY_FILES]);
        
        // Prepare the result
        $result = array(
            // FileSystem changes
            self::KEY_ACTIONS_FS => $this->_parseActionFs($filesList),

            // DataBase changes
            self::KEY_ACTIONS_DB => $wpData,
        );
        
        // Save the Data
        $this->_saveFile($result, self::FILE_ACTIONS);
        
        // All done
        return $result;
    }
    
    /**
     * Save the files diff
     */
    protected function _parseActionFs($wpDataFiles) {
        // Create the directory
        if (!is_dir($filesPath = $this->_exportPath . '/' . self::FOLDER_FILES)) {
            Folder::create($filesPath, 0777, true);
        }
        
        // Clean-up
        Folder::clean($filesPath);
        
        // Prepare the result
        $result = array();
        
        // Go through the paths
        foreach ($this->_snapshot->getPaths() as $fileName => $relativePath) {
            if (!preg_match('%^wp\-content\/uploads\/%', $relativePath)) {
                continue;
            }
            
            // Remove the prefix
            $relativePath = preg_replace('%^wp\-content\/uploads\/%', '', $relativePath);
            
            // Use $wpDataFiles to remove unused files from the snapshot
            if (!in_array($relativePath, $wpDataFiles)) {
                continue;
            }
            
            // Prepare the file key
            $fileKey = md5($relativePath);
            
            // Store the data
            $result[$fileKey] = $relativePath;
            
            // Store the file
            copy($this->_snapshot->getLocation() . '/' . $fileName, $filesPath . '/' . $fileKey . '._st');
        }
        
        // All done
        return $result;
    }
    
    /**
     * Parse the customizer data
     * 
     * @param array $customizerArray
     */
    protected function _parseActionDbCustomizer($customizerArray) {
        // Prepare the result
        $result = array();
        
        // Go through the details
        foreach ($customizerArray as $key => $value) {
            // Ignore empty strings
            if (is_string($value) && !strlen($value)) {
                continue;
            }
            
            // Append the result
            $result[$key] = $value;
        }
        
        // Sort the array
        ksort($result);

        // All done
        return $result;
    }
    
    /**
     * Save the data in a PHP file.
     * The <b>snapshot-data-template.php</b> template is used, with support for 
     * data tags.
     * 
     * @param array  $information Associative array
     * @param string $fileName    PHP file name, ending with ".php"
     */
    protected function _saveFile($information, $fileName) {
        // Prepare the data type
        $dataType = ucfirst(preg_replace('%(?:\.php$|\W+)%i', '', $fileName));
        
        // Prepare the PHP code for the array contents
        $code = '';
        
        // Go through the data sets
        foreach ($information as $dataKey => $dataValue) {
            // Convert 2 spaces into 4 at line beginnings
            $exportedValue = preg_replace('%^((?: {2})*)%m', '${1}${1}', var_export($dataValue, true));
            
            // Custom exports
            switch ($dataKey) {
                // Non-associative array
                case self::KEY_COLORS:
                    $exportedValue = 'array(' . implode(', ', array_map(function($item){return var_export($item, true);}, $dataValue)). ')';
                    break;
                
                // Database actions keys
                case self::KEY_ACTIONS_DB:
                    $exportedValue = 'array(' . PHP_EOL . PHP_EOL;
                    
                    // Replace the keys
                    foreach ($dataValue as $dataValueKey => $dataValueValue) {
                        // Convert 2 spaces into 4 at line beginnings
                        $exportedDbValue = preg_replace('%^((?: {2})*)%m', '${1}${1}', var_export($dataValueValue, true));
                        
                        // Indent
                        $exportedDbValue = trim(
                            preg_replace(
                                '%^%m', '${0}' . str_repeat('    ', 1), 
                                $exportedDbValue
                            )
                        );
                        $exportedValue .= '    // ' . ucwords(preg_replace('%\W+%', ' ', $dataKey . ' ' . $dataValueKey)) . PHP_EOL;
                        $exportedValue .= '    St_Snapshot::DATA_KEY_' . strtoupper(preg_replace('%\W+%', '_', $dataValueKey)) . ' => ' . $exportedDbValue . ',' . PHP_EOL . PHP_EOL;
                    }
                    
                    $exportedValue .= ')' . PHP_EOL;
                    break;
            }
            
            // i18n
            if (is_string($dataValue)) {
                $exportedValue = "__(${exportedValue}, '{project.destDir}')";
            }
            
            // Append the code
            $code .= 
                '    // ' . ucwords(preg_replace('%\W+%', ' ', $dataKey)) . PHP_EOL .
                '    St_Snapshot::KEY_' . preg_replace('%\W+%', '_', strtoupper($dataKey)) . ' => ' . 
                // Indent
                trim(
                    preg_replace(
                        '%^%m', '${0}' . str_repeat('    ', 1), 
                        $exportedValue
                    )
                ) . ',' . PHP_EOL . 
                '    ' . PHP_EOL;
        }
        
        // Prepare the contents
        $contents = str_replace(
            array(
                '__ID__',
                '__TYPE__',
                '__CODE__'
            ), 
            array(
                $this->_snapshot->getId(),
                $dataType,
                trim($code)
            ), 
            file_get_contents(ROOT . '/web/resources/wordpress/snapshot-data-template.php')
        );
        
        // Live mode
        if (!$this->getTestMode()) {
            // Parse the data tags
            $contents = Addons::getInstance()->parseDataKeys(
                $contents, 
                Model_Project_Config::CATEGORY_CORE, 
                null, 
                'php'
            );
        }
        
        // Save the file
        file_put_contents($this->_exportPath . '/' . $fileName, $contents);
    }
    
}

/* EOF */