<?php
/**
 * Theme Warlock - Notifier
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Notifier {
    
    // Event types
    const EVENT_SUCCESS = 'success';
    const EVENT_FAILURE = 'failure';
    const EVENT_STATS   = 'stats';
    
    // Email parameters
    const EMAIL_SUBJECT     = 'subject';
    const EMAIL_BODY        = 'body';
    const EMAIL_TO          = 'to';
    const EMAIL_ATTACHMENTS = 'attachments';
    
    /**
     * Notifier
     * 
     * @var Notifier
     */
    protected static $_instance;
    
    /**
     * PHPMailer instance
     * 
     * @var PHPMailer
     */
    protected $_phpMailer;
    
    /**
     * Notifier
     */
    protected function __construct() {
        // Set the PHP Mailer
        $this->_phpMailer = new PHPMailer();
        
        // Configure the mailer
        $this->_phpMailer->isSMTP();
        $this->_phpMailer->SMTPAuth   = true;
        $this->_phpMailer->SMTPSecure = 'ssl';
        $this->_phpMailer->Port       = 465;
        $this->_phpMailer->Host       = Config::get()->notifierSmtpHost;
        $this->_phpMailer->Username   = Config::get()->notifierSmtpEmail;
        $this->_phpMailer->Password   = Config::get()->notifierSmtpPassword;
        
        // Configure the e-mail details
        $this->_phpMailer->From     = Config::get()->notifierSmtpEmail;
        $this->_phpMailer->FromName = 'Theme Warlock';
        $this->_phpMailer->addAddress(
            Config::get()->notifierAdminEmail, 
            Config::get()->authorName
        );
        
        // Set the format as HTML
        $this->_phpMailer->isHTML(true);
    }
    
    /**
     * Notifier instance
     * 
     * @return Notifier
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    
    /**
     * Get the PHP Mailer options
     * 
     * @return PHPMailer
     */
    public function phpMailer() {
        return $this->_phpMailer;
    }
    
    /**
     * Track a success
     * 
     * @param string $eventName    Event name
     * @param array  $eventDetails Event details
     * @return boolean True on success, false on failure
     */
    public function success($eventName, Array $eventDetails = array()) {
        return $this->_track($eventName, self::EVENT_SUCCESS, $eventDetails);
    }
    
    /**
     * Track a failure
     * 
     * @param string $eventName    Event name
     * @param array  $eventDetails Event details
     * @return boolean True on success, false on failure
     */
    public function failure($eventName, Array $eventDetails = array()) {
        return $this->_track($eventName, self::EVENT_FAILURE, $eventDetails);
    }
    
    /**
     * Track a statistic
     * 
     * @param string $eventName    Event name
     * @param array  $eventDetails Event details
     * @return boolean True on success, false on failure
     */
    public function stats($eventName, Array $eventDetails = array()) {
        return $this->_track($eventName, self::EVENT_STATS, $eventDetails);
    }
    
    /**
     * Send a detailed HTML notification
     * 
     * @param string $eventName    Event name
     * @param string $eventDetails Event details as HTML
     * @return string
     */
    public function html($eventName, $eventDetails) {
        return $this->_track($eventName, self::EVENT_STATS, $eventDetails);
    }
    
    /**
     * Track an event
     * 
     * @param string $eventName    Event name
     * @param string $eventType    Event type
     * @param array  $eventDetails Event details
     * @return boolean True on success, false on failure
     */
    protected function _track($eventName, $eventType, $eventDetails = array()) {
        // Prepare the title
        $title = ucfirst($eventType) . ': ' . $eventName;

        // Prepare a buffer
        ob_start();
        
        // Prepare the event color
        $eventColor = '#000000';
        
        switch ($eventType) {
            case self::EVENT_SUCCESS:
                $eventColor = '#007ee6';
                break;
            
            case self::EVENT_FAILURE:
                $eventColor = '#d74118';
                break;
            
            case self::EVENT_STATS:
                $eventColor = '#535353';
                break;
        }
        
        // Get the user model
        $userModel = Session::getInstance()->get(Session::PARAM_WEB_USER_MODEL);

        // Get the user role
        $userName = (null != $userModel && property_exists($userModel, 'name')) ? $userModel->name : '<i>System</i>';
        
        // Filter-out notifications for specific users
        if (preg_match('%' . Config::get()->notifierUserFilter . '%i', $userName)) {
            // Nothing to do here
            Log::check(Log::LEVEL_DEBUG) && Log::debug('Skipped e-mail...');
            return;
        }
        
        // Get the template
        require ROOT . '/web/tpl/notifier.phtml';

        // Get the HTML
        $html = ob_get_clean();
        
        // Prepare the payload
        $payload = array(
            self::EMAIL_SUBJECT     => $eventName,
            self::EMAIL_BODY        => $html,
            self::EMAIL_TO          => $this->_phpMailer->getToAddresses(),
            self::EMAIL_ATTACHMENTS => $this->_phpMailer->getAttachments(),
        );
        
        // Encode it
        $payloadEncoded = json_encode($payload);
        
        // Prepare the payload name
        $payloadName = uniqid('', true) . '.txt';
        
        // Create the file
        if (!is_dir($payloadTempDir = ROOT . '/web/' . IO::tempFolder() . '/notifier-email')) {
            Folder::create($payloadTempDir, 0777, true);
        }
        
        // Store the data
        file_put_contents($payloadTempDir . '/' . $payloadName, $payloadEncoded);
        
        // Send the e-mail
        Process::startTool(Cli_Run_Integration::TOOL_SENDMAIL . ' ' . $payloadName, null, null, null, true, true);
    }
    
    /**
     * Send the e-mail; used to asynchronously send e-mails with other methods (success, failure etc.)
     * 
     * @param string $payloadName Payload name
     */
    public function sendEmail($payloadName = null) {
        // Name is required
        if (!strlen($payloadName)) {
            Console::p('Payload is mandatory', false);
            return;
        }
        
        // Validate the payload name
        if (!file_exists($payloadTempPath = ROOT . '/web/' . IO::tempFolder() . '/notifier-email/' . $payloadName)) {
            Console::p('Payload "' . $payloadName . '" not found', false);
            return;
        }
        
        // Get the payload
        $payloadJson = file_get_contents($payloadTempPath);
        
        // Remove the file
        @unlink($payloadTempPath);
        
        // Validate lenght
        if (!strlen($payloadJson)) {
            Console::p('Payload is mandatory', false);
            return;
        }
        
        // Get the data
        $payloadArray = @json_decode($payloadJson, true);
        
        // Not an array
        if (!is_array($payloadArray)) {
            Console::p('Invalid payload JSON', false);
            return;
        }
        
        // Validate the payload array
        if (!isset($payloadArray[self::EMAIL_SUBJECT])) {
            Console::p('Subject not specified', false);
            return;
        }
        if (!isset($payloadArray[self::EMAIL_BODY])) {
            Console::p('Body not specified', false);
            return;
        }
        if (!isset($payloadArray[self::EMAIL_TO])) {
            Console::p('Recipient not specified', false);
            return;
        }
        if (!isset($payloadArray[self::EMAIL_ATTACHMENTS])) {
            Console::p('Attachments not specified', false);
            return;
        }
        
        // Set the subject
        $this->_phpMailer->Subject = $payloadArray[self::EMAIL_SUBJECT];
        
        // Set the body
        $this->_phpMailer->Body = $payloadArray[self::EMAIL_BODY];
        
        // Add the address(es)
        foreach ($payloadArray[self::EMAIL_TO] as $addressDetails) {
            // Invalid attachment data
            if (count($addressDetails) < 2) {
                continue;
            }
            
            // Get the address details
            list($addressEmail, $addressName) = $addressDetails;
            
            // Add the address to PHP Mailer
            $this->_phpMailer->addAddress($addressEmail, $addressName);
        }
        
        // Add the attachment(s)
        foreach ($payloadArray[self::EMAIL_ATTACHMENTS] as $attachmentDetails) {
            // Invalid attachment data
            if (count($attachmentDetails) < 7) {
                continue;
            }
            
            // Get the attachment details
            list($path,,$name,$encoding,$type,,$disposition) = $attachmentDetails;
            
            // Add the attachment to PHP Mailer
            $this->_phpMailer->addAttachment($path, $name, $encoding, $type, $disposition);
        }

        // Send the e-mail
        try {
            Console::p('Sending e-mail...');
            @$this->_phpMailer->send();
            Console::p('E-mail sent successfully!');
        } catch (Exception $exc) {
            Console::p('E-mail could not be sent', false);
            return false;
        }
        
        return true;
    }
}

/*EOF*/