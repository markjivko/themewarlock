/**
 * Copyright 2019 Theme Warlock
 */
$(document).ready(function(){
    "use strict";
    
    // Define the main class
    var ThemeWarlock = function() {
        var _this = this;
        
        // Get the info about this page
        this.init = function() {
            // Add a project
            var modalAddObject = $('#modal-add');
            modalAddObject.find('form').submit(function(e) {
                // Stop here
                e.preventDefault();
                e.stopPropagation();
                
                // Get the form data
                var formData = $(this).serializeAssoc();
                var themeName = formData['themeName'].trim();
                
                // Invalid name
                if (themeName.length == 0) {
                    tools.notify("You must specify a theme name!", "warning");
                    return false;
                }
                
                // Save the data
                tools.performAjax('project/create', {
                    themeName: themeName,
                    themeFramework: formData['themeFramework'],
                }, function(msg) {
                    // All went well
                    if (msg.status == "success") {
                        // Move to the new page
                        window.setTimeout(function(){window.location.href = msg.result;}, 500);
                        modalAddObject.modal('hide');
                    }
                });
                
                // Stop here
                return false;
            });
            modalAddObject.find('[data-role="create"]').click(function() {
                modalAddObject.find('form').submit();
            });
            $('button[data-role=add]').click(function(){
                modalAddObject.modal('show');
            });
            
            // Edit a project
            $('[data-role=edit]').click(function() {
                var projectId = $(this).attr('data-project-id');
                var userId = $(this).attr('data-user-id');
                
                // Request edit
                tools.performAjax('project/getPath/' + projectId + '/' + userId + '/1', {}, function(msg) {
                    // All went well
                    if (msg.status == "success") {
                        // Move to the new page
                        window.location.href = msg.result;
                    }
                }, false);
            });
            
            // Delete a project
            $('[data-role=delete]').click(function() {
                var projectId = $(this).attr('data-project-id');
                var userId = $(this).attr('data-user-id');
                
                // Request edit
                if (window.confirm('Are you sure you want to delete project #' + projectId + ' by user #' + userId + '?')) {
                    tools.performAjax('project/delete/' + projectId + '/' + userId, {}, function(msg) {
                        // All went well
                        if (msg.status == "success") {
                            // Move to the new page
                            window.setTimeout(function(){window.location.href = window.location.href;}, 500);
                        }
                    });
                }
            });
            
            // Add tooltips
            $('[title]').tooltip();
            
            // Checker
            var statusChecker = {
                _objects: [],
                _data: [],
                init: function() {
                    // Get the generator buttons
                    statusChecker._objects = $('[data-role="generate"]');
                    
                    // Go through the list
                    $.each(statusChecker._objects, function() {
                        // Add the binome
                        statusChecker._data[statusChecker._data.length] = $(this).attr('data-project-id') + '-' + $(this).attr('data-user-id');
                        
                        // Checker object
                        var checkerObject = $(this);
                        
                        // Generate a project
                        checkerObject.click(function(){
                            // Get the project ID
                            var projectId = $(this).attr('data-project-id');

                            // Get the user ID
                            var userId = $(this).attr('data-user-id');
                            
                            // Confirm
                            if (confirm('Are you sure you want to Generate project #' + projectId + ' by user #' + userId + "?")) {
                                tools.performAjax('project/taskToggle/' + projectId + '/' + userId, {}, function(msg) {
                                    if ('success' == msg.status) {
                                        statusChecker.updateIcon(checkerObject, msg.result);
                                    }
                                }, false);
                            }
                        });
                    });
                    
                    // Start the ticker
                    this.tick();
                },
                updateIcon: function(object, objectData) {
                    // Update the status
                    object.attr('data-status', objectData.status);

                    // Set the progress
                    object.find('.progress').css({width: objectData.percent + '%'});

                    // Prepare the class
                    var glyphClass = 'ok';
                    var titlePrefix = 'Generate';
                    switch(objectData.status) {
                        case 'pending':
                            glyphClass = 'hourglass';
                            titlePrefix = 'Pause';
                            break;

                        case 'working':
                            glyphClass = 'flash';
                            titlePrefix = 'Stop';
                            break;

                        case 'stopped':
                            glyphClass = 'pause';
                            titlePrefix = 'Regenerate';
                            break;

                        case 'failed':
                            glyphClass = 'ban-circle';
                            titlePrefix = 'Regenerate';
                            break;
                    }

                    // Replace the glyphicon
                    object.find('.glyphicon').attr('class', 'glyphicon glyphicon-' + glyphClass);

                    // Calculate the duration
                    var duration = objectData.endTime > objectData.startTime ? (objectData.endTime - objectData.startTime) : 0;

                    // Set the title
                    object.attr('data-original-title', titlePrefix + ' (' + objectData.status + ') ' + objectData.lastErr + ' | ' + objectData.percent + '% in ' + duration + 's');
                },
                tick: function() {
                    // Request status
                    tools.performAjax('project/taskCheck', {data: statusChecker._data}, function(msg) {
                        if (msg.status == "success") {
                            var pendingTasks = false;
                            $.each(statusChecker._objects, function() {
                                // Get the data
                                var objectData = msg.result[$(this).attr('data-project-id') + '-' + $(this).attr('data-user-id')];
                                
                                // Status defined
                                if (null != objectData) {
                                    // One task is pending
                                    if ('pending' == objectData.status || 'working' == objectData.status) {
                                        pendingTasks = true;
                                    }
                                    
                                    // Update the icon
                                    statusChecker.updateIcon($(this), objectData);
                                }
                            });
                            
                            // Check once per second or twice a minute
                            window.setTimeout(statusChecker.tick, pendingTasks ? 1000 : 10000);
                        }
                    }, false);
                },
            };
            
            // Admin only
            if ($('.footer .revision').hasClass('admin')) {
                statusChecker.init();
            }
            
            // Add a project
            var modalPreviewObject = $('#modal-preview');
            
            // Publish
            $('[data-role="publish"]').click(function(){
                // Get the project ID
                var projectId = $(this).attr('data-project-id');

                // Get the user ID
                var userId = $(this).attr('data-user-id');

                // Confirm
                if (confirm('Are you sure you want to Publish project #' + projectId + ' by user #' + userId + "?")) {
                    // Request theme publish
                    tools.performAjax('project/publish/' + projectId + '/' + userId, {}, function(msg) {
                        // @FIXME Improve this mechanism
                    });
                }
            });
            
            // Preview file size
            $('[data-role="download"] [data-download]').hover(function(e){
                // Don't care about the previews
                if (/^(snapshot\-\d+\-preview|docs)/g.test($(this).attr('data-download'))) {
                    return;
                }
                
                // Not initialized
                if (!$(this).find('.file-size').length) {
                    var projectId = $(this).parents('[data-role="download"]').attr('data-project-id');
                    var userId = $(this).parents('[data-role="download"]').attr('data-user-id');
                    var downloadTag = $(this).attr('data-download');
                    
                    // Prepare the object
                    var fileSize = $('<span class="file-size">...</span>');
                    
                    // Check the file size
                    tools.performAjax('project/download/' + projectId + '/' + userId + '/' + downloadTag + '/size', {}, function(msg) {
                        if ("success" === msg.status) {
                            fileSize.html('( ' + msg.result + ' )');
                        } else {
                            fileSize.html('( - )');
                        }
                    }, false);
                    
                    // Append it
                    $(this).append(fileSize);
                }
            });
            
            // Download
            $('[data-role="download"] [data-download]').click(function(e){
                e.preventDefault();
                e.stopPropagation();
                var projectId = $(this).parents('[data-role="download"]').attr('data-project-id');
                var userId = $(this).parents('[data-role="download"]').attr('data-user-id');
                var downloadTag = $(this).attr('data-download');
                
                // Pre-download check
                tools.performAjax('project/download/' + projectId + '/' + userId + '/' + downloadTag + '/check', {}, function(msg) {
                    // All went well
                    if (msg.status == "success") {
                        // Preview images
                        if (/^(snapshot\-\d+\-preview|docs)/g.test(downloadTag)) {
                            // Set the title
                            modalPreviewObject.find('.modal-title').html(downloadTag);

                            // Prepare the image object
                            var imageObject = $('<img />');

                            // Set the source
                            imageObject.attr('src', '/ajax/project/download/' + projectId + '/' + userId + '/' + downloadTag + '?z=' + Math.random());

                            // Set the image
                            modalPreviewObject.find('.modal-body').html('').append(imageObject);
                            
                            // Show the modal
                            modalPreviewObject.modal('show');
                        } else {
                            tools.notify('Started downloading...');
                            window.location.href = '/ajax/project/download/' + projectId + '/' + userId + '/' + downloadTag + '?z=' + Math.random();
                        }
                    }
                }, false);
                
                // Stop here
                return false;
            });
        }
    };
    
    // Load the class
    var instance = new ThemeWarlock();
    instance.init();
});