<?php
/**
 * Theme Warlock - Tasks_3Screenshots
 * 
 * @title      Generate screenshots
 * @desc       Generate the screenshots for the current snapshot
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Tasks_3Screenshots {

    /**
     * Actions
     * 
     * @return null
     */
    public function v1() {
        // Starting point
        PercentBar::display(0);
        
        // Generate the screenshot(s)
        $this->_getScreenshots();
    }
    
    /**
     * Generate the screenshot(s)
     * 
     * @throws Exception
     */
    protected function _getScreenshots() {
        // Nothing to do
        if (!isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID])) {
            throw new Exception('No snapshot ID provided');
        }
        
        // Get the list of snapshots
        $snapshot = WordPress_Snapshots::getInstance()->getById(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID]);
        
        // Grab the original screenshots
        $snapshot->getScreenGrabber()->grab();
        PercentBar::display(25);
        
        // Import the snapshot
        WordPress::executeAction(
            WordPress::TOOLS_TW,
            WordPress::TOOL_TW_SNAPSHOT_IMPORT,
            array($snapshot->getId(), Tasks_1NewProject::$destDir)
        );
        PercentBar::display(75);
        
        // Grab the "Demo Content" screenshots and store them locally
        $snapshot->getScreenGrabber()->grab(WordPress_Snapshots_ScreenGrabber::SUFFIX_CUSTOMER_SNAPSHOT);
        PercentBar::display(100);
    }
}

/* EOF */