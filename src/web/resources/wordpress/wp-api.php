<?php

/**
 * Theme Warlock-WordPress API - This file should not be used directly
 * It is meant to be injected into the WordPress website, executed via a POST request and deleted immediately
 *
 * @package WordPress
 * @subpackage Administration
 */

/**
 * No cookie required for our admin
 * 
 * @return boolean
 */
function wp_validate_auth_cookie() {
    return true;
}

class Tw_Wp_SnapshotImporter {
    
    /**
     * Import a specific customer-facing snapshot
     * 
     * @param int $snapshotId Snapshot ID
     * @return boolean
     */
    public static function run($snapshotId) {
        // Prepare the class name
        $snapshotManager = 'St_SnapshotManager';
        
        // Class not found
        if (!class_exists($snapshotManager)) {
            throw new Exception('Snapshot manager not installed!');
        }
        
        // Initialize the filesystem
        WP_Filesystem();
        
        // Install
        $snapshotManager::getInstance()->getById($snapshotId)->install();

        // All went well
        return true;
    }
}

/**
 * Snapshot Exporter
 */
class Tw_Wp_SnapshotExporter {

    const KEY_FILES               = 'files';
    const KEY_CUSTOMIZER          = 'customizer';
    const KEY_CUSTOMIZER_EXTENDED = 'customizer-extended';
    const KEY_WIDGETS             = 'widgets';
    const KEY_REV_SLIDER          = 'rev-slider';
    const KEY_REV_SLIDER_TABLES   = 'rev-slider-tables';
    const KEY_CONTENT             = 'content';
    const KEY_CONTENT_POSTS       = 'content-posts';
    const KEY_CONTENT_TERMS       = 'content-terms';
    
    // Term keys
    const TERM_ID          = 'id';
    const TERM_SLUG        = 'slug';
    const TERM_TAXONOMY    = 'taxonomy';
    const TERM_TAXONOMY_ID = 'taxonomy_id';
    const TERM_PARENT      = 'parent';
    const TERM_NAME        = 'name';
    const TERM_GROUP       = 'group';
    const TERM_COUNT       = 'count';
    const TERM_DESCRIPTION = 'description';
    
    // Post keys
    const POST_ID             = 'ID';
    const POST_TITLE          = 'post_title';
    const POST_CONTENT        = 'post_content';
    const POST_EXCERPT        = 'post_excerpt';
    const POST_DATE           = 'post_date';
    const POST_DATE_GMT       = 'post_date_gmt';
    const POST_MODIFIED       = 'post_modified';
    const POST_MODIFIED_GMT   = 'post_modified_gmt';
    const POST_STATUS         = 'post_status';
    const POST_NAME           = 'post_name';
    const POST_TYPE           = 'post_type';
    const POST_MIME_TYPE      = 'post_mime_type';
    const POST_PASSWORD       = 'post_password';
    const POST_MENU_ORDER     = 'menu_order';
    const POST_PARENT         = 'post_parent';
    const POST_COMMENT_STATUS = 'comment_status';
    const POST_COMMENT_COUNT  = 'comment_count';
    const POST_PING_STATUS    = 'ping_status';
    const POST_GUID           = 'guid';
    
    // Comments keys
    const COMMENT_ID           = 'comment_ID';
    const COMMENT_POST_ID      = 'comment_post_ID';
    const COMMENT_AUTHOR       = 'comment_author';
    const COMMENT_AUTHOR_EMAIL = 'comment_author_email';
    const COMMENT_AUTHOR_URL   = 'comment_author_url';
    const COMMENT_AUTHOR_IP    = 'comment_author_IP';
    const COMMENT_DATE         = 'comment_date';
    const COMMENT_DATE_GMT     = 'comment_date_gmt';
    const COMMENT_CONTENT      = 'comment_content';
    const COMMENT_APPROVED     = 'comment_approved';
    const COMMENT_TYPE         = 'comment_type';
    const COMMENT_PARENT       = 'comment_parent';
    const COMMENT_USER_ID      = 'user_id';

    // Extra keys
    const EXTRA_META       = 'meta';
    const EXTRA_IS_STICKY  = 'is_sticky';
    const EXTRA_TAXONOMIES = 'taxonomies';
    const EXTRA_COMMENTS   = 'comments';
    
    /**
     * An array of core options that shouldn't be imported.
     *
     * @since 0.3
     * @access private
     * @var array $core_options
     */
    static private $core_options = array(
        'blogname',
        'blogdescription',
        'site_icon',
    );
    
