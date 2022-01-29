<?php
/**
 * {project.destProjectName} Core Utilities
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Exception handling
 */
class St_Exception extends Exception {
    /**
     * Error code
     * 
     * @var string
     */
    protected $_errorCode = '';
    
    /**
     * Identifier
     * 
     * @var mixed
     */
    protected $_identifier = null;
    
    /**
     * Exception Handling
     * 
     * @param string $errorMessage Error message
     * @param string $errorCode    Internal error code
     */
    public function __construct($errorMessage, $errorCode, $identifier = null) {
        parent::__construct($errorMessage, 0, null);
        
        // Store the error code string
        $this->_errorCode = trim($errorCode);
        
        // Store the identifier, if provided
        $this->_identifier = $identifier;
    }
    
    /**
     * Get the error code
     * 
     * @return string Error Code
     */
    public function getErrorCode() {
        return $this->_errorCode;
    }
    
    /**
     * Get the exception identifier
     * 
     * @return mixed|null
     */
    public function getIdentifier() {
        return $this->_identifier;
    }
}

/**
 * AJAX calls handling
 */
class St_Ajax {
    
    // AJAX Elements
    const AJAX_SUCCESS         = 'success';
    const AJAX_DATA            = 'data';
    const AJAX_DATA_CODE       = 'code';
    const AJAX_DATA_MESSAGE    = 'message';
    const AJAX_ERROR_CODE      = 'errorCode';
    const AJAX_ERROR_MESSAGE   = 'errorMessage';
    const AJAX_ERRORS_CAUGHT   = 'errorsCaught';
    
    // AJAX Keys
    const AJAX_KEY_METHOD      = 'st_ajax_method';
    const AJAX_KEY_ARGS        = 'st_ajax_args';
    const AJAX_KEY_NONCE       = 'st_ajax_nonce';
    const AJAX_KEY_VALIDATE_FS = 'st_ajax_validate_fs';
    
    /**
     * Store of caught exceptions
     * 
     * @var Exceptions[]
     */
    protected static $_caughtExceptions = array();
    
    /**
     * Store caught exceptions during AJAX request
     */
    public static function storeCaughtException($exc) {
        if ($exc instanceof Exception) {
            self::$_caughtExceptions[] = $exc;
        }
    }
    
