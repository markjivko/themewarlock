<?php

/**
 * Create the snapshots
 * 
 * - generate previews
 * - append to the documentation
 * - add to project
 */
class Tasks_2Snapshot {
    
    /**
     * Actions
     * 
     * @return null
     */
    public function v1() {
        // Starting point
        PercentBar::display(0);
        
        // Export and save screenshots
        $this->_getSnapshotExport();
    }
    
    /**
     * Export the current snapshot
     * 
     * @throws Exception
     */
    protected function _getSnapshotExport() {
        // Nothing to do
        if (!isset(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID])) {
            throw new Exception('No snapshot ID provided');
        }
        
        // Get the list of snapshots
        $snapshot = WordPress_Snapshots::getInstance()->getById(Cli_Run_Integration::$options[Cli_Run_Integration::IOPT_SNAPSHOT_ID]);
        
        // Export the current snapshot to a compliant package
        Log::check(Log::LEVEL_INFO) && Log::info('Exporting current snapshot into a compliant package...');
        $snapshot->getExporter()->export();

        // Compute the percent
        PercentBar::display(100);
    }
}

/*EOF*/