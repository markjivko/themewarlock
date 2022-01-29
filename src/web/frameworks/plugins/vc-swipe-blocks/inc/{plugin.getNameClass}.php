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
                        'heading' => esc_html__({addon.descBlocks}, '{project.destDir}'),
                        'param_name' => 'blocks',
                        'value' => '',
                        'params' => array(
                            array(
                                'type' => 'attach_image',
                                'heading' => esc_html__('Image', '{project.destDir}' ),
                                'param_name' => 'image',
                                'value' => '',
                                'description' => esc_html__({addon.descBlocksImage}, '{project.destDir}'),
                            ),
                            array(
                                'type' => 'textfield',
                                'heading' => esc_html__('Title', '{project.destDir}' ),
                                'param_name' => 'title',
                                'value' => '',
                                'description' => esc_html__({addon.descBlocksTitle}, '{project.destDir}'),
                            ),
                            array(
                                'type' => 'textarea',
                                'heading' => esc_html__('Content', '{project.destDir}' ),
                                'param_name' => 'content',
                                'value' => '',
                                'description' => esc_html__({addon.descBlocksContent}, '{project.destDir}'),
                            ),
                        ),
                        'group' => esc_html__({addon.descBlocks}, '{project.destDir}'),
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
        $html .= '<div class="col-12 offset-0 col-sm-10 offset-sm-1"><div class="row swipe">';
        
        // Swipe Blocks provided
        if (strlen($blocks)) {
            // Attempt decoding
            $blocksArray = @json_decode(urldecode($blocks), true);
            
            // Valid format
            if (is_array($blocksArray)) {
                foreach ($blocksArray as $blockKey => $blockData) {
                    if (is_array($blockData) && isset($blockData['image']) && isset($blockData['title']) && isset($blockData['content'])) {
                        if (is_numeric($blockData['image'])) {
                            // Get the image data
                            $imageBlockData = wp_get_attachment_image_src($blockData['image'], 'thumbnail');
                            
                            // Get the Image SRC
                            $imageSrc = is_array($imageBlockData) ? current($imageBlockData) : '';
                            
                            // Append the HTML
                            $html .= '<div class="col-12"><div>' . 
                                esc_html($blockData['content']) . 
                                '<span class="author" style="background-image: url(\'' . esc_url($imageSrc) . '\');">' . 
                                    '<span>' . $blockData['title'] . '</span>' . 
                                '</span>' .
                            '</div></div>';
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