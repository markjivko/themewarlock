{add after="\$plugins\s*=\s*array\s*\(" indent="3"}
// Theme Check
array(
    'name'               => 'Theme Check',
    'slug'               => 'theme-check',
    'source'             => $pluginsPath . '/theme-check.tar',
    'required'           => true,
    'version'            => '20160523.1',
    'force_activation'   => {utils.staging},
    'force_deactivation' => false,
    'external_url'       => '',
    'is_callable'        => '',
),
{/add}