    /**
     * Run AJAX methods from the provided <b>$object</b>
     * 
     * @param object          $object             Class instance that handles AJAX calls
     * @param string|string[] $capabilities       (optional) Required user capabilities, a string or an array of strings; default <b>'manage_options'</b>
     */
    public static function run($object = null, $capabilities = 'manage_options') {
        // Get the WP_FileSystem_Base
        global $wp_filesystem;
        
        // JSON result
        header("Content-Type: application/json");
        
        // Prepare the result
        $result = array(
            self::AJAX_SUCCESS => true,
            self::AJAX_DATA    => null,
        );
        
        // Validate the filesystem
        $validateFileSystem = isset($_POST[self::AJAX_KEY_VALIDATE_FS]) && in_array(strtolower($_POST[self::AJAX_KEY_VALIDATE_FS]), array('true', '1', 'yes', 'on'));
        
        try {
            // Nonce failed
            if (!check_ajax_referer('{project.prefix}', self::AJAX_KEY_NONCE, false)) {
                throw new St_Exception(
                    __('Invalid Nonce.', '{project.destDir}'), 
                    'invalid_nonce'
                );
            }
            
            // Validate the object
            if (!is_object($object)) {
                throw new St_Exception(
                    __('Invalid AJAX Handler.', '{project.destDir}'), 
                    'invalid_handler'
                );
            }
            
            // Go through the capabilities list
            $capabilities = is_array($capabilities) ? $capabilities : array($capabilities);
            foreach ($capabilities as $capability) {
                if (!current_user_can($capability)) {
                    throw new St_Exception(
                        sprintf(__('Insufficient privileges, %s not allowed.', '{project.destDir}'), $capability), 
                        'insufficient_privileges'
                    );
                }
            }
            
            // Get the method name
            if (!isset($_POST[self::AJAX_KEY_METHOD]) || !strlen($_POST[self::AJAX_KEY_METHOD])) {
                throw new St_Exception(
                    __('Method not defined', '{project.destDir}'), 
                    'method_not_defined'
                );
            }
            
            // Prepare the internal method name
            $methodName = 'ajax' . 
                ucfirst(
                    trim(
                        preg_replace(
                            '%\W+%', 
                            '', 
                            $_POST[self::AJAX_KEY_METHOD]
                        )
                    )
                );
            
            // Method not found
            if (!method_exists($object, $methodName)) {
                throw new St_Exception(
                    __('Invalid method specified', '{project.destDir}'), 
                    'invalid_method'
                );
            }
            
            // Prepare the method arguments
            $methodArguments = isset($_POST[self::AJAX_KEY_ARGS]) ? $_POST[self::AJAX_KEY_ARGS] : array();
            if (!is_array($methodArguments)) {
                throw new St_Exception(
                    __('Invalid method arguments', '{project.destDir}'), 
                    'invalid_arguments'
                );
            }
            
            // Validate the file system connection
            if ($validateFileSystem) {
                ob_start();
                $fileSystemCredentials = request_filesystem_credentials(wp_nonce_url('themes.php?page={project.prefix}_theme_manager', '{project.prefix}'));
                ob_end_clean();
                
                // Invalid result
                if (!$fileSystemCredentials) {
                    throw new St_Exception(
                        __('Unable to connect to the filesystem. Please confirm your credentials.', '{project.destDir}'), 
                        'unable_to_connect_to_filesystem'
                    );
                }
                
                // Initialize
                WP_Filesystem($fileSystemCredentials);

                // No access
                if (!$wp_filesystem instanceof WP_Filesystem_Base) {
                    throw new St_Exception(
                        __('You do not have access to the filesystem.', '{project.destDir}'), 
                        'unable_to_connect_to_filesystem'
                    );
                }
                
                // Pass through the error from WP_Filesystem if one was raised.
                if (is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
                    throw new St_Exception(
                        esc_html($wp_filesystem->errors->get_error_message()), 
                        'unable_to_connect_to_filesystem'
                    );
                }
            }
            
            // Execute the method
            $result[self::AJAX_DATA] = call_user_func_array(array($object, $methodName), $methodArguments);
            
            // Pass through the error from WP_Filesystem if one was raised.
            if ($validateFileSystem) {
                if ($wp_filesystem instanceof WP_Filesystem_Base && is_wp_error($wp_filesystem->errors) && $wp_filesystem->errors->get_error_code()) {
                    throw new St_Exception(
                        esc_html($wp_filesystem->errors->get_error_message()), 
                        'unable_to_connect_to_filesystem'
                    );
                }
            }
            
            // WordPress Error
            if ($result[self::AJAX_DATA] instanceof WP_Error) {
                // Prepare the result
                $result = array();
                
                // Go through the errors
                foreach ($result[self::AJAX_DATA]->errors as $code => $messages ) {
                    foreach ($messages as $message) {
                        $result[] = array(
                            self::AJAX_DATA_CODE    => $code, 
                            self::AJAX_DATA_MESSAGE => $message
                        );
                    }
                }
                
                // Mark the error
                $result[self::AJAX_SUCCESS] = false;
                $response[self::AJAX_DATA] = $result;
            }
        } catch (St_Exception $excExtended) {
            // Mark the St_Exception
            $result[self::AJAX_SUCCESS] = false;
            $result[self::AJAX_ERROR_CODE] = $excExtended->getErrorCode();
            $result[self::AJAX_ERROR_MESSAGE] = $excExtended->getMessage();
        } catch (Exception $exc) {
            // Mark the generic Exception
            $result[self::AJAX_SUCCESS] = false;
            $result[self::AJAX_ERROR_MESSAGE] = $exc->getMessage();
        }
        
        // Prepare the errors array
        $errors = array();
        
        // Go through the exception list
        foreach (self::$_caughtExceptions as /* @var $exc Exception */ $exc) {
            if ($exc instanceof St_Exception) {
                $errors[] = '#' . $exc->getIdentifier() . ': (<i>' . $exc->getErrorCode() . '</i>) ' . $exc->getMessage();
            } elseif ($exc instanceof Exception) {
                $errors[] = $exc->getMessage();
            }
        }
        
        // Store the errors caught along the way
        if (count($errors)) {
            $result[self::AJAX_ERRORS_CAUGHT] = $errors;
        }
        
        // Output the result
        echo json_encode($result);
        
        // Stop here
        exit();
    }
}

/*EOF*/