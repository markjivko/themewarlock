<?php
/**
 * The template for displaying the footer
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

?>
    </div></div></div><!-- #content -->

    <footer id="colophon" class="site-footer <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>" role="contentinfo">
        <div class="site-info {Call.core.getContentColumns.content-area.p0}">
        </div><!-- .site-info -->
    </footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>