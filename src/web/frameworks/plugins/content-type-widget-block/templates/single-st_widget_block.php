<?php
/**
 * The template for displaying all "Widget Blocks"
 * 
 * Template Name: Widget Blocks Template
 * Template Post Type: st_widgets
 * 
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}
?><!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>" />
        <meta name="robots" content="noindex, nofollow, noarchive, noodp, nosnippet, noydir" />
        <meta name="googlebot" content="noindex" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <link rel="profile" href="http://gmpg.org/xfn/11" />
        <?php wp_head(); ?>
    </head>
    <body <?php body_class('compose-mode'); ?>>
        <div id="page" class="container st_widget_block">
            <div id="content" class="site-content row no-gutters">
                <div class="col-12">
                    <div class="row">
                        <h2 class="page-title"><?php echo _x('Widget Block', 'singular', '{project.destDir}');?>:  <b><?php echo get_the_title();?></b></h2>
                    </div>
                    <div class="row">
                        <div id="primary" class="content-area col-12">
                            <main id="main" class="site-main" role="main">
                                <?php
                                while ( have_posts() ) : 

                                    the_post();

                                    the_content();

                                endwhile; // End of the loop.
                                ?>
                            </main><!-- #main -->
                        </div><!-- #primary -->
                    </div>
                </div>
            </div>
        </div><!-- #page -->
        <?php wp_footer(); ?>
    </body>
</html>
<?php

/*EOF*/