    /**
     * Store the exported data
     * 
     * @var array
     */
    protected static $_data = array();
    
    /**
     * Store the placeholders
     * 
     * @var array
     */
    protected static $_placeholders = array();

    /**
     * Run all the export tools
     * 
     * @return array
     */
    public static function run() {
        // Get the upload directory information
        $uploadDir = wp_upload_dir(null, false);
        
        // Initialize the placeholders
        $placeholders = array(
            '{{__UPLOAD_URL__}}' => $uploadDir['baseurl'],
            '{{__SITE_URL__}}'   => get_site_url(),
        );

        // Convert the keys to Regular Expressions
        foreach ($placeholders as $placeholderKey => $placeholderValue) {
            self::$_placeholders[$placeholderKey] = '%' . preg_quote($placeholderValue) . '%i';
        }
        
        // Initialize the files list
        self::$_data[self::KEY_FILES] = array();

        // Customizer
        self::exportCustomizer();
        
        // Widgets, Sidebars
        self::exportWidgets();

        // Slider
        self::exportRevolutionSlider();
        
        // Posts (and comments), Terms
        self::exportContent();

        // All done
        return self::$_data;
    }

    /**
     * Multi-option helper for deep placeholder replacement
     * 
     * @param string $string Multi-option string to perform replacements on
     * @return string
     */
    protected static function _replacePlaceholderDeepMO($string, $urlEncoded = true) {
        // Prepare the options
        $options = array();

        // Go through each detail
        foreach(explode('|', $string) as $option) {
            // Not a key-value pair
            if (false === strpos($option, ':')) {
                $options[] = '';
                continue;
            }

            // Get the key and value
            list($optionKey, $optionValue) = explode(':', $option);

            // Parse the value
            $optionValue = self::_replacePlaceholders($urlEncoded ? rawurldecode($optionValue) : $optionValue);

            // Store in the final array
            $options[] = $optionKey . ':' . ($urlEncoded ? rawurlencode($optionValue) : $optionValue);
        }

        // Store the value
        return implode('|', $options);
    }
    
