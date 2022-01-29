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
    
    /**
     * Get the available categories
     */
    protected function _getCategories() {
        $categories = array(
            '-'
        );
        foreach(get_categories() as $category ) {
            // Base category
            if (0 == $category->category_parent) {
                $categories[] = $category->name;
            }
        } 
        return $categories;
    }
    
    // Element Mapping
    public function vc_infobox_mapping() {
        // Stop all if VC is not enabled
        if (!defined('WPB_VC_VERSION')) {
            return;
        }
        
        // Prepare the maximum number of posts options
        $maxPostsCount = array();
        for($increment = 1; $increment <= 10; $increment++) {
            $maxPostsCount[$increment * 3] = $increment * 3;
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
                        'type' => 'dropdown',
                        'holder' => 'h3',
                        'heading' => esc_html__('Category', '{project.destDir}'),
                        'param_name' => 'category',
                        'value' => $this->_getCategories(),
                        'std' => '',
                        'description' => esc_html__('Show a grid of posts from this category', '{project.destDir}'),
                        'admin_label' => false,
                        'weight' => 0,
                        'group' => esc_html__('Options', '{project.destDir}'),
                    ),  
                         
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
                        'heading' => esc_html__('Maximum number of posts', '{project.destDir}'),
                        'param_name' => 'max_posts_count',
                        'value' => $maxPostsCount,
                        'std' => 12,
                        'description' => esc_html__('Set the maximum number of posts to display', '{project.destDir}'),
                        'group' => esc_html__('Options', '{project.destDir}'),
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
        
        // Add the content
        $html .= '<div class="col-12 offset-0 col-sm-10 offset-sm-1">';

        // Valid category name
        if ('' != $category) {
            $categoryId = 0;
            foreach(get_categories() as $categoryData ) {
                // Base category
                if (0 == $categoryData->category_parent) {
                    // Found by name
                    if ($categoryData->name == $category) { 
                        $categoryId = $categoryData->cat_ID;
                        break;
                    }
                }
            } 
            
            // Valid category ID
            if (0 != $categoryId) {
                // Get al the posts
                $postsList = get_posts(
                    array(
                        'category'       => $categoryId, 
                        'post_type'      => 'post',
                        'posts_per_page' => $max_posts_count,
                    )
                );
                
                // Prepare the entries
                $htmlRelEntries = '';
                
                // Prepare all the unique categories
                $tagsUnique = array();
                
                // Go through the posts
                foreach ($postsList as /*@var WP_Post $post*/ $post) {
                    // Prepare the tags
                    $tags = array();
                    
                    // Go through the defined tags
                    foreach (wp_get_post_tags($post->ID) as $postTag) {
                        $tags[$postTag->slug] = $postTag->name;
                        $tagsUnique[$postTag->slug] = $postTag->name;
                    }
                    
                    // No tags found
                    if (!count($tags)) {
                        $tags['none'] = esc_html__('None', '{project.destDir}');
                    }
                    
                    // Prepare the title and subtitle
                    $postTitle = htmlspecialchars($post->post_title);
                    $postSubtitle = htmlspecialchars(implode(', ', $tags));
                    $postFeatureImage = get_the_post_thumbnail_url($post->ID, 'large');
                    
                    // Append the item
                    $htmlRelEntries .= '<div data-rel="' . htmlspecialchars(implode(' ', array_keys($tags))) . '" class="col-6 col-sm-4">' . 
                        '<div class="preview' . (strlen($postFeatureImage) ? '' : ' no-feature') . '"' . (strlen($postFeatureImage) ? ' style="background-image:url(' . $postFeatureImage . ');"' : '') . '>' . 
                            '<div class="cover"></div>' . 
                            '<a href="' . get_permalink($post->ID) . '" class="full"><i class="sc-t"></i><i class="sc-b"></i><span class="icon-eye-1"></span></a>' .
                        '</div>' . 
                        (strlen($postTitle) ? ('<h3>' . $postTitle . '</h3>') : '')  . 
                        (strlen($postSubtitle) ? ('<h4>' . $postSubtitle . '</h4>') : '') .
                    '</div>';
                }
                
                // Add the list
                $html .= '<div class="col-12">
                    <h1>'. $title . ' <span>' . $subtitle . '</span></h1>
                    <ul class="sort">
                        <li data-rel="*" class="active">All</li>';
                
                // Add the other categories
                foreach ($tagsUnique as $listRelSlug => $listRelName) {
                    $html .= '<li data-rel="' . htmlspecialchars($listRelSlug) . '">' . htmlspecialchars($listRelName) . '</li>';
                }
                
                // Close the list
                $html .= '
                    </ul>
                </div><div class="isotope row">' . $htmlRelEntries . '</div>';
            }
        } else {
            // No category selected
            $html .= '<h1>' . esc_html__({addon.title}, '{project.destDir}') . '<span>' . esc_html__({addon.warnNoCategory}, '{project.destDir}') . '</span></h1>';
        }
        
        // Close the div
        $html .= '</div></div>';
         
        // All done
        return $html;
    }
     
}
 
// Element Class Init
new {project.prefix}_{Plugin.getNameVar}();

/*EOF*/