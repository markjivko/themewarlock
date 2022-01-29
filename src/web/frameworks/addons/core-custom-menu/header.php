{add if="core-custom-menu.flavorDefault" before="^\s*<\s*nav\b" indent="6"}
<button class="menu-toggle" id="site-navigation-button" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e({addon.menuName}, '{project.destDir}'); ?></button>
{/add}
{add if="core-custom-menu.flavorDefault" before="^\s*<\s*\/\s*nav\s*>" indent="7"}
<?php
wp_nav_menu(array(
    'theme_location' => 'menu-{addon.menuId}',
    'menu_id'        => 'primary-menu',
));
?>
{/add}
{add if="core-custom-menu.flavorDefault" replace="class\s*=\s*\"main\-navigation\""}class="<?php echo St_CoreCache::get(St_CoreCache::ST_CUSTOM_MENU_FLOATING_CLASS);?>"{/add}
{add if="core-custom-menu.flavorSplit" replace="^\s*<header.*?>.*?<\/\s*header\s*>" indent="3"}
<header id="masthead" class="site-header <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>" data-role="banner">
    <div class="container">
        <div class="{Call.core.getContentColumns.content-area.p0}" data-role="th-holder">
            <div class="row">
                <div class="col-5">
                    <div data-role="th-left-bkg"></div>
                    <div data-role="th-left">
                        <div class="col-12" data-role="th-social">
                            {if.onepage.showAccounts}
                                <?php
                                    {foreach.onepage.getAccounts}
                                        {call.onepage.customizer.social_accounts_{@Value}}

                                        // Show the {@Value} account if URL was defined by user
                                        if(strlen({call.onepage.customizer.social_accounts_{@Value}.exportVarName})) {
                                            echo '<a rel="nofollow" target="_blank" href="' . esc_html({call.onepage.customizer.social_accounts_{@Value}.exportVarName}) . '"><span class="fab fa-{@Value}"></span></a>';
                                        }
                                    {/foreach.onepage.getAccounts}
                                ?>
                            {/if.onepage.showAccounts}
                        </div>
                        <div class="col-12">
                            <nav id="primary-navigation" class="<?php echo St_CoreCache::get(St_CoreCache::ST_CUSTOM_MENU_FLOATING_CLASS);?>" role="navigation">
                                <?php
                                    wp_nav_menu(array(
                                        'theme_location' => 'menu-{addon.menuId}',
                                        'menu_id'        => 'primary-menu',
                                    ));
                                ?>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="col-2">
                    <div class="site-icon">
                        <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                            <span><?php bloginfo('name'); ?></span>
                        </a>
                    </div>
                </div>
                <div class="col-5">
                    <div data-role="th-right-bkg"></div>
                    <div data-role="th-right">
                        <div class="col-12 site-branding">
                            <?php $description = get_bloginfo('description', 'display');
                                if ($description || is_customize_preview()) : ?>
                                <p class="site-description"><?php echo esc_html($description); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="col-12">
                            <!-- .site-branding -->
                            <button class="menu-toggle" id="site-navigation-button" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e({addon.menuName}, '{project.destDir}'); ?></button>
                            <nav id="site-navigation" class="<?php echo St_CoreCache::get(St_CoreCache::ST_CUSTOM_MENU_FLOATING_CLASS);?>" role="navigation">
                                <?php
                                    wp_nav_menu(array(
                                        'theme_location' => 'menu-{addon.menuId}-two',
                                        'menu_id'        => 'secondary-menu',
                                    ));
                                ?>
                            </nav><!-- .main-navigation -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
{/add}