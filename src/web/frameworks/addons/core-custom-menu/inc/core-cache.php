{add after="class\s+\w+CoreCache\s*\{" indent="1"}

/**
 * Get the floating navbar class
 */
const ST_CUSTOM_MENU_FLOATING_CLASS = '{project.prefix}_custom_menu_floating_class';
{if.core.useStoryline}
/**
 * Whether or not to build the menu dynamically
 */
const ST_CUSTOM_MENU_DYNAMIC = '{project.prefix}_custom_menu_dynamic';
{/if.core.useStoryline}
{/add}