<?php
/**
 * Theme Warlock - WordPress_Customizer_Element_Section
 * 
 * @copyright  (c) 2019, Mark Jivko
 * @author     Mark Jivko https://markjivko.com
 * @package    Theme Warlock
 * @since      TW 1.0
 */

class WordPress_Customizer_Element_Section extends WordPress_Customizer_Element {

    /**
     * Section parent panel
     * 
     * @var WordPress_Customizer_Element_Panel|null
     */
    protected $_panel = null;
    
    /**
     * Generate the PHP code that adds a WP Customizer Section
     * 
     * @return string
     */
    public function register() {
        // Export the section ID
        $sectionIdExported = $this->_exportMixed($this->getId());
        
        // Export the section name
        $sectionNameExported = $this->_exportMixed(
            $this->getName(), 
            $this->getTranslateName()
        );
        
        // Export the section description
        $sectionDescriptionExported = $this->_exportMixed(
            $this->getDescription(), 
            $this->getTranslateDescription()
        );
        
        // Prepare the panel ID
        $panelIdExported = $this->_exportMixed(null == $this->getPanel() ? null : $this->getPanel()->getId());
            
        // Prepare the section
        return <<<"CODE"
// Add the '$this->_name' section
\$wp_customize->add_section($sectionIdExported, array(
    'priority'       => $this->_priority,
    'title'          => $sectionNameExported,
    'description'    => $sectionDescriptionExported,
    'panel'          => $panelIdExported,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '',
));
CODE;
    }

    /**
     * Get this section's parent panel
     * 
     * @return WordPress_Customizer_Element_Panel|null
     */
    public function getPanel() {
        return $this->_panel;
    }
    
    /**
     * Set this section's parent panel
     * 
     * @param WordPress_Customizer_Element_Panel $panel Section parent panel
     * @return WordPress_Customizer_Element_Section
     */
    public function setPanel(WordPress_Customizer_Element_Panel $panel) {
        $this->_panel = $panel;
        return $this;
    }
}

/* EOF */