<?php
/**
 * Theme Warlock - Controller_System
 * 
 * @title      System controller
 * @desc       System web tools
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class Controller_System extends Controller {

    /**
     * Configuration
     * 
     * @name Configuration
     * @allowed admin
     */
    public function index() {
        // Get the view
        $view = new View_System();
        
        // Add the scripts
        $view->addJs('system/index');
        
        // Set the placeholder
        $view->setPlaceholder(View_System::PH_ENTRIES, Config_Items_Descriptor::getInstance()->describe());
        
        // Output the part
        echo $view->getPart(View_System::PART_SYSTEM_INDEX);
        
        // Display it
        $view->display();
    }
}

/*EOF*/