    /**
     * Replace placeholders in a post content, accounting for WordPress shortcodes
     * 
     * @param string $contents Post content
     * @return string
     */
    protected static function _replacePlaceholdersDeep($contents) {
        $result = preg_replace_callback(
            '%\[(\w+)\s*([^\]]*?)\]%ims', 
            function($item) {
                // Get the shortcode details
                $shortCodeType = $item[1];
                $shortCodeContent = $item[2];
                
                // Replace the content details
                $shortCodeContent = preg_replace_callback(
                    '%(\s*)(\w+)\s*=\s*"([^"]*?)"(\s*)%ims', 
                    function($item) {
                        // Store the spaces
                        $spaceBefore = $item[1];
                        $spaceAfter = $item[4];
                        
                        // Get the attribute name
                        $attrName = $item[2];
                        
                        // Is this a multi-option?
                        if (preg_match('%url$%', $attrName) && preg_match('%^\w+\:.*?\|%', $item[3])) {
                            // Plain text
                            $attrValueEncoded = false;
                            
                            // Store the new value
                            $attrValue = self::_replacePlaceholderDeepMO($item[3]);
                        } else {
                            // Get the attribute value
                            $attrValue = rawurldecode($item[3]);

                            // Store whether or not this was encoded
                            $attrValueEncoded = ($attrValue !== $item[3]);
                        
                            // Attempt JSON decode
                            do {
                                if (!is_numeric($attrValue) && 'null' !== $attrValue) {
                                    // Attempt decoding
                                    $attrValueArray = @json_decode($attrValue, true);

                                    // Valid array
                                    if (is_array($attrValueArray)) {

                                        // Go through the array
                                        foreach ($attrValueArray as $attrValueArrayKey => $attrValueArrayValues) {

                                            // Valid array
                                            if (is_array($attrValueArrayValues)) {
                                                // Go through the array
                                                foreach ($attrValueArrayValues as $key => $originalValue) {
                                                    // Is this a multi-option?
                                                    if (preg_match('%url$%', $key) && preg_match('%^\w+\:.*?\|%', $originalValue)) {
                                                        // Store the new value
                                                        $value = self::_replacePlaceholderDeepMO($originalValue);
                                                        
                                                        // Plain text
                                                        $valueEncoded = false;
                                                    } else {
                                                        // Get the decoded value
                                                        $value = rawurldecode($originalValue);
                                                        
                                                        // Store the encoded flag
                                                        $valueEncoded = ($value !== $originalValue);

                                                        do {
                                                            // Final nested layer
                                                            if (!is_numeric($value) && 'null' !== $value) {
                                                                // Get the final layer
                                                                $finalLayerArray = @json_decode($value, true);

                                                                // Valid definitions
                                                                if (is_array($finalLayerArray)) {
                                                                    // Go through the array
                                                                    foreach ($finalLayerArray as $finalLayerKey => $finalLayerValues) {
                                                                        if (is_array($finalLayerValues)) {
                                                                            foreach ($finalLayerValues as $fKey => $fValue) {
                                                                                // Is this a multi-option?
                                                                                if (preg_match('%url$%', $fKey) && preg_match('%^\w+\:.*?\|%', $fValue)) {
                                                                                    // Plain text
                                                                                    $valueEncoded = false;

                                                                                    // Store the new value
                                                                                    $finalLayerArray[$finalLayerKey][$fKey] = self::_replacePlaceholderDeepMO($fValue, false);
                                                                                } else {
                                                                                    // Re-encode the final value
                                                                                    $finalLayerArray[$finalLayerKey][$fKey] = self::_replacePlaceholders($fValue);
                                                                                }
                                                                            }
                                                                        }
                                                                    }

                                                                    // Restore the value
                                                                    $value = json_encode($finalLayerArray, JSON_UNESCAPED_SLASHES);
                                                                    break;
                                                                }
                                                            } 

                                                            // Find placeholders
                                                            $value = self::_replacePlaceholders($value);

                                                        } while (false);
                                                    }
                                                    // Re-encode the value
                                                    $attrValueArray[$attrValueArrayKey][$key] = $valueEncoded ? rawurlencode($value) : $value;
                                                }
                                            }
                                        }

                                        // Store the array back
                                        $attrValue = json_encode($attrValueArray, JSON_UNESCAPED_SLASHES);
                                        break;
                                    }
                                }

                                // Find placeholders
                                $attrValue = self::_replacePlaceholders($attrValue);

                            } while (false);
                        }
                        
                        // Re-encode the value
                        if ($attrValueEncoded) {
                            $attrValue = rawurlencode($attrValue);
                        }
                        
                        // All done
                        return "{$spaceBefore}{$attrName}=\"{$attrValue}\"{$spaceAfter}";
                    }, 
                    $shortCodeContent
                );
                
                // Prepare the separator
                $separator = '';
                if (strlen($shortCodeContent)) {
                    $separator = ' ';
                }
                
                // All done
                return "[{$shortCodeType}{$separator}{$shortCodeContent}]";
            }, $contents
        );
            
        // Final pass
        return self::_replacePlaceholders($result);
    } 
    
    /**
     * Replace the placeholders (domain, uploads path etc.) and mark needed files
     * 
     * @param string $string
     * @return string
     */
    protected static function _replacePlaceholders($string) {
        if (!is_string($string) || !count(self::$_placeholders)) {
            return $string;
        }
        
        // Mark each file needed
        if (preg_match_all('%\bwp\-content\/uploads\/(.*?\.\w+)\b%ims', $string, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                self::$_data[self::KEY_FILES][] = $match[1];
            }
        }
        
        // Replace the values
        return preg_replace(
            array_values(self::$_placeholders), array_keys(self::$_placeholders), $string
        );
    }

    /**
     * Get the term meta
     * 
     * @param WP_Term $term WordPress Term object
     * @return array Associative array
     */
    protected static function _getTermMeta($term) {
        global $wpdb;

        // Prepare the result
        $result = array();

        // Go through the results
        foreach ($wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->termmeta WHERE term_id = %d", $term->term_id)) as $meta) {
            $result[$meta->meta_key] = self::_replacePlaceholders($meta->meta_value);
        }

        // All done
        return $result;
    }
    
    /**
     * Export the website content<ul>
     * <li>Posts (and comments)</li>
     * <li>Tags</li>
     * <li>Navigation Menus</li>
     * <li>Categories</li>
     * <li>Terms</li>
     * </ul>
     */
    public static function exportContent() {
        global $wpdb, $post, $wp_query;

        // Initialize the content
        self::$_data[self::KEY_CONTENT] = array();

        // Prepare the terms
        self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_TERMS] = array();
        
