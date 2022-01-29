<?php
/**
 * Theme Warlock - Controller_Ajax_Projects
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_Ajax_Project extends Controller_Ajax {

    // Project errors
    const ERROR_EDIT_TOKEN_EXPIRED        = 'EDIT_TOKEN_EXPIRED';
    const ERROR_PROJECT_LOCKED_FOR_EXPORT = 'PROJECT_LOCKED_FOR_EXPORT';
    
    public static $logIgnore = array(
        'getPath',
        'taskCheck',
        'snapshotCapture',
        'snapshotImageView',
        'stageStatus',
        'imageView',
        'uiSetPreview',
    );
    
    /**
     * Generate a new quote for the current project
     * 
     * @return string Quote
     * @allowed admin,manager
     * @throws Exception
     */
    public function actionGetQuote($projectId, $userId, $category) {
        // Get the quote data
        $quote = Whisper_Inspiration::getQuote();

        // Store the result
        return sprintf(
            '%2$s | %1$s',
            $quote['text'],
            $quote['author']
        );
    }
    
    /**
     * Get the font weight available for a font family
     * 
     * @return string[] List of font styles/weights
     * @allowed admin,manager
     * @throws Exception
     */
    public function actionGetFontWeights() {
        // Get the theme name
        $fontFamily = trim(Input::getInstance()->postRequest('fontFamily'));
        
        // Invalid font family
        if (!strlen($fontFamily)) {
            throw new Exception('Font family must be specified');
        }
        
        // Go through the font list
        $result = null;
        foreach (Google_Fonts::FONT_LIST as $fontDetails) {
            if ($fontDetails[Google_Fonts::FONT_FAMILY] == $fontFamily) {
                $result = $fontDetails[Google_Fonts::FONT_STYLES];
                break;
            }
        }
        
        // Nothing found
        if (null === $result) {
            throw new Exception('Font family "' . $fontFamily . '" was not definde in Google_Fonts');
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the available tags for the current file type
     * 
     * @return string HTML Documentation
     * @allowed admin,manager
     * @throws Exception
     */
    public function actionGetTagDocs($extension = Model_Project_Config_Item_Code::EXT_JS) {
        // Parse the text
        $description = Parsedown::instance()->text(file_get_contents(ROOT . '/README.md'));
        
        // @TODO use the $extension parameter to narrow down the details
        return $description;
    }
    
    /**
     * Create a new project AND mark it as current
     * 
     * @return string Project path
     * @allowed admin,manager
     * @throws Exception
     */
    public function create() {
        // Get the theme name
        $themeName = trim(Input::getInstance()->postRequest('themeName'));
        
        // Mandatory theme name
        if (!strlen($themeName)) {
            throw new Exception('Theme name is mandatory');
        }
        
        // Validate length
        if (strlen($themeName) > 100) {
            throw new Exception('Theme name must be lower than 100 characters');
        }
        
        // Get the framework ID
        $themeFramework = Input::getInstance()->postRequest('themeFramework');
        
        // Get all the available framework types
        $availableFrameworks = Framework::getAll(true);
        
        // Validate the framework
        if (!isset($availableFrameworks[$themeFramework])) {
            throw new Exception('Invalid framework ID specified');
        }
        
        /*@var $userModel Model_User*/
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
        // Get the projects instance
        $projects = Model_Projects::getInstance($userModel->id);
        
        /*@var $project Model_Project*/
        $project = $projects->create(null, $themeName, $themeFramework);
        
        // All done
        echo 'Created project #' . $project->getProjectId() . ' successfully!';
        
        // Prepare the path
        return rtrim('/admin/project/' . $project->getProjectId() . '/' . ($userModel->id != $project->getUserId() ? $project->getUserId() : ''));
    }
    
    /**
     * Get the path to a project AND mark it as our current project
     * 
     * @param int $projectId Project ID
     * @param int $userId    User ID
     * @param int $mark      (optional) Mark the project as current; default <b>0</b>
     * @return string Project path
     * @allowed admin,manager
     * @throws Exception
     */
    public function getPath($projectId = null, $userId = null, $mark = 0) {
        /*@var $userModel Model_User*/
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
        /*@var $project Model_Project*/ // Check the project exists and mark it
        $project = $this->_getProject($projectId, $userId, false, 1 === intval($mark));

        // Update the marker age
        if (0 === intval($mark)) {
            $project->getMarker()->touch();
        }
        
        // Prepare the path
        return rtrim('/admin/project/' . $project->getProjectId() . '/' . ($userModel->id != $project->getUserId() ? $project->getUserId() : ''));
    }
    
    /**
     * Delete a project
     * 
     * @return boolean
     * @allowed admin,manager
     * @throws Exception
     */
    public function delete($projectId = null, $userId = null) {
        /*@var $userModel Model_User*/
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
        // Get the projects instance
        $projects = Model_Projects::getInstance($userModel->id);
        
        // Delete the project
        return $projects->delete($projectId, $userId);
    }
    
    /**
     * Delete a snapshot's preview image
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotImageRegenerate($projectId = null, $userId = null, $snapshotId = null) {
        // Snapshot ID is mandatory
        if (null === $snapshotId || !is_numeric($snapshotId)) {
            throw new Exception('Snapshot ID is mandatory');
        }
            
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Get the snapshots manager
        $snapshots = WordPress_Snapshots::getInstance(
            $project->getProjectId(), 
            $project->getUserId()
        );
        
        // Re-generate the preview image
        $snapshots->getById($snapshotId)->generatePeview();
        
        // Inform the user
        echo 'Successfully regenerated snapshot #' . $snapshotId . '\'s preview image';
    }
    
    /**
     * Upload a snapshot's preview image
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotImageUpload($projectId = null, $userId = null, $snapshotId = null) {
        // Snapshot ID is mandatory
        if (null === $snapshotId || !is_numeric($snapshotId)) {
            throw new Exception('Snapshot ID is mandatory');
        }
            
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);

        // No files uploaded
        if (!isset($_FILES) || !isset($_FILES['file']) || !isset($_FILES['file']['tmp_name'])) {
            throw new Exception('No image file was uploaded');
        }
        
        // Get the snapshots manager
        $snapshots = WordPress_Snapshots::getInstance(
            $project->getProjectId(), 
            $project->getUserId()
        );
        
        // Attempt to set the preview image
        $snapshots->getById($snapshotId)->setPreview($_FILES['file']['tmp_name']);
        
        // Inform the user
        echo 'Successfully uploaded snapshot #' . $snapshotId . '\'s preview image';
    }
    
    /**
     * View a snapshot's preview or pattern image
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotImageView($projectId = null, $userId = null, $snapshotId = null, $type = 'preview') {
        try {
            // Snapshot ID is mandatory
            if (null === $snapshotId || !is_numeric($snapshotId)) {
                throw new Exception('Snapshot ID is mandatory');
            }
            
            /*@var $project Model_Project*/
            $project = $this->_getProject($projectId, $userId);

            // Get the snapshots manager
            $snapshots = WordPress_Snapshots::getInstance(
                $project->getProjectId(), 
                $project->getUserId()
            );
            
            // Prepare the image path
            $imagePath = null;
            switch ($type) {
                case 'preview':
                    $imagePath = $snapshots->getById($snapshotId)->getPreviewPath();
                    break;
                
                case 'pattern';
                    $imagePath = $snapshots->getById($snapshotId)->getPatternObject()->getPatternPath();
                    break;
            }
            
            // Invalid image type
            if (null === $imagePath) {
                throw new Exception('Invalid resource type');
            }
            
            // Invalid file
            if (!is_file($imagePath)) {
                throw new Exception(ucfirst($type) . ' file not defiend');
            }
        } catch (Exception $exc) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('[' . __CLASS__ . '::' . __METHOD__ . ' Caught Exception] ' . $exc->getMessage(), $exc->getFile(), $exc->getLine());
            $imagePath = ROOT . '/img/icons/upload-image.png';
        }
        
        // Clear the buffers
        while(@ob_end_clean());

        // Get the image information
        $imageInfo = @getimagesize($imagePath);
        
        // Output the file
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $imageInfo['mime']);
        header('Content-Disposition: filename="' . basename($imagePath) . '"'); 
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        readfile($imagePath);

        // Stop here
        exit();
    }
    
    /**
     * Get the available snapshots
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotsGetAll($projectId = null, $userId = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Get the snapshots manager
        $snapshots = WordPress_Snapshots::getInstance(
            $project->getProjectId(), 
            $project->getUserId()
        );

        // Prepare the data
        return array(
            'snapshots' => array_map(
                function(/*@var $item WordPress_Snapshots_Snapshot*/ $snapshot) {
                    return $snapshot->toArray();
                }, 
                $snapshots->getAll()
            ),
            'patterns' => (new Pattern())->getAll(),
        );
    }
    
    /**
     * Get the available snapshots
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotCapture($projectId = null, $userId = null) {
        // Get the ID
        $snapshotId = Input::getInstance()->postRequest('snapshotId');
        if (!is_numeric($snapshotId)) {
            throw new Exception('Invalid shapshot ID');
        }
        $snapshotId = intval($snapshotId);
        
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Get the snapshots manager
        $snapshots = WordPress_Snapshots::getInstance(
            $project->getProjectId(), 
            $project->getUserId()
        );
        
        // Get the snapshot
        /* @var $snapshot WordPress_Snapshots_Snapshot */
        $snapshot = $snapshots->getById($snapshotId);
        
        // Update the snapshot
        $snapshot->capture();
    }
    
    /**
     * Add a new snapshot
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotsCreate($projectId = null, $userId = null) {
        // Get the ID
        $snapshotIdFrom = intval(Input::getInstance()->postRequest('snapshotIdFrom'));
        if ($snapshotIdFrom <= 0) {
            $snapshotIdFrom = null;
        }
        
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Get the snapshots manager
        $snapshots = WordPress_Snapshots::getInstance(
            $project->getProjectId(), 
            $project->getUserId()
        );
        
        // Add a blank snapshot
        $snapshot = $snapshots->add('', '', $snapshotIdFrom);
        
        // Update the snapshot
        return $snapshot->getId();
    }
    
    /**
     * Update a snapshot's title and description
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotUpdate($projectId = null, $userId = null) {
        // Get the ID
        $snapshotId = Input::getInstance()->postRequest('snapshotId');
        if (!is_numeric($snapshotId)) {
            throw new Exception('Invalid shapshot ID');
        }
        $snapshotId = intval($snapshotId);
        
        // Get the title
        $title = html_entity_decode(trim(Input::getInstance()->postRequest('snapshotTitle')), ENT_QUOTES, "UTF-8");
        
        // Get the description
        $description = html_entity_decode(trim(Input::getInstance()->postRequest('snapshotDescription')), ENT_QUOTES, "UTF-8");
        
        // Get the pattern
        $pattern = html_entity_decode(trim(Input::getInstance()->postRequest('snapshotPattern')), ENT_QUOTES, "UTF-8");
        
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Get the snapshots manager
        $snapshots = WordPress_Snapshots::getInstance(
            $project->getProjectId(), 
            $project->getUserId()
        );
        
        /*@var $snapshot WordPress_Snapshots_Snapshot*/
        $snapshot = $snapshots->getById($snapshotId);
        
        // Update the snapshot title and description
        $snapshot->setTitle($title)->setDescription($description)->setPattern($pattern, true);
    }
    
    /**
     * Delete a snapshot
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function snapshotDelete($projectId = null, $userId = null) {
        // Get the ID
        $snapshotId = Input::getInstance()->postRequest('snapshotId');
        if (!is_numeric($snapshotId)) {
            throw new Exception('Invalid shapshot ID');
        }
        $snapshotId = intval($snapshotId);
        
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Get the snapshots manager
        $snapshots = WordPress_Snapshots::getInstance(
            $project->getProjectId(), 
            $project->getUserId()
        );
        
        // Get the snapshot
        $snapshot = $snapshots->getById($snapshotId);
        
        // Update the snapshot
        $snapshot->delete();
    }
    
    /**
     * Get the project data
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function getData($projectId = null, $userId = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);

        // Get the project configuration as an array
        return $project->getConfig()->toArray();
    }
    
    /**
     * Update a project's category: <ul>
     * <li>Model_Project_Config::CATEGORY_CORE</li>
     * <li>Model_Project_Config::CATEGORY_ADDON . '_addonName'</li>
     * <li>Model_Project_Config::CATEGORY_PLUS</li>
     * </ul>
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function categoryUpdate($projectId = null, $userId = null, $category = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Prepare the data
        $data = isset($_POST) ? $_POST : null;
        if (!is_array($data)) {
            throw new Exception('Invalid data');
        }
        
        // Set the data
        $project->getConfig()->setProjectAssoc($category, $data, true)->save();
    }
    
    /**
     * Delete a project's add-on
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function categoryDelete($projectId = null, $userId = null, $category = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);
        
        // Set the data
        $project->getConfig()->setProjectAssoc($category, array(), false)->save();
    }
    
    /**
     * Get the status for a staging
     * 
     * @return string Project path
     * @allowed admin,manager
     * @throws Exception
     */
    public function stageStatus($projectId = null, $userId = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId, true);

        // Get the current project's status from the logs
        return $project->getStatus();
    }
    
    /**
     * Get the HTML preview for a markdown text
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function getMarkdown() {
        // Get the text
        $text = html_entity_decode(trim(Input::getInstance()->postRequest('text')), ENT_QUOTES, "UTF-8");
        
        // Parse the text
        return Parsedown::instance()->text($text);
    }
    
    /**
     * Stage a project
     * 
     * @return string Project path
     * @allowed admin,manager
     * @throws Exception
     */
    public function stage($projectId = null, $userId = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);

        // Get the ID
        $snapshotId = Input::getInstance()->postRequest('snapshotId');
        if (!is_numeric($snapshotId)) {
            $snapshotId = null;
        } else {
            $snapshotId = intval($snapshotId);
        }
        
        // Stage the current project
        $project->stage($snapshotId);
        
        // Inform the user
        echo 'Staged snapshot #' . $snapshotId . ' of project #' . $projectId . ' successfully';
        
        // Get the project's name
        return $project->getConfig()->getDestDir();
    }
    
    /**
     * Upload an image
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function imageUpload($projectId = null, $userId = null, $category = null, $imageName = null, $imageIndex = 0) {
        // No image name specified
        if (null === $imageName) {
            throw new Exception('Image name must be specified');
        }
        
        // Sanitize the image index
        $imageIndex = intval($imageIndex);
        
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);

        // No files uploaded
        if (!isset($_FILES) || !isset($_FILES['file']) || !isset($_FILES['file']['tmp_name'])) {
            throw new Exception('No image file was uploaded');
        }
        
        // Set the data, throwing exceptions when needed
        $projectData = $project->getConfig()->getProjectData();

        // Category not found
        if (!isset($projectData[$category])) {
            throw new Exception('Category not found');
        }

        // Image not found
        if (!isset($projectData[$category][$imageName])) {
            throw new Exception('Element not found');
        }

        /* @var $imageConfig Model_Project_Config_Item_Image */
        $imageConfig = $projectData[$category][$imageName];

        // Validate the type
        if (!$imageConfig instanceof Model_Project_Config_Item_Image) {
            throw new Exception('Element is not an image');
        }

        // Images list
        if ($imageConfig->isList()) {
            // Get the image values
            $imageValues = $imageConfig->getValue();

            do {
                // Go through the images
                foreach($imageValues as $imageKey => $imageFileName) {
                    // Found our entry
                    if ($imageIndex == intval(preg_replace('%^.*?\-(\d+)\.\w+$%', '${1}', basename($imageFileName)))) {
                        // UPDATE the entry
                        $imageValues[$imageKey] = $_FILES['file']['tmp_name'];
                        break 2;
                    }
                }
                
                // CREATE the entry
                $imageValues[] = $_FILES['file']['tmp_name'];
            } while (false);
            
            // Update the listing
            $imageConfig->setValue($imageValues, true);
        } else {
            // Set the data, throwing exceptions when needed
            $project->getConfig()->setProjectAssoc(
                $category, 
                array(
                    $imageName => $_FILES['file']['tmp_name'],
                ), 
                true
            );
        }
        
        // Inform the user
        echo 'Successfully uploaded image "' . $imageName . '" to "' . $category . '"';
    }
    
    /**
     * Get the file path for the specified project and download tag
     * 
     * @param Model_Project $project     Project object
     * @param string        $downloadTag Download tag
     * @return string|null
     * @throws Exception
     */
    protected function _getProjectDistPath($project, $downloadTag) {
        // Get the file path
        $filePath = null;
        switch ($downloadTag) {
            case Dist::DOWNLOAD_KEY_MARKETPLACE:
                $filePath = $project->getConfig()->getProjectPath() . '/' . IO::FOLDER_NAME . '/' . Dist::FOLDER_MARKETPLACE . '.zip';
                break;
            
            case Dist::DOWNLOAD_KEY_EXPORT:
                $filePath = $project->getConfig()->getProjectPath() . '/' . IO::FOLDER_NAME . '/' . Dist::FOLDER_EXPORT . '.zip';
                break;
            
            case Dist::DOWNLOAD_KEY_DOCS:
                $filePath = $project->getConfig()->getProjectPath() . '/' . IO::FOLDER_NAME . '/' . Dist::FOLDER_DOCS . '/' . Dist::FILE_DOCS_SCREENSHOT;
                break;
            
            default:
                if (preg_match('%^snapshot\-(\d+)(?:-(preview\-[\w\-]+))?$%', $downloadTag, $snapshotMatches)) {
                    // Prepare the archive file name
                    $fileName = $snapshotMatches[1] . '.zip';
                    
                    // Alternative preview images
                    if (isset($snapshotMatches[2])) {
                        switch ($snapshotMatches[2]) {
                            // Live or Presentation preview
                            case 'preview-live':
                                $fileName = $snapshotMatches[1] . '.png';
                                break;
                            
                            // The way the theme will look to the client after installing a (pruned) snapshot
                            case 'preview-demo':
                                $fileName = $snapshotMatches[1] . '-' . WordPress_Snapshots_ScreenGrabber::SUFFIX_CUSTOMER_SNAPSHOT . '.png';
                                break;
                            
                            // Invalid request - don't snoop around!
                            default:
                                $fileName = null;
                                break;
                        }
                    }
                    
                    // Valid file name
                    if (null !== $fileName) {
                        $filePath = $project->getConfig()->getProjectPath() . '/' . IO::FOLDER_NAME . '/' . Dist::FOLDER_SNAPSHOTS . '/' . $fileName;
                    }
                }
                break;
        }
        
        // Invalid request
        if (null === $filePath) {
            throw new Exception('Invalid download tag');
        }
        
        // File not found
        if (!file_exists($filePath)) {
            throw new Exception('File "' . basename($filePath) . '" not found. Try regenerating the project.');
        }
        
        return $filePath;
    }
    
    /**
     * Download or check a downloadable associated with a project
     * 
     * @allowed admin,manager
     * @param int     $projectId   Project ID
     * @param int     $userId      User ID
     * @param string  $downloadTag Download tag
     * @param boolean $checkMode   (optional) Chec mode, one of <ul>
     * <li><b>'check'</b> - Verifies that the downloadable exists</li>
     * <li><b>'size'</b> - Get the downloadable file size in "K/M/G" format</li>
     * </ul> default <b>null</b>
     * @return boolean
     * @throws Exception
     */
    public function download($projectId = null, $userId = null, $downloadTag = null, $checkMode = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId, true);
        
        // Get the file path
        $filePath = $this->_getProjectDistPath($project, $downloadTag);
        
        // Get the project data
        $projectData = $project->getConfig()->getProjectData();
        
        // Check mode
        switch ($checkMode) {
            case 'check':
                // The file exists
                return true;
                break;
            
            case 'size':
                // Get the file size using system tools
                return trim(shell_exec('ls -shL ' . escapeshellarg($filePath) . ' | awk \'NR=1{print $1}\''));
                break;
        }
        
        // Figure out the content type
        $contentType = 'application/octet-stream';
        
        // Preview images in browser - except for the thubnail, which should be forcibly downloaded
        if (preg_match('%\.(png|gif|jpe?g)$%i', basename($filePath))) {
            // Get the image information
            $imageInfo = @getimagesize($filePath);

            // Output the file
            if (is_array($imageInfo) && isset($imageInfo['mime'])) {
                $contentType = $imageInfo['mime'];
            }
        }
        
        // Set the content length
        $contentLength = trim(shell_exec('wc -c < ' . escapeshellarg($filePath)));
        
        // Prepare the filename
        $fileName = basename($filePath);
        
        // Custom file name
        switch ($downloadTag) {
            case Dist::DOWNLOAD_KEY_DOCS:
                $fileName = $downloadTag . '-' . $fileName;
                break;
            
            case Dist::DOWNLOAD_KEY_MARKETPLACE:
                /* @var $marketPlace Model_Project_Config_Item_String */
                $marketPlace = $projectData[Model_Project_Config::CATEGORY_CORE][Cli_Run_Integration::OPT_PROJECT_MARKETPLACE];
                
                // Compute the new file name
                $fileName = $marketPlace->getValue() . '.zip';
                break;
        }
        
        // Prefix the user ID and project ID
        $fileName = 'u' . $userId . '-p'. $projectId . '-' . $fileName;
        
        // Clear the buffers
        while(@ob_end_clean());

        // Prepare the headers
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $contentType);
        if (strlen($contentLength)) {
            header('Content-Length: ' . $contentLength);
        }
        header('Content-Disposition: filename="' . $fileName . '"'); 
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        // Output the file
        readfile($filePath);

        // Stop here
        exit();
    }
    
    /**
     * Delete an image
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function imageDelete($projectId = null, $userId = null, $category = null, $imageName = null, $imageIndex = 0) {
        // No image name specified
        if (null === $imageName) {
            throw new Exception('Image name must be specified');
        }
        
        // Sanitize the image index
        $imageIndex = intval($imageIndex);
        
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId);

        // Set the data, throwing exceptions when needed
        $projectData = $project->getConfig()->getProjectData();

        // Category not found
        if (!isset($projectData[$category])) {
            throw new Exception('Category not found');
        }

        // Image not found
        if (!isset($projectData[$category][$imageName])) {
            throw new Exception('Element not found');
        }

        /* @var $imageConfig Model_Project_Config_Item_Image */
        $imageConfig = $projectData[$category][$imageName];

        // Validate the type
        if (!$imageConfig instanceof Model_Project_Config_Item_Image) {
            throw new Exception('Element is not an image');
        }

        // Images list
        if ($imageConfig->isList()) {
            // Get the image values
            $imageValues = $imageConfig->getValue();

            do {
                // Go through the images
                foreach($imageValues as $imageKey => $imageFileName) {
                    // Found our entry
                    if ($imageIndex == intval(preg_replace('%^.*?\-(\d+)\.\w+$%', '${1}', basename($imageFileName)))) {
                        // Remove the entry
                        unset($imageValues[$imageKey]);
                        
                        // Update the listing
                        $imageConfig->setValue($imageValues, true);
                        break 2;
                    }
                }
                
                // Index not defined
                throw new Exception('No image found at index ' . $imageIndex);
            } while (false);
            
        } else {
            // Set the data, throwing exceptions when needed
            $project->getConfig()->setProjectAssoc(
                $category, 
                array(
                    $imageName => Model_Project_Config_Item::ITEM_DEFAULT,
                ), 
                true
            );
        }
        
        echo 'Successfully deleted image "' . $imageName . '" from "' . $category . '"';
    }
    
    /**
     * Preview of a UI set
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function uiSetPreview($uiSetName = null) {
        try {
            // No image name specified
            if (null === $uiSetName) {
                throw new Exception('UI Set must be specified');
            }
            
            // Invalid image name
            if (!is_file($imagePath = UiSets::getInstance()->get($uiSetName)->getPreviewPath())) {
                UiSets::getInstance()->get($uiSetName)->generatePreview();
            }
        } catch (Exception $exc) {
            Log::check(Log::LEVEL_WARNING) && Log::warning('[' . __CLASS__ . '::' . __METHOD__ . ' Caught Exception] ' . $exc->getMessage(), $exc->getFile(), $exc->getLine());
            $imagePath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_UI_SETS . '/default.png';
        }
        
        // Clear the buffers
        while(@ob_end_clean());

        // Get the image information
        $imageInfo = @getimagesize($imagePath);
        
        // Output the file
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $imageInfo['mime']);
        header('Content-Disposition: filename="' . basename($imagePath) . '"'); 
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        readfile($imagePath);

        // Stop here
        exit();
    }
    
    /**
     * View an image
     * 
     * @allowed admin,manager
     * @throws Exception
     */
    public function imageView($projectId = null, $userId = null, $category = null, $imageName = null, $imageIndex = 0) {
        try {
            // No image name specified
            if (null === $imageName) {
                throw new Exception('Image name must be specified');
            }
            
            // Sanitize the image index
            $imageIndex = intval($imageIndex);

            /*@var $project Model_Project*/
            $project = $this->_getProject($projectId, $userId);

            // Set the data, throwing exceptions when needed
            $projectData = $project->getConfig()->getProjectData();

            // Category not found
            if (!isset($projectData[$category])) {
                throw new Exception('Category not found');
            }

            // Image not found
            if (!isset($projectData[$category][$imageName])) {
                throw new Exception('Element not found');
            }

            /* @var $imageConfig Model_Project_Config_Item_Image */
            $imageConfig = $projectData[$category][$imageName];

            // Validate the type
            if (!$imageConfig instanceof Model_Project_Config_Item_Image) {
                throw new Exception('Element is not an image');
            }

            // Images list
            if ($imageConfig->isList()) {
                // Get the image values
                $imagePaths = $imageConfig->getPath();

                do {
                    // Go through the images
                    foreach($imagePaths as $imageKey => $imagePath) {
                        // Found our entry
                        if ($imageIndex == intval(preg_replace('%^.*?\-(\d+)\.\w+$%', '${1}', basename($imagePath)))) {
                            $imagePath = $imagePaths[$imageKey];
                            break 2;
                        }
                    }

                    // Index not defined
                    throw new Exception('No image found at index ' . $imageIndex);
                } while (false);
            } else {
                // Get the image path on disk
                $imagePath = $imageConfig->getPath();
            }

            // Nothing found
            if (null === $imagePath) {
                throw new Exception('No image found for ' . json_encode(func_get_args()));
            }
        } catch (Exception $exc) {
            Log::check(Log::LEVEL_DEBUG) && Log::debug('[' . __CLASS__ . '::' . __METHOD__ . ' Caught Exception] ' . $exc->getMessage(), $exc->getFile(), $exc->getLine());
            $imagePath = ROOT . '/img/icons/upload-image.png';
        }
        
        // Clear the buffers
        while(@ob_end_clean());

        // Get the image information
        $imageInfo = @getimagesize($imagePath);
        
        // Output the file
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $imageInfo['mime']);
        header('Content-Disposition: filename="' . basename($imagePath) . '"'); 
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        readfile($imagePath);

        // Stop here
        exit();
    }
    
    /**
     * Publish a project
     * 
     * @return string Project path
     * @allowed admin,manager
     * @throws Exception
     */
    public function publish($projectId = null, $userId = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId, true);
        
        // Prepare the publisher
        $publisher = new WordPress_Publisher($project->getUserId(), $project->getProjectId());
        
        // Publish
        $publisher->publish();
        
        // All went well
        return true;
    }
    
    /**
     * Enqueue/dequeue a project
     * 
     * @return string Project path
     * @allowed admin,manager
     * @throws Exception
     */
    public function taskToggle($projectId = null, $userId = null) {
        /*@var $project Model_Project*/
        $project = $this->_getProject($projectId, $userId, true);
        
        // Get the task
        $task = TaskManager::getInstance()->get($userId, $projectId);
        
        // Enqueue the task
        try {
            if (TaskManager_Task::STATUS_PENDING === $task->getStatus()) {
                $task->dequeue();
            } else {
                $task->enqueue();
            }
        } catch (Exception $exc) {
            $task->stop();
        }
        
        // All went well
        return $task->toArray();
    }
    
    /**
     * Get the status of the defined projects
     * 
     * @return string Project path
     * @allowed admin,manager
     * @throws Exception
     */
    public function taskCheck() {
        // Get the data
        $data = Input::getInstance()->postRequest('data');

        // Not valid
        if (!is_array($data) || !count($data)) {
            throw new Exception('Invalid projects to check');
        }
        
        // Prepare the result
        $result = array();
        
        // Prepare the list of tasks
        $tasks = TaskManager::getInstance()->getAll();
        
        // Go through the list
        foreach ($data as $dataPair) {
            // Invalid pair
            if (!preg_match('%^\d+\-\d+$%', $dataPair)) {
                continue;
            }
            
            // Get the pair
            list($projectId, $userId) = explode('-', $dataPair);

            // Prepare the result for this pair
            $dataResult = null;

            // Check all the tasks
            foreach ($tasks as $task) {
                if ($projectId == $task->getProjectId() && $userId == $task->getUserId()) {
                    $dataResult = $task->toArray();
                    break;
                }
            }

            // Store the result
            $result[$projectId . '-' . $userId] = $dataResult;
        }
        
        // All done
        return $result;
    }
    
    /**
     * Get the project for the current user
     * 
     * @param int     $projectId     Project ID
     * @param int     $userId        User ID
     * @param boolean $ignoreMarkers (optional) Prevent 
     * <b>Controller_Ajax_Project::ERROR_PROJECT_LOCKED_FOR_EXPORT</b> or 
     * <b>Controller_Ajax_Project::ERROR_EDIT_TOKEN_EXPIRED</b> Exceptions (@see Controller_Ajax_Project::taskToggle);
     * default <b>false</b>
     * @param boolean $mark          (optional) Mark this as the user's current project; default <b>false</b>
     * @return Model_Project
     */
    protected function _getProject($projectId = null, $userId = null, $ignoreMarkers = false, $mark = false) {
        /*@var $userModel Model_User*/
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);
        
        // Get the projects instance
        $projects = Model_Projects::getInstance($userModel->id);
        
        // Store the session
        WordPress_Session::getInstance()->setUserId($userId);
        WordPress_Session::getInstance()->setProjectId($projectId);
        
        /*@var $project Model_Project*/
        $project = $projects->get(
            WordPress_Session::getInstance()->getProjectId(), 
            WordPress_Session::getInstance()->getUserId()
        );
        
        // Session expired
        if (!$ignoreMarkers) {
            // A task is pending or has started for this project, abort
            if (TaskManager::getInstance()->isLockedForExport($userId)) {
                // Log the event
                TaskbarNotifier::sendMessage(
                    'Exception', 
                    $userModel->name . ' got "Project locked for export" ' . ($userId != $userModel->id ? 'as user #' . $userId : '') . ' on project #' . $projectId,
                    TaskbarNotifier::TYPE_WARNING
                );

                // Stop any AJAX actions
                throw new Exception(Controller_Ajax_Project::ERROR_PROJECT_LOCKED_FOR_EXPORT);
            }
        
            if (!$mark && !$project->getMarker()->isMarked()) {
                // Log the event
                TaskbarNotifier::sendMessage(
                    'Exception', 
                    $userModel->name . ' got "Edit token expired" ' . ($userId != $userModel->id ? 'as user #' . $userId : '') . ' on project #' . $projectId,
                    TaskbarNotifier::TYPE_WARNING
                );

                // Stop any AJAX actions
                throw new Exception(Controller_Ajax_Project::ERROR_EDIT_TOKEN_EXPIRED);
            } 

            // Mark for the first time or re-mark
            $mark && $project->getMarker()->mark();
        }
        
        // All done
        return $project;
    }
    
}

/* EOF */