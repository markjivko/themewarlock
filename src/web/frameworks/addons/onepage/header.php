{add before="^\s*<header" indent="3"}
{foreach.onepage.getWidgetAreas.header}
<?php if (is_active_sidebar({@key})): ?>
    <aside id="header_{@Key}" class="sidebar-header header-{@Key} <?php echo St_CoreCache::get(St_CoreCache::ST_GET_ROWS_CLASS);?>">
        <div class="col-12">
            <?php dynamic_sidebar({@key}); ?>
        </div>
    </aside>
<?php endif; ?>
{/foreach.onepage.getWidgetAreas.header}
{/add}
{add replace="class\s*=\s*\"container\b" if="onepage.layoutToggle"}class="<?php echo St_CoreCache::get(St_CoreCache::ST_GET_PAGE_CLASS);?>{/add}