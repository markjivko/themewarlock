{add after="class\s+\w+CoreCache\s*\{" indent="1"}

/**
 * Get the rows class, "" or "no-gutter"
 */
const ST_GET_ROWS_CLASS = '{project.prefix}_get_rows_class';
{/add}
{add after="class\s+St_CoreCache\s*\{" indent="1" if="onepage.layoutToggle"}

/**
 * Get the page class, "container" or "container-fluid"
 */
const ST_GET_PAGE_CLASS = '{project.prefix}_get_page_class';
{/add}