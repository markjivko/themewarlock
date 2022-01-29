<?php
/**
 * Additional features to allow styling of the templates
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

/**
 * Adds custom classes to the array of body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function {project.prefix}_body_classes($classes) {
    // Adds a class of group-blog to blogs with more than 1 published author.
    if (is_multi_author()) {
        $classes[] = 'group-blog';
    }

    // Adds a class of hfeed to non-singular pages.
    if (!is_singular()) {
        $classes[] = 'hfeed';
    }

    return $classes;
}
add_filter('body_class', '{project.prefix}_body_classes');

/**
 * Add Bootstrap classes to search form elements
 */
function {project.prefix}_widget_search_form($html) {
    return preg_replace(
        array(
            '%\bsearch\-submit\b%',
            '%\bsearch\-field\b%',
        ), 
        array(
            '$0 btn',
            '$0 form-control',
        ), 
        $html
    );
}
add_filter('get_search_form', '{project.prefix}_widget_search_form');

/**
 * Twitter Bootstrap - VisualComposer compatibility
 * 
 * @param string $vcCssClassName Class name
 * @param string $vcElementType  Element type (vc_row* or vc_column*)
 * @return strubg
 */
function {project.prefix}_vc_bootstrap_integration($vcCssClassName, $vcElementType) {
    // Keep the original classes in the Frontend Editor
    if (vc_is_inline()) {
        return $vcCssClassName;
    }
    
    // Row element
    if (in_array($vcElementType, array('vc_row', 'vc_row_inner'))) {
        // Remove VisualComposer-specific row class names
        $vcCssClassName = trim('row ' . preg_replace('%\b(?:wpb_row|vc_row-fluid|vc_row)\b%', '', $vcCssClassName));
    }

    // Column element
    if (in_array($vcElementType, array('vc_column', 'vc_column_inner'))) {
        // Remove VisualComposer-specific column class names
        $vcCssClassName = preg_replace('%\b(?:wpb_column|vc_column_container)\b%', '', $vcCssClassName);

        // Replace the column names
        $vcCssClassName = trim(
            preg_replace_callback(
                '%\bvc_col\-(xs|sm|md|lg)\-(offset\-)?(\d+)\b%', 
                function($item) {
                    // Get the parts
                    list (, $layoutSize, $offset, $columnSize) = $item;
                    
                    // Bootstrap 4 has removed the xs size for col and offset
                    $layoutSizePart = ('xs' === $layoutSize ? '' : '-' . $layoutSize);
                    
                    // Bootstrap 4 does not include the "col-" prefix for offsets
                    if (strlen($offset)) {
                        // Ex: "vc_col-xs-offset-0" becomes "offset-0"
                        return 'offset' . $layoutSizePart . '-' . $columnSize;
                    }
                    
                    // Bootstrap 4 column template
                    return 'col' . $layoutSizePart . '-' . $columnSize;
                }, 
                $vcCssClassName
            )
        );
    }

    return $vcCssClassName;
}
add_filter('vc_shortcodes_css_class', '{project.prefix}_vc_bootstrap_integration', 10, 2);

/**
 * Help section
 */
if (!function_exists('{project.prefix}_help_section')) {
    function {project.prefix}_help_section() {
        // Prepare the screen object
        $screen = get_current_screen();
        
        // Main help tab
        $screen->add_help_tab(array(
            'id'      => '{project.prefix}_help_section',
            'title'   => '{project.destProjectName} by {config.authorName}',
            'content' => 
                '<p>' .
                    '<h2>' . 
                        __('Getting started is really easy!', '{project.destDir}') . 
                    '</h2>' . 
                    '<ul>' . 
                        '<li>' . 
                            sprintf(
                                __('Just visit the %s page and install your favorite snapshot.', '{project.destDir}'),
                                '<b>' . 
                                    '<a href="' . admin_url('themes.php?page={project.prefix}_theme_manager') . '">' . 
                                        __('Demo Content', '{project.destDir}') . 
                                    '</a>' . 
                                '</b>'
                            ) . 
                        '</li>' . 
                        '<li>' . 
                            __('Need a little more help?', '{project.destDir}') . ' ' . 
                            ' <a target="_blank" href="{utils.common.themeDocsUrl}">' . 
                                __('Check out our concise documentation.', '{project.destDir}') . ' ' .
                            '</a>' .
                        '</li>' . 
                    '</ul>' . 
                '</p>' . 
                '<p>' . 
                    '<a target="_blank" href="{utils.common.marketProfileUrl}">' .
                        __('Your rating and feedback are appreciated!', '{project.destDir}') . 
                    '</a>' . 
                '</p>',
        ));
        
        // Sidebar info
        $screen->set_help_sidebar({options.projectQuote});
    }
    
    add_action('load-themes.php', '{project.prefix}_help_section');
}

/*EOF*/