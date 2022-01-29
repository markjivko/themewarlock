<?php
/**
 * Theme Warlock - WordPress_Customizer_Element_Panel
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Customizer_Element_Panel extends WordPress_Customizer_Element {

    /**
     * Generate the PHP code that adds a WP Customizer Panel
     * 
     * @return string
     */
    public function register() {
        // Export the panel ID
        $panelIdExported = $this->_exportMixed($this->getId());
        
        // Export the panel name
        $panelNameExported = $this->_exportMixed(
            $this->getName(), 
            $this->getTranslateName()
        );
        
        // Export the panel description
        $panelDescriptionExported = $this->_exportMixed(
            $this->getDescription(), 
            $this->getTranslateDescription()
        );

        // Prepare the panel
        return <<<"CODE"
// Add the '$this->_name' panel
\$wp_customize->add_panel($panelIdExported, array(
    'priority'       => $this->_priority,
    'title'          => $panelNameExported,
    'description'    => $panelDescriptionExported,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
));
CODE;
    }
    
}

/* EOF */