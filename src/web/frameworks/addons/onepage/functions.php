{add after="\/\/\s*\#Dependencies\#"}
// Custom Layout Controls
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/onepage-layout.php';
{/add}
{add after="function\s+\w+_widgets_init\s*\(\s*\)\s*\{" indent="1"}
{foreach.onepage.getWidgetAreas}
    // {@Value.name} ({@Key}) widget area
    register_sidebar(array(
        'name'          => esc_html__({@value.name}, '{project.destDir}'),
        'id'            => {@key},
        'description'   => esc_html__({@value.description}, '{project.destDir}'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
{/foreach.onepage.getWidgetAreas}
{/add}