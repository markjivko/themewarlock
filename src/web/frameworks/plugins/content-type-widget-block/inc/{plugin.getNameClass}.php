<?php
/**
 * {Plugin.getNameClass}
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Widget Blocks wrapper
 */
if (!class_exists('st_widget_block_wrapper')) {
    class st_widget_block_wrapper extends WP_Widget {

        /**
         * Widget field keys
         */
        const FIELD_CONTENT_TYPE_WIDGET_ID = 'contentTypeWidgetBlockId';

        /**
         * Widget fields
         */
        protected $_widgetFields = array();

        // Constructor
        public function __construct() {
            parent::__construct(
                // Base ID
                'st_widget_block_wrapper', 

                // Widget name
                '{config.authorName}: ' . esc_html__('Widget Block', '{project.destDir}'), 

                // Widget options
                array(
                    'description' => esc_html__('A Widget Block is a custom post type you can edit with your favorite page builder and insert into widget areas.', '{project.destDir}'), 
                )
            );
        }

        /**
         * Get the current widget's fields
         */ 
        protected function _getWidgetFields() {
            // Widget fields not defined
            if (!count($this->_widgetFields)) {
                // Prepare the widget options
                $widgetOptions = array(
                    0 => '- ' . esc_html__('Select a Widget Block', '{project.destDir}') . ' -',
                );

                // Get the loop
                $loop = new WP_Query(array(
                    'post_type' => 'st_widget_block'
                ));

                // Go through the posts
                while ($loop->have_posts()) {
                    // Prepare the data
                    $loop->the_post();

                    // Get the post data
                    $post = get_post();
                    if (!empty($post)) {
                        // Store the information
                        $widgetOptions[$post->ID] = $post->post_title;
                    }
                }
                
                // Reset the data
                wp_reset_postdata();
                
                // No block defined
                if (1 === count($widgetOptions)) {
                    $widgetOptions[0] = esc_html__('No Widget Block defined', '{project.destDir}');
                }

                // Store the translated values (where applicable)
                $this->_widgetFields = array(
                    self::FIELD_CONTENT_TYPE_WIDGET_ID => array(
                        '{config.authorName}: ' . esc_html__('Widget Block', '{project.destDir}'), 
                        0,
                        $widgetOptions,
                    ),
                );
            }

            // Get from cache
            return $this->_widgetFields;
        }

        // Widget Frontend
        public function widget($args, $instance) {
            // Display the result
            if (isset($instance[self::FIELD_CONTENT_TYPE_WIDGET_ID]) && strlen($instance[self::FIELD_CONTENT_TYPE_WIDGET_ID])) {
                // Get the post ID
                $postId = intval($instance[self::FIELD_CONTENT_TYPE_WIDGET_ID]);

                // Valid post ID provided
                if ($postId > 0) {
                    echo apply_filters('the_content', get_post_field('post_content', $postId));
                }
            }
        }

        // Widget Backend 
        public function form($instance) {
            // Go throught the widget fields
            foreach ($this->_getWidgetFields() as $fieldName => $fieldInfo) {
                // Get the field data - both values are translated
                list($fieldDescription, $fieldDefault) = $fieldInfo;

                // Get the field value
                $fieldValue = isset($instance[$fieldName]) ? $instance[$fieldName] : $fieldDefault;

                // List of options
                if (isset($fieldInfo[2]) && is_array($fieldInfo[2])) {
                    // Add the field form element
                    echo '<p>' .
                        '<label for="' . $this->get_field_id($fieldName) . '">' . $fieldDescription . '</label>' .
                        '<select class="widefat" id="' . $this->get_field_id($fieldName) . '" name="' . $this->get_field_name($fieldName) . '">';

                    // Set the options
                    foreach ($fieldInfo[2] as $fieldOptionsKey => $fieldOptionsValue) {
                        echo '<option ' . ($fieldValue == $fieldOptionsKey ? 'selected="selected"' : '') . ' value="' . esc_attr($fieldOptionsKey) . '">' . esc_attr($fieldOptionsValue) . '</option>';
                    }

                    // Close the form field
                    echo '</select></p>';
                } else {
                    // Add the field form element
                    echo '<p>' .
                        '<label for="' . $this->get_field_id($fieldName) . '">' . $fieldDescription . '</label>' .
                        '<input class="widefat" id="' . $this->get_field_id($fieldName) . '" name="' . $this->get_field_name($fieldName) . '" type="text" value="' . esc_attr($fieldValue) . '" />' .
                    '</p>';
                }
            }

        }

        // Update widget replacing old instances with new
        public function update($new_instance, $old_instance) {
            // Prepare the result
            $instance = array();

            // Go throught the widget fields
            foreach (array_keys($this->_getWidgetFields()) as $fieldName) {
                $instance[$fieldName] = (!empty($new_instance[$fieldName])) ? strip_tags($new_instance[$fieldName]) : '';
            }

            // All done
            return $instance;
        }

    } // End of st_widget_block_wrapper WP_Widget class
}

