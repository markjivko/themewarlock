jQuery && jQuery(document).ready(function(){
    var $ = jQuery;
    
    // {Plugin.getSlug}
    var {Plugin.getNameClass} = {
        init: function() {
            
        },
    };
    
    // Initialize
    {Plugin.getNameClass}.init();
    
    // Visual Composer Reload
    $(window).on('vc_reload', {Plugin.getNameClass}.init);
});

/*EOF*/