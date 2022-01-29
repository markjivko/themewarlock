<?php if (!defined('WPINC')) {die;}?>
<div 
    data-role="{project.destDir}-contact-slider-form" 
    data-button-text="<?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_BUTTON_TEXT]);?>" 
    data-thank-you-text="<?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_THANK_YOU_TEXT]);?>" 
    style="position: relative; overflow: hidden; height:0px; margin-bottom: -1.5em;">
    <div class="slate">
        <h2><?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_TITLE_TEXT]);?> <span><?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_SUB_TITLE_TEXT]);?></span></h2>
        <form method="post" class="validate text-center">
            <div class="initial-input">
                <input name="st-contact-widget-nonce" type="hidden" value="<?php echo esc_html(wp_create_nonce('{project.prefix}-contact-widget'));?>" />
                <input class="form-control" name="st-contact-name" type="text" placeholder="<?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_INPUT_NAME_TEXT]);?>" required="" />
                <input class="form-control" name="st-contact-email" type="email" placeholder="<?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_INPUT_EMAIL_TEXT]);?>" required="" />
                <textarea class="form-control" name="st-contact-content" placeholder="<?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_INPUT_MESSAGE_TEXT]);?>" required=""></textarea>
            </div>
            <?php if (strlen($keyReCaptchaSite)):?>
                <div class="g-recaptcha" data-sitekey="<?php echo esc_html($keyReCaptchaSite);?>"></div>
            <?php endif;?>
            <input class="btn btn-info" type="submit" value="<?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_INPUT_BUTTON_TEXT]);?>" />
        </form>
        <address>
            <strong><?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_LABEL_COMPANY_TEXT]);?></strong><br/>
            <?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_LABEL_ADDRESS_TEXT]);?><br/>
            <abbr>P:</abbr> <?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_LABEL_PHONE_TEXT]);?>
        </address>
    </div>
    <?php 
        // Prepare the default color object/string
        $colorToUse = class_exists('St_Colors') ? St_Colors::get()->color($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_COLOR_INDEX]) : '#ffffff';
    ?>
    <div 
        data-role="{project.destDir}-contact-slider-map" 
        data-map-address="<?php echo esc_html($atts[{project.prefix}_{Plugin.getNameVar}::FIELD_FORM_LABEL_ADDRESS_TEXT]);?>" 
        data-map-color-lighter="<?php echo esc_html(is_string($colorToUse) ? $colorToUse : $colorToUse->lighter()->hex());?>"
        data-map-color-darker="<?php echo esc_html(is_string($colorToUse) ? $colorToUse : $colorToUse->darker()->hex());?>"
        data-map-color-complement="<?php echo esc_html(is_string($colorToUse) ? $colorToUse : $colorToUse->complement()->hex());?>">
        <?php if (is_user_logged_in()):?>
            <span class="warning-maps"><?php echo esc_html__('Please set your Google Maps API key', '{project.destDir}');?></span>
        <?php endif;?>
    </div>
</div>