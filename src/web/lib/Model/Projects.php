<?php
/**
 * Theme Warlock - Projects
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Projects {

    /**
     * Default user ID
     */
    const USER_DEFAULT_ID = 0;

    /**
     * Projects instance
     * 
     * @var typModel_Projectse 
     */
    protected static $_instances = array();
    
    /**
     * User model
     * 
     * @var Model_User
     */
    protected $_userModel = null;
    
    /**
     * Projects
     * 
     * @return Model_Projects
     */
    public static function getInstance($userId) {
        if (!isset(self::$_instances[$userId])) {
            self::$_instances[$userId] = new self($userId);
        }
        
        // All done
        return self::$_instances[$userId];
    }
    
        
    /**
     * Path helper
     * 
     * @param int        $userId    User ID
     * @param int|string $projectId Project ID; can also use '*' for glob patterns
     * @return string
     */
    public static function getProjectPath($userId, $projectId) {
        // Get the project folder
        $projectFolder = WordPress_Session::USER_PREFIX . (null === $userId ? Model_Projects::USER_DEFAULT_ID : $userId);
        
        // All done
        return dirname(Config::getProjectsPath()) . '/' . $projectFolder . '/' . $projectId;
    }
    
    /**
     * Projects handler
     * 
     * @param int $userId Current user ID
     * @throws Exception
     */
    protected function __construct($userId) {
        if (php_sapi_name() != "cli") {
            // Get the user model
            $this->_userModel = new Model_User($userId);

            // User not found
            if (!$this->_userModel->exists()) {
                throw new Exception('User ' . $userId . ' does not exist');
            }
        }
        
        // Create the projects dir
        if (!is_dir(Config::getProjectsPath())) {
            Folder::create(Config::getProjectsPath(), 0777, true);
        }
    }
    
    /**
     * Get the available list of projects, including other user's projects for admins
     * 
     * @param string $customUserId (optional) Custom user id; Admins are allowed to fetch other users' projects
     * @return Model_Project[] List of projects
     */
    public function getAll($customUserId = null) {
        // Get the list
        $projectsList = glob(
            dirname(Config::getProjectsPath()) . '/' . (Session::ROLE_ADMIN == $this->_getSessionUserRole() ? (null === $customUserId ? '*' : WordPress_Session::USER_PREFIX . $customUserId) : (WordPress_Session::USER_PREFIX . $this->_getSessionUserId())) . '/*', 
            GLOB_ONLYDIR
        );
        
        // Sort it naturally
        rsort($projectsList);
        
        // Get the projects list
        return array_filter(array_map(
            function($item){
                // Prepare the project info
                $projectId = basename($item);
                $userId = preg_replace('%^' . preg_quote(WordPress_Session::USER_PREFIX) . '%', '', basename(dirname($item)));
                
                // Exclude user 0's projects - used for theme deployment only
                if (0 == $userId) {
                    return null;
                }
                
                // Return the object
                return new Model_Project($userId, $projectId);
            },
            $projectsList
        ));
    }
    
    /**
     * Get the information on a specific project
     * 
     * @param int $projectId Project ID
     * @param int $userId    (optional) User ID
     * @return Model_Project
     * @throws Exception
     */
    public function get($projectId, $userId = null) {
        // Default user
        $this->_defaultUser($userId);
        
        // Check project access
        $this->_checkAccess($userId);
        
        // Project not found
        if (!is_dir($this->_getPath($userId, $projectId))) {
            throw new Exception('Project ' . $userId . '/' . $projectId . ' not found');
        }
        
        // All done
        return new Model_Project($userId, $projectId);
    }
    
    /**
     * Create an empty project
     * 
     * @param int    $userId           (optional) User ID
     * @param string $projectName      (optional) Theme name
     * @param string $projectFramework (optional) Theme Framework ID
     * @return Model_Project
     * @throws Exception
     */
    public function create($userId = null, $projectName = null, $projectFramework = null) {
        // Default user
        $this->_defaultUser($userId);
        
        // Check project access
        $this->_checkAccess($userId);
        
        // Create the project folder
        $projects = array_map(function($item) {return basename($item);}, glob($this->_getPath($userId, '*'), GLOB_ONLYDIR));
        
        // Get the id
        $projectId = 1;
        if (count($projects)) {
            $projectId = max($projects) + 1;
        }
        
        // Create the new path
        if (!is_dir($newPath = $this->_getPath($userId, $projectId))) {
            Folder::create($newPath, 0777, true);
        }
        
        /*@var $modelProject Model_Project*/
        $modelProject = new Model_Project($userId, $projectId);
        
        // Mark this as our current project
        $modelProject->getMarker()->mark();
        
        // Prepare the extra data
        $extraData = array();
        
        // Project name provided
        if (null !== $projectName) {
            $extraData[Cli_Run_Integration::OPT_PROJECT_NAME] = $projectName;
        }
        
        // Framework ID provided
        if (null !== $projectFramework) {
            $extraData[Cli_Run_Integration::OPT_PROJECT_FRAMEWORK] = $projectFramework;
        }
        
        // Set the data; allow for the one-time modification of "disabled" config items
        if (count($extraData)) {
            $modelProject->getConfig()->setProjectAssoc(
                Model_Project_Config::CATEGORY_CORE,
                $extraData,
                false,
                false
            )->save();
        }
        
        // Stage the project
        $modelProject->stage();
        
        // All done
        return $modelProject;
    }
    
    /**
     * Get the default user in case none is provided
     * 
     * @param int|string $userId User ID
     */
    protected function _defaultUser(&$userId) {
        // User not provided
        if (null === $userId) {
            $userId = $this->_getSessionUserId();
        }
    }
    
    /**
     * Get the current session user ID; CLI defaults to WordPress_Session::DEFAULT_USER_ID
     * @return type
     */
    protected function _getSessionUserId() {
        return (null === $this->_userModel ? WordPress_Session::getInstance()->getUserId() : $this->_userModel->id);
    }
    
    /**
     * Get the current session user role; CLI defaults to Session::ROLE_ADMIN
     * 
     * @return string
     */
    protected function _getSessionUserRole() {
        return (null === $this->_userModel ? Session::ROLE_ADMIN : $this->_userModel->role);
    }
    
    /**
     * Validates the user access
     * 
     * @param int $userId User ID
     * @throws Exception
     */
    protected function _checkAccess($userId) {
        // Access denied
        if (Session::ROLE_ADMIN != $this->_getSessionUserRole() && $userId != $this->_getSessionUserId()) {
            throw new Exception('Access denied to user ' . $userId);
        }
    }
    
    /**
     * Get a project's path
     * 
     * @param int        $userId    User ID
     * @param int|string $projectId Project ID; can also use '*' for glob patterns
     * @return string Project path
     * @throws Exception
     */
    protected function _getPath($userId, $projectId) {
        // Not numeric
        if (!is_numeric($userId)) {
            throw new Exception('User ID must be a number');
        }

        // CLI has full rights
        if (php_sapi_name() != "cli") {
            // Get the user model
            $userModel = new Model_User($userId);
            if (!$userModel->exists()) {
                throw new Exception('Invalid user specified');
            }
        }
        
        // Get the path
        return self::getProjectPath($userId, $projectId);
    }
    
    /**
     * Delete a project
     * 
     * @param int $projectId Project ID
     * @param int $userId    User ID
     * @return boolean
     */
    public function delete($projectId, $userId) {
        // Default user
        $this->_defaultUser($userId);
        
        // Check project access
        $this->_checkAccess($userId);
        
        // Project not found
        if (!is_dir($projectPath = $this->_getPath($userId, $projectId))) {
            return false;
        }
        
        // Remove the project
        Folder::clean($projectPath, true);
        
        // All done
        return true;
    }
}

/* EOF */
