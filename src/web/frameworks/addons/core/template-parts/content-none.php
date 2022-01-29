<?php
/**
 * Template part for displaying a message that posts cannot be found
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package {project.destProjectName}
 */
if (!defined('WPINC')) {die;}

?>
<section class="no-results not-found">
    <header class="page-header">
        <h1 class="page-title"><?php esc_html_e( 'Nothing Found', '{project.destDir}' ); ?></h1>
    </header><!-- .page-header -->

    <div class="page-content">
        <?php
        if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>

            <p><?php
                printf(
                    wp_kses(
                        /* translators: 1: link to WP admin new post page. */
                        __( 'Ready to publish your first post?', '{project.destDir}' ) . 
                        ' <a href="%1$s">' . __('Get started here.', '{project.destDir}' ) . '</a>',
                        array(
                            'a' => array(
                                'href' => array(),
                            ),
                        )
                    ),
                    esc_url( admin_url( 'post-new.php' ) )
                );
            ?></p>

        <?php elseif ( is_search() ) : ?>

            <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', '{project.destDir}' ); ?></p>
            <?php
                get_search_form();

        else : ?>

            <p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', '{project.destDir}' ); ?></p>
            <?php
                get_search_form();

        endif; ?>
    </div><!-- .page-content -->
</section><!-- .no-results -->
