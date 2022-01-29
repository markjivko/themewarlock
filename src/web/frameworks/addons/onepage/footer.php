{add after="class\s*=\s*"site\-info.*?>" indent="3"}{foreach.onepage.getWidgetAreas.footer}
<?php if (is_active_sidebar({@key})): ?>
    <aside id="footer_{@Key}" class="sidebar-footer footer-{@Key} <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>">
        <div class="col-12">
            <?php dynamic_sidebar({@key}); ?>
        </div>
    </aside>
<?php endif; ?>
{/foreach.onepage.getWidgetAreas.footer}{if.onepage.footerCopy}
    <?php
        {call.onepage.customizer.footer_copy_toggle}

        // Do not show the copyright
        $showCopyRightFlag = ('off' !== {call.onepage.customizer.footer_copy_toggle.exportVarName});
        
        // Show the copyright section
        if ($showCopyRightFlag):
    ?>
    <aside id="footer_final_copyright" class="sidebar-footer footer-final-copyright <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>">
        <div class="col-6">
            <a href="<?php echo esc_url('//wordpress.org/'); ?>"><?php
                /* translators: %s: CMS name, i.e. WordPress. */
                printf(
                    esc_html__('Proudly powered by %s', '{project.destDir}'), 
                    '<b>WordPress</b>'
                );
            ?></a>
        </div>
        <div class="col-6 text-right">
            <?php
                /* translators: 1: Theme name, 2: Theme author. */
                printf( 
                    esc_html__('Theme %1$s by %2$s', '{project.destDir}'), 
                    '<b>{Options.projectName}</b>', 
                    '<a target="_blank" href="' . esc_url('{utils.common.themeUrl}') . '">{config.authorName}</a>' 
                );
            ?>
        </div>
    </aside>
    <?php endif;?>
{/if.onepage.footerCopy}{/add}