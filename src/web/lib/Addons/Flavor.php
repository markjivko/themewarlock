<?php
/**
 * Theme Warlock - Addons_Flavor
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Addons_Flavor {

    /**
     * Get the Add-Ons flavor configuration item
     * 
     * @param string $addonName    Current add-on
     * @param string $currentValue Current flavor value
     * @return Model_Project_Config_Item_String|null Null if not flavors were defined
     */
    public static function getConfigItem($addonName, $currentValue = null) {
        // Prepare the flavors
        $addonFlavors = array_map('basename', glob(ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName . '/' . Addons::GO_FOLDER_NAME . '/*', GLOB_ONLYDIR));
        
        // Remove the Docs folder
        $addonFlavors = array_values(array_filter($addonFlavors, function($item) {
            return WordPress_Docs::FOLDER_NAME !== $item;
        }));
        
        // Prepare the flavors descriptions
        $addonFlavorsDescriptions = array();
        foreach ($addonFlavors as $addonFlavor) {
            // Prepare the description
            $description = '';
            
            // Prepare the info file
            if (is_file($addonFlavorInfoPath = ROOT . '/web/' . Framework::FOLDER . '/' . Framework::FOLDER_ADDONS . '/' . $addonName . '/' . Addons::GO_FOLDER_NAME . '/' . $addonFlavor . '/' . Addons::FLAVOR_FILE_INFO)) {
                // Get the readme
                $description = file_get_contents($addonFlavorInfoPath);

                // Parse the text
                $description = Parsedown::instance()->text($description);
                
                // Increment the headings
                $description = preg_replace_callback('%<(\/?)\s*h(\d)\s*>%', function($item){
                    // Prepare the headhing
                    $heading = intval($item[2]) + 1;

                    // Out of bounds
                    if ($heading > 6) {
                        $heading = 6;
                    }

                    // Replace the HTML tag
                    return '<' . $item[1] . 'h' . ($heading) . '>';
                }, $description);
            }
            
            // Store the data
            $addonFlavorsDescriptions[$addonFlavor] = array(
                ucfirst($addonFlavor),
                $description
            );
        }
        
        // Valid number of flavors found
        if (count($addonFlavors)) {
            // Prepare the default value
            $defaultValue = in_array(Addons::FLAVOR_NAME_DEFAULT, $addonFlavors) ? Addons::FLAVOR_NAME_DEFAULT : $addonFlavors[0];
            
            // Add the flavor element
            return (new Model_Project_Config_Item_String(Model_Project_Config_Item::KEY_FLAVOR))
                ->setValue(null !== $currentValue ? $currentValue : $defaultValue)
                ->setOptions($addonFlavors)
                ->setMetaOptionsDetails($addonFlavorsDescriptions)
                ->setMetaTitle('Flavor preset')
                ->setMetaDescription('Select a flavor preset (custom CSS and JS rules) for ' . $addonName)
                ->setMetaHeader('Flavor');
        }
        
        // No definitions found
        return null;
    }

}

/* EOF */
