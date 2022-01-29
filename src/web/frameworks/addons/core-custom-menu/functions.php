{add after="load_theme_textdomain\s*\(.*?\)\s*\;" indent="2"}

// This theme uses wp_nav_menu()
register_nav_menus( array(
    'menu-{addon.menuId}'     => esc_html__({addon.menuName}, '{project.destDir}'),
{if.core-custom-menu.flavorSplit}
    'menu-{addon.menuId}-two' => esc_html__('{Addon.menuName} 2', '{project.destDir}'),
{/if.core-custom-menu.flavorSplit}
));
{/add}
{add after="\/\/\s*\#Dependencies\#"}
// Custom Menu
require_once St_CoreCache::get(St_CoreCache::GET_TEMPLATE_DIRECTORY) . '/inc/core-custom-menu.php';
{/add}
