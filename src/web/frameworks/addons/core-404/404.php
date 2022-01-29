{remove}.*{/remove}
<?php
/**
 * The template for displaying 404 (not found) pages
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

// Prepare the default color objects/strings
$colorA = class_exists('St_Colors') ? St_Colors::get()->color(1)->original()->hex() : '#000000';
$colorB = class_exists('St_Colors') ? St_Colors::get()->color(1)->lighter()->hex() : '#cccccc';

get_header();
?>
<div id="primary" class="content-area {Call.core.getContentColumns.content-area.p0}">
    <main id="main" class="site-main container d-flex" role="main">

        <section class="error-404 not-found align-self-center text-center" data-color-a="<?php echo esc_html($colorA); ?>" data-color-b="<?php echo esc_html($colorB); ?>">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('Oops! That page can\'t be found.', '{project.destDir}'); ?></h1>
            </header><!-- .page-header -->

            <div class="page-content">
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try one of the links below or a search?', '{project.destDir}'); ?></p>

                <?php get_search_form();?>

            </div><!-- .page-content -->
        </section><!-- .error-404 -->

    </main><!-- #main -->
</div><!-- #primary -->

<?php
get_footer();

/*EOF*/