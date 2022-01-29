jQuery(document).ready(function(){
    // Create
    $('#newUserForm').submit(function(e){
        // Prevent the default
        e.preventDefault();
        e.stopPropagation();
        
        // Save the data
        tools.performAjax('user/create', {
            email: $('#newEmail').val(),
            name: $('#newName').val(),
            password: $('#newPassword').val(),
            role: $('#newRole').val(),
        }, function(msg) {
            if (msg.status == "success") {
                window.setTimeout(function(){location.reload();}, 1000);
            }
        });
    });
    
    // Tooltips
    $('[title]').tooltip();
    
    // Update
    $.each($('button.button-edit'), function() {
        $(this).click(function(){
            var enabled = $(this).children('span').hasClass('glyphicon-ok');
            
            // Not enabled
            if (!enabled) {
                // Enable icon
                $(this).children('span').removeClass('glyphicon-pencil').addClass('glyphicon-ok');
                
                // Enable fields
                $(this).parents('tr').find('[name=name],[name=password],[name=role]').removeAttr("disabled");
            } else {
                // Disable icon
                $(this).children('span').removeClass('glyphicon-ok').addClass('glyphicon-pencil');
                
                // Disable fields
                $(this).parents('tr').find('[name=name],[name=password],[name=role]').attr("disabled", "disabled");
                
                // Save the data
                tools.performAjax('user/update', {
                    password: $(this).parents('tr').find('[name=password]').val() ? $(this).parents('tr').find('[name=password]').val() : null,
                    name: $(this).parents('tr').find('[name=name]').val(),
                    email: $(this).parents('tr').find('[name=email]').val(),
                    role: $(this).parents('tr').find('[name=role]').val(),
                });
            }
        });
    });
    
    // Initialize environment
    $.each($('button.button-init'), function() {
        $(this).click(function(){
            // Save the data
            tools.performAjax('user/wpInit/' + $(this).attr('data-id'), {}, function(msg) {});
        });
    });
    
    // Delete
    $.each($('button.button-delete'), function() {
        $(this).click(function(){
            if (confirm('Are you sure you want to delete ' + $(this).parents('tr').find('[name=email]').val() + '?')) {
                // Save the data
                tools.performAjax('user/delete', {
                    email: $(this).parents('tr').find('[name=email]').val(),
                }, function(msg) {
                    if (msg.status == "success") {
                        window.setTimeout(function(){location.reload();}, 1000);
                    }
                });
            }
        });
    });
});

/*EOF*/