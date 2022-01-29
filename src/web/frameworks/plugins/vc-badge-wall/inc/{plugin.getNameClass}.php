<?php
/**
 * {Plugin.getNameClass}
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

// Element Class 
class {project.prefix}_{Plugin.getNameVar} extends WPBakeryShortCode {
     
    // Element Init
    function __construct() {
        add_action('init', array($this, 'vc_infobox_mapping'), 12);
        add_action('wp_enqueue_scripts', array($this, 'vc_scripts'));
        add_shortcode('{project.prefix}_{Plugin.getNameVar}', array($this, 'vc_infobox_html'));
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
                        'type' => 'param_group',
                        'heading' => esc_html__({addon.title}, '{project.destDir}'),
                        'param_name' => 'badges',
                        'value' => '',
                        'params' => array(
                            array(
                                'type' => 'attach_image',
                                'heading' => esc_html__('Image', '{project.destDir}'),
                                'param_name' => 'image',
                                'description' => esc_html__({addon.descImage}, '{project.destDir}'),
                                'admin_label' => false,
                            ),
                            array(
                                'type' => 'vc_link',
                                'heading' => esc_html__('URL', '{project.destDir}' ),
                                'param_name' => 'url',
                                'value' => '',
                                'description' => esc_html__({addon.descUrl}, '{project.destDir}'),
                            ),
                        ),
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),
                                    
                    array(
                        'type' => 'attach_image',
                        'heading' => esc_html__('Background image', '{project.destDir}' ),
                        'param_name' => 'background',
                        'value' => plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}/img/background.jpg',
                        'description' => esc_html__('Set the plugin\'s background image', '{project.destDir}'),
                        'group' => esc_html__('Style', '{project.destDir}'),
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
        
        // Prepare the script URL
        $scriptUrl = plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}';
        
        // Prepare the CSS scripts
        $cssScripts = array(
            '{project.destDir}-{Plugin.getSlug}-style' => array('style', {plugin.getVersion}),
        );

        // Enqueue the CSS
        foreach ($cssScripts as $cssScriptName => $cssScriptData) {
            list($cssScriptFile, $cssScriptVersion) = $cssScriptData;
            wp_enqueue_style($cssScriptName, $scriptUrl . '/css/' . $cssScriptFile . '.css', array(), $cssScriptVersion);
        }
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

        // Numeric value given for the background
        if (is_numeric($background)) {
            // Get the Image data
            $backgroundData = wp_get_attachment_image_src($background, 'full');
            
            // Valid attachment found
            $background = is_array($backgroundData) ? current($backgroundData) : '';
        }
        
        // Prepare the HTML
        $html = '<div ' . ($storyline_append_to_menu ? '' : ' data-storyline-unlisted="true"') . ' class="{Plugin.getSlug} row no-gutters" data-name="' . htmlspecialchars($title) . '" style="background-image: url(' . esc_html($background) . ');">';
        
        // Add the title
        $html .= '<div class="col-12"><h1>'. $title . ' <span>' . $subtitle . '</span></h1></div>';
        
        // Add the content
        $html .= '<div class="col-12 offset-0 col-sm-10 offset-sm-1"><div class="row justify-content-center">';
        
        // Get the badges array
        $badgesArray = array();
        if (strlen($badges)) {
            // Attempt decoding
            $badgesArray = @json_decode(urldecode($badges), true);
            
            // Not a valid value
            if (!is_array($badgesArray)) {
                $badgesArray = array();
            }
        }
        
        // Go through the badges
        foreach ($badgesArray as $badgeData) {
            if (is_array($badgeData) && isset($badgeData['image']) && isset($badgeData['url'])) {
                if (is_numeric($badgeData['image'])) {
                    $imageData = wp_get_attachment_image_src($badgeData['image'], 'thumbnail');
                    
                    // Get the Image SRC
                    $imageSrc = is_array($imageData) ? current($imageData) : '';
                    
                    // Get the link details
                    $badgeVcLink = vc_build_link($badgeData['url']);
                    
                    // Prepare the anchor attributes
                    $anchorAtts = 'href="' . esc_attr($badgeVcLink['url']) . '"';
                    if (strlen($badgeVcLink['title'])) {
                        $anchorAtts .= ' title="' . esc_attr($badgeVcLink['title']) . '"';
                    }
                    if (strlen($badgeVcLink['target'])) {
                        $anchorAtts .= ' target="' . esc_attr($badgeVcLink['target']) . '"';
                    }
                    if (strlen($badgeVcLink['rel'])) {
                        $anchorAtts .= ' rel="' . esc_attr($badgeVcLink['rel']) . '"';
                    }
                    
                    // Add the badge
                    $html .= '<div class="col-6 col-sm-3 col-md-2" data-role="column">' . 
                        '<div class="badge-holder">' . 
                            '<a ' . $anchorAtts . '>' . 
                                '<div class="bkg" style="background-image: url(\'' . esc_url($imageSrc) . '\');"></div>' . 
                            '</a>' . 
                        '</div>' .
                    '</div>';
                }
            }
        }
        
        // Close the holders
        $html .= '</div></div></div>';
        
        // All done
        return $html;
    }
     
}

// Element Class Init
new {project.prefix}_{Plugin.getNameVar}();

/*EOF*/