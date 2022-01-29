<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

get_header();
?>
<div id="primary" class="content-area {Call.core.getContentColumns}">
    <main id="main" class="site-main container" role="main">

        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Oops! That page can\'t be found.', '{project.destDir}'); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content">
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', '{project.destDir}'); ?></p>

                <?php
                get_search_form();

                the_widget('WP_Widget_Recent_Posts');

                // Only show the widget if site has multiple categories.
                if ({project.prefix}_categorized_blog()) :
                ?>

                <div class="widget widget_categories">
                    <h2 class="widget-title"><?php esc_html_e('Most Used Categories', '{project.destDir}'); ?></h2>
                    <ul>
                        <?php
                        wp_list_categories(array(
                            'orderby' => 'count',
                            'order' => 'DESC',
                            'show_count' => 1,
                            'title_li' => '',
                            'number' => 10,
                        ));
                        ?>
                    </ul>
                </div><!-- .widget -->

                <?php
                endif;

                /* translators: %1$s: smiley */
                $archive_content = '<p>' . sprintf(esc_html__('Try looking in the monthly archives. %1$s', '{project.destDir}'), convert_smilies(':)')) . '</p>';
                the_widget('WP_Widget_Archives', 'dropdown=1', "after_title=</h2>$archive_content");

                the_widget('WP_Widget_Tag_Cloud');
                ?>

            </div><!-- .page-content -->
        </section><!-- .error-404 -->

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();
