<?php
/**
 * Theme Warlock - Model_Project_Marker
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Model_Project_Marker {

    /**
     * Maximum session age in seconds
     */
    const MAX_SESSION_AGE = 180;
    
    /**
     * Editor ID
     * 
     * @var int
     */
    protected $_editorId;
    
    /**
     * User ID
     * 
     * @var int
     */
    protected $_userId;
    
    /**
     * Project ID
     * 
     * @var int
     */
    protected $_projectId;
    
    /**
     * Paths to the marker files
     * 
     * @var string[]
     */
    protected $_markerPaths;
    
    /**
     * Server session
     * 
     * @var Session
     */
    protected $_session;
    
    /**
     * The key for our token in the Session-stored array
     * 
     * @var string
     */
    protected $_sessionTokenKey;
    
    /**
     * Project marker; used to force users to work on one project at a time
     * 
     * @param int $userId    User/Creator ID
     * @param int $projectId Project ID
     */
    public function __construct($userId, $projectId) {
        // Store the editor
        $this->_editorId = $userId;
        
        // Store the project details
        $this->_userId = $userId;
        $this->_projectId = $projectId;
        
        // Prepare the session instance
        $this->_session = Session::getInstance();
        
        // Prepare the key for our token in the Session-stored array
        $this->_sessionTokenKey = 'u' . $this->_userId . '-p' . $this->_projectId;
        
        // Create the marker directory
        if (!is_dir($markerDir = $this->_getMarkerDir())) {
            Folder::create($markerDir, 0777, true);
        }
        
        // Editor ID provided
        if (null !== $userModel = $this->_session->get(Session::PARAM_WEB_USER_MODEL)) {
            if ($userModel instanceof Model_User) {
                $this->_editorId = $userModel->id;
            }
        }
    }
    
    /**
     * Get the markers temporary path
     * 
     * @return string
     */
    protected function _getMarkerDir() {
        return ROOT . '/web/temp/project-markers';
    }
    
    /**
     * Get the marker path
     * 
     * @return string
     */
    protected function _getMarkerPath() {
        // Cache check
        if (!isset($this->_markerPaths[$this->_editorId])) {
            // Store in cache
            $this->_markerPaths[$this->_editorId] = $this->_getMarkerDir() . '/u' . $this->_userId . '-p' . $this->_projectId . '.mark';
        }
        
        // All done
        return $this->_markerPaths[$this->_editorId];
    }

    /**
     * Store the current token to session
     */
    protected function _sessionTokenSet($token) {
        // Get the tokens from the session
        $tokens = $this->_session->get(Session::PARAM_WEB_PROJECT_EDIT_TOKEN);
        
        // Validate
        if (!is_array($tokens)) {
            $tokens = array();
        }
        
        // Store our token
        $tokens[$this->_sessionTokenKey] = $token;
        
        // Update the session
        $this->_session->set(Session::PARAM_WEB_PROJECT_EDIT_TOKEN, $tokens);
    }
    
    /**
     * Get the token stored in session for this user and project
     * 
     * @return string
     */
    protected function _sessionTokenGet() {
        // Get the tokens from the session
        $tokens = $this->_session->get(Session::PARAM_WEB_PROJECT_EDIT_TOKEN);
        
        // Token defined for this user and project
        if (is_array($tokens) && isset($tokens[$this->_sessionTokenKey])) {
            return $tokens[$this->_sessionTokenKey];
        }
        
        // Token not found
        return null;
    }
    
    /**
     * Remove the marker
     * 
     * @return Model_Project_Marker
     */
    public function unmark() {
        // Remove the marker
        if (is_file($this->_getMarkerPath())) {
            @unlink($this->_getMarkerPath());
            Log::check(Log::LEVEL_INFO) && Log::info('Unmarked project');
        }
        return $this;
    }
    
    /**
     * Mark this project
     * 
     * @return Model_Project_Marker
     */
    public function mark() {
        // Prepare the edit token
        $editToken = md5(uniqid('', true));
        
        // Set the edit token
        $this->_sessionTokenSet($editToken);

        // Store the marker
        file_put_contents($this->_getMarkerPath(), $this->_editorId . '-' . $editToken);
        
        // Log the event
        Log::check(Log::LEVEL_INFO) && Log::info('Marked project');
        return $this;
    }
    
    /**
     * Update the file age
     * 
     * @return Model_Project_Marker
     */
    public function touch() {
        if (is_file($this->_getMarkerPath())) {
            touch($this->_getMarkerPath());
            Log::check(Log::LEVEL_DEBUG) && Log::debug('Updated marker age');
        }
        return $this;
    }
    
    /**
     * Check whether this project was marked within the last MAX_SESSION_AGE seconds and return the editor's ID.
     * 
     * @param boolean $validateEditorAndSession (optional) Check the session-stored edit token; default <b>true</b>
     * @return boolean|int Editor ID on success, false on failure
     */
    public function isMarked($validateEditorAndSession = true) {
        // Marker found
        if (is_file($this->_getMarkerPath())) {
            // Could not read, race condition; assume everything is OK for now
            if (false === $fileContents = file_get_contents($this->_getMarkerPath())) {
                Log::check(Log::LEVEL_WARNING) && Log::warning('Model_Project_Marker::isMarked() race condition');
                return $this->_editorId;
            }
            
            // Get the stored project ID and edit token
            list($editorId, $editToken) = explode('-', $fileContents);
            
            // Cast to int
            $editorId = intval($editorId);
            
            // Do not check the session; useful when checking other users' projects
            if (!$validateEditorAndSession) {
                return $editorId;
            }

            // Check the editor ID, session token and session age
            if ($this->_editorId == $editorId && $this->_sessionTokenGet() === $editToken && time() - filemtime($this->_getMarkerPath()) <= self::MAX_SESSION_AGE) {
                return $editorId;
            }
        }
        
        // Marker not defined or could not validate session token
        return false;
    }
    
    /**
     * Get the flag age (UNIX timestamp)
     * 
     * @return int|null
     */
    public function getAge() {
        // Marker found
        if (is_file($this->_getMarkerPath())) {
            clearstatcache();
            
            // Could not read, race condition; assume everything is OK for now
            if (false === $fileMTime = filemtime($this->_getMarkerPath())) {
                Log::check(Log::LEVEL_WARNING) && Log::warning('Model_Project_Marker::getAge() race condition');
                return time();
            }
            
            // Calculate marker age
            return time() - $fileMTime;
        }
        
        // Marker not defined
        return null;
    }
    
    /**
     * Get the marker age in English
     * 
     * @return string|null
     */
    public function getAgeVerbose() {
        // Get the marker age in seconds
        if (null !== $ageInSeconds = $this->getAge()) {
            // Sanitize the result
            $ageInSeconds = ($ageInSeconds < 1)? 1 : $ageInSeconds;
            
            // Prepare the age tokens
            $tokens = array (
                31536000 => 'year',
                2592000 => 'month',
                604800 => 'week',
                86400 => 'day',
                3600 => 'hour',
                60 => 'minute',
                1 => 'second'
            );
            
            // Prepare the age parts
            $dateParts = array();
            
            // Go through the tokens
            foreach ($tokens as $unit => $text) {
                if ($ageInSeconds < $unit) { 
                    continue;
                }
                
                // Compute the number of units
                $numberOfUnits = floor($ageInSeconds / $unit);
                
                // Store the age part
                $dateParts[] = $numberOfUnits . ' ' . $text . (1 == $numberOfUnits ? '' : 's');
                
                // Trim the seconds
                $ageInSeconds -= $numberOfUnits * $unit;
            }
            
            // All done
            return implode(', ', $dateParts);
        }
        
        // Age could not be verified
        return null;
    }
    
}

/* EOF */