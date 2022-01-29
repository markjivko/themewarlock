jQuery && jQuery(document).ready(function(){
    var $ = jQuery;
    
    // {Plugin.getSlug} animation
    var {Plugin.getNameClass} = {
        init: function() {
            // Multi-instance
            $.each($('.{Plugin.getSlug}'), function(){
                var parent = $(this);
                
                // Set the click events
                parent.find('.sort > li').unbind('click').click(function() {
                    var options = {
                        filter: $(this).attr('data-rel') == '*' ? '[data-rel]' : '[data-rel~="' + $(this).attr('data-rel') + '"]',
                    };

                    if (!$(this).hasClass('active')) {
                        $.each(parent.find('.sort > li'), function() {
                            $(this).removeClass('active');
                        });
                        $(this).addClass('active');
                    }

                    // Re-initiate Isotope
                    parent.find('.isotope').isotope(options);
                });
            });
        },
    };
    
    // Initialize
    {Plugin.getNameClass}.init();
    
    // Visual Composer Reload
    $(window).on('vc_reload', {Plugin.getNameClass}.init);
});

/*EOF*/