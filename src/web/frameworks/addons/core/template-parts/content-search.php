<?php
/**
 * Template part for displaying results in search pages
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
        <?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>

        <?php if ( 'post' === get_post_type() ) : ?>
        <div class="entry-meta">
            <?php {project.prefix}_posted_on(); ?>
        </div><!-- .entry-meta -->
        <?php endif; ?>
    </header><!-- .entry-header -->

    <div class="entry-summary">
        <?php the_excerpt(); ?>
    </div><!-- .entry-summary -->

    <footer class="entry-footer <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>">
        <div class="col">
            <?php {project.prefix}_entry_footer(); ?>
        </div>
    </footer><!-- .entry-footer -->
</article><!-- #post-<?php the_ID(); ?> -->
