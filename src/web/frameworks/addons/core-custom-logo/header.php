{add after="\brel\s*=\s*\"home\"\s*>" indent="8"}
<?php
    if (has_custom_logo()) {
        // Get the custom logo ID
        $customLogoId = get_theme_mod('custom_logo');

        // Get the image information
        $logo = wp_get_attachment_image_src($customLogoId , 'full');

        // Display the logo
        echo '<img alt="Logo" src="'. esc_url($logo[0]) .'" />';
    }
?>
{/add}