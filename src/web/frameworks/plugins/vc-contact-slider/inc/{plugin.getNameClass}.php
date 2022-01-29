<?php
/**
 * {Plugin.getNameClass}
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

// Element Class 
class {project.prefix}_{Plugin.getNameVar} extends WPBakeryShortCode {
    
    /**
     * AJAX Fields
     */
    const AJAX_RESULT_STATUS  = 'status';
    const AJAX_RESULT_MESSAGE = 'message';
    
    /**
     * Field keys
     */
    const FIELD_API_KEY_GOOGLE_MAPS       = 'api_key_google_maps';
    const FIELD_API_KEY_RE_CAPTCHA_SITE   = 'api_key_re_captcha_site';
    const FIELD_API_KEY_RE_CAPTCHA_SECRET = 'api_key_re_captcha_secret';
    const FIELD_BUTTON_TEXT               = 'form_button_text';
    const FIELD_FORM_COLOR_INDEX          = 'form_color_index';
    const FIELD_FORM_TITLE_TEXT           = 'form_title_text';
    const FIELD_FORM_SUB_TITLE_TEXT       = 'form_sub_title_text';
    const FIELD_FORM_INPUT_NAME_TEXT      = 'form_input_name_text';
    const FIELD_FORM_INPUT_EMAIL_TEXT     = 'form_input_email_text';
    const FIELD_FORM_INPUT_MESSAGE_TEXT   = 'form_input_message_text';
    const FIELD_FORM_INPUT_BUTTON_TEXT    = 'form_input_button_text';
    const FIELD_FORM_LABEL_COMPANY_TEXT   = 'form_label_company_text';
    const FIELD_FORM_LABEL_PHONE_TEXT     = 'form_label_phone_text';
    const FIELD_FORM_LABEL_ADDRESS_TEXT   = 'form_label_address_text';
    const FIELD_FORM_THANK_YOU_TEXT       = 'form_thank_you_text';
    
    // Element Init
    function __construct() {
        // VC Mapping
        add_action('init', array($this, 'vc_infobox_mapping'), 12);
        add_action('wp_enqueue_scripts', array($this, 'vc_scripts'));
        add_shortcode('{project.prefix}_{Plugin.getNameVar}', array($this, 'vc_infobox_html'));
        
        // AJAX
        add_action('wp_ajax_nopriv_st_contact_slider_ajax', array($this, 'ajaxHandler'));
        add_action('wp_ajax_st_contact_slider_ajax', array($this, 'ajaxHandler'));

        // Customizer
        add_action('customize_register', array($this, 'customizer'));
    }
    
    /**
     * Store the API information in a private place
     * 
     * Note: These settings are theme-specific, so changing the theme requires 
     * re-setting the Credentials
     */
    public function customizer($wp_customize) {
        // Add the '{addon.title}' section
        $wp_customize->add_section('st_section_plugin_{Plugin.getNameVar}', array(
            'priority'       => 165,
            'panel'          => function_exists('{project.prefix}_setup') ? {call.core.getThemePanel.getId} : '',
            'title'          => esc_html__({addon.title}, '{project.destDir}'),
            'description'    => esc_html__({addon.description}, '{project.destDir}') . ' Get your keys from <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key/">here<a/> and <a target="_blank" href="https://developers.google.com/recaptcha/intro/">here</a>.',
            'capability'     => 'edit_theme_options',
            'theme_supports' => '',
        ));

        // Prepare the list
        $optionsList = array(
            self::FIELD_API_KEY_GOOGLE_MAPS       => esc_html__('Google Maps API Key', '{project.destDir}'),
            self::FIELD_API_KEY_RE_CAPTCHA_SITE   => esc_html__('ReCaptcha Site Key', '{project.destDir}'),
            self::FIELD_API_KEY_RE_CAPTCHA_SECRET => esc_html__('ReCaptcha Secret Key', '{project.destDir}'),
        );
        
        // Store the options
        foreach ($optionsList as $optionKey => $optionTitle) {
            // Setting
            $wp_customize->add_setting('st_setting_plugin_{Plugin.getNameVar}_' . $optionKey, array(
                'default'           => '',
                'transport'         => 'refresh',
                'sanitize_callback' => 'wp_filter_nohtml_kses',
            ));

            // Control
            $wp_customize->add_control(new WP_Customize_Control($wp_customize, 'st_setting_plugin_{Plugin.getNameVar}_' . $optionKey, array(
                'section'     => 'st_section_plugin_{Plugin.getNameVar}',
                'type'        => 'text',
                'label'       => esc_html__($optionTitle, '{project.destDir}'),
                'description' => '',
            )));
        }
    }
    
    // AJAX
    public function ajaxHandler() {
        // Prepare the result
        $result = array(
            {project.prefix}_{Plugin.getNameVar}::AJAX_RESULT_STATUS  => true,
            {project.prefix}_{Plugin.getNameVar}::AJAX_RESULT_MESSAGE => null,
        );
        
        try {
            // No reCaptcha code
            if (!isset($_REQUEST['g-recaptcha-response'])) {
                throw new Exception(esc_html__('Invalid request, reCaptcha response code missing', '{project.destDir}'));
            }

            // Nonce failed
            if (!check_ajax_referer('{project.prefix}-contact-widget', 'st-contact-widget-nonce', false)) {
                throw new Exception(esc_html__('Invalid Nonce.', '{project.destDir}'));
            }

            // Get the options
            $keyReCaptchaSecret = get_theme_mod('st_setting_plugin_{Plugin.getNameVar}_' . {project.prefix}_{Plugin.getNameVar}::FIELD_API_KEY_RE_CAPTCHA_SECRET, '');

            // reCaptcha Secret not defined
            if (!strlen($keyReCaptchaSecret)) {
                throw new Exception(esc_html__('reCaptcha Secret not defined, please check your settings', '{project.destDir}'));
            }
            
            // Get the response
            $reCaptchaResponse = wp_remote_post(
                'https://www.google.com/recaptcha/api/siteverify',
                array(
                    'method' => 'POST',
                    'body' => array(
                        'secret'   => $keyReCaptchaSecret,
                        'response' => trim($_REQUEST['g-recaptcha-response']),
                    ),
                )
            );
            $reCaptchaResult = isset($reCaptchaResponse['body']) ? @json_decode($reCaptchaResponse['body'], true) : null;

            // Could not connect
            if (!is_array($reCaptchaResult)) {
                throw new Exception(esc_html__('Could not connect to the ReCaptcha server', '{project.destDir}'));
            }

            // Remote errors
            if (isset($reCaptchaResult["error-codes"]) && count($reCaptchaResult["error-codes"])) {
                throw new Exception(
                    sprintf(
                        esc_html__('ReCaptcha errors: %s', '{project.destDir}'), 
                        implode(', ', $reCaptchaResult["error-codes"])
                    )
                );
            }

            // Generic error
            if (!isset($reCaptchaResult['success']) || !$reCaptchaResult['success']) {
                throw new Exception(esc_html__('ReCaptcha unsuccessful', '{project.destDir}'));
            }

            // Prepare the comment data
            $commentData = array(
                'comment_author'       => sanitize_text_field($_REQUEST['st-contact-name']),
                'comment_author_email' => sanitize_text_field($_REQUEST['st-contact-email']),
                'comment_content'      => sanitize_text_field($_REQUEST['st-contact-content']),
                'comment_author_url'   => '', // No URL available in the contact form
                'comment_type'         => '', // Empty for regular comments, 'pingback' for pingbacks, 'trackback' for trackbacks
                'comment_approved'     => 0,  // Contact form comments should not be visible
                'comment_parent'       => 0,  // No parent comment
            );

            // Insert the new comment
            try {
                if (!wp_new_comment($commentData, true)) {
                    throw new Exception(esc_html__('Could not save your message. Please try again later.', '{project.destDir}'));
                }
            } catch (phpmailerException $phpMailException) {
                // Nothing to do here; bug fixed in WP 4.7+
                // @see https://core.trac.wordpress.org/ticket/37736
            }
        } catch (Exception $exc) {
            $result[{project.prefix}_{Plugin.getNameVar}::AJAX_RESULT_STATUS] = false;
            $result[{project.prefix}_{Plugin.getNameVar}::AJAX_RESULT_MESSAGE] = $exc->getMessage();
        }
        
        // Output the result
        echo json_encode($result);
        
        // Prevent any other output
        exit();
    }
    
    // Element Mapping
    public function vc_infobox_mapping() {
        // Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }
         
        // Map the block with vc_map()
        vc_map( 
            array(
                'name' => '{project.destProjectName}: ' . esc_html__({addon.title}, '{project.destDir}'),
                'base' => '{project.prefix}_{Plugin.getNameVar}',
                'description' => esc_html__({addon.description}, '{project.destDir}'), 
                'category' => '{project.destProjectName}',   
                'icon' => plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}/vc-elements/icon.png', 
                'params' => array(   
                         
                    array(
                        'type' => 'textfield',
                        'holder' => 'h3',
                        'class' => 'title-class',
                        'heading' => esc_html__('Title', '{project.destDir}'),
                        'param_name' => 'title',
                        'value' => esc_html__({addon.defSectTitle}, '{project.destDir}'),
                        'description' => esc_html__('Section Title', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),  
                     
                    array(
                        'type' => 'textarea',
                        'holder' => 'div',
                        'class' => 'subtitle-class',
                        'heading' => esc_html__('Subtitle', '{project.destDir}'),
                        'param_name' => 'subtitle',
                        'value' => esc_html__({addon.defSectSubtitle}, '{project.destDir}'),
                        'description' => esc_html__('Section Subtitle', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),
                    
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__('Map color set', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_COLOR_INDEX,
                        'value' => array(
{foreach.core.getColors}
                            esc_html__({@value.name}, '{project.destDir}') => {@key},
{/foreach.core.getColors}
                        ),
                        'std' => 1,
                        'description' => esc_html__('Map main color', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ), 
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Button text', '{project.destDir}'),
                        'param_name' => self::FIELD_BUTTON_TEXT,
                        'value' => esc_html__('Contact Us', '{project.destDir}'),
                        'description' => esc_html__('Text on the toggle button', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Title', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_TITLE_TEXT,
                        'value' => esc_html__('Contact Us', '{project.destDir}'),
                        'description' => esc_html__('Contact form title', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Subtitle', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_SUB_TITLE_TEXT,
                        'value' => esc_html__('Contact Us', '{project.destDir}'),
                        'description' => esc_html__('Contact form subtitle', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Input - Name', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_INPUT_NAME_TEXT,
                        'value' => esc_html__('Name', '{project.destDir}'),
                        'description' => esc_html__('Value for the name input field', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Input - Email', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_INPUT_EMAIL_TEXT,
                        'value' => esc_html__('Email', '{project.destDir}'),
                        'description' => esc_html__('Value for the email input field', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Input - Message', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_INPUT_MESSAGE_TEXT,
                        'value' => esc_html__('Message', '{project.destDir}'),
                        'description' => esc_html__('Value for the message textarea', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Button - Submit', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_INPUT_BUTTON_TEXT,
                        'value' => esc_html__('Send', '{project.destDir}'),
                        'description' => esc_html__('Value for the form submission button', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ), 
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Label - Company', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_LABEL_COMPANY_TEXT,
                        'value' => '{config.authorName}',
                        'description' => esc_html__('Company label', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Label - Phone', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_LABEL_PHONE_TEXT,
                        'value' => '{config.authorPhone}',
                        'description' => esc_html__('Phone number label', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Form Label - Address', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_LABEL_ADDRESS_TEXT,
                        'value' => '{config.authorAddress}',
                        'description' => esc_html__('Address label', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                            
                    array(
                        'type' => 'textfield',
                        'heading' => esc_html__('Thank You Note', '{project.destDir}'),
                        'param_name' => self::FIELD_FORM_THANK_YOU_TEXT,
                        'value' => esc_html__('Thank you!', '{project.destDir}'),
                        'description' => esc_html__('Final thank you note', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Contact Form', '{project.destDir}'),
                    ),  
                    
{if.core.useStoryline}
                    array(
                        'type' => 'checkbox',
                        'heading' => esc_html__('Append to menu', '{project.destDir}'),
                        'param_name' => 'storyline_append_to_menu',
                        'description' => esc_html__('Append this block to the "Dynamic menu", if it was enabled in "Customizer - Menu area"', '{project.destDir}'),
                        'group' => 'StoryLine.js',
                        'std' => 'true',
                    ),
{/if.core.useStoryline}
                ),
            )
        );                                
        
    }
     
    // Add the scripts
    public function vc_scripts() {
        {utils.common.enqueueScripts}
        
        // Get the options
        $keyGoogleMaps = get_theme_mod(
            'st_setting_plugin_{Plugin.getNameVar}_' . {project.prefix}_{Plugin.getNameVar}::FIELD_API_KEY_GOOGLE_MAPS, 
            '{if.core.staging}{config.apiKeyGoogleMaps}{/if.core.staging}'
        );
        $keyReCaptchaSite = get_theme_mod(
            'st_setting_plugin_{Plugin.getNameVar}_' . {project.prefix}_{Plugin.getNameVar}::FIELD_API_KEY_RE_CAPTCHA_SITE, 
            '{if.core.staging}{config.apiKeyReCaptchaSite}{/if.core.staging}'
        );
        $keyReCaptchaSecret = get_theme_mod(
            'st_setting_plugin_{Plugin.getNameVar}_' . {project.prefix}_{Plugin.getNameVar}::FIELD_API_KEY_RE_CAPTCHA_SECRET, 
            '{if.core.staging}{config.apiKeyReCaptchaSecret}{/if.core.staging}'
        );
        
        // Enqueue the Google Maps API script
        if (strlen($keyGoogleMaps)) {
            wp_enqueue_script(
                '{project.destDir}-google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key=' . urlencode($keyGoogleMaps), 
                array(), 
                null,
                true
            );
        }
        
        // Enqueue the reCAPTCHA script
        if (strlen($keyReCaptchaSite) && strlen($keyReCaptchaSecret)) {
            wp_enqueue_script(
                '{project.destDir}-recaptcha-api',
                'https://www.google.com/recaptcha/api.js', 
                array(), 
                null,
                true
            );
        }
        
        // Prepare the script URL
        $scriptUrl = plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}';
        
        // Enqueue the main JS/CSS files
        wp_enqueue_script(
            '{project.destDir}-{Plugin.getSlug}-functions', 
            $scriptUrl . '/js/functions.js', 
            array(), 
            {plugin.getVersion},
            true
        );
        wp_localize_script(
            '{project.destDir}-{Plugin.getSlug}-functions', 
            '{project.prefix}_contact_slider',
            array(
                'ajax_url' => admin_url('admin-ajax.php?action=st_contact_slider_ajax')
            )
        );
        wp_enqueue_style(
            '{project.destDir}-{Plugin.getSlug}-style', 
            $scriptUrl . '/css/style.css', 
            array(), 
            {plugin.getVersion}
        );
    }
     
    // Element HTML
    public function vc_infobox_html($atts) {
        // Params extraction
        extract(
            $atts = shortcode_atts(
                vc_map_get_defaults('{project.prefix}_{Plugin.getNameVar}'), 
                $atts
            )
        );

        // Get the site key
        $keyReCaptchaSite = get_theme_mod('st_setting_plugin_{Plugin.getNameVar}_' . {project.prefix}_{Plugin.getNameVar}::FIELD_API_KEY_RE_CAPTCHA_SITE, '');
        
        // Prepare the HTML
        $html = '<div ' . ($storyline_append_to_menu ? '' : ' data-storyline-unlisted="true"') . ' class="{Plugin.getSlug} row no-gutters" data-name="' . htmlspecialchars($title) . '">';
        
        // Prepare the buffer
        ob_start();

        // Load the template
        require dirname(dirname(__FILE__)) . '/template-parts/contact-slider.php';

        // Store the result
        $html .= ob_get_clean();
        
        // Close the div
        $html .= '</div>';
        
        // All done
        return $html;
    }
     
}

// Element Class Init
new {project.prefix}_{Plugin.getNameVar}();

/*EOF*/