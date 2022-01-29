jQuery(document).ready(function(){
    var $ = jQuery;
    
    // Add the tooltips
    $('[title]').tooltip();
    
    // Update the entries
    $('input,select').change(function() {
        // Prepare the parent group
        var parentGroup = $(this).parents('div.form-group');
        
        // Perform the change
        tools.performAjax('system/config', {name: $(this).attr('name'), value: $(this).val()}, function(msg) {
            // Mark the document
            if("success" === msg.status) {
                if (parentGroup.hasClass('has-error')) {
                    parentGroup.removeClass('has-error');
                }
            } else {
                if (!parentGroup.hasClass('has-error')) {
                    parentGroup.addClass('has-error');
                }
            }
        }, false);
    });
});