<?php
/**
 * Theme Warlock - WordPress_Snapshots_ScreenGrabber
 * 
 * @title      Snapshots Screen Grabber
 * @desc       Grab screenshots for the current snapshot
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Snapshots_ScreenGrabber {

    const SUFFIX_NONE              = '';
    const SUFFIX_CUSTOMER_SNAPSHOT = 'customer-snapshot';
    
    /**
     * Parent snapshot object
     * 
     * @var WordPress_Snapshots_Snapshot
     */
    protected $_snapshot;
    
    /**
     * Screenshot grabbing tool
     *
     * @var Screenshot
     */
    protected $_screenshot;
    
    /**
     * Available suffixes
     * 
     * @var string[]
     */
    protected $_suffixes;
    
    /**
     * Screen Grabber - can only be constructed by a WordPress_Snapshots_Snapshot instance
     * 
     * @param WordPress_Snapshots_Snapshot $snapshotObject Snapshot object
     * 
     */
    public function __construct(WordPress_Snapshots_Snapshot $snapshotObject) {
        // Store the parent snapshot
        $this->_snapshot = $snapshotObject;
        
        // Screenshot grabber
        $this->_screenshot = new Screenshot();
        
        // Store the suffixes
        $this->_suffixes = array_values(
            array_filter(
                (new ReflectionClass($this))->getConstants(), 
                function($constantKey) {
                    return preg_match('%^SUFFIX_%', $constantKey);
                }, 
                ARRAY_FILTER_USE_KEY
            )
        );
    }
    
    /**
     * Grab screenshot(s)
     * 
     * @param string  $screenGrabSuffix (optional) Suffix for the current screen grab;
     * default <b>empty string</b>
     * @param boolean $customerFacing   (optional) Whether to make the current 
     * screen grab available to the customer; default <b>false</b>
     * @param int     $width            (optional) Screenshot Width; default 
     * <b>WordPress_Snapshots_Snapshot::PREVIEW_WIDTH</b>
     */
    public function grab($screenGrabSuffix = self::SUFFIX_NONE, $customerFacing = false, $width = WordPress_Snapshots_Snapshot::PREVIEW_WIDTH) {
        // Sanitize the suffix
        if (!in_array($screenGrabSuffix, $this->_suffixes)) {
            $screenGrabSuffix = self::SUFFIX_NONE;
        }
        
        // Prepare the end particle
        $fileNameSuffix = strlen($screenGrabSuffix) ? ('-' . $screenGrabSuffix) : '';
        
        // Log the task
        Log::check(Log::LEVEL_INFO) && Log::info('Grabbing ' . ($customerFacing ? 'customer-facing' : 'private') . (strlen($screenGrabSuffix) ? ' "' . $screenGrabSuffix . '" suffixed' : '') . ' screenshots @' . $width . 'px...');
        
        // Prepare the local save path
        $localSavePath = IO::outputPath() . '/' . Dist::FOLDER_SNAPSHOTS . '/' . $this->_snapshot->getId() . $fileNameSuffix . '.png';
        
        // Prepare the "Demo Content" save path or the local path
        $screenshotPath = $customerFacing ? (rtrim($this->_snapshot->getDistPath(), '\\/') . $fileNameSuffix . '.png') : $localSavePath;
        
        // Grab the screenshot in the "Demo Content" customer-facing folder
        $this->_screenshot->grab(
            'http://' . $this->_snapshot->getProject()->getConfig()->getSandboxDomain(), 
            $screenshotPath,
            $width
        );

        // Copy the snapshot locally as well; if not customer-facing, the snapshot is already stored locally
        if ($customerFacing && is_dir(IO::outputPath() . '/' . Dist::FOLDER_SNAPSHOTS)) {
            copy($screenshotPath, $localSavePath);
        }
    }
}

/* EOF */