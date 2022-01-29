{add after="\$plugins\s*=\s*array\s*\(" indent="3"}
// WPBakery Page Builder
array(
    'name'               => 'WPBakery Page Builder',
    'slug'               => 'js_composer',
    'source'             => $pluginsPath . '/js_composer.tar',
    'required'           => true,
    'version'            => '6.0.3',
    'force_activation'   => {utils.staging},
    'force_deactivation' => false,
    'external_url'       => '',
    'is_callable'        => '',
),
{/add}