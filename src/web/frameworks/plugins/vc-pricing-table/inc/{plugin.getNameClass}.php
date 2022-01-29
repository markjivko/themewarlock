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
                        'heading' => esc_html__('Price Plans', '{project.destDir}'),
                        'param_name' => 'plans',
                        'value' => '',
                        'params' => array(
                            array(
                                'type' => 'textfield',
                                'heading' => esc_html__('Price plan name', '{project.destDir}' ),
                                'param_name' => 'name',
                                'value' => '',
                                'description' => esc_html__('Enter price plan name', '{project.destDir}'),
                            ),
                            array(
                                'type' => 'textfield',
                                'heading' => esc_html__('Price plan value', '{project.destDir}' ),
                                'param_name' => 'price',
                                'value' => '',
                                'description' => esc_html__('Enter the price plan value', '{project.destDir}'),
                            ),
                            array(
                                'type' => 'textfield',
                                'heading' => esc_html__('Sign up button text', '{project.destDir}' ),
                                'param_name' => 'button_text',
                                'value' => '',
                                'description' => esc_html__('Enter text on the sign up button', '{project.destDir}'),
                            ),
                            array(
                                'type' => 'vc_link',
                                'heading' => esc_html__('Sign up button URL', '{project.destDir}' ),
                                'param_name' => 'button_url',
                                'value' => '',
                                'description' => esc_html__('Enter URL for the sign up button', '{project.destDir}'),
                            ),
                            array(
                                'type' => 'param_group',
                                'heading' => esc_html__('Price Plan details', '{project.destDir}'),
                                'param_name' => 'items',
                                'value' => urlencode(json_encode(array(
                                    array(
                                        'item_quantity' => '',
                                        'item_description' => '',
                                    ),
                                ))),
                                'params' => array(
                                    array(
                                        'type' => 'textfield',
                                        'heading' => esc_html__('Price plan item quantity', '{project.destDir}' ),
                                        'param_name' => 'item_quantity',
                                        'value' => '',
                                        'description' => esc_html__('Enter a price plan item quantity. Ex: 10GB', '{project.destDir}'),
                                    ),
                                    array(
                                        'type' => 'textfield',
                                        'heading' => esc_html__('Price plan item quantity', '{project.destDir}' ),
                                        'param_name' => 'item_description',
                                        'value' => '',
                                        'description' => esc_html__('Enter a price plan item quantity. Ex: storage space', '{project.destDir}'),
                                    ),
                                ),
                            ),
                        ),
                        'group' => esc_html__('Price Plans', '{project.destDir}'),
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

        // Prepare the JS scripts
        $jsScripts = array(
            '{project.destDir}-{Plugin.getSlug}-functions' => array('functions', {plugin.getVersion}),
        );

        // Enqueue the JS
        foreach ($jsScripts as $jsScriptName => $jsScriptData) {
            list($jsScriptFile, $jsScriptVersion) = $jsScriptData;
            wp_enqueue_script(
                $jsScriptName, 
                preg_match('%^https?\:\/\/%', $jsScriptFile) ? $jsScriptFile : $scriptUrl . '/js/' . $jsScriptFile . '.js', 
                array(), 
                $jsScriptVersion,
                true
            );
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
        
        // Prepare the HTML
        $html = '<div ' . ($storyline_append_to_menu ? '' : ' data-storyline-unlisted="true"') . ' class="{Plugin.getSlug} row no-gutters" data-name="' . htmlspecialchars($title) . '">';
        
        // Add the title
        $html .= '<div class="col-12"><h1>'. $title . ' <span>' . $subtitle . '</span></h1></div>';
        
        // Add the content
        $html .= '<div class="col-12 offset-0 col-sm-10 offset-sm-1" data-role="table"><div class="row no-gutters justify-content-center">';
        
        // Price Plans provided
        if (strlen($plans)) {
            // Attempt decoding
            $plansArray = @json_decode(urldecode($plans), true);
            
            // Valid format
            if (is_array($plansArray)) {
                foreach ($plansArray as $planKey => $planData) {
                    if (is_array($planData) && isset($planData['name']) && isset($planData['price']) && isset($planData['button_text']) && isset($planData['button_url']) && isset($planData['items'])) {
                        // Decode the items
                        $planData['items'] = @json_decode(urldecode($planData['items']), true);

                        // Valid list
                        if (is_array($planData['items'])) {
                            // Start the column
                            $html .= '<div class="col">';
                            
                            // Add the header
                            $html .= '<div class="pricing-table-header">' . $planData['name'] . ' <span>' . $planData['price'] . '</span></div>';
                            
                            // Go through the items
                            foreach ($planData['items'] as $itemKey => $itemData) {
                                // Valid data set
                                if (is_array($itemData) && isset($itemData['item_quantity']) && isset($itemData['item_description'])) {
                                    $html .= '<div class="pricing-table-row' . (0 == $itemKey ? ' first' : '') . '"><b>' . $itemData['item_quantity'] . '</b> <span>' . $itemData['item_description'] . '</span></div>';
                                }
                            }
                            
                            // Get the link details
                            $planVcLink = vc_build_link($planData['button_url']);

                            // Prepare the anchor attributes
                            $anchorAtts = ' href="' . esc_attr($planVcLink['url']) . '"';
                            if (strlen($planVcLink['title'])) {
                                $anchorAtts .= ' title="' . esc_attr($planVcLink['title']) . '"';
                            }
                            if (strlen($planVcLink['target'])) {
                                $anchorAtts .= ' target="' . esc_attr($planVcLink['target']) . '"';
                            }
                            if (strlen($planVcLink['rel'])) {
                                $anchorAtts .= ' rel="' . esc_attr($planVcLink['rel']) . '"';
                            }
                            
                            // Add the footer
                            $html .= '<div class="pricing-table-footer">' . 
                                '<a ' . $anchorAtts . ' class="btn">' . 
                                    $planData['button_text'] . 
                                '</a>' . 
                            '</div>';
                            
                            // End the column
                            $html .= '</div>';
                        }
                    }
                    
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