<?php
/**
 * The sidebar{foreach.onepage.getWidgetAreas.sidebar}
 * - {@Key}{/foreach.onepage.getWidgetAreas.sidebar}
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

?>
{if.onepage.hasSidebars}
<div class="sidebar-holder {Call.core.getContentColumns.sidebar}">
    <div class="row">
{foreach.onepage.getWidgetAreas.sidebar}
    <?php if (is_active_sidebar({@key})): ?>
        <!-- {@Key} -->
        <aside id="secondary_{@Key}" class="widget-area widget-area-{@Key} col" role="complementary">
            <?php dynamic_sidebar({@key}); ?>
        </aside>
        <!-- /{@Key} -->
    <?php endif; ?>
{/foreach.onepage.getWidgetAreas.sidebar}
    </div>
</div>
{/if.onepage.hasSidebars}