/**
 * Initialize the content type
 */
if (!function_exists('st_{Plugin.getNameVar}_init')) {
    function st_{Plugin.getNameVar}_init() {
        // Prepare the supported features
        $supports = array(
            'title', // post title
            'editor', // post content
            'author', // post author
        );
        
        // Prepare the labels
        $labels = array(
            'name'           => _x('Widget Blocks', 'plural', '{project.destDir}'),
            'singular_name'  => _x('Widget Block', 'singular', '{project.destDir}'),
            'menu_name'      => _x('Widget Blocks', 'admin menu', '{project.destDir}'),
            'name_admin_bar' => _x('Widget Blocks', 'admin bar', '{project.destDir}'),
            'add_new'        => _x('Add New', 'add new', '{project.destDir}'),
            'add_new_item'   => esc_html__('Add New Widget Block', '{project.destDir}'),
            'new_item'       => esc_html__('New Widget Blocks', '{project.destDir}'),
            'edit_item'      => esc_html__('Edit Widget Blocks', '{project.destDir}'),
            'view_item'      => esc_html__('View Widget Blocks', '{project.destDir}'),
            'all_items'      => esc_html__('All Widget Blocks', '{project.destDir}'),
            'search_items'   => esc_html__('Search Widget Blocks', '{project.destDir}'),
            'not_found'      => esc_html__('No Widget Blocks found.', '{project.destDir}'),
        );
        
        // Register the widgets content type
        register_post_type('st_widget_block', array(
            'menu_icon'           => 'dashicons-layout',
            'supports'            => $supports,
            'labels'              => $labels,
            'publicly_queryable'  => true,
            'public'              => true,
            'exclude_from_search' => true,
            'show_ui'             => true,
            'map_meta_cap'        => true,
            'query_var'           => false,
            'has_archive'         => false,
            'hierarchical'        => false,
        ));
    }
    
    add_action('init', 'st_{Plugin.getNameVar}_init', 1);
}

/**
 * Help section
 */
