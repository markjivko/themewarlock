<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

?><!doctype html>
<html <?php language_attributes(); ?>>
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?>>
        <div id="page" class="container">
            <a class="skip-link screen-reader-text" href="#content"><?php esc_html_e('Skip to content', '{project.destDir}'); ?></a>
            <header id="masthead" class="site-header <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>" data-role="banner">
                <div class="{Call.core.getContentColumns.content-area.p0}">
                    <div class="site-branding">
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <span><?php bloginfo('name'); ?></span>
                            </a>
                        </h1>
                        <?php $description = get_bloginfo('description', 'display');
                            if ($description || is_customize_preview()) : ?>
                            <p class="site-description"><?php echo esc_html($description); ?></p>
                        <?php endif; ?>
                    </div><!-- .site-branding -->
                    <nav id="site-navigation" class="main-navigation" role="navigation">
                    </nav><!-- .main-navigation -->
                </div>
            </header><!-- #masthead -->
            <div id="content" class="site-content <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>">
                <div class="col-12">
                    <div class="row">