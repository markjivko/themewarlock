jQuery(document).ready(function(){
    var $ = jQuery;
   
    // Reload the log
    var reloadLog = function(logView, maxResults) {
        // Get the default maximum
        if ("undefined" == typeof maxResults) {
            maxResults = 500;
        }
        
        // Get the info modal
        var modalInfo = $('.modal#info');
        
        // Maximum length of arguments listing
        var vArgumentsMaxLength = 150;
        
        // Clear the HTML
        logView.parents('.panel').find('.panel-footer').html('Loading...');
        
        // Perform the action
        tools.performAjax('utils/logView/' + logView.attr('log-view-id'), {max:maxResults}, function (msg) {
            // Clean-up
            logView.html('');
            
            // Done
            logView.parents('.panel').find('.btn-refresh').removeClass('glyphicon-hourglass').removeClass('glyphicon-refresh').addClass('glyphicon-refresh');
            
            // Interpret the result
            if (msg.status == "success") {
                // Prepare the table
                var table = $('<table class="table table-striped table-hover"><thead><th>Date and time</th><th width="150">IP</th><th>Controller</th><th>Method</th><th>Arguments</th></thead><tbody></tbody></table>');
                
                // Get the table body
                var tableBody = table.find('tbody');
                
                // Store the excerpt
                logView.parents('.panel').find('.panel-footer').html(msg.result.length + ' entries. Last action at <b>' + (msg.result[0][0]) + '</b> from <a href="https://www.google.com/search?q=' + (msg.result[0][1]) + '" target="_blank">' + (msg.result[0][1]) + '</a>, ' + msg.result[0][2] + '::' + msg.result[0][3] + '(' + ("object" == typeof msg.result[0][4] ? msg.result[0][4].join(', ') : '') + ') with ' + (null == msg.result[0][5] ? 'no' : (Object.keys(msg.result[0][5]).length)) + ' arguments.');
                
                // Parse the result
                $.each(msg.result, function(k,v) {
                    // Prepare the arguments
                    var vArguments = JSON.stringify(v[5]);
                    
                    // Minify the string
                    if (null != vArguments && vArguments.length > vArgumentsMaxLength) {
                        vArguments = vArguments.substring(0, vArgumentsMaxLength - 3) + "...";
                    }
                    
                    // Prepare the row
                    var tableRow = $('<tr><td width="200"><b>' + v[0] + '</b></td><td><a href="https://www.google.com/search?q=' + (v[1]) + '" target="_blank">' + v[1] + '</a></td><td>' + v[2] + '</td><td>' + v[3] + '(' + ("object" == typeof v[4] ? v[4].join(', ') : '') + ')</td><td class="args" title="Click for more info">' + vArguments + '</td></tr>');

                    // Set the on-click behavior
                    tableRow.find('td.args').click(function(){
                        // Prepare the arguments
                        var vArgumentsFull = '<pre>' + tools.json.prettyPrint(v[5]) + '</pre>';
                    
                        // Set the modal
                        modalInfo.find('.modal-title').html(v[0] + ' - ' + v[2] + '::' + v[3] + '(' + ("object" == typeof v[4] ? v[4].join(', ') : '') + ')');
                        modalInfo.find('.modal-body').html(vArgumentsFull);
                        modalInfo.modal('show');
                    });

                    // Append to the body
                    tableBody.append(tableRow);
                });
                
                // Insert the table
                logView.append(table);
            } else {
                logView.html('<div class="alert alert-warning" role="alert">' + msg.result + '</div>');
                logView.parents('.panel').find('.panel-footer').html('Error loading the logs...');
            }
        }, false);
    };
    
    // Reload on-click
    $('.btn-refresh').click(function(e) {
        // Prevent bubbling
        e.preventDefault();
        e.stopPropagation();
        
        // Working
        $(this).removeClass('glyphicon-hourglass').removeClass('glyphicon-refresh').addClass('glyphicon-hourglass');
        
        // Reload the log
        reloadLog($(this).parents('.panel').find('.log-view'), 5000);
    });
    
    // Get the data
    $.each($('.log-view'), function(){
        // Store the view
        var logView = $(this);
        
        // Reload the logs
        reloadLog(logView);
    });
});