        // Get the raw data
        $postTerms = (array) get_terms(get_taxonomies(array('_builtin' => true)), array('get' => 'all'));
        
        // Save the terms
        while ($postTerm = array_shift($postTerms)) {
            if ($postTerm->parent == 0 || isset(self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_TERMS][$postTerm->parent])) {
                self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_TERMS][$postTerm->term_id] = array(
                    self::TERM_ID          => $postTerm->term_id,
                    self::TERM_TAXONOMY_ID => $postTerm->term_taxonomy_id,
                    self::TERM_TAXONOMY    => $postTerm->taxonomy,
                    self::TERM_SLUG        => $postTerm->slug,
                    self::TERM_PARENT      => $postTerm->parent,
                    self::TERM_NAME        => $postTerm->name,
                    self::TERM_GROUP       => $postTerm->term_group,
                    self::TERM_COUNT       => $postTerm->count,
                    self::TERM_DESCRIPTION => base64_encode(self::_replacePlaceholders($postTerm->description)),
                    self::EXTRA_META       => self::_getTermMeta($postTerm),
                );
            } else {
                $postTerms[] = $postTerm;
            }
        }
        unset($postTerms);
        
        // Get the post types
        $postTypes = array_filter(get_post_types(array(
            '_builtin' => true
        )), function($item) {
            // Ignore some data sets
            return !in_array($item, array(
                // Never store our changesets
                'customize_changeset', 
                
                // Never store revisions
                'revision', 
                
                // Never overwrite user's custom CSS!
                'custom-css',
            ));
        });
        
        // Include custom widget blocks
        $postTypes[] = 'st_widget_block';

        // Prepare the IN clause
        $inClause = implode(',', array_fill(0, count($postTypes), '%s'));

        // Prepare the where statement
        $where = $wpdb->prepare("{$wpdb->posts}.post_type IN ({$inClause})", $postTypes);

        // Never include drafts
        $where .= " AND {$wpdb->posts}.post_status NOT IN ('draft', 'auto-draft')";

        // Grab a snapshot of post IDs, just in case it changes during the export.
        $post_ids = $wpdb->get_col("SELECT ID FROM {$wpdb->posts} WHERE $where");

        // Fake being in the loop.
        $wp_query->in_the_loop = true;

        // Prepare the tags
        self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS] = array();
        
        // Fetch 20 posts at a time rather than loading the entire table into memory.
        while ($next_posts = array_splice($post_ids, 0, 20)) {
            $where = 'WHERE ID IN (' . join(',', $next_posts) . ')';
            $posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} $where");

            // Begin Loop.
            foreach ($posts as $post) {
                setup_postdata($post);
                self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID] = array(
                    self::POST_ID             => intval($post->ID),
                    self::POST_TITLE          => $post->post_title,
                    self::POST_CONTENT        => base64_encode(self::_replacePlaceholdersDeep($post->post_content)),
                    self::POST_EXCERPT        => base64_encode(self::_replacePlaceholders($post->post_excerpt)),
                    self::POST_STATUS         => $post->post_status,
                    self::POST_NAME           => $post->post_name,
                    self::POST_TYPE           => $post->post_type,
                    self::POST_MIME_TYPE      => $post->post_mime_type,
                    self::POST_PASSWORD       => $post->post_password,
                    self::POST_MENU_ORDER     => intval($post->menu_order),
                    self::POST_PARENT         => intval($post->post_parent),
                    self::POST_COMMENT_STATUS => $post->comment_status,
                    self::POST_COMMENT_COUNT  => intval($post->comment_count),
                    self::POST_PING_STATUS    => $post->ping_status,
                    self::POST_GUID           => self::_replacePlaceholders(get_the_guid()),
                );
                
                // Set the sticky flag
                self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_IS_STICKY] = (is_sticky($post->ID) ? 1 : 0);
                
                // Prepare the taxonomies
                self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_TAXONOMIES] = array();

                // Parse the data
                $taxonomies = get_object_taxonomies($post->post_type);
                if (!empty($taxonomies)) {
                    // Go through the data
                    foreach ((array) wp_get_object_terms($post->ID, $taxonomies) as $term) {
                        if (!isset(self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_TAXONOMIES][$term->taxonomy])) {
                            self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_TAXONOMIES][$term->taxonomy] = array();
                        }
                        
                        // Append the term ID
                        self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_TAXONOMIES][$term->taxonomy][] = $term->term_id;
                    }
                }
                
                // Prepare the post meta
                self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_META] = array();
                
                // Parse the data
                foreach ($wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->postmeta} WHERE post_id = %d", $post->ID)) as $postMeta) {
                    if ('_edit_lock' === $postMeta->meta_key) {
                        continue;
                    }
                    
                    // Replace placeholders flag
                    $replacePlaceholders = true;
                    
                    // Metadata fix
                    if ('_wp_attachment_metadata' === $postMeta->meta_key) {
                        $replacePlaceholders = false;
                        
                        // Deserialize
                        $metaValue = unserialize($postMeta->meta_value);
                        
                        // Remove the sizes array
                        unset($metaValue['sizes']);
                        
                        // Re-serialize
                        $postMeta->meta_value = serialize($metaValue);
                    }
                    
                    // Attached file
                    if ('_wp_attached_file' === $postMeta->meta_key) {
                        self::$_data[self::KEY_FILES][] = $postMeta->meta_value;
                    }
                    
                    // Save the pair
                    self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_META][$postMeta->meta_key] = $replacePlaceholders ? self::_replacePlaceholders($postMeta->meta_value) : $postMeta->meta_value;
                }
                
                // Prepare the post comments
                self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_COMMENTS] = array();
                
                // Get the comments for this post
                $comments = $wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->comments} WHERE comment_post_ID = %d AND comment_approved <> 'spam'", $post->ID));
                
                // Convert them to a comment object
                foreach (array_map('get_comment', $comments) as $comment) {
                    // Get the comment ID
                    $commentId = intval($comment->comment_ID);
                    
                    // Store the data
                    self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_COMMENTS][$commentId] = array(
                        self::COMMENT_ID           => $commentId,
                        self::COMMENT_POST_ID      => $comment->comment_post_ID,
                        self::COMMENT_AUTHOR       => $comment->comment_author,
                        self::COMMENT_AUTHOR_EMAIL => $comment->comment_author_email,
                        self::COMMENT_AUTHOR_URL   => self::_replacePlaceholders($comment->comment_author_url),
                        self::COMMENT_AUTHOR_IP    => $comment->comment_author_IP,
                        self::COMMENT_DATE         => $comment->comment_date,
                        self::COMMENT_DATE_GMT     => $comment->comment_date_gmt,
                        self::COMMENT_CONTENT      => base64_encode(self::_replacePlaceholders($comment->comment_content)),
                        self::COMMENT_APPROVED     => $comment->comment_approved,
                        self::COMMENT_TYPE         => $comment->comment_type,
                        self::COMMENT_PARENT       => intval($comment->comment_parent),
                        self::COMMENT_USER_ID      => intval($comment->user_id),
                    );
                    
                    // Prepare the meta
                    self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_COMMENTS][$commentId][self::EXTRA_META] = array();
                    
                    // Go through the comment meta
                    foreach ($wpdb->get_results($wpdb->prepare("SELECT * FROM {$wpdb->commentmeta} WHERE comment_id = %d", $commentId)) as $commentMeta) {
                        self::$_data[self::KEY_CONTENT][self::KEY_CONTENT_POSTS][$post->ID][self::EXTRA_COMMENTS][$commentId][self::EXTRA_META][$commentMeta->meta_key] = self::_replacePlaceholders($commentMeta->meta_value);
                    }
                }
            }
        }

        // Unique values
        self::$_data[self::KEY_FILES] = array_values(array_unique(self::$_data[self::KEY_FILES]));
    }

    /**
    * An extended version of <b>array_walk_recursive</b> that goes through leafs and nodes
    * 
    * @see http://php.net/manual/en/function.array-walk-recursive.php
    * @param array    $input        Array
    * @param callable $userFunction Callable
    * @param mixed    $userData     (optional) User Data; default <b>null</b>
    */
    protected static function _walkRecursive(&$input, $userFunction, $userData = null) {
        // Invalid arguments
        if (!is_array($input) || !is_callable($userFunction)) {
            return;
        }

        // Go through the nodes
        foreach ($input as $key => $value) {
            // Call the user function and pass all the arguments 
            call_user_func_array($userFunction, array(&$value, $key, $userData));

            // Go through to the next level
            if (is_array($value)) {
                self::_walkRecursive($value, $userFunction, $userData);
            }

            // Store all user changes
            $input[$key] = $value;
        }
    }

    /**
     * Export the Slider Revolution settings
     */
    public static function exportRevolutionSlider() {
        global $wpdb;
        
        // Revolution slider not installed, skip the export
        if (!class_exists('RevSliderGlobals')) {
            return;
        }
        
        // Initialize the store
        self::$_data[self::KEY_REV_SLIDER] = array(
            self::KEY_REV_SLIDER_TABLES  => array(),
        );
        
        // Export each table
        $tablesList = array(
            RevSliderGlobals::TABLE_SLIDERS_NAME       => RevSliderGlobals::$table_sliders,
            RevSliderGlobals::TABLE_SLIDES_NAME        => RevSliderGlobals::$table_slides,
            RevSliderGlobals::TABLE_STATIC_SLIDES_NAME => RevSliderGlobals::$table_static_slides,
            RevSliderGlobals::TABLE_LAYER_ANIMS_NAME   => RevSliderGlobals::$table_layer_anims,
            RevSliderGlobals::TABLE_NAVIGATION_NAME    => RevSliderGlobals::$table_navigation,
        );
        
        // Go through the tables
        foreach ($tablesList as $tableKey => $tableName) {
            // Prepare the table
            self::$_data[self::KEY_REV_SLIDER][self::KEY_REV_SLIDER_TABLES][$tableKey] = array();
            
            // Get the entries
            $entries = $wpdb->get_results("SELECT * FROM {$tableName}", ARRAY_A);

            // Begin Loop.
            foreach ($entries as $rowData) {
                // Parse the entry
                foreach ($rowData as $colName => $colValue) {
                    if (in_array($colName, array('params', 'layers', 'settings'))) {
                        // Deserialize the value
                        $colValueDecoded = @json_decode($colValue, true);
                        
                        // Successful decoding
                        if (null !== $colValueDecoded) {
                            if (is_array($colValueDecoded)) {
                                self::_walkRecursive($colValueDecoded, function(&$item, $key) {
                                    if (is_string($item)) {
                                        $item = self::_replacePlaceholders($item);
                                    }
                                });
                            } elseif (is_string($colValueDecoded)) {
                                $colValueDecoded = self::_replacePlaceholders($colValueDecoded);
                            }
                            
                            // Re-serialize and save the value
                            $rowData[$colName] = json_encode($colValueDecoded);
                        }
                    }
                }
                
                // Store the element
                self::$_data[self::KEY_REV_SLIDER][self::KEY_REV_SLIDER_TABLES][$tableKey][$rowData['id']] = $rowData;
            }
        }
    }
    
    /**
     * Export the WordPress Widget and Sidebar settings
     */
    public static function exportWidgets() {
        global $wp_registered_widgets;
        
        // Initialize the store
        self::$_data[self::KEY_WIDGETS] = array();
        
        // Get the sidebars
        $sidebars = get_option('sidebars_widgets');
        
        // Ignore the inactive list
        $sidebars['wp_inactive_widgets'] = array();
        
        // Save the sidebars
        self::$_data[self::KEY_WIDGETS]['sidebars_widgets'] = $sidebars;
        
        // Store the used IDs
        $usedWidgetKeys = array();
        
        // Gather the widgets
        foreach ($sidebars as $sidebarName => $sidebarWidgets) {
            // Not a valid widgets list
            if (!is_array($sidebarWidgets)) {
                continue;
            }
            
            // Go through each widget
            foreach ($sidebarWidgets as $sidebarWidget) {
                // Widget not registered
                if (!isset($wp_registered_widgets[$sidebarWidget])) {
                    continue;
                }
                
                // Get the widget name
                $widgetName = $wp_registered_widgets[$sidebarWidget]['callback'][0]->option_name;
                
                // Get thw widget key
                $widgetKey = $wp_registered_widgets[$sidebarWidget]['params'][0]['number'];
                
                // Get the widget data
                $widgetOptions = get_option($widgetName);
                
                // Initialize the widget option
                if (!isset(self::$_data[self::KEY_WIDGETS][$widgetName])) {
                    self::$_data[self::KEY_WIDGETS][$widgetName] = $widgetOptions;
                }
                
                // Prepare the used widget IDs store
                if (!isset($usedWidgetKeys[$widgetName])) {
                    $usedWidgetKeys[$widgetName] = array();
                }
                $usedWidgetKeys[$widgetName][] = $widgetKey;
                    
                // Parse the Widget Data for the current key
                foreach (self::$_data[self::KEY_WIDGETS][$widgetName][$widgetKey] as $wdKey => $wdValue) {
                    // Replace the placeholders
                    $wdValue = self::_replacePlaceholders($wdValue);
                    
                    // Store the information
                    self::$_data[self::KEY_WIDGETS][$widgetName][$widgetKey][$wdKey] = $wdValue;
                }
            }
        }
        
        // Remove unused widget keys
        foreach (self::$_data[self::KEY_WIDGETS] as $widgetName => $widgetOptions) {
            foreach (array_keys($widgetOptions) as $widgetKey) {
                if (is_int($widgetKey) && !in_array($widgetKey, $usedWidgetKeys[$widgetName])) {
                    unset(self::$_data[self::KEY_WIDGETS][$widgetName][$widgetKey]);
                }
            }
        }
    }
    
    /**
     * Export the WordPress Customizer settings
     */
    public static function exportCustomizer() {
        global $wp_customize;

        // Retrieve all theme modifications
        $mods = get_theme_mods();

        // Prepare the result
        self::$_data[self::KEY_CUSTOMIZER] = is_array($mods) ? $mods : array();
        self::$_data[self::KEY_CUSTOMIZER_EXTENDED] = array();

        // Go through the mods
        foreach (self::$_data[self::KEY_CUSTOMIZER] as $key => $value) {
            if (in_array($key, array(0), true)) {
                unset(self::$_data[self::KEY_CUSTOMIZER][$key]);
                continue;
            }

            // Set the placeholders
            self::$_data[self::KEY_CUSTOMIZER][$key] = self::_replacePlaceholders($value);
        }

        // Get options from the Customizer API
        foreach ($wp_customize->settings() as $key => $setting) {
            if ('option' == $setting->type) {
                // Ignore the widget data
                if (preg_match('%(?:^widget_|sidebars_)%', $key)) {
                    continue;
                }

                // Don't save core options
                if (in_array($key, self::$core_options)) {
                    continue;
                }

                // Add the customizer key
                self::$_data[self::KEY_CUSTOMIZER_EXTENDED][$key] = $setting->value();
            }
        }
    }
}

