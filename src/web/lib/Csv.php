<?php
/**
 * Theme Warlock - Csv
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Csv {
    
    const TRUE  = 'y';
    const FALSE = 'n';
    
    /**
     * Get the data from a run.csv file
     * 
     * @param string $csvPath CSV path
     * @return array
     */
    public static function getData($csvPath) {
        // Prepare the data array
        $data = array();

        // Read the file
        if (false !== ($handle = fopen($csvPath, "r"))) {
            while (false !== ($row = fgetcsv($handle, 0, ',', '"', "\\"))) {
                $row = array_map('trim', $row);

                // Good data
                if ('' !== $row[0]) {
                    $data[$row[0]] = isset($row[1]) ? $row[1] : '';
                    
                    // Get the addons as JSON
                    if (in_array($row[0], Cli_Run_Integration::JSON_OPTIONS)) {
                        $data[$row[0]] = @json_decode($data[$row[0]], true);
                    }
                }
            }
            fclose($handle);
        }
        
        // Run.csv
        if ('run.csv' === basename($csvPath)) {
            // Remove invalid values
            foreach(array_keys($data) as $dataKey) {
                if (!isset(Cli_Run_Integration::OPT_DETAILS[$dataKey]) && !in_array($dataKey, Cli_Run_Integration::JSON_OPTIONS)) {
                    unset($data[$dataKey]);
                }
            }
        }

        // All done
        return $data;
    }
    
    /**
     * Save the data in a run.csv file
     * 
     * @param array  $data    Data
     * @param string $csvPath CSV path
     * @return null
     */
    public static function setData($data, $csvPath) {
        // Run.csv
        if ('run.csv' === basename($csvPath)) {
            // Remove invalid keys
            foreach(array_keys($data) as $dataKey) {
                if (!isset(Cli_Run_Integration::OPT_DETAILS[$dataKey]) && !in_array($dataKey, Cli_Run_Integration::JSON_OPTIONS)) {
                    unset($data[$dataKey]);
                }
            }
        }
        
        // Get the file handler
        $fileHandler = fopen($csvPath, 'w');

        // Save the options
        foreach ($data as $key => $value) {
            // Store the addons as JSON
            if (in_array($key, Cli_Run_Integration::JSON_OPTIONS)) {
                $value = json_encode($value);
            }
            
            // Save the file
            fputcsv($fileHandler, array($key, $value), ',', '"', "\\");
        }

        // Close the file handler
        fclose($fileHandler);
    }
}

/*EOF*/