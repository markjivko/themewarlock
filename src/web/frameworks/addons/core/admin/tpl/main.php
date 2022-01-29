<?php
/**
 * {project.destProjectName} Theme Manager Template
 * 
 * {utils.common.copyright}
 */
if (!defined('WPINC')) {die;}

?>
<div class="clear"></div>
<div class="container-holder row no-gutters align-items-center" style="display: none;">
    <div class="logo-start">
        <span>
            <?php echo __('Demo Content Installer', '{project.destDir}');?><br/>
            <?php echo __('for',  '{project.destDir}');?> <b>{project.destProjectName}</b> <sup>v.<b>{project.versionVerbose}</b></sup><br/>
        </span>
    </div>
    <div class="container" data-role="admin-container">
        <div class="row" data-role="sliders-row">
            <div class="col-12 offset-0" data-role="content">
                <div class="row align-items-center" data-role="title-row">
                    <div class="col-10 offset-1 text-center" data-role="title">
                        <span data-role="add-snapshot">+</span>
                        <h1></h1>
                        <p></p>
                        <span data-role="delete-snapshot-placeholder">+</span>
                    </div>
                    <form action="<?php echo esc_html(admin_url('admin-ajax.php'));?>" data-role="upload-form" method="post" class="hidden" enctype="multipart/form-data">
                        <input type="file" name="file" />
                    </form>
                </div>
                <div class="row navigation-row align-items-center">
                    <div class="col-2">
                        <div data-role="nav-left">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                 viewBox="0 0 477.175 477.175" style="enable-background:new 0 0 477.175 477.175;" xml:space="preserve">
                                <g>
                                    <path d="M145.188,238.575l215.5-215.5c5.3-5.3,5.3-13.8,0-19.1s-13.8-5.3-19.1,0l-225.1,225.1c-5.3,5.3-5.3,13.8,0,19.1l225.1,225
                                          c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4c5.3-5.3,5.3-13.8,0-19.1L145.188,238.575z"/>
                                </g>
                            </svg>
                        </div>
                    </div>
                    <div class="col-8 text-center">
                        <div data-role="install">
                            <span></span>
                            <div class="blur-background"></div>
                        </div>
                    </div>
                    <div class="col-2">
                        <div data-role="nav-right">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                 viewBox="0 0 477.175 477.175" style="enable-background:new 0 0 477.175 477.175;" xml:space="preserve">
                                <g>
                                    <path d="M360.731,229.075l-225.1-225.1c-5.3-5.3-13.8-5.3-19.1,0s-5.3,13.8,0,19.1l215.5,215.5l-215.5,215.5
                                          c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4c3.4,0,6.9-1.3,9.5-4l225.1-225.1C365.931,242.875,365.931,234.275,360.731,229.075z
                                          "/>
                                </g>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="row justify-content-center align-items-center" data-role="bullets"></div>
                <div data-role="ribbon">
                    <div data-role="delete-snapshot">+</div>
                    <span>
                        <div class="lds-ripple"><div></div><div></div></div>
                    </span>
                </div>
                <div data-role="images"></div>
            </div>
        </div>
        <div class="row" data-role="header-row">
            <div class="col-12 offset-0">
                <div class="header">
                    <div class="info row no-gutters align-items-center">
                        <div class="col-10 offset-1">
                            <div class="row align-items-center">
                                <div class="col-12 col-sm-6 text-center text-sm-left">
                                    <span class="icon"></span>
                                    <span class="title">
                                        <b>{project.destProjectName}</b> <sup>v.<b>{project.versionVerbose}</b></sup> by {config.authorName}&trade;
                                    </span>
                                </div>
                                <div class="col-12 col-sm-6 text-center text-sm-right">
                                    <span class="status-area"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>

<?php 

// Request WP_Filesystem credentials if necessary
wp_print_request_filesystem_credentials_modal();

/*EOF*/