class Tw_Wp_Api {

    /**
     * Initialize the WordPress Customizer settings for these methods
     * 
     * @var string[]
     */
    protected static $_wpCustomizerMethods = array(
        'snapshotImport',
        'snapshotExport',
    );

    /**
     *
     * @var type 
     */
    protected static $_instance;

    /**
     * WordPress Temporary API
     * 
     * @global type $current_user
     */
    protected function __construct() {
        // Access to the current user
        global $current_user, $pagenow, $_wp_submenu_nopriv;

        // Set the environment
        $current_user = json_decode(json_encode(array('ID' => 1)));
        $pagenow = 'admin.php';
        $_wp_submenu_nopriv = array();

        // WP_Customizer must be initialized
        if (in_array($_POST['method'], self::$_wpCustomizerMethods)) {
            $_REQUEST['wp_customize'] = 'on';
        }

        /** WordPress Administration Bootstrap */
        require_once(dirname(__FILE__) . '/admin.php');
    }

    /**
     * WordPress Temporary API
     * 
     * @return Tw_Wp_Api
     */
    public static function getInstance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Try to execute the requested method
     */
    protected function _executeMethod() {
        if (!isset($_POST)) {
            throw new Exception('No POST data provided');
        }
        if (!isset($_POST['method'])) {
            throw new Exception('No method provided');
        }

        // Get the method name
        $methodName = trim($_POST['method']);

        // Validate the method
        if (!method_exists($this, $methodName)) {
            throw new Exception('Method "' . $methodName . '" not implemented');
        }

        // Get the method arguments
        $methodArguments = isset($_POST['arguments']) ? (array) $_POST['arguments'] : array();

        // Prepare the result
        return call_user_func_array(array($this, $methodName), $methodArguments);
    }

