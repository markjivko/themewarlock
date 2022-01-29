/**
 * Copyright 2019 Theme Warlock
 */
$(document).ready(function(){
    "use strict";
    
    // Define the main class
    var ThemeWarlockProject = function() {
        var _this = this;
        
        // Project item constants
        var Model_Project_Config_Item = {
            // Extra keys
            KEY_FLAVOR             : '_flavor',
            KEY_ICON               : '_icon',
            KEY_CSS                : 'css',
            KEY_CSS_INLINE         : 'cssi',
            KEY_JS                 : 'js',
            
            // Common keys
            JSON_TYPE                 : 't',
            JSON_KEY                  : 'k',
            JSON_VALUE                : 'v',
            JSON_IS_LIST              : 'l',
            JSON_OPTIONS              : 'o',
            JSON_OPTIONS_STRICT       : 's',
            JSON_IS_DISABLED          : 'x',
            JSON_IS_ON_DISK           : 'd',
            JSON_IS_ON_DISK_BINARY    : 'db',
            JSON_EXTENSION            : 'e',
            JSON_META_TITLE           : 'mt',
            JSON_META_DESCRIPTION     : 'md',
            JSON_META_SYMBOL          : 'my',
            JSON_META_DEPENDS         : 'ms',
            JSON_META_DEPENDS_ADDON   : 'msa',
            JSON_META_HEADER          : 'mh',
            JSON_META_URL             : 'mu',
            JSON_META_REFRESH         : 'mr',
            JSON_META_OPTIONS_DETAILS : 'mod',
            JSON_META_OPTIONS_CLONE   : 'moc',
            JSON_META_OPTIONS_PICKER  : 'mop',
            
            // String keys
            JSON_IS_TEXTAREA       : 'st',
            JSON_IS_MARKDOWN       : 'sm',
            JSON_STRING_REGEX      : 'sr',
            JSON_STRING_OPTIONAL   : 'so',
            
            // Integer keys
            JSON_INT_MIN           : 'inm',
            JSON_INT_MAX           : 'inx',
            
            // Image keys
            JSON_IMAGE_WIDTH       : 'iw',
            JSON_IMAGE_HEIGHT      : 'ih',
            JSON_IMAGE_TYPE        : 'it',
            
            // Color keys
            JSON_COLOR_RGBA        : 'c4',
        };
        
        // Snapshots keys
        var WordPress_Snapshots_Snapshot = {
            // Common keys
            JSON_ID          : 'i',
            JSON_TITLE       : 't',
            JSON_DESCRIPTION : 'd',
            JSON_PATTERN     : 'p',
        };
        
        this._pageTitle = null;
        this._saveTime = null;
        this._objectTitle = null;
        this._objectProject = null;
        this._objectPreview = null;
        this._objectTools = null;
        this._objectSnapshots = null;
        this._cache = {};
        this._cookieManager = {
            _getCookieName: function(cname) {
                // Project-specific cookies
                if ("undefined" != typeof _this._projectId) {
                    cname = "ThemeWarlock_"  + cname + '_' + _this._projectId;
                }
                
                // All done
                return cname;
            },
            setCookie: function(cname, cvalue, exdays) {
                // Default value
                if ("undefined" == typeof exdays) {
                    exdays = 365;
                }
                
                // Prepare the date
                var d = new Date();
                d.setTime(d.getTime() + exdays * 86400000);
                
                // Get the cookie name
                cname = this._getCookieName(cname);
                
                // Set the cookie
                document.cookie = cname + "=" + cvalue + ";expires=" + d.toUTCString() + ";path=/";
            },
            getCookie: function(cname) {
                // Get the cookie name
                cname = this._getCookieName(cname);
                
                // Create the cookie
                var name = cname + "=";
                var ca = document.cookie.split(';');
                for(var i = 0; i < ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }
        };
        
        this.ajax = function(url, data, callback, loading, progressCallback) {
            // Change the title
            _this._objectTitle.html(_this._pageTitle + ' ❤');
            
            // Execute the standard AJAX with session check
            tools.performAjax(
                url, 
                data, 
                function(msg) {
                    // Restore the title
                    window.setTimeout(function(){_this._objectTitle.html(_this._pageTitle);}, 750);
                    
                    // Invoke the callback
                    callback(msg);
                }, 
                loading, 
                progressCallback
            );
        };
        
        this._snapshotManager = {
            saveAllowed: false,
            _modalBody: null,
            _currentSnapshot: null,
            setBody: function(modalBody) {
                this._modalBody = $(modalBody);
            },
            create: function(fromId) {
                // Default snapshot ID
                if ("undefined" === typeof fromId) {
                    fromId = 0;
                }
                
                // Add a snapshot
                _this.ajax('project/snapshotsCreate/' + _this._projectId + '/' + _this._userId, {
                    snapshotIdFrom: fromId,
                }, function(msg) {
                    // Set the URL
                    if ("success" == msg.status && "number" == typeof msg.result) {
                        // Set the snapshot ID
                        _this._snapshotManager.activate(msg.result);
                    }
                });
            },
            activate: function(snapshotId) {
                // Set the snapshot ID
                _this._cookieManager.setCookie('snapshotId', snapshotId);
                
                // Reload the snapshot data
                _this._snapshotManager.getAll();
            },
            delete: function(snapshotId) {
                // Remove a snapshot
                _this.ajax('project/snapshotDelete/' + _this._projectId + '/' + _this._userId, {
                    snapshotId: snapshotId,
                }, function(msg) {
                    // Set the URL
                    if ("success" == msg.status) {
                        // Removed the currently active snapshot
                        if (snapshotId == _this._cookieManager.getCookie('snapshotId')) {
                            _this._cookieManager.setCookie('snapshotId', '');
                        }
                        
                        // Reload the snapshot data
                        _this._snapshotManager.getAll();
                    }
                });
            },
            update: function(snapshotId, title, description, pattern, callable) {
                // Not a function
                if ("function" !== typeof callable) {
                    callable = function(msg) {};
                }
                
                // Remove a snapshot
                _this.ajax('project/snapshotUpdate/' + _this._projectId + '/' + _this._userId, {
                    snapshotId: snapshotId,
                    snapshotTitle: title,
                    snapshotDescription: description,
                    snapshotPattern: pattern
                }, callable, false);
            },
            getAll: function() {
                // Add Snapshot Image control
                var addImageControlSnapshot = function(snapshotId) {
                    // Convert to integer
                    snapshotId = parseInt(snapshotId, 10);
                    
                    // Prepare the control element
                    var controlElementObject = $('<div class="image-control"><span class="glyphicon glyphicon-refresh" data-role="regenerate"></span><img data-role="image" height="75" /></div>');

                    // Set the image source
                    controlElementObject.find('img')
                        .attr('src', '/ajax/project/snapshotImageView/' + _this._projectId + '/' + _this._userId + '/' + snapshotId + '?' + _this._getCacheBuster())
                        .hover(function(){
                            $(this).css({'margin-left': (controlElementObject.width() - $(this).width()) + 'px'});
                        }, function(){
                            $(this).css({'margin-left': '0px'});
                        });
                    
                    // Set the delete action
                    controlElementObject.find('[data-role="regenerate"]').click(function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Perform the ajax
                        _this.ajax(
                            // Upload manager
                            'project/snapshotImageRegenerate/' + _this._projectId + '/' + _this._userId + '/' + snapshotId,
                            {},
                            function(msg){
                                if ("undefined" != typeof msg.status && "success" == msg.status) {
                                    // Re-load the image
                                    controlElementObject.find('img').attr('src', '/ajax/project/snapshotImageView/' + _this._projectId + '/' + _this._userId + '/' + snapshotId + '?' + _this._getCacheBuster());
                                }
                            }
                        );

                        // Stop here
                        return false;
                    });

                    // Set the upload action
                    controlElementObject.click(function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Store the original object
                        var uploadObject = $(this);

                        // Prepare the upload bar
                        var uploadBarObject = $('<div class="upload-bar"><div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div></div>');

                        // Prepare the upload object
                        var formObject = $('<form enctype="multipart/form-data" method="post"><input type="file" name="file" /></form>');

                        // Trigger the event
                        formObject.find('input').click();

                        // Uploading a file
                        formObject.find('input').change(function() {
                            // Add the upload bar
                            uploadObject.append(uploadBarObject);

                            // Perform the ajax
                            _this.ajax(
                                // Upload manager
                                'project/snapshotImageUpload/' + _this._projectId + '/' + _this._userId + '/' + snapshotId,
                                // This is a file
                                new FormData(formObject.get(0)),
                                // Callback on upload finished
                                function(msg) {
                                    // Remove the progress indicator
                                    uploadBarObject.remove();

                                    // Something went wrong
                                    if ("failure" === msg.status) {
                                        tools.notify(msg.result, "warning");
                                    } else {
                                        // All done
                                        msg.content && tools.notify(msg.content);

                                        // Re-load the layers
                                        uploadObject.find('img').attr('src', '/ajax/project/snapshotImageView/' + _this._projectId + '/' + _this._userId + '/' + snapshotId + '?' + _this._getCacheBuster());
                                    }
                                },
                                // Don't show a loading overlay
                                false,
                                // Callback during file upload
                                function(percentage) {
                                    // Show the progress
                                    uploadBarObject.find('.progress-bar').attr('aria-valuenow', percentage).css({
                                        width: percentage + '%',
                                    })
                                }
                            );
                        });
                    });
                    
                    // All done
                    return controlElementObject;
                };
                
                // Get the snapshots
                _this.ajax('project/snapshotsGetAll/' + _this._projectId + '/' + _this._userId, {}, function(msg) {
                    if ("success" == msg.status && "object" == typeof msg.result) {
                        // Get available patterns
                        var availablePatterns = msg.result.patterns;
                        
                        // Prepare the options
                        var patternOptions = '';
                        $.each(availablePatterns, function(k,v){
                            patternOptions += '<option value="' + v + '">' + v + '</option>';
                        });
                        
                        // Override the result
                        msg.result = msg.result.snapshots;
                        
                        // Reset the current snapshot ID
                        _this._snapshotManager._currentSnapshot = null;
                        
                        // Get the current ID
                        var currentSnapshotFromCookie = _this._cookieManager.getCookie('snapshotId');
                        
                        // Not an empty string
                        if ("" != currentSnapshotFromCookie) {
                            // Convert to int
                            currentSnapshotFromCookie = parseInt(currentSnapshotFromCookie, 10);
                            
                            // Found in list
                            var isValid = false;
                            $.each(msg.result, function(k,v) {
                                // ID key defined
                                if ("undefined" != typeof v[WordPress_Snapshots_Snapshot.JSON_ID]) {
                                    if (v[WordPress_Snapshots_Snapshot.JSON_ID] == currentSnapshotFromCookie) {
                                        // Found the value
                                        isValid = true;

                                        // Stop iterator
                                        return;
                                    }
                                }
                            });
                            
                            // Valid ID
                            if (isValid) {
                                _this._snapshotManager._currentSnapshot = currentSnapshotFromCookie;
                            }
                        }
                        
                        // Must find a default
                        if (null == _this._snapshotManager._currentSnapshot) {
                            // Get the first item in the list
                            if ("undefined" != typeof msg.result[0] && "undefined" != typeof msg.result[0][WordPress_Snapshots_Snapshot.JSON_ID]) {
                                _this._snapshotManager._currentSnapshot = msg.result[0][WordPress_Snapshots_Snapshot.JSON_ID];
                            }
                        }
                        
                        // Set the status
                        _this._objectSnapshots.find('.status > .snapshot-id').html(_this._snapshotManager._currentSnapshot);
                        
                        // Prepare the status checker state machine
                        var stageStateMachine = {
                            active: true,
                            object: $('body > .loading > span > p'),
                            next: function(firstRun) {
                                if ("undefined" !== typeof firstRun && firstRun) {
                                    tools.loading.print('<span class="glyphicon glyphicon-flash"></span> Staging snapshot #' + _this._snapshotManager._currentSnapshot + ' of project #' + _this._projectId);
                                }
                                
                                // Perform the next request
                                window.setTimeout(function(){
                                    // Prepare the request
                                    _this.ajax(
                                        'project/stageStatus/' + _this._projectId + '/' + _this._userId,
                                        {},
                                        function(msg) {
                                            do {
                                                if (stageStateMachine.active) {
                                                    // Show the result
                                                    if (null != msg.result && msg.result.length) {
                                                        // Prepare the icon
                                                        var messageIcon = 'flash';
                                                        if (msg.result.match(/^started\b/ig)) {
                                                            messageIcon = 'play';
                                                        }
                                                        if (msg.result.match(/^finished\b/ig)) {
                                                            messageIcon = 'ok';
                                                        }

                                                        tools.loading.print('<span class="glyphicon glyphicon-' + messageIcon + '"></span> ' + msg.result);
                                                    }

                                                    // Go for the next message
                                                    window.setTimeout(stageStateMachine.next, 450);
                                                    break;
                                                }
                                            } while (false);
                                        }, 
                                        false
                                    );
                                }, firstRun ? 2000 : 200);
                            },
                        };
                        
                        // Run the status checker
                        stageStateMachine.next(true);
                        
                        // Stage the current project
                        _this.ajax('project/stage/' + _this._projectId + '/' + _this._userId, {
                            snapshotId: _this._snapshotManager._currentSnapshot,
                        }, function(msg) {
                            stageStateMachine.active = false;

                            // Keep the loading active
                            tools.loading.show();
                            
                            // Close when done
                            _this._objectPreview[0].onload = function(){
                                window.setTimeout(function(){
                                    tools.loading.hide();
                                }, 1000);
                            };
                            
                            // Set the URL
                            _this._objectPreview.attr('src', '//' + _this._sandboxDomain + '/wp-admin/themes.php');
                            
                            // Snapshot save allowed
                            _this._snapshotManager.saveAllowed = true;
                        });
                        
                        // Prepare the form elements
                        var formElements = $('<form class="form-horizontal form-snapshots" data-category="snapshots" auto-complete="off"></form>');
                        
                        // Add the elements
                        $.each(msg.result, function(k,v) {
                            // ID key defined
                            if ("undefined" != typeof v[WordPress_Snapshots_Snapshot.JSON_ID]) {
                                
                                var modelItem = $(
                                    '<div class="snapshot' + (_this._snapshotManager._currentSnapshot == v[WordPress_Snapshots_Snapshot.JSON_ID] ? ' current' : '') + '" data-snapshot-id="' + v[WordPress_Snapshots_Snapshot.JSON_ID] + '">' +
                                        '<h4>' + 
                                            '<div class="input-group">' + 
                                                (_this._snapshotManager._currentSnapshot != v[WordPress_Snapshots_Snapshot.JSON_ID] ? '<div class="input-group-btn"><span class="btn btn-success" data-role="enable" data-toggle="tooltip" data-placement="bottom" title="Activate"><span class="glyphicon glyphicon-ok"></span></span></div>' : '') + 
                                                '<div class="title">' + 
                                                    (_this._snapshotManager._currentSnapshot == v[WordPress_Snapshots_Snapshot.JSON_ID] ? '<strong><span class="glyphicon glyphicon-check"></span> ' : '') + 'Snapshot #' + v[WordPress_Snapshots_Snapshot.JSON_ID] + (_this._snapshotManager._currentSnapshot == v[WordPress_Snapshots_Snapshot.JSON_ID] ? '</strong>' : '') + 
                                                '</div>' + 
                                                '<div class="input-group-btn"><span class="btn btn-info" data-role="add" title="Duplicate" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-duplicate"></span></span></div>' + 
                                                '<div class="input-group-btn"><span class="btn btn-danger" data-role="delete" title="Delete" data-toggle="tooltip" data-placement="bottom"><span class="glyphicon glyphicon-remove"></span></span></div>' + 
                                            '</div>' + 
                                        '</h4>' + 
                                        '<div class="row no-gutters">' +
                                            '<div class="col-sm-2 col-sm-offset-1">' + 
                                                '<div data-role="snapshot-image" title="Snapshot Preview (1200x900)" data-placement="top"></div>' + 
                                            '</div>' + 
                                            '<div class="col-sm-8">' + 
                                                '<div class="row no-gutters">' + 
                                                    '<div class="col-sm-10">' +
                                                        '<input type="text" name="title" title="Snapshot Title" data-placement="top" value="' + tools.php.htmlentities(v[WordPress_Snapshots_Snapshot.JSON_TITLE]) + '" class="form-control" autofocus="" required="" placeholder="Snapshot name" />' +
                                                    '</div>' + 
                                                    '<div class="col-sm-2">' + 
                                                        '<select name="pattern" title="Pattern for masking deliverables" data-toggle="tooltip" data-placement="top" class="form-control">' + patternOptions + '</select>' +
                                                    '</div>' + 
                                                    '<div class="col-sm-12">' + 
                                                        '<input name="description" title="Snapshot Description" data-placement="bottom" class="form-control" autofocus required value="' + tools.php.htmlentities(v[WordPress_Snapshots_Snapshot.JSON_DESCRIPTION]) + '" placeholder="Snapshot description" />' + 
                                                    '</div>' + 
                                                '</div>' + 
                                            '</div>' + 
                                        '</div>' + 
                                    '</div>'
                                );
                        
                                // Add the image control
                                modelItem.find('[data-role="snapshot-image"]').append(addImageControlSnapshot(v[WordPress_Snapshots_Snapshot.JSON_ID]));
                                
                                // Select the current pattern
                                modelItem.find('[name="pattern"]').val(v[WordPress_Snapshots_Snapshot.JSON_PATTERN]);
                                
                                // Update the preview
                                modelItem.css('background-image', 'url(/ajax/project/snapshotImageView/' + _this._projectId + '/' + _this._userId + '/' + v[WordPress_Snapshots_Snapshot.JSON_ID]+ '/pattern?' + _this._getCacheBuster() + ')');
                                
                                // Save on change
                                modelItem.find('input,select').change(function() {
                                    var title = modelItem.find('[name="title"]').val();
                                    var description = modelItem.find('[name="description"]').val();
                                    var pattern = modelItem.find('[name="pattern"]').val();
                                    
                                    // Update the snapshot details
                                    _this._snapshotManager.update(
                                        v[WordPress_Snapshots_Snapshot.JSON_ID],
                                        title,
                                        description,
                                        pattern,
                                        function(msg) {
                                            // Nothing to do
                                            modelItem.css('background-image', 'url(/ajax/project/snapshotImageView/' + _this._projectId + '/' + _this._userId + '/' + v[WordPress_Snapshots_Snapshot.JSON_ID]+ '/pattern?' + _this._getCacheBuster() + ')');
                                        }
                                    );
                                });
                                
                                // Enable the tooltips
                                modelItem.find('[title]').tooltip();
                                
                                // Set actions
                                modelItem.find('[data-role=add]').click(function() {
                                    if (confirm('Are you sure you want to duplicate snapshot #' + v[WordPress_Snapshots_Snapshot.JSON_ID] + '?')) {
                                       _this._snapshotManager.create(v[WordPress_Snapshots_Snapshot.JSON_ID]);
                                       _this._snapshotManager._modalBody.parents('.modal').modal('hide');
                                    }
                                });
                                modelItem.find('[data-role=enable]').click(function() {
                                    _this._snapshotManager.activate(v[WordPress_Snapshots_Snapshot.JSON_ID]);
                                    _this._snapshotManager._modalBody.parents('.modal').modal('hide');
                                });
                                modelItem.find('[data-role=delete]').click(function() {
                                    if (confirm('Are you sure you want to delete snapshot #' + v[WordPress_Snapshots_Snapshot.JSON_ID] + '?')) {
                                        _this._snapshotManager.delete(v[WordPress_Snapshots_Snapshot.JSON_ID]);
                                        _this._snapshotManager._modalBody.parents('.modal').modal('hide');
                                    }
                                });
                                
                                // Append the item
                                formElements.append(modelItem);
                            }
                        });
                        
                        // Prepare the add button
                        var addButton = $(
                            '<span class="btn btn-success btn-add-blank" title="Add a blank snapshot: no posts, no media etc." data-toggle="tooltip" data-placement="top"><span class="glyphicon glyphicon-plus"></span> Add a blank snapshot</span>'
                        );
                
                        // Set the tooltip
                        addButton.tooltip();
                
                        // Set the action
                        addButton.click(function() {
                            _this._snapshotManager.create();
                            _this._snapshotManager._modalBody.parents('.modal').modal('hide');
                        });
                
                        // Append the button
                        formElements.append(addButton);
                        
                        // Add the form elements
                        _this._snapshotManager._modalBody.html('');
                        _this._snapshotManager._modalBody.append(formElements);
                    }
                }, false);
            },
            
            save: function() {
                // Cannot save yet
                if (!this.saveAllowed) {
                    return;
                }

                // Cannot save when something else is up
                if ($('body > .loading').is(':visible')) {
                    return;
                }

                // Prevent multiple actions
                if (_this._objectSnapshots.find('.status').hasClass('isloading')) {
                    return;
                }

                // Set the flag
                _this._objectSnapshots.find('.status').addClass('isloading');
                
                // Capture a snapshot
                _this.ajax('project/snapshotCapture/' + _this._projectId + '/' + _this._userId, {
                    snapshotId: _this._snapshotManager._currentSnapshot,
                }, function(msg) {
                    // Remove the flag
                    _this._objectSnapshots.find('.status').removeClass('isloading');
                }, false);
            },
        };
        
        this._projectId = null;
        this._userId = null;
        this._sandboxDomain = null;
        
        // Get the project information
        this._getData = function() {
            // Get the data
            _this.ajax('project/getData/' + this._projectId + '/' + this._userId, {}, function(msg) {
                // All went well
                if (msg.status == "success") {
                    // Load the preview
                    if ("undefined" != typeof msg["result"] && "undefined" != typeof msg["result"]["sandboxDomain"]) {
                        // Store the domain
                        _this._sandboxDomain = msg["result"]["sandboxDomain"];
                        
                        // Generate the menu
                        _this._updateMenu(msg["result"]["config"]);

                        // Get the snapshot data
                        _this._snapshotManager.getAll();
                        
                        // Check task is still allowed; do not re-mark the current project
                        window.setInterval(function() {
                            _this.ajax('project/getPath/' + _this._projectId + '/' + _this._userId, {}, function(msg) {}, false);
                        }, 5000);
                    }
                }
            }, false);
        };
        
        // Prepare a cache buster
        this._getCacheBuster = function() {
            return 'z=' + Math.random();
        };
        
        // Menu update utility
        this._updateMenu = function(config) {
            // Remove all modals
            $('body > .modal, body > .modal-backdrop').remove();
            $('body').removeClass('modal-open');
            
            // Prepare the menu
            var menuObject = $('<div class="menu"></div>');

            // Prepare the home menu item
            var menuItemHome = $('<a class="menu-item menu-item-back noselect" href="/" data-toggle="tooltip" data-placement="right" title="Back"><span class="glyphicon glyphicon-chevron-left"></span><span class="icon"></span></a>');
            
            // Tooltips
            menuItemHome.tooltip();
            
            // Add the icon
            menuObject.append(menuItemHome);

            // HTML Helper
            var htmlHelper = {
                uiLabel: function(labelObject, editable, projectItem) {
                    // Defaults
                    if ("undefined" === typeof editable) {
                        editable = false;
                    }
                    if ("undefined" === typeof projectItem) {
                        projectItem = null;
                    }
                    
                    // Color
                    if ("undefined" !== projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                        if (!editable && "color" === projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                            // Get the tiny color
                            var tinyColor = new tinycolor(labelObject.find('input').val(), "hex8");

                            // Prepare the object
                            var colorBlock = $("<div class='sp-preview sp-preview-check-radio'><div class='sp-preview-inner'></div></div>");
                            
                            // Set the color block's background
                            colorBlock.find('.sp-preview-inner').css({'background-color': tinyColor.toRgbString()});

                            // Prepend the color block
                            colorBlock.insertAfter(labelObject.find('input'));
                        }
                    }
                    
                    // Decorator
                    var decorate = function() {
                        // Deactivate the current label
                        labelObject.removeClass('checkbox-active');
                        
                        // Deactivate other radio elements' labels
                        if (labelObject.find('input[type=radio]').length) {
                            labelObject.siblings().removeClass('checkbox-active');
                        }
                        
                        // Current label should be activated
                        if (labelObject.find('input[type=checkbox],input[type=radio]').prop('checked')) {
                            labelObject.addClass('checkbox-active');
                        }
                    };
                    
                    // Trigger the decoration
                    labelObject.click(decorate);
                    
                    // Run the tool now
                    decorate();
                },
                restrictInt: function(controlElementObject, projectItem) {
                    controlElementObject.attr('type', 'number');
                            
                    // Set the minimum accepted value
                    if (null !== projectItem[Model_Project_Config_Item.JSON_INT_MIN]) {
                        controlElementObject.attr('min', projectItem[Model_Project_Config_Item.JSON_INT_MIN]);
                    }

                    // Set the maximum accepted value
                    if (null !== projectItem[Model_Project_Config_Item.JSON_INT_MAX]) {
                        controlElementObject.attr('max', projectItem[Model_Project_Config_Item.JSON_INT_MAX]);
                    }

                    // Extra precautions
                    if (null !== projectItem[Model_Project_Config_Item.JSON_INT_MAX] || null !== projectItem[Model_Project_Config_Item.JSON_INT_MIN]) {
                        // Listen for changes
                        controlElementObject.keyup(function(){
                            // Get the current value
                            var value = parseInt($(this).val(), 10);

                            // Minimum
                            if (null !== projectItem[Model_Project_Config_Item.JSON_INT_MIN] && value < projectItem[Model_Project_Config_Item.JSON_INT_MIN]) {
                                $(this).val(projectItem[Model_Project_Config_Item.JSON_INT_MIN]);
                            }

                            // Maximum
                            if (null !== projectItem[Model_Project_Config_Item.JSON_INT_MAX] && value > projectItem[Model_Project_Config_Item.JSON_INT_MAX]) {
                                $(this).val(projectItem[Model_Project_Config_Item.JSON_INT_MAX]);
                            }
                        });
                    }
                },
                addImageControl: function(projectItem, category, imageValue, newElement) {
                    // Is this a new element?
                    newElement = "undefined" == typeof newElement ? false : true === newElement;
                    
                    // Prepare the control element
                    var controlElementObject = $('<div class="image-control"><span class="glyphicon glyphicon-remove" data-role="delete"></span><img data-role="image" height="80" /></div>');

                    // Prepare the image key
                    var imageKey = parseInt(imageValue.replace(/^.*?\-(\d+)\.\w+$/g, '$1'), 10);
                    
                    // Prepare the image key
                    var getImageKey = function() {
                        return isNaN(imageKey) ? 0 : imageKey;
                    };

                    // Set the image source
                    controlElementObject.find('img')
                        .attr('src', '/ajax/project/imageView/' + _this._projectId + '/' + _this._userId + '/' + category + '/' + projectItem[Model_Project_Config_Item.JSON_KEY] + '/' + getImageKey() + '?' + _this._getCacheBuster())
                        .hover(function(){
                            $(this).css({'margin-left': (controlElementObject.width() - $(this).width()) + 'px'});
                        }, function(){
                            $(this).css({'margin-left': '0px'});
                        });
                    controlElementObject.attr('data-image-key', getImageKey());
                    
                    // New element
                    if (projectItem[Model_Project_Config_Item.JSON_IS_LIST] && newElement) {
                        controlElementObject.attr('data-image-new', 'true');
                    }
                    
                    // Set the delete action
                    controlElementObject.find('[data-role="delete"]').click(function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Simply remove new items
                        if (projectItem[Model_Project_Config_Item.JSON_IS_LIST] && newElement) {
                            controlElementObject.remove();
                            return false;
                        }
                        
                        // Perform the ajax
                        _this.ajax(
                            // Upload manager
                            'project/imageDelete/' + _this._projectId + '/' + _this._userId + '/' + category + '/' + projectItem[Model_Project_Config_Item.JSON_KEY] + '/' + getImageKey(),
                            {},
                            function(msg){
                                if ("undefined" != typeof msg.status && "success" == msg.status) {
                                    // Remove the image
                                    if (projectItem[Model_Project_Config_Item.JSON_IS_LIST]) {
                                        controlElementObject.remove();
                                    } else {
                                        // Re-load the image
                                        controlElementObject.find('img').attr('src', '/ajax/project/imageView/' + _this._projectId + '/' + _this._userId + '/' + category + '/' + projectItem[Model_Project_Config_Item.JSON_KEY] + '/' + getImageKey() + '?' + _this._getCacheBuster());
                                    }
                                }
                            }
                        );

                        // Stop here
                        return false;
                    });

                    // Set the upload action
                    controlElementObject.click(function(e) {
                        e.preventDefault();
                        e.stopPropagation();

                        // Store the original object
                        var uploadObject = $(this);

                        // Prepare the upload bar
                        var uploadBarObject = $('<div class="upload-bar"><div class="progress"><div class="progress-bar progress-bar-striped active" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div></div></div>');

                        // Prepare the upload object
                        var formObject = $('<form enctype="multipart/form-data" method="post"><input type="file" name="file" /></form>');

                        // Trigger the event
                        formObject.find('input').click();

                        // Uploading a file
                        formObject.find('input').change(function() {
                            // Add the upload bar
                            uploadObject.append(uploadBarObject);

                            // Perform the ajax
                            _this.ajax(
                                // Upload manager
                                'project/imageUpload/' + _this._projectId + '/' + _this._userId + '/' + category + '/' + projectItem[Model_Project_Config_Item.JSON_KEY] + '/' + getImageKey(),
                                // This is a file
                                new FormData(formObject.get(0)),
                                // Callback on upload finished
                                function(msg) {
                                    // Remove the progress indicator
                                    uploadBarObject.remove();

                                    // Something went wrong
                                    if ("failure" === msg.status) {
                                        tools.notify(msg.result, "warning");
                                    } else {
                                        // All done
                                        msg.content && tools.notify(msg.content);

                                        // Re-load the layers
                                        uploadObject.find('img').attr('src', '/ajax/project/imageView/' + _this._projectId + '/' + _this._userId + '/' + category + '/' + projectItem[Model_Project_Config_Item.JSON_KEY] + '/' + getImageKey() + '?' + _this._getCacheBuster());
                                        
                                        // No longer a new element
                                        if (projectItem[Model_Project_Config_Item.JSON_IS_LIST] && newElement) {
                                            newElement = false;
                                            controlElementObject.removeAttr('data-image-new');
                                        }
                                    }
                                },
                                // Don't show a loading overlay
                                false,
                                // Callback during file upload
                                function(percentage) {
                                    // Show the progress
                                    uploadBarObject.find('.progress-bar').attr('aria-valuenow', percentage).css({
                                        width: percentage + '%',
                                    })
                                }
                            );
                        });
                    });
                    
                    // All done
                    return controlElementObject;
                },
                addImageControlPlus: function(projectItem, category) {
                    // Prepare the control element
                    var controlObject = $('<div class="image-control-plus"><span class="glyphicon glyphicon-plus"></span></div>');
                    
                    // On-click
                    controlObject.click(function(){
                        // Get the max ID
                        var maxId = -1;
                        $.each(controlObject.parent().find('.image-control'), function(){
                            // Do not add more than 1 new image at a time
                            if ('true' == $(this).attr('data-image-new')) {
                                maxId = false;
                                return;
                            }
                            
                            // Get the ID
                            var id = parseInt($(this).attr('data-image-key'), 10);
                            
                            // Store the max value
                            if (id > maxId) {
                                maxId = id;
                            }
                        });
                        
                        // Add the control element
                        if (false !== maxId) {
                            htmlHelper.addImageControl(projectItem, category, 'image-' + (maxId+1) + '.png', true).insertBefore(controlObject);
                        }
                    });
                    
                    // All done
                    return controlObject;
                },
                addEditableCheckboxes: function(projectItem, category) {
                    // Checkbox group
                    var controlElementObject = $('<div class="well well-xsm"></div>');
                    
                    // Colors are uppercased
                    if ('color' === projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                        controlElementObject.addClass('uppercased');
                    }
                    
                    // Checkbox creator
                    var createCheckbox = function(optionValue, editable) {
                        // Set the default for the optionValue
                        if ("undefined" == typeof optionValue) {
                            optionValue = '';
                        }
                        
                        // Set the default for editable
                        if ("undefined" == typeof editable) {
                            editable = false;
                        }
                        
                        // Custom text defined
                        var optionText = optionValue;
                        if ("undefined" !== typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS] && "undefined" !== typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue] && "string" === typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue][0]) {
                            optionText = tools.php.strip_tags(projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue][0]);
                        }
                        
                        // Prepare the checkbox
                        var optionObject = $(
                            '<label class="btn btn-default"><input name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '[]" type="checkbox" value="' + tools.php.htmlentities(optionValue) + '">' + (editable ? '' : ' ' + optionText) + '</label>'
                        );

                        // Mark as checked
                        if ("object" == typeof projectItem[Model_Project_Config_Item.JSON_VALUE]) {
                            $.each(projectItem[Model_Project_Config_Item.JSON_VALUE], function(k, userValue) {
                                if (optionValue == userValue) {
                                    optionObject.find('input[type=checkbox]').prop('checked', true);
                                    return;
                                }
                            });
                        }
                        
                        // Editable item
                        if (editable) {
                            // Prepare the input field
                            var checkboxInput = $('<input type="text" class="checkbox-input form-control" value="' + tools.php.htmlentities(optionValue) + '"/>');
                            optionObject.find('input[type=checkbox]').prop('checked', true).css({display: 'none'});
                            
                            // Restrict the integer field
                            if ('int' == projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                                htmlHelper.restrictInt(checkboxInput, projectItem);
                            }
                            
                            // Set the listener
                            checkboxInput.keyup(function(){
                                $(this).parents('label').find('input[type=checkbox]').val($(this).val());
                            });
                            
                            // Prepare the remove tag
                            var checkboxClose = $('<span class="glyphicon glyphicon-remove" data-role="remove"></span>');
                            
                            // Set the listener
                            checkboxClose.click(function(){
                                optionObject.remove();
                            });
                            
                            // Append the controllers
                            optionObject.addClass('label-with-input').append(checkboxInput).append(checkboxClose);
                            
                            // Color field
                            if ('color' == projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                                optionObject.addClass('input-group');
                                checkboxClose.addClass('input-group-addon');
                                checkboxInput.spectrum({
                                    showAlpha: true,
                                    showInitial: true,
                                    appendTo: "parent",
                                    preferredFormat: "hex8",
                                });
                            }
                        }

                        // Enhance the label
                        htmlHelper.uiLabel(optionObject, editable, projectItem);
                        
                        // All done
                        return optionObject;
                    };
                    
                    // Plus button creator
                    var createPlusButton = function() {
                        // Prepare the plus object
                        var plusObject = $('<span class="glyphicon glyphicon-plus" data-role="checkbox-plus"></span>');

                        // Add a new element
                        plusObject.click(function(){
                            createCheckbox("", true).insertBefore(plusObject);
                        });
                        
                        // All done
                        return plusObject;
                    };
                    
                    // Prepare the extra values
                    var extraValues = projectItem[Model_Project_Config_Item.JSON_VALUE];
                        
                    // Items available
                    if (projectItem[Model_Project_Config_Item.JSON_OPTIONS].length > 0) {
                        // Add the checkboxes
                        $.each(projectItem[Model_Project_Config_Item.JSON_OPTIONS], function(k, optionValue) {
                            // Append the option
                            controlElementObject.append(createCheckbox(optionValue));
                            
                            // Prepare the extra values
                            if ("object" === typeof extraValues) {
                                extraValues = tools.php.removeFirst(extraValues, optionValue);
                            }
                        });

                        // Non-strict, user options allowed
                        if (!projectItem[Model_Project_Config_Item.JSON_OPTIONS_STRICT]) {
                            if ("object" == typeof projectItem[Model_Project_Config_Item.JSON_VALUE]) {
                                $.each(extraValues, function(k, optionValue) {
                                    // Append the option
                                    controlElementObject.append(createCheckbox(optionValue, true));
                                });
                            }

                            // Append the plus
                            controlElementObject.append(createPlusButton());
                        }
                    } else {
                        // Valid values defined
                        if ("object" == typeof projectItem[Model_Project_Config_Item.JSON_VALUE]) {
                            $.each(projectItem[Model_Project_Config_Item.JSON_VALUE], function(k, optionValue) {
                                // Append the option
                                controlElementObject.append(createCheckbox(optionValue, true));
                            });
                        }
                        
                        // Append the plus
                        controlElementObject.append(createPlusButton());
                    }
                    
                    // All done
                    return controlElementObject;
                },
                addBooleanControl: function(projectItem, category) {
                    // Checkbox group
                    var controlElementObject = $('<input type="checkbox" name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" />');
                    
                    // Store the state
                    controlElementObject.prop('checked', projectItem[Model_Project_Config_Item.JSON_VALUE]);

                    // Prepare the holder
                    var controlHolder = $(
                        '<div data-role="toggle" data-state="' + (projectItem[Model_Project_Config_Item.JSON_VALUE] ? 'on' : 'off') + '">'
                            + '<div class="switch">'
                                + '<b class="on"><span class="glyphicon glyphicon-ok"><span></b>'
                                + '<i><span class="glyphicon glyphicon-option-vertical"><span></i>'
                                + '<b class="off"><span class="glyphicon glyphicon-remove"></span></b>'
                            +'</div>'
                        + '</div>'
                    );
                    
                    // Udpdate the dependencies
                    var updateDependencies = function(newState) {
                        // Update all dependencies
                        $.each($('[data-depends="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '"]'), function(){
                            if (newState) {
                                $(this).slideDown();
                            } else {
                                $(this).slideUp();
                            }
                        });
                    };
                    
                    // Append the element
                    controlHolder.append(controlElementObject);
                    
                    // Toggle events
                    controlHolder.click(function() {
                        // Get the current state
                        var currentState = ('on' == $(this).attr('data-state'));
                        
                        // Change the state
                        $(this).attr('data-state', currentState ? 'off' : 'on');
                        
                        // Update the control
                        $(this).find('input').prop('checked', !currentState);
                        
                        // Update the dependencies
                        updateDependencies(!currentState);
                    });
                    
                    // All done
                    return controlHolder;
                }
            };

            // Add each configuration item
            $.each(config, function(category, details) {
                // Prepare the menu icon symbol
                var menuIconSymbol = '';
                
                // Prepare the gyphicon
                var glyphicon = 'cog';
                
                // Prepare the label
                var categoryLabel = '<strong>' + tools.php.ucfirst(category.replace(/-/g, ' ')) + '</strong> settings';
                
                // Prepare the headers
                var categoryHeaders = {};
                
                // Addon
                if (category.match(/^addon\-/g)) {
                    glyphicon = 'flash';
                    categoryLabel = 'Add-on <strong>' + category.replace(/^addon\-/g, '') + '</strong> settings';
                    
                    // Framework Configuration
                    if (category == ('addon-' + config.core.projectFramework.v)) {
                        glyphicon = 'dashboard';
                        categoryLabel = 'Framework <strong>' + category.replace(/^addon\-/g, '') + '</strong> settings';
                    }
                }
                
                // Visual composer
                if (category.match(/^addon\-core\-vc\-/)) {
                    menuIconSymbol = '<svg width="12px" height="12px" viewBox="0 0 66 50" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g id="Page-1" stroke="none" stroke-width="1" fill="#ffffff" fill-rule="evenodd"><path d="M51.3446356,9.04135214 C46.8606356,8.68235214 44.9736356,9.78835214 42.8356356,10.0803521 C45.0046356,11.2153521 47.9606356,12.1793521 51.5436356,11.9703521 C48.2436356,13.2663521 42.8866356,12.8233521 39.1886356,10.5643521 C38.2256356,9.97535214 37.2136356,9.04535214 36.4556356,8.30235214 C33.4586356,5.58335214 31.2466356,0.401352144 21.6826356,0.0183521443 C9.68663559,-0.456647856 0.464635589,8.34735214 0.0156355886,19.6453521 C-0.435364411,30.9433521 8.92563559,40.4883521 20.9226356,40.9633521 C21.0806356,40.9713521 21.2386356,40.9693521 21.3946356,40.9693521 C24.5316356,40.7853521 28.6646356,39.5333521 31.7776356,37.6143521 C30.1426356,39.9343521 24.0316356,42.3893521 20.8506356,43.1673521 C21.1696356,45.6943521 22.5216356,46.8693521 23.6306356,47.6643521 C26.0896356,49.4243521 29.0086356,46.9343521 35.7406356,47.0583521 C39.4866356,47.1273521 43.3506356,48.0593521 46.4746356,49.8083521 L49.7806356,38.2683521 C58.1826356,38.3983521 65.1806356,32.2053521 65.4966356,24.2503521 C65.8176356,16.1623521 59.9106356,9.72335214 51.3446356,9.04135214 L51.3446356,9.04135214 Z" id="Fill-41" fill="#0073aa"></path></g></svg>';
                }
                
                // Add an addon
                if (category == "plus") {
                    glyphicon = 'plus';
                    menuIconSymbol = '+';
                    categoryLabel = 'Enable an add-on';
                }
                
                // Custom icon
                if ("undefined" != typeof details[Model_Project_Config_Item.KEY_ICON]) {
                    // Custom value provided
                    if ("string" == typeof details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_VALUE]) {
                        // The framework default icon is "flash"; override it
                        if (category == ('addon-' + config.core.projectFramework.v) && 'flash' == details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_VALUE]) {
                            glyphicon = 'dashboard';
                        } else {
                            glyphicon = details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_VALUE];
                        }
                    }
                    
                    // Custom title defined
                    if ("string" == typeof details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_TITLE] && details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_TITLE].length) {
                        categoryLabel = details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_TITLE];
                    }
                    
                    // Custom icon letter defined
                    if ("string" == typeof details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_SYMBOL] && details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_SYMBOL].length) {
                        menuIconSymbol = details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_SYMBOL];
                    }
                }
                
                // Prepare the menu item
                var menuItem = $('<div class="menu-item noselect category-' + category + '" data-toggle="tooltip" data-placement="right" title="' + tools.php.htmlentities(tools.php.strip_tags(categoryLabel)) + '"><span class="glyphicon glyphicon-' + glyphicon + '"></span>' + (menuIconSymbol.length ? ('<span class="letter">' + menuIconSymbol + '</span>') : '') + '</div>');
                
                // Tooltips
                menuItem.tooltip();
                
                // Prepare the form elements
                var formElements = $('<form class="form-horizontal" data-category="' + category + '" auto-complete="off"><input type="submit" class="hidden-input" /></form>');
                formElements.submit(function(e) {
                    // Prevent the default
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Snapshot re-save not allowed until the form finishes loading
                    _this._snapshotManager.saveAllowed = false;

                    // Update the project configuration
                    _this.ajax('project/categoryUpdate/' + _this._projectId + '/' + _this._userId + '/' + $(this).attr('data-category'), $(this).serializeAssoc(), function(msg) {
                        // Close the modal
                        formElements.parents('.modal').modal('hide');
                        
                        // All went well
                        if (msg.status === "success") {
                            // Refresh the layout
                            _this._getData();
                        }
                    });
                    
                    // Stop here
                    return false;
                });
                
                // Go through the details
                $.each(details, function(k, projectItem){
                    // Ignore the internal keys (prefixed with _, except for specific keys)
                    if (
                        projectItem[Model_Project_Config_Item.JSON_KEY].match(/^_/g) && 
                        -1 === [Model_Project_Config_Item.KEY_FLAVOR].indexOf(projectItem[Model_Project_Config_Item.JSON_KEY])) {
                        return;
                    }
                    
                    // Same options as this other object
                    if(projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_CLONE].length) {
                        // Other object defined
                        if ("undefined" !== details[projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_CLONE]]) {
                            // Copy-over the options
                            projectItem[Model_Project_Config_Item.JSON_OPTIONS] = details[projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_CLONE]][Model_Project_Config_Item.JSON_OPTIONS];
                        }
                    }
                    
                    // Prepare the control
                    var controlElementObject = $('<input type="text" name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" value="' + tools.php.htmlentities(projectItem[Model_Project_Config_Item.JSON_VALUE]) + '" class="form-control" autofocus required />');
                    
                    // Disabled
                    if (projectItem[Model_Project_Config_Item.JSON_IS_DISABLED]) {
                        controlElementObject.attr('disabled', 'disabled');
                    }
                    
                    // Prepare the optionable flag
                    var controlOptionable = false;
                    
                    // Implement different control types
                    switch(projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                        case 'int':
                            // Restrict the integer field
                            htmlHelper.restrictInt(controlElementObject, projectItem);
                            
                            // "options" supported
                            controlOptionable = true;
                            break;
                            
                        case 'string':
                            // Set as textarea
                            if (projectItem[Model_Project_Config_Item.JSON_IS_TEXTAREA]) {
                                controlElementObject = $('<textarea name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" class="form-control" autofocus required rows="3">' + projectItem[Model_Project_Config_Item.JSON_VALUE] + '</textarea>');
                            }
                            
                            // Markdown
                            if (projectItem[Model_Project_Config_Item.JSON_IS_MARKDOWN]) {
                                controlElementObject = $('<div class="markdown-holder"><textarea name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" class="form-control" autofocus required rows="3">' + projectItem[Model_Project_Config_Item.JSON_VALUE] + '</textarea><div class="preview form-control well"></div></div>');
                            }
                            
                            // "options" supported
                            controlOptionable = true;
                            break;
                            
                        case 'font':
                            // "options" supported
                            controlOptionable = true;
                            break;
                            
                        case 'color':
                            // "options" supported
                            controlOptionable = true;
                            break;
                        
                        case 'bool':
                            // Prepare the holder
                            controlElementObject = htmlHelper.addBooleanControl(projectItem, category);
                            break;
                        
                        // No support for "options" (value alternatives)
                        case 'image':
                            // Prepare the holder
                            controlElementObject = $('<div class="image-holder"></div>');
                            
                            // Prepare the images list
                            var images = projectItem[Model_Project_Config_Item.JSON_IS_LIST] ? projectItem[Model_Project_Config_Item.JSON_VALUE] : [projectItem[Model_Project_Config_Item.JSON_VALUE]];
                            
                            // Add the image control elements
                            $.each(images, function(k,v) {
                                // Append the item
                                controlElementObject.append(htmlHelper.addImageControl(projectItem, category, v));
                            });
                            
                            // List
                            if (projectItem[Model_Project_Config_Item.JSON_IS_LIST]) {
                                controlElementObject.addClass('well well-xsm');
                                controlElementObject.append(htmlHelper.addImageControlPlus(projectItem, category));
                            }
                            break;
                        
                        // No support for "options" (value alternatives) or "list" (array of values)
                        case 'code':
                            controlElementObject = $('<textarea data-code-ext="' + projectItem[Model_Project_Config_Item.JSON_EXTENSION] + '" name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" class="form-control" autofocus required rows="3">' + projectItem[Model_Project_Config_Item.JSON_VALUE] + '</textarea>');
                            break;
                    }

                    // A list of options is available
                    if (controlOptionable) {
                        // List of elements
                        if (projectItem[Model_Project_Config_Item.JSON_IS_LIST]) {
                            // Prepare the checkboxes
                            controlElementObject = htmlHelper.addEditableCheckboxes(projectItem);
                        } else {
                            // Options defined
                            if (projectItem[Model_Project_Config_Item.JSON_OPTIONS].length > 0) {
                                // Strict selection
                                if (projectItem[Model_Project_Config_Item.JSON_OPTIONS_STRICT]) {
                                    // Select element
                                    controlElementObject = $('<select name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" class="form-control" autofocus="" required=""></select>');
                                    $.each(projectItem[Model_Project_Config_Item.JSON_OPTIONS], function(k, optionValue) {
                                        // Prepare the option text
                                        var optionText = optionValue;
                                        
                                        // Custom text defined
                                        if ("undefined" !== typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS] && "undefined" !== typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue] && "string" === typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue][0]) {
                                            optionText = tools.php.strip_tags(projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue][0]);
                                        }

                                        // Prepare the option
                                        var optionObject = $('<option value="' + tools.php.htmlentities(optionValue) + '">' + tools.php.htmlentities(optionText) + '</option>');

                                        // Current
                                        if (optionValue == projectItem[Model_Project_Config_Item.JSON_VALUE]) {
                                            optionObject.attr('selected', 'selected');
                                        }

                                        // Append the option
                                        controlElementObject.append(optionObject);
                                    });
                                } else {
                                    // Value already selected
                                    var radioSelected = false;
                                    
                                    // Radio group
                                    controlElementObject = $('<div class="well well-xsm"></div>');
                                    
                                    // Colors are uppercased
                                    if ('color' === projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                                        controlElementObject.addClass('uppercased');
                                    }
                    
                                    $.each(projectItem[Model_Project_Config_Item.JSON_OPTIONS], function(k, optionValue) {
                                        // Prepare the radio ID
                                        var radioId = category + '-' + projectItem[Model_Project_Config_Item.JSON_KEY] + '-' + k;
                                        
                                        // Custom text
                                        var optionText = optionValue;
                                        if ("undefined" !== typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS] && "undefined" !== typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue] && "string" === typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue][0]) {
                                            optionText = tools.php.strip_tags(projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][optionValue][0]);
                                        }
                                        
                                        // Prepare the option
                                        var radioObject = $('<label class="btn btn-default" for="' + radioId + '"><input type="radio" name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" value="' + tools.php.htmlentities(optionValue) + '" id="' + radioId + '" /> ' + (optionText) + '</label>');
                                        
                                        // Current
                                        if (optionValue == projectItem[Model_Project_Config_Item.JSON_VALUE]) {
                                            radioObject.find('input[type=radio]').prop('checked', true);
                                            radioSelected = true;
                                        }
                                        
                                        // Enhance the label
                                        htmlHelper.uiLabel(radioObject, false, projectItem);
                                        
                                        // Append the option
                                        controlElementObject.append(radioObject);
                                        
                                    });
                                    
                                    // Add the radio-input element
                                    var radioInputObjectValue = radioSelected ? '' : projectItem[Model_Project_Config_Item.JSON_VALUE];
                                    var radioInputObjectId = category + '-' + projectItem[Model_Project_Config_Item.JSON_KEY] + '-x';
                                    var radioInputObject = $('<label class="btn btn-default label-with-input" for="' + radioInputObjectId + '"><input type="radio" name="' + projectItem[Model_Project_Config_Item.JSON_KEY] + '" value="' + tools.php.htmlentities(radioInputObjectValue) + '" id="' + radioInputObjectId + '" /> <input type="text" class="form-control" value="' + tools.php.htmlentities(radioInputObjectValue) + '"/></label>');
                                        
                                    if (!radioSelected) {
                                        radioInputObject.find('input[type=radio]').prop('checked', true);
                                    }
                                    
                                    // Update the radio element's value
                                    radioInputObject.find('input[type=text]').keyup(function(){
                                        $(this).parents('label').find('input[type=radio]').val($(this).val());
                                    }).click(function(e){
                                        $(this).parents('label').find('input[type=radio]').prop('checked', true);
                                    });
                                    
                                    // Restrict the integer field
                                    if ('int' == projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                                        htmlHelper.restrictInt(radioInputObject.find('input[type=text]'), projectItem);
                                    }
                                    
                                    // Enhance the label
                                    htmlHelper.uiLabel(radioInputObject, true, projectItem);
                                    
                                    // Append the option
                                    controlElementObject.append(radioInputObject);
                                    
                                    // Color field
                                    if ('color' == projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                                        radioInputObject.addClass('color-radio');
                                        radioInputObject.find('input[type=text]').spectrum({
                                            showAlpha: true,
                                            showInitial: true,
                                            appendTo: "parent",
                                            preferredFormat: "hex8",
                                        });
                                    }
                                }
                            }
                        }
                        
                        // Use a picker - not available for Strict options (drop-down menu)
                        if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_PICKER] && projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_PICKER]) {
                            if (controlElementObject.hasClass('well')) {
                                // Prepare the search element
                                var searchElement = $('<input class="form-control form-options-picker" placeholder="Type to search for a \'' + projectItem[Model_Project_Config_Item.JSON_TYPE] + '\' item..."/>');
                                
                                // Get all the labels
                                var labels = controlElementObject.children('label');
                                
                                // Hide inactive checkboxes
                                labels.not('.checkbox-active').css({display: 'none'});
                                
                                // Search
                                searchElement.keyup(function(){
                                    // Get the trimmed search value
                                    var textQuoted = $(this).val().replace(/(^\s+|\s+$)/ig, '').replace(new RegExp('[.\\\\+*?\\[\\^\\]$(){}=!<>|:\\-]', 'ig'), '\\$&');
                                    
                                    // Go through all the inactive labels
                                    $.each(labels.not('.checkbox-active'), function(k,v){
                                        $(v).css({
                                            display: (textQuoted.length && $(v).text().match(new RegExp(textQuoted, 'ig'))) ? 'inline-block' : 'none'
                                        });
                                    });
                                });
                                
                                // Trigger a search to re-hide inactive labels
                                labels.click(function(){
                                    searchElement.keyup();
                                });
                                
                                // Append the search element
                                controlElementObject.prepend(searchElement);
                            }
                        }
                    }
                    
                    // Prepare the label
                    var projectItemLabel = tools.php.ucfirst(projectItem[Model_Project_Config_Item.JSON_KEY].replace(/([A-Z])/g, ' $1'));
                    
                    // Custom label
                    if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_TITLE] && projectItem[Model_Project_Config_Item.JSON_META_TITLE].length) {
                        projectItemLabel = tools.php.ucfirst(projectItem[Model_Project_Config_Item.JSON_META_TITLE]);
                    }
                    
                    // Implement different control types
                    switch(projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                        case 'int':
                            // Prepare the integer field limits
                            var intLimits = [];
                            
                            // Minimum restriction defined
                            if (null !== projectItem[Model_Project_Config_Item.JSON_INT_MIN]) {
                                intLimits[intLimits.length] = 'min. <b>' + projectItem[Model_Project_Config_Item.JSON_INT_MIN] + '</b>';
                            }
                            
                            // Maximum restriction defined
                            if (null !== projectItem[Model_Project_Config_Item.JSON_INT_MAX]) {
                                intLimits[intLimits.length] = 'max. <b>' + projectItem[Model_Project_Config_Item.JSON_INT_MAX] + '</b>';
                            }
                            
                            // Restrictions were imposed
                            if (intLimits.length > 0) {
                                projectItemLabel += '<br/><span class="small">' + intLimits.join(', ') + '</span>';
                            }
                            break;
                        
                        case 'string':
                            // Optional string
                            if (projectItem[Model_Project_Config_Item.JSON_STRING_OPTIONAL]) {
                                projectItemLabel += '<br/><span class="small"><b>Optional</b></span>';
                                controlElementObject.removeAttr('required');
                            }
                            
                            // PHP RegEx rextriction
                            if (projectItem[Model_Project_Config_Item.JSON_STRING_REGEX].length) {
                                controlElementObject.attr('title', 'RegEx: ' + $('<div/>').text(projectItem[Model_Project_Config_Item.JSON_STRING_REGEX]).html()).tooltip();
                            }
                            break;
                            
                        case 'color':
                            // Color-specific labels
                            break;
                            
                        case 'bool':
                            // Boolean-specific labels
                            break;
                        
                        case 'image':
                            // Get the width/height text
                            var widthHeight = (null !== projectItem[Model_Project_Config_Item.JSON_IMAGE_WIDTH] || null !== projectItem[Model_Project_Config_Item.JSON_IMAGE_HEIGHT]) ? 
                                ((null === projectItem[Model_Project_Config_Item.JSON_IMAGE_WIDTH] ? '?' : projectItem[Model_Project_Config_Item.JSON_IMAGE_WIDTH]) + 'x' + (null === projectItem[Model_Project_Config_Item.JSON_IMAGE_HEIGHT] ? '?' : projectItem[Model_Project_Config_Item.JSON_IMAGE_HEIGHT])) : 
                                'any size';
                                
                            // Create the label
                            projectItemLabel += '<br/><span class="small">(' + projectItem[Model_Project_Config_Item.JSON_IMAGE_TYPE] + ', ' + widthHeight + ')</span>';
                            break;
                            
                        case 'code':
                            projectItemLabel += '<br/><span class="small"><b>' + projectItem[Model_Project_Config_Item.JSON_EXTENSION].toUpperCase() + '</b> code</span>';
                            break;
                    }
                    
                    // Prepare the item ID
                    var projectItemId = ('menu-item-' + category + '-' + projectItem[Model_Project_Config_Item.JSON_KEY]).replace(/\_/g, '-');
                    
                    // Prepare the URL object
                    var projectItemUrl = '';
                    if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_URL] && projectItem[Model_Project_Config_Item.JSON_META_URL].length) {
                        projectItemUrl = ' <a href="' + projectItem[Model_Project_Config_Item.JSON_META_URL] + '" target="_blank" data-role="extra-info"><span class="glyphicon glyphicon-link"></span></a>';
                    }
                    
                    // Prepare the item
                    var projectItemObject = $(
                        '<div class="form-group">' +
                            '<label class="col-sm-3 control-label" data-toggle="tooltip" data-placement="top" for="' + projectItemId + '">' + (projectItemLabel + projectItemUrl) + '</label>' +
                            '<div class="col-sm-9 project-item-control">' +
                            '</div>' +
                        '</div>'
                    );
            
                    // Force label click actions
                    switch(projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                        case 'bool':
                            projectItemObject.find('label').click(function(){
                                controlElementObject.click();
                            });
                            break;
                            
                        case 'code':
                            // One of the default Code elements
                            if (Model_Project_Config_Item.KEY_CSS == projectItem[Model_Project_Config_Item.JSON_KEY] || Model_Project_Config_Item.KEY_CSS_INLINE == projectItem[Model_Project_Config_Item.JSON_KEY] || Model_Project_Config_Item.KEY_JS == projectItem[Model_Project_Config_Item.JSON_KEY]) {
                                // Set the attribute
                                projectItemObject.attr('data-code', 'addon');
                            }
                            
                            // Prepare the icon 
                            var codeElementDocumentationButton = $('<span class="glyphicon glyphicon-education" data-role="documentation" title="Learn more about available tags"></span>');
                            
                            // Enable the title
                            codeElementDocumentationButton.tooltip();
                            
                            // Set the on-click event
                            codeElementDocumentationButton.click(function(e) {
                                e.preventDefault();
                                e.stopPropagation();
                                
                                // Initialize the cache
                                if ("undefined" === typeof _this._cache["documentation-tags"]) {
                                    _this._cache["documentation-tags"] = {};
                                }
                                
                                // Show the documentation
                                var showTheDocs = function(text) {
                                    // Prepare the Documentation
                                    var modal = $(
                                        '<div class="modal fade" id="modal-documentation-tags-' + projectItem[Model_Project_Config_Item.JSON_EXTENSION] + '">' +
                                            '<div class="modal-dialog modal-lg">' +
                                                '<div class="modal-content">' +
                                                    '<div class="modal-header">' +
                                                        '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                                        '<h3 class="modal-title">' + projectItem[Model_Project_Config_Item.JSON_EXTENSION].toUpperCase() + ' Data Tags Documentation</h3>' +
                                                    '</div>' +
                                                    '<div class="modal-body">' +
                                                        text +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>' +
                                        '</div>'
                                    );
                                    modal.modal('show');
                                }
                                
                                // Cache hit
                                if ("undefined" !== typeof _this._cache["documentation-tags"][projectItem[Model_Project_Config_Item.JSON_EXTENSION]]) {
                                    // Show the documentation
                                    showTheDocs(_this._cache["documentation-tags"][projectItem[Model_Project_Config_Item.JSON_EXTENSION]]);
                                } else {
                                    // Get the docs
                                    _this.ajax('project/actionGetTagDocs/' + projectItem[Model_Project_Config_Item.JSON_EXTENSION], {}, function(msg) {
                                        if (msg.status == "success" && "string" == typeof msg.result) {
                                            // Store to cache
                                            _this._cache["documentation-tags"][projectItem[Model_Project_Config_Item.JSON_EXTENSION]] = msg.result;
                                            
                                            // Show the documentation
                                            showTheDocs(msg.result);
                                        }
                                    }, false);
                                }
                                
                                // Stop here
                                return false;
                            });
                            
                            // Append it
                            projectItemObject.find('label').append(codeElementDocumentationButton);
                            break;
                    }
            
                    // Repopulate action
                    if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_REFRESH] && projectItem[Model_Project_Config_Item.JSON_META_REFRESH].length) {
                        var projectItemActionObject = $('<span class="glyphicon glyphicon-refresh" data-role="refresh"></span>');
                        var actionName = "action" + projectItem[Model_Project_Config_Item.JSON_META_REFRESH].charAt(0).toUpperCase() + projectItem[Model_Project_Config_Item.JSON_META_REFRESH].slice(1);
                        
                        // Prepare the on-click event
                        projectItemActionObject.click(function(){
                            $({deg: 0}).animate({deg: 360}, {duration: 360, step: function(deg){
                                projectItemActionObject.css({transform: 'rotate(' + deg + 'deg)'});
                            }});
                            _this.ajax('project/' + actionName + '/' + _this._projectId + '/' + _this._userId + '/' + $(this).attr('data-category'), {}, function(msg) {
                                if (msg.status == "success" && "string" == typeof msg.result) {
                                    // Refresh the layout
                                    controlElementObject.val(msg.result);
                                }
                            }, false);
                        });
                        
                        // Append the element
                        projectItemObject.find('label').append(projectItemActionObject);
                    }
                    
                    // Markdown action
                    if (projectItem[Model_Project_Config_Item.JSON_IS_MARKDOWN]) {
                        var projectItemActionObject = $('<span class="glyphicon glyphicon-eye-open" data-role="refresh"></span>');
                        
                        // Prepare the on-click event
                        projectItemActionObject.click(function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            
                            if (controlElementObject.hasClass('active')) {
                                // Back to the original
                                projectItemActionObject.removeClass('glyphicon-eye-close').addClass('glyphicon-eye-open');
                                
                                // Hide the preview
                                controlElementObject.removeClass('active');
                            } else {
                                // Set the text
                                controlElementObject.find('.preview').html('...');
                                
                                // Get the translation
                                _this.ajax('project/getMarkdown', {'text': controlElementObject.find('textarea').val()}, function(msg) {
                                    if (msg.status == "success" && "string" == typeof msg.result) {
                                        // Change the icon
                                        projectItemActionObject.removeClass('glyphicon-eye-open').addClass('glyphicon-eye-close');
                                
                                        // Refresh the layout
                                        controlElementObject.find('.preview').html(msg.result);
                                        
                                        // Show the preview
                                        controlElementObject.addClass('active');
                                    }
                                }, false);
                            }
                            
                            return false;
                        });
                        
                        // Append the element
                        projectItemObject.find('label').append(projectItemActionObject).append('<br/><span class="small">(<a href="https://github.com/adam-p/markdown-here/wiki/Markdown-Cheatsheet" target="_blank"><b>MarkDown</b></a> syntax)</span>');
                    }
            
                    // Custom description
                    if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_DESCRIPTION] && projectItem[Model_Project_Config_Item.JSON_META_DESCRIPTION].length) {
                        projectItemObject.find('label').attr('title', projectItem[Model_Project_Config_Item.JSON_META_DESCRIPTION]).tooltip();
                    }
                    
                    // Custom symbol
                    if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_SYMBOL] && projectItem[Model_Project_Config_Item.JSON_META_SYMBOL].length) {
                        // Prepare the badge
                        var projectItemBadge = $('<span class="badge badge-normal">' + projectItem[Model_Project_Config_Item.JSON_META_SYMBOL] + '</span>');
                        
                        // Append the element
                        projectItemObject.find('label').prepend(projectItemBadge);
                    }
            
                    // Custom dependency
                    if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_DEPENDS] && projectItem[Model_Project_Config_Item.JSON_META_DEPENDS].length) {
                        projectItemObject.attr('data-depends', projectItem[Model_Project_Config_Item.JSON_META_DEPENDS]);
                        
                        // Prepare the parent denepdency
                        var parentDependency = null;
                        var dependencyAddon = null;
                        if ("undefined" !== typeof details[projectItem[Model_Project_Config_Item.JSON_META_DEPENDS]]) {
                            parentDependency = details[projectItem[Model_Project_Config_Item.JSON_META_DEPENDS]];
                        } else {
                            // Another item's key?
                            if (projectItem[Model_Project_Config_Item.JSON_META_DEPENDS_ADDON].length 
                                && "undefined" !== typeof config[projectItem[Model_Project_Config_Item.JSON_META_DEPENDS_ADDON]] 
                                && "undefined" !== typeof config[projectItem[Model_Project_Config_Item.JSON_META_DEPENDS_ADDON]][projectItem[Model_Project_Config_Item.JSON_META_DEPENDS]]) {
                                // Store the dependency addon
                                parentDependency = config[projectItem[Model_Project_Config_Item.JSON_META_DEPENDS_ADDON]][projectItem[Model_Project_Config_Item.JSON_META_DEPENDS]];
                                
                                // Prepare the addon name
                                dependencyAddon = tools.php.ucfirst(projectItem[Model_Project_Config_Item.JSON_META_DEPENDS_ADDON].replace(/^addon\-/g, '').replace(/\-/, ' '));
                            }
                        }
                        
                        // Dependency is defined
                        if (null !== parentDependency) {
                            if ("undefined" !== typeof parentDependency[Model_Project_Config_Item.JSON_VALUE]) {
                                // Object is disabled
                                if (true !== parentDependency[Model_Project_Config_Item.JSON_VALUE]) {
                                    projectItemObject.css({display: "none"});
                                }
                                
                                // Prepend the dependency
                                if ("undefined" != typeof parentDependency[Model_Project_Config_Item.JSON_META_TITLE]) {
                                    projectItemObject.find('label').prepend('<span class="small">Depends on <b>' + (null === dependencyAddon ? '' : (dependencyAddon + ': ')) + parentDependency[Model_Project_Config_Item.JSON_META_TITLE] + '</b><br/></span>');
                                }
                            }
                        }
                    }
            
                    // Set the ID
                    controlElementObject.attr('id', projectItemId);
                    
                    // Markdown child
                    if (projectItem[Model_Project_Config_Item.JSON_IS_MARKDOWN]) {
                        controlElementObject.removeAttr('id');
                        controlElementObject.find('textarea').attr('id', projectItemId);
                    }
                    
                    // Append the element
                    projectItemObject.find('.project-item-control').append(controlElementObject);
                    
                    // @TODO Multi-color support
                    if ('color' == projectItem[Model_Project_Config_Item.JSON_TYPE]) {
                        if (controlElementObject.is('input')) {
                            controlElementObject.spectrum({
                                showAlpha: true,
                                showInitial: true,
                                appendTo: "parent",
                                preferredFormat: "hex8",
                            });
                        }
                        if (controlElementObject.is('select')) {
                            // Uppercased
                            controlElementObject.addClass('uppercased');
                            
                            // Prepare the object
                            var colorBlock = $("<label class='input-group-addon' for='" + controlElementObject.attr('id') + "'><div class='sp-preview'><div class='sp-preview-inner'></div></div></label>");

                            // Refresh the color block
                            var colorBlockRefresh = function() {
                                // Get the tiny color
                                var tinyColor = new tinycolor(controlElementObject.val(), "hex8");

                                // Set the color block's background
                                colorBlock.find('.sp-preview-inner').css({'background-color': tinyColor.toRgbString()});
                            };

                            // Wrap the element as part of an input-group
                            controlElementObject.wrap('<div class="input-group"></div>');

                            // Add the color block
                            colorBlock.insertBefore(controlElementObject);

                            // Color change callback
                            controlElementObject.change(colorBlockRefresh);
                            colorBlockRefresh();
                        }
                    }
                    
                    // Custom description for options
                    if (projectItem[Model_Project_Config_Item.JSON_OPTIONS].length > 0) {
                        if ('font' == projectItem[Model_Project_Config_Item.JSON_TYPE] || ("object" == typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS] && Object.keys(projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS]).length)) {
                            // Prepare the holder
                            var optionsMetaObject = $('<div class="project-options-meta bs-callout"></div>');

                            // Append it
                            projectItemObject.find('.project-item-control').append(optionsMetaObject);
                            
                            // Prepare the listener
                            var showOptionsMeta = function(customObject) {
                                // Reset the entry
                                optionsMetaObject.html('').css({display: 'none'});

                                // Get the current key
                                var currentKey = ("undefined" === typeof customObject ? controlElementObject.val() : (null != customObject ? customObject.val() : null));
                                
                                // Font previews
                                if ('font' == projectItem[Model_Project_Config_Item.JSON_TYPE] && null !== currentKey && currentKey.length) {
                                    if ('undefined' === typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS]) {
                                        projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS] = {};
                                    }
                                    
                                    // Element not loaded
                                    if ('undefined' === typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey]) {
                                        // Prepare the element
                                        projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey] = [
                                            'Google Font',
                                            '<span style="font-family: ' + tools.php.htmlentities(currentKey) + ';">The quick brown fox jumps over the lazy dog.<br/>0123456789 !@#$%^&*()</span>'
                                        ];
                                        
                                        // Get the font weights for this font family
                                        _this.ajax('project/actionGetFontWeights', {fontFamily: currentKey}, function(msg){
                                            if ('success' === msg.status) {
                                                projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey][1] += '<br/><br/><b>Font weight' + (msg.result.length == 1 ? '' : 's') + ':</b> ' + msg.result.join(', ');
                                            } else {
                                                projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey][1] += '<br/><br/><b>Font family not found!</b>';
                                            }
                                            showOptionsMeta(customObject);
                                        }, false);
                                        
                                        // Load the font just once
                                        $('head').append('<style type="text/css">@import url("//fonts.googleapis.com/css?family=' + tools.php.htmlentities(currentKey) + '");</style>');
                                    }
                                }
                                
                                // Value is defined
                                if (null != currentKey && "undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey]) {
                                    if ("string" == typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey][0] && "string" == typeof projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey][1]) {
                                        // Prepare the details
                                        var title = projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey][0];
                                        var desc = projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey][1];

                                        // UI Sets have an image preview
                                        if ("core" === category && "projectUiSet" === projectItem[Model_Project_Config_Item.JSON_KEY]) {
                                            desc = '<img src="/ajax/project/uiSetPreview/' + currentKey + '" title="' + tools.php.htmlentities(projectItem[Model_Project_Config_Item.JSON_META_OPTIONS_DETAILS][currentKey][1]) + '"/> <br/>' + desc;
                                        }

                                        // Store the text
                                        optionsMetaObject.html(
                                            '<blockquote>' + 
                                                '<p>'+ desc + '</p>' + 
                                                '<footer><span title="' + tools.php.htmlentities(tools.php.strip_tags(title)) + '">' + currentKey + '</span></footer>' + 
                                            '</blockquote>'
                                        ).css({display: 'block'});
                                    }
                                }
                            };

                            // Bind the listener
                            if (controlElementObject.is('select')) {
                                // Drop-down
                                controlElementObject.change(function(){
                                    showOptionsMeta();
                                });
                            } else {
                                // List of checboxes/radios/text inputs
                                controlElementObject.find('label').hover(function(){
                                    // Don't add details to user input
                                    if (!$(this).find('input[type=text]').length) {
                                        showOptionsMeta($(this).find('input').not('[type=text]'));
                                    }
                                });
                            }

                            // Run first time
                            showOptionsMeta();
                        }
                    }
                    
                    // Item header
                    if ("undefined" != typeof projectItem[Model_Project_Config_Item.JSON_META_HEADER] && projectItem[Model_Project_Config_Item.JSON_META_HEADER].length) {
                        // Store the headers
                        if ("undefined" == typeof categoryHeaders[category]) {
                            categoryHeaders[category] = 0;
                            
                            // This is not the first element
                            if(Object.keys(details)[0] != projectItem[Model_Project_Config_Item.JSON_KEY] && !Object.keys(details)[0].match(/^_/g)) {
                                categoryHeaders[category]++;
                            }
                        }
                        
                        // Add the header
                        projectItemObject.prepend((categoryHeaders[category] > 0 ? '<hr/>' : '') + '<h4>' + projectItem[Model_Project_Config_Item.JSON_META_HEADER] + '</h4>');
                        
                        // Increment the counter
                        categoryHeaders[category]++;
                    }
            
                    // Append the item
                    formElements.append(projectItemObject);
                });
                
                // Removable addon flag; core and addon-{projectFramework} cannot be removed
                var removableAddon = category.match(/^addon\-/g) && category != ('addon-' + config.core.projectFramework.v);
                
                // Prepare the form
                var modal = $(
                    '<div class="modal fade" id="modal-' + category + '">' +
                        '<div class="modal-dialog modal-lg">' +
                            '<div class="modal-content">' +
                                '<div class="modal-header">' +
                                    '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                    '<h3 class="modal-title">' + categoryLabel + '</h3>' +
                                '</div>' +
                                '<div class="modal-body">' +
                                '</div>' +
                                '<div class="modal-footer">' +
                                    (removableAddon ? '<button type="button" data-role="remove-addon" data-category="' + category + '" class="btn btn-danger"><span class="glyphicon glyphicon-remove"></span> Remove Add-On</button>' : '') +
                                    '<button type="button" class="btn btn-success" data-role="submit-addon">' + ('plus' == category ? '<span class="glyphicon glyphicon-plus"></span> Enable' : '<span class="glyphicon glyphicon-ok"></span> Save') + '</button>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
        
                // Get the addon code objects
                var addonCodeObjects = formElements.find('[data-code="addon"]');
                
                // Code objects found
                if (addonCodeObjects.length) {
                    // Prepare the toggler
                    var addonCodeObjectsToggle = $('<span data-role="data-code-toggle" class="glyphicon glyphicon-cog"></span>');
                    
                    // Set the click event
                    addonCodeObjectsToggle.click(function(){
                        // Toggle the objects visibility
                        if (addonCodeObjects.is(':visible')) {
                            addonCodeObjects.slideUp().removeClass('active');
                            addonCodeObjectsToggle.hasClass('active') && addonCodeObjectsToggle.removeClass('active');
                        } else {
                            addonCodeObjects.addClass('active').slideDown();
                            !addonCodeObjectsToggle.hasClass('active') && addonCodeObjectsToggle.addClass('active');
                        }
                    });
                    
                    // Prepend the toggler
                    modal.find('.modal-footer').prepend(addonCodeObjectsToggle);
                }
        
                // Set the addon's description
                if ("undefined" != typeof details[Model_Project_Config_Item.KEY_ICON] && "string" == typeof details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_DESCRIPTION] && details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_DESCRIPTION].length) {
                    var collapsibleDetails = $(
                        '<div id="meta-desc-' + category + '" class="addon-description panel-collapse collapse" role="tabpanel">' + 
                            '<blockquote>' + 
                                '<p>' + details[Model_Project_Config_Item.KEY_ICON][Model_Project_Config_Item.JSON_META_DESCRIPTION] + '</p>' + 
                                '<footer><cite>' + category + '</cite></footer>' + 
                            '</blockquote>' + 
                        '</div>' +
                        '<span class="addon-description-handle glyphicon glyphicon-question-sign" data-toggle="collapse" aria-expanded="false" data-target="#meta-desc-'+category+'"></span>'
                    );
            
                    // Prepend the item
                    modal.find('.modal-footer').prepend(collapsibleDetails);
                }
                
                // Append the form elements
                modal.find('.modal-body').append(formElements);
                
                // Submit the form
                modal.find('[data-role="submit-addon"]').click(function() {
                    formElements.submit();
                });
                modal.find('[data-role="remove-addon"]').click(function() {
                    if (confirm('Are you sure you want to disable this Add-on?')) {
                        // Remove the Add-on
                        _this.ajax('project/categoryDelete/' + _this._projectId + '/' + _this._userId + '/' + $(this).attr('data-category'), {}, function(msg) {
                            // All went well
                            if (msg.status == "success") {
                                // Refresh the layout
                                _this._getData();
                            }
                            
                            // Hide the modal
                            modal.modal('hide');
                        });
                    }
                });
                
                // When the modal is hidden
                modal.on('hidden.bs.modal', function(e) {
                    // The button should no longer be pressed
                    menuItem.removeClass('focused'); 
                });
                
                // When showing the modal
                modal.on('shown.bs.modal', function(e) {
                    // Hide the code objects
                    if (addonCodeObjects.length) {
                        if (addonCodeObjects.is(':visible')) {
                            addonCodeObjectsToggle.click();
                        }
                    }
                    
                    // Code textareas
                    $.each(modal.find('textarea[data-code-ext]'), function(){
                        var textarea = $(this);
                        
                        // Prepare the mime type
                        var mimeType = "text/javascript";
                        switch ($(this).attr('data-code-ext')) {
                            case "css":
                                mimeType = "text/css";
                                break;
                        }
                        
                        // Don't re-run
                        if ("true" == textarea.attr('data-codemirrored')) {
                            return;
                        }
                        
                        // Set the flag
                        textarea.attr('data-codemirrored', 'true');
                        
                        // Create the text editor
                        CodeMirror.fromTextArea(textarea[0], {
                            mode: mimeType,
                            lineNumbers: true,
                            autoCloseBrackets: true,
                            autoCloseTags: true,
                            indentUnit: 4,
                            electricChars: true,
                            extraKeys: {
                                "Ctrl-Space": "autocomplete",
                            },
                        }).on('change',function(cMirror){
                            textarea.html(cMirror.getValue()).val(cMirror.getValue());
                        });
                    });
                });
                
                // Prepare the action
                menuItem.click(function() {
                    // Mark the button as focused
                    !menuItem.hasClass('focused') && menuItem.addClass('focused');
                    
                    // Show the modal
                    modal.modal('show');
                });
                
                // Append the menu item
                menuObject.append(menuItem);
            });
            
            // Add the snapshots
            _this._objectSnapshots = $(
                '<div class="snapshots" data-toggle="tooltip" data-placement="right">' +
                    '<span class="status" title="Save" data-placement="top"><span class="snapshot-id"></span><span class="glyphicon glyphicon-refresh"></span><span class="glyphicon glyphicon-ok"></span></span>' + 
                    '<span class="options" title="Edit snapshots" data-placement="right"><span class="glyphicon glyphicon-chevron-right"></span></span>' +
                '</div>'
            );
            _this._objectSnapshots.find('[title]').tooltip();
    
            // Prepare the form
            var modalSnapshots = $(
                '<div class="modal fade" id="modal-snapshots">' +
                    '<div class="modal-dialog modal-lg">' +
                        '<div class="modal-content">' +
                            '<div class="modal-header">' +
                                '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
                                '<h3 class="modal-title">Snapshots</h3>' +
                            '</div>' +
                            '<div class="modal-body">' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
    
            // Store the modal body
            _this._snapshotManager.setBody(modalSnapshots.find('.modal-body'));
    
            // Save the current snapshot
            _this._objectSnapshots.find('.status').click(function() {
                _this._snapshotManager.save();
            });
            
            // More options
            _this._objectSnapshots.find('.options').click(function() {
                modalSnapshots.modal('show');
            });
            
            // Add the snapshot item
            menuObject.append(_this._objectSnapshots);
            
            // Add the menu object
            _this._objectTools.html('');
            _this._objectTools.append(menuObject);
            
            // Set the timer for 2 minutes
            if (null === _this._saveTime) {
                _this._saveTime = window.setInterval(function(){
                    _this._snapshotManager.save();
                }, 120000);
            }
        };
        
        this._keyBindings = function() {
            $(window).bind('keydown', function(event) {
                if (event.ctrlKey || event.metaKey) {
                    switch (String.fromCharCode(event.which).toLowerCase()) {
                        case 's':
                            // Prevent a page save
                            event.preventDefault();
                            event.stopPropagation();
                            
                            // Get the visible modal
                            var visibleModal = $('.modal:visible');
                            if (visibleModal.length) {
                                // Submit the addon modal
                                visibleModal.find('[data-role="submit-addon"]').click();
                            } else {
                                // Save the current snapshot
                                $('[data-role="tools"] .snapshots .status').click();
                            }
                            break;
                    }
                }
            });
        };
        
        // Get the info about this page
        this.init = function() {
            // Store the original page title
            this._objectTitle = $('title');
            this._pageTitle = this._objectTitle.text();
            
            // Key bindings
            this._keyBindings();

            this._objectProject = $('div[data-role="project"]');
            
            // Not an error page
            if (this._objectProject.length) {
                // Store the secondary objects
                this._objectPreview = this._objectProject.find('[data-role="preview"]');
                this._objectTools = this._objectProject.find('[data-role="tools"]');
                
                // Append the message area
                this._objectProject.append('<div id="message-area"></div>');
                
                // Replace the container
                $('.container').replaceWith(this._objectProject);
                
                // Set the details
                this._projectId = this._objectProject.attr('data-project-id');
                this._userId = this._objectProject.attr('data-user-id');
                
                // Get the project information
                this._getData();
            }
        };
    };
    
    // Load the class
    var instance = new ThemeWarlockProject();
    instance.init();
});

/*EOF*/