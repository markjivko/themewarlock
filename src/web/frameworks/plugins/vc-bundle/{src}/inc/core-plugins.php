{add after="\$plugins\s*=\s*array\s*\(" indent="2"}
    // WPBakery Page Builder - Bundle for theme {project.destProjectName}
    array(
        'name'               => '{project.destProjectName} - WPBakery Page Builder Add-Ons Bundle',
        'slug'               => {call.core.getVcBundleName},
        'source'             => $pluginsPath . '/{Call.core.getVcBundleName}.tar',
        'required'           => true,
        'version'            => '{project.versionVerbose}',
        'force_activation'   => {utils.staging},
        'force_deactivation' => false,
        'external_url'       => '',
        'is_callable'        => '',
        'source_type'        => 'bundled',
    ),
{/add}