    /**
     * Enable the theme
     * 
     * @param string $themeName Theme name
     * @return boolean
     * @throws Exception
     */
    public function themeEnable($themeName = null) {
        // Theme name not provided
        if (null == $themeName) {
            throw new Exception('Theme name is mandatory');
        }

        /* @var $theme WP_Theme */
        $theme = wp_get_theme($themeName);

        // All done
        if (!is_object($theme) || !$theme->exists()) {
            throw new Exception('Theme "' . $themeName . '" does not exist');
        }

        // Get the current theme
        $currentTheme = wp_get_theme();

        // Switch the theme
        if ($currentTheme->get_stylesheet() != $theme->get_stylesheet()) {
            switch_theme($theme->get_stylesheet());
        }
    }

    /**
     * Install a WordPress plugin
     * 
     * @param string $pluginName Plugin name
     * @return boolean
     * @throws Exception
     */
    public function pluginInstall($pluginName = null) {
        // Store the start time
        $startTime = microtime(true);
        
        // Get the plugin upgrader
        require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

        // Get the plugin activation class
        $pluginActivation = TGM_Plugin_Activation::get_instance();

        // Set to automatic
        $pluginActivation->config(array(
            'is_automatic' => true,
        ));

        // Invalid plugin
        if (!isset($pluginActivation->plugins[$pluginName])) {
            throw new Exception('Plugin "' . $pluginName . '" not found');
        }

        // Get the download path
        $pluginPath = null;
        if (!$pluginActivation->is_plugin_installed($pluginName)) {
            $pluginPath = $pluginActivation->get_download_url($pluginName);
        }

        // Prevent RevSlider's remote checks
        if ('revslider' == $pluginName) {
            // Prevent server requests
            $futureTime = time() + 86400;

            // Update the check ptions
            update_option('revslider_server_refresh', $futureTime);
            update_option('revslider-update-check', $futureTime);
            update_option('revslider-update-check-short', $futureTime);
            update_option('revslider-library-check', $futureTime);
            update_option('revslider-templates-check', $futureTime);
        }
        
        // Valid plugin archive path found
        if (null !== $pluginPath) {
            // Activate the plugins
            $skin = new Plugin_Installer_Skin(array('type' => 'upload'));

            // Create a new instance of Plugin_Upgrader.
            $upgrader = new Plugin_Upgrader($skin);

            // Install from source
            $upgrader->install($pluginPath);
            
            // Don't try to activate on upgrade of active plugin as WP will do this already.
            if (!is_plugin_active( $pluginName)) {
                foreach (glob(WP_PLUGIN_DIR . '/' . $pluginName . '/*.php' ) as $phpFile) {
                    // Get the plugin information
                    $pluginInfo = get_plugin_data($phpFile, false, false );
                    
                    // Data found
                    if (isset($pluginInfo['Name']) && strlen($pluginInfo['Name'])) {
                        // Activate the plugin
                        activate_plugin($phpFile);
                        break;
                    }
                }
            }
            {log.debug}array('path' => $pluginPath, 'time' => microtime(true) - $startTime){/log.debug}
        }
        
        // Remove Theme-Check archive to prevent false flags
        if ('theme-check' == $pluginName) {
            if (is_file($themeCheckArchivePath = get_template_directory() . '/plugins/theme-check.tar')) {
                @unlink($themeCheckArchivePath);
            }
        }

        // All went well
        return true;
    }

