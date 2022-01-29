jQuery && jQuery(document).ready(function(){
    var $ = jQuery;
    
    // {Plugin.getSlug}
    var {Plugin.getNameClass} = {
        init: function() {
            // Multi-instance
            $.each($('.{Plugin.getSlug}'), function(){
                $(this).find('[data-role="table"] > .row > .col').hover(function() {
                    $(this).parents('.row').find('.col').removeClass('active');
                    $(this).addClass('active');
                });
                $(this).find('.col').eq(0).addClass('active');
            });
        },
    };
    
    // Initialize
    {Plugin.getNameClass}.init();
    
    // Visual Composer Reload
    $(window).on('vc_reload', {Plugin.getNameClass}.init);
});

/*EOF*/