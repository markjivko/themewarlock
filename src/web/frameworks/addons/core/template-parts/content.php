<?php
/**
 * Template part for displaying posts
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package {project.destProjectName}
 */
if (!defined('WPINC')) {die;}

?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="post-thumbnail-holder">
    <?php if (has_post_thumbnail()): ?>
        <?php the_post_thumbnail('full');?>
    <?php endif;?>
    </div><!-- .post-thumbnail-holder -->
    <header class="entry-header">
        <?php if ('post' === get_post_type()) : ?>
            <div class="entry-meta">
                <?php {project.prefix}_posted_on(); ?>
            </div><!-- .entry-meta -->
        <?php endif; ?>
        <?php
        if (is_singular()) :
            the_title( '<h1 class="entry-title">', '</h1>' );
        else :
            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
        endif;
        ?>
    </header><!-- .entry-header -->

    <div class="entry-content">
        <?php
            the_content(
                sprintf(
                    wp_kses(
                        /* translators: %s: Name of current post. Only visible to screen readers */
                        __('Continue reading "%s"', '{project.destDir}'),
                        array(
                            'span' => array(
                                'class' => array(),
                            ),
                        )
                    ),
                    '<span class="screen-reader-text">' . get_the_title() . '</span>'
                ) 
            );

            wp_link_pages( array(
                'before' => '<div class="page-links">' . esc_html__( 'Pages:', '{project.destDir}' ),
                'after'  => '</div>',
            ) );
        ?>
    </div><!-- .entry-content -->

    <footer class="entry-footer <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>">
        <div class="col">
            <?php {project.prefix}_entry_footer(); ?>
        </div>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