    /**
     * Export the current WordPress settings:<ul>
     * <li>Customizer</li>
     * <li>Widgets</li>
     * <li>Pages and Posts</li>
     * </ul>
     * 
     * @param int $snapshotId Snapshot ID
     * @return array
     */
    public function snapshotExport($snapshotId) {
        // Export the WordPress Customizer settings
        return Tw_Wp_SnapshotExporter::run();
    }
    
    /**
     * Import a customer-facing snapshot
     * 
     * @param int    $snapshotId Snapshot ID
     * @return boolean
     */
    public function snapshotImport($snapshotId) {
        // Export the WordPress Customizer settings
        return Tw_Wp_SnapshotImporter::run($snapshotId);
    }

    /**
     * Run the tool
     */
    public function run($local = false) {
        // Prepare the result
        $status = true;
        $result = null;

        // Start the buffer
        ob_start();
        try {
            $result = $this->_executeMethod();
        } catch (Exception $ex) {
            $result = $ex->getMessage();
            $status = false;
        }

        // Positive result
        if (null === $result) {
            $result = true;
        }

        // Get the output
        $content = ob_get_clean();

        // Prepare the data
        $data = array(
            'status' => $status,
            'result' => $result,
            'content' => $content,
        );

        // Local mode
        if ($local) {
            return $data;
        }

        // All done
        echo json_encode($data);
    }

}

// Execute the request
Tw_Wp_Api::getInstance()->run();
