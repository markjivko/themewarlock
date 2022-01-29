{add after="\$plugins\s*=\s*array\s*\(" indent="3"}
// Revolution slider
array(
    'name'               => 'Slider Revolution',
    'slug'               => 'revslider',
    'source'             => $pluginsPath . '/revslider.tar',
    'required'           => true,
    'version'            => '5.4.8.3',
    'force_activation'   => {utils.staging},
    'force_deactivation' => false,
    'external_url'       => '',
    'is_callable'        => '',
),
{/add}