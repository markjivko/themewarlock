/**
 * Copyright 2019 Theme Warlock
 */
jQuery(document).ready(function(){
    "use strict";

    $ = jQuery;

    // Tooltips
    $('[title]').tooltip();

    // Handle the language change
    $('[data-role="language"]').change(function() {
        window.location.href = "/admin/translations/" + $(this).val();
    });
    
    // Handle the delete 
    $('button[data-role="remove"]').click(function(){
        var currentInput = $(this);
        tools.performAjax(
            // AJAX path
            'translations/delete',
            // POST keys
            {
                key: $(this).parents('tr').find('[data-key]').attr('data-key'),
            },
            function(msg) {
                if ('string' == typeof msg['status']) {
                    if ('success' == msg['status']) {
                        currentInput.parents('tr').remove();
                    }
                }
            },
            false
        );
    });
    
    // Adding a change trigger for all input elements
    var inputListener = function(){
        var currentInput = $(this);
        tools.performAjax(
            // AJAX path
            'translations/update/' + $('select[data-role="language"]').val(),
            // POST keys
            {
                key: $(this).attr('data-key'),
                index: $(this).attr('data-index'),
                value: $(this).val(),
            },
            function(msg) {
                if ('string' == typeof msg['status']) {
                    // Set the status
                    currentInput.attr('data-status', msg['status']);
                    if ('success' == msg['status']) {
                        if('string' == typeof msg['result']) {
                            // Store the result
                            currentInput.val(msg['result']);
                            
                            // Remove the auto flag
                            currentInput.parents('tr').removeClass('info');
                        }
                    } else {
                        if ('string' == typeof msg['result']) {
                            tools.notify(msg['result'], 'warning');
                        }
                    }
                }
            },
            false
        );
    };
    $('[data-role="translation"]').blur(inputListener);
});