<?php
/**
 * Custom template tags for this theme
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

if (!function_exists('{project.prefix}_posted_on')) {
    /**
     * Prints HTML with meta information for the current post-date/time and author.
     */
    function {project.prefix}_posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( 'c' ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( 'c' ) ),
            esc_html( get_the_modified_date() )
        );

        $posted_on = sprintf(
            /* translators: %s: post date. */
            esc_html_x( 'Posted on %s', 'post date', '{project.destDir}' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

        $byline = sprintf(
            /* translators: %s: post author. */
            esc_html_x( 'by %s', 'post author', '{project.destDir}' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span><span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.
    }
}

if (!function_exists( '{project.prefix}_entry_footer')) {
    /**
     * Prints HTML with meta information for the categories, tags and comments.
     */
    function {project.prefix}_entry_footer() {
        // Hide category and tag text for pages.
        if ( 'post' === get_post_type() ) {
            $categories_list = get_the_category_list(', ', '{project.destDir}');
            if ( $categories_list && {project.prefix}_categorized_blog() ) {
                /* translators: 1: list of categories. */
                printf( '<span class="cat-links">' . esc_html__( 'Posted in %1$s', '{project.destDir}' ) . '</span>', $categories_list ); // WPCS: XSS OK.
            }

            $tags_list = get_the_tag_list('', ', ');
            if ( $tags_list ) {
                /* translators: 1: list of tags. */
                printf( '<span class="tags-links">' . esc_html__( 'Tagged %1$s', '{project.destDir}' ) . '</span>', $tags_list ); // WPCS: XSS OK.
            }
        }

        if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
            echo '<span class="comments-link">';
            comments_popup_link(
                sprintf(
                    wp_kses(
                        /* translators: %s: post title */
                        __('Leave a Comment on %s', '{project.destDir}'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    '<span class="screen-reader-text">' . get_the_title() . '</span>'
                )
            );
            echo '</span>';
        }

        edit_post_link(
            sprintf(
                wp_kses(
                    /* translators: %s: Name of current post. Only visible to screen readers */
                    __('Edit %s', '{project.destDir}'),
                    array(
                        'span' => array(
                            'class' => array(),
                        ),
                    )
                ),
                '<span class="screen-reader-text">' . get_the_title() . '</span>'
            ),
            '<span class="edit-link">',
            '</span>'
        );
    }
}

/**
 * Returns true if a blog has more than 1 category.
 *
 * @return bool
 */
function {project.prefix}_categorized_blog() {
    $all_the_cool_cats = get_transient( '{project.prefix}_categories' );
    if ( false === $all_the_cool_cats ) {
        // Create an array of all the categories that are attached to posts.
        $all_the_cool_cats = get_categories( array(
            'fields'     => 'ids',
            'hide_empty' => 1,
            // We only need to know if there is more than one category.
            'number'     => 2,
        ) );

        // Count the number of categories that are attached to the posts.
        $all_the_cool_cats = count( $all_the_cool_cats );

        set_transient( '{project.prefix}_categories', $all_the_cool_cats );
    }

    if ( $all_the_cool_cats > 1 || is_preview() ) {
        // This blog has more than 1 category so {project.prefix}_categorized_blog should return true.
        return true;
    } else {
        // This blog has only 1 category so {project.prefix}_categorized_blog should return false.
        return false;
    }
}

/**
 * Flush out the transients used in {project.prefix}_categorized_blog.
 */
function {project.prefix}_category_transient_flusher() {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    delete_transient('{project.prefix}_categories');
}

add_action('edit_category', '{project.prefix}_category_transient_flusher');
add_action('save_post',     '{project.prefix}_category_transient_flusher');

/*EOF*/