<?php
/**
 * Add support for custom fonts
 * 
 * @link https://developer.wordpress.org/themes/customize-api/
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

if (!class_exists('St_Fonts')) {
    
    /**
     * Custom Fonts helper class
     * 
     * @example St_Fonts::get()->family(St_Fonts::FONT_CLASS_H1);
     */
    class St_Fonts {
{foreach.core.getFonts}
        // {@Value.name} font class
        const FONT_CLASS_{@Value.const} = {@key};
{/foreach.core.getFonts}
        // Default font family
        const DEFAULT_FONT_FAMILY = 'inherit';
        
        // Minimum required font weights and styles per font family for each font class
        const FONT_FAMILIES = array();
        
        /**
         * Instance of St_Fonts
         * 
         * @var St_Fonts
         */
        protected static $_instance = null;
        
        /**
         * Store the font family for each font class as they were selected by the
         * theme user
         */
        protected $_fontFamiliesSelected = array();
        
        /**
         * Singleton instance of St_Fonts
         * 
         * @return St_Fonts
         */
        public static function get() {
            if (null === self::$_instance) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }
        
        /**
         * Custom Fonts
         */
        protected function __construct() {
{foreach.core.getFonts}
            // Get font family for font class "{@Key}"
            $this->_fontFamiliesSelected[self::FONT_CLASS_{@Value.const}] = get_theme_mod('st_font_{@Key}', '');
            
            // Store the default for the font class "{@Key}"
            if (!strlen($this->_fontFamiliesSelected[self::FONT_CLASS_{@Value.const}])) {
                $this->_fontFamiliesSelected[self::FONT_CLASS_{@Value.const}] = {@value.default};
            }
{/foreach.core.getFonts}
        }
        
        /**
         * Export all the required font families for this theme, in a format
         * that is compatible with the <b>family</b> GET argument in the
         * <b>//fonts.googleapis.com/css</b> URL
         * 
         * @return string List of all the required Google Font families and 
         * their corresponding font weights
         * @example "fontFamilyA:weightA,weightB|fontFamilyB:weightA,weightB"
         */
        public function exportFontFamilies() {
            // Prepare the list of font families and their required weights
            $googleFontFamilies = array();
            $fontFamilies = St_Fonts::FONT_FAMILIES;
            
            // Go through our fonts
            foreach ($this->_fontFamiliesSelected as $fontClass => $fontFamily) {
                // Not a valid font class
                if (!isset($fontFamilies[$fontClass])) {
                    continue;
                }

                // Not a Google Font
                if (!isset($fontFamilies[$fontClass][$fontFamily])) {
                    continue;
                }

                // Font family weights not defined
                if (!isset($googleFontFamilies[$fontFamily])) {
                    $googleFontFamilies[$fontFamily] = array();
                }
                
                // Append the font family weights
                $googleFontFamilies[$fontFamily] = array_unique(
                    array_merge(
                        $googleFontFamilies[$fontFamily],
                        St_Fonts::FONT_FAMILIES[$fontClass][$fontFamily]
                    )
                );
            }

            // Prepare the final strings
            $googleFontFamiliesFormatted = array();
            
            // Append the fontFamily:weightA,weightB items
            foreach ($googleFontFamilies as $fontFamily => $fontStyles) {
                $googleFontFamiliesFormatted[] = $fontFamily . ':' . implode(',', $fontStyles);
            }
            
            // Separate font families definitions with a pipe character
            return implode('|', $googleFontFamiliesFormatted);
        }
        
        /**
         * Get the font family for this font class, as selected by the end user
         * of the theme
         * 
         * @param string $fontClass <p>Font Family Class, one of<ul>{foreach.core.getFonts}
         * <li>St_Fonts::FONT_CLASS_{@Value.const}</li>{/foreach.core.getFonts}
         * </ul>
         * </p>
         * @return string
         */
        public function family($fontClass) {
            // Font class not allowed
            $fontFamilies = self::FONT_FAMILIES;
            if (!isset($fontFamilies[$fontClass])) {
                $fontClass = self::FONT_CLASS_TEXT;
            }
            
            // Valid font family selected for this font class
            if (isset($this->_fontFamiliesSelected[$fontClass]) && strlen($this->_fontFamiliesSelected[$fontClass])) {
                return $this->_fontFamiliesSelected[$fontClass];
            }
            
            // Nothing found, revert to the inherited font family
            return self::DEFAULT_FONT_FAMILY;
        }
    }
}

/**
 * Register the custom fonts
 * 
 * @param WP_Customize_Manager $wp_customize WordPress Customize Manager
 */
function {project.prefix}_customize_custom_fonts_register($wp_customize) {
    // Prepare the section ID
    $sectionId = 'st_custom_fonts_section';
        
    // Add the Fonts section
    $wp_customize->add_section($sectionId, array(
        'title'          => __('Typography', '{project.destDir}'),
        'description'    => __('Assign font families to different UI elements.', '{project.destDir}') . ' ' . 
            sprintf(__('Check out %s for more details on each font family.', '{project.destDir}'), '<a href="//fonts.google.com/" target="_blank">Google Fonts</a>'),
        'panel'          => {call.core.getThemePanel.getId},
        'capability'     => 'edit_theme_options',
        'theme_supports' => '',
    ));
    
{foreach.core.getFonts}
    // Prepare the available font families
    $fontFamilies{@Key} = array(
        '' => __('Default', '{project.destDir}')
    );
    
    // Add the available font families
    foreach (array_keys(St_Fonts::FONT_FAMILIES[St_Fonts::FONT_CLASS_{@Value.const}]) as $fontFamily) {
        $fontFamilies{@Key}[$fontFamily] = $fontFamily;
    }
    
    // {@Value.name} font setting
    $wp_customize->add_setting('st_font_{@Key}', array(
        'default'           => {@value.default},
        'transport'         => 'refresh',
        'sanitize_callback' => 'esc_attr',
    ));
    
    // Customize the font families list for this font control
    $st_font_{@Key}_families = $fontFamilies{@Key};
    $st_font_{@Key}_families[''] = '{@Value.default} (' . $st_font_{@Key}_families[''] . ')';
        
    // {@Value.name} font control
    $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'st_font_{@Key}', array(
        'section'     => $sectionId,
        'type'        => 'select',
        'label'       => __({@value.name}, '{project.destDir}'),
        'choices'     => $st_font_{@Key}_families,
    )));
{/foreach.core.getFonts}
}
add_action('customize_register', '{project.prefix}_customize_custom_fonts_register');

/**
 * Load the required Google Fonts
 */
function {project.prefix}_custom_fonts_styles($wp_customize) {
    // Get the font families definition
    $googleFontFamiliesExported = St_Fonts::get()->exportFontFamilies();
    
    // Valid definitions found
    if (strlen($googleFontFamiliesExported)) {
        // Load the CSS
        wp_enqueue_style(
            '{project.prefix}-font-families',
            'https://fonts.googleapis.com/css?family=' . $googleFontFamiliesExported,
            array(), 
            '{project.versionVerbose}'
        );
    }
}
add_action('wp_enqueue_scripts', '{project.prefix}_custom_fonts_styles', 999);

/*EOF*/