if (!function_exists('st_{Plugin.getNameVar}_help_section')) {
    function st_{Plugin.getNameVar}_help_section() {
        // Prepare the screen object
        $screen = get_current_screen();

        // Add the help tab to the Widgets area and the "Widget Blocks" tab
        if ('edit' === $screen->base && 'st_widget_block' === $screen->post_type) {
            // Main help tab
            $screen->add_help_tab(array(
                'id'      => 'st_{Plugin.getNameVar}_help',
                'title'   => esc_html__('Widget Blocks', '{project.destDir}'),
                'content' => '<p>' . 
                    sprintf(
                        esc_html__('%s are a custom post type. You can edit them as you would normally edit a post or a page - using your favorite page builder.', '{project.destDir}'),
                        '<b>' . esc_html__('Widget Blocks', '{project.destDir}') . '</b>'
                    ) . 
                    '<br/>' . 
                    sprintf(
                        esc_html__('You can then use any "widget block" in a widget area with the "%s" widget.', '{project.destDir}'),
                        '<b>{config.authorName}: ' . esc_html__('Widget Block', '{project.destDir}') . '</b>'
                    ). 
                    '</p>',
            ));
            
            // WPBakery Page Builder integration
            $screen->add_help_tab(array(
                'id'      => 'st_{Plugin.getNameVar}_help_vc',
                'title'   => esc_html__('WPBakery Page Builder integration', '{project.destDir}'),
                'content' => 
                    '<p>' . 
                        sprintf(
                            esc_html__('In order to use "%s" to edit your custom widget blocks follow these steps:', '{project.destDir}'),
                            '<b>WPBakery Page Builder</b>'
                        ) . 
                    '</p>' . 
                    '<p>' . 
                        '<ol>' . 
                            '<li>' . 
                                sprintf(
                                    esc_html__('Visit %s', '{project.destDir}'),
                                    '<code>WPBakery Page Builder &gt; ' . esc_html__('Role Manager', '{project.destDir}') . '</code>'
                                ) . 
                            '</li>' . 
                            '<li>' . 
                                sprintf(
                                    esc_html__('Click on the %s drop-down', '{project.destDir}'),
                                    '<code>' . esc_html__('Post Types', '{project.destDir}') . '</code>'
                                ) . 
                            '</li>' . 
                            '<li>' . 
                                sprintf(
                                    esc_html__('Select %s', '{project.destDir}'),
                                    '<code>' . esc_html__('Custom', '{project.destDir}') . '</code>'
                                ) . 
                            '</li>' .
                             '<li>' . 
                                sprintf(
                                    esc_html__('Enable %s', '{project.destDir}'),
                                    '<code>st_widget_block</code>'
                                ) . 
                            '</li>' .
                        '</ol>' .
                    '</p>',
            ));
        }
    }
    
    add_action('load-edit.php', 'st_{Plugin.getNameVar}_help_section');
}

/**
 * Flush rewrite rules if the flag is set and remove it
 */
if (!function_exists('st_{Plugin.getNameVar}_check')) {
    function st_{Plugin.getNameVar}_check() {
        if (get_option('st_{Plugin.getNameVar}_reset_flush_rules_flag')) {
            flush_rewrite_rules();
            delete_option('st_{Plugin.getNameVar}_reset_flush_rules_flag');
        }
    }

    add_action('init', 'st_{Plugin.getNameVar}_check', 2);
}

/**
 * Reset the flush rules flag on setup
 */
if (!function_exists('st_content_type_widget_block_activated')) {
    function st_content_type_widget_block_activated() {
        if (!get_option('st_{Plugin.getNameVar}_reset_flush_rules_flag')) {
            add_option('st_{Plugin.getNameVar}_reset_flush_rules_flag', true);
        }
    }
}

/**
 * Custom page template
 */
if (!function_exists('st_{Plugin.getNameVar}_page_template')) {
    function st_{Plugin.getNameVar}_page_template($page_template) {
        global $post, $wp_query;

        // "Widget Block" template
        if (null !== $post && property_exists($post, 'post_type') && 'st_widget_block' === $post->post_type) {
            do {
                // Do not show the Widget Blocks pages publicly
                if(!is_user_logged_in()) {
                    // Set the page to 404
                    $wp_query->set_404();
                    
                    // Set the headers
                    status_header(404);
                    nocache_headers();

                    // Show 404 template
                    $page_template = get_404_template();

                    // Stop here
                    break;
                } 
                
                // Add the scripts
                add_action('wp_enqueue_scripts', function(){
                    wp_enqueue_style(
                        '{project.destDir}-{Plugin.getSlug}-style', 
                        plugins_url() . '/{Call.core.getVcBundleName}/{Plugin.getSlug}/css/style.css', 
                        array(), 
                        {plugin.getVersion}
                    );
                });

                // Custom page template
                $page_template = dirname(dirname( __FILE__ )) . '/templates/single-st_widget_block.php';
            
            } while(false);
        }

        return $page_template;
    }
    
    add_filter('template_include', 'st_{Plugin.getNameVar}_page_template');
}

/**
 * Initialize the Widget Blocks Wrapper
 */
if (!function_exists('st_{Plugin.getNameVar}_widgets_init')) {
    function st_{Plugin.getNameVar}_widgets_init() {
        register_widget('st_widget_block_wrapper');
    }
    
    add_action('widgets_init', 'st_{Plugin.getNameVar}_widgets_init');
}

/*EOF*/