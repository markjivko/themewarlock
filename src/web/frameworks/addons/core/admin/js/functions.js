/**
 * {project.destProjectName} Theme Manager UI
 * 
 * {utils.common.copyright}
 */
jQuery && jQuery(document).ready(function () {
    "use strict";
    var $ = jQuery;
    
    /**
     * Prepare the Theme Manager UI handler
     */
    var {project.prefix}_theme_manager = {
        _notifyLastMessage: null,
        notify: function(message, status) {
            {if.core.staging}console.log([message, status]);{/if.core.staging}
            
            // Defaults
            if ("undefined" === typeof message) {
                message = null;
            }
            if ("undefined" === typeof status) {
                status = true;
            }
            
            // Do not duplicate tasks
            if (message === this._notifyLastMessage) {
                return;
            }
            
            // Store the last message
            this._notifyLastMessage = message;
            
            // Hide all messages
            if (null === message) {
                $('.status-area').html('');
                return;
            }

            // Store the current message
            if ('' == $('.status-area').html()) {
                $('.status-area').html('<div class="' + (status ? 'status-success' : 'status-error') + '"><span class="message">' + message + '</span> <span class="loading"></span></div>')
            } else {
                $('.status-area').children('div').attr('class', status ? 'status-success' : 'status-error');
                $('.status-area').find('.message').html(message);
            }
        },
        slider: {
            _objects: {},
            _slides: {},
            _slideCurrent: null,
            _setRibbon: function(colorA, colorB, installed) {
                // The Ribbon canvas was not defined
                if ("undefined" === typeof this._objects.ribbonCanvas) {
                    return;
                }
                if ("undefined" === typeof installed) {
                    installed = false;
                }
                
                // Prepare the dimensions
                var width = 150;
                var height = 150;
                
                // Prepare the parent
                var parent = this._objects.ribbonCanvas.parent('div');
                
                // Set the parent class
                if (installed) {
                    !parent.hasClass('installed') && parent.addClass('installed');
                } else {
                    parent.removeClass('installed');
                }
                
                // Prepare the canvas
                this._objects.ribbonCanvas.attr('width', width).attr('height', height);

                // Prepare the context
                var ctx = this._objects.ribbonCanvas[0].getContext('2d');
                
                // Draw the white triangle
                ctx.beginPath();
                
                // Top-right
                ctx.moveTo(width, 0); 
                
                // Bottom-right
                ctx.lineTo(width, height);
                
                // Top-left
                ctx.lineTo(0, 0);

                // Prepare the fill gradient; x1, y1, x2, y3; top-left to bottom-right
                var fillGradient = ctx.createLinearGradient(0, 0, width, height);
                
                // Add the colors
                fillGradient.addColorStop(0, colorA);
                fillGradient.addColorStop(1, colorB);

                // Background color
                ctx.fillStyle = fillGradient;

                // Fill the path
                ctx.fill();

                // Close the triangle
                ctx.closePath();
            },
            _showSlide: function(slideKey) {
                // Search slide by ID
                if ("undefined" !== typeof slideKey) {
                    // Convert to int
                    slideKey = parseInt(slideKey, 10);
                    
                    // Validate the key
                    if (slideKey >= 0 && slideKey <= this._slides.length - 1) {
                        this._slideCurrent = slideKey;
                    }
                }
                
                // Invalid slide
                if("undefined" === typeof this._slides[this._slideCurrent]) {
                    return;
                }
                
                // Update the text
                this._objects.install.find('span').html(this._slides[this._slideCurrent]['installed'] ? {project.prefix}.text_uninstall : {project.prefix}.text_install);
                
                // Show the image
                this._objects.images.find('.preview').removeClass('active');
                this._objects.images.find('.preview[data-key="' + this._slideCurrent + '"]').addClass('active');
                
                // Update the canvas
                this._setRibbon(
                    this._slides[this._slideCurrent]['colors'][0],
                    this._slides[this._slideCurrent]['colors'][1],
                    this._slides[this._slideCurrent]['installed']
                );
        
                // Set the installed class
                this._objects.title.removeClass('is-installed');
                if (this._slides[this._slideCurrent]['installed']) {
                    this._objects.title.addClass('is-installed');
                }
                
                // Update the bullets
                this._objects.bullets.find('.bullet').removeClass('active');
                this._objects.bullets.find('.bullet[data-key="' + this._slideCurrent + '"]').addClass('active');
                
                // Update the title and description
                this._objects.title.find('h1').html(this._slides[this._slideCurrent]['title'].length ? this._slides[this._slideCurrent]['title'] : ('#' + this._slides[this._slideCurrent]['id']));
                this._objects.title.find('p').html(this._slides[this._slideCurrent]['description'].length ? this._slides[this._slideCurrent]['description'] : '...');
            },
            _prev: function(){
                // First time
                if (null === this._slideCurrent) {
                    this._slideCurrent = 0;
                } else {
                    this._slideCurrent--;
                }
                
                // Cycle
                if (this._slideCurrent < 0) {
                    this._slideCurrent = this._slides.length - 1;
                }
                
                // Show the slide
                this._showSlide();
            },
            _next: function() {
                // First time
                if (null === this._slideCurrent) {
                    this._slideCurrent = 0;
                } else {
                    this._slideCurrent++;
                }
                
                // Cycle
                if (this._slideCurrent >= this._slides.length) {
                    this._slideCurrent = 0;
                }
                
                // Show the slide
                this._showSlide();
            },
            init: function(slides, isError) {
                var _this = this;
                
                // Default parameter
                if ("undefined" === typeof isError) {
                    isError = false;
                }
                
                // Store the slides
                this._slides = slides;
                
                // Store the objects
                if ("undefined" === typeof this._objects.container) {
                    this._objects.container = $('[data-role="admin-container"]');
                }
                if ("undefined" === typeof this._objects.images) {
                    this._objects.images = $('[data-role="images"]');
                }
                if ("undefined" === typeof this._objects.ribbonCanvas) {
                    this._objects.ribbonCanvas = $('<canvas></canvas>');
                    $('[data-role="ribbon"]').append(this._objects.ribbonCanvas);
                }
                if ("undefined" === typeof this._objects.navLeft) {
                    this._objects.navLeft = $('[data-role="nav-left"]');
                }
                if ("undefined" === typeof this._objects.navRight) {
                    this._objects.navRight = $('[data-role="nav-right"]');
                }
                if ("undefined" === typeof this._objects.bullets) {
                    this._objects.bullets = $('[data-role="bullets"]');
                }
                if ("undefined" === typeof this._objects.title) {
                    this._objects.title = $('[data-role="title"]');
                }
                if ("undefined" === typeof this._objects.install) {
                    this._objects.install = $('[data-role="install"]');
                }
                if ("undefined" === typeof this._objects.addSnapshot) {
                    this._objects.addSnapshot = $('[data-role="add-snapshot"]');
                }
                if ("undefined" === typeof this._objects.deleteSnapshot) {
                    this._objects.deleteSnapshot = $('[data-role="delete-snapshot"]');
                }
                if ("undefined" === typeof this._objects.uploadForm) {
                    this._objects.uploadForm = $('[data-role="upload-form"]');
                }
                
                // Reset the images object
                this._objects.images.html('');
                
                // Reset the bullets object
                this._objects.bullets.html('');
                
                // Single slide check
                if (1 === this._slides.length) {
                    if (!this._objects.container.hasClass('one-slide')) {
                        this._objects.container.addClass('one-slide');
                    }
                } else {
                    if (this._objects.container.hasClass('one-slide')) {
                        this._objects.container.removeClass('one-slide');
                    }
                }
                
                // Go through the slides
                $.each(this._slides, function(k,v) {
                    // Prepare the preview image
                    var previewImageObject = $('<div class="preview"></div>');
                    
                    // Cast the ID
                    _this._slides[k]['id'] = parseInt(v.id, 10);
                    
                    // Set its background
                    previewImageObject.attr('data-key', k);
                    
                    // Preview defined
                    if ("undefined" !== typeof v.preview) {
                        previewImageObject.css({
                            'background-image': 'url("' + encodeURI(v.preview) + '")',
                        });
                    }
                    
                    // Append it to the objects
                    _this._objects.images.append(previewImageObject);
                    
                    // Prepare the bullet
                    var bulletObject = $('<div class="bullet"></div>');
                    
                    // Installed slide
                    if (v.installed) {
                        bulletObject.addClass('installed');
                    }
                    
                    // Set the event
                    bulletObject.html(v.id).attr('data-key', k).unbind('click').click(function(){
                        _this._showSlide(k);
                    });
                    
                    // Append to bullet list
                    _this._objects.bullets.append(bulletObject);
                });
                
                // Set the navigation
                this._objects.navLeft.unbind('click').click(function(){_this._prev();});
                this._objects.navRight.unbind('click').click(function(){_this._next();});
                
                // Status checker method
                var statusCheck = {
                    _snapshotId: null,
                    _status: false,
                    _tick: function() {
                        // Stopped or invalid Snapshot ID
                        if (!this._status || "number" !== typeof this._snapshotId) {
                            return;
                        }
                        
                        // Perform the request
                        {project.prefix}.tools.ajax(
                            'getStatus', 
                            [this._snapshotId], 
                            function(msg) {
                                // Valid status found
                                if ("object" === typeof msg.data && null !== msg.data && msg.data.length) {
                                    // Store the message
                                    {project.prefix}_theme_manager.notify(msg.data[0], msg.data[1]);
                                }
                                
                                // Go through the loop @0.5s
                                window.setTimeout(function(){statusCheck._tick();}, 300);
                            }, 
                            false
                        );
                    },
                    start: function(snapshotId) {
                        // Store the snapshot Id
                        this._snapshotId = snapshotId;
                        
                        // Start if necessary
                        if (!this._status) {
                            // Hide the controls
                            _this._objects.container.addClass('working');
                    
                            // Mark the task as active
                            this._status = true;

                            // Start the ticker
                            this._tick();
                        }
                    },
                    stop: function() {
                        // Show the controls
                        _this._objects.container.removeClass('working');
                            
                        // Mark the task as inactive
                        this._status = false;
                    }
                };
                
                // Set the flags
                var uploadingFlag = false;
                var setUploadingFlag = function(value) {
                    uploadingFlag = (true === value);
                    if (uploadingFlag) {
                        if (!_this._objects.addSnapshot.hasClass('uploading')) {
                            _this._objects.addSnapshot.addClass('uploading');
                        }
                    } else {
                        if (_this._objects.addSnapshot.hasClass('uploading')) {
                            _this._objects.addSnapshot.removeClass('uploading');
                        }
                    }
                };
                var deletingFlag = false;
                var setDeletingFlag = function(value) {
                    deletingFlag = (true === value);
                    if (deletingFlag) {
                        if (!_this._objects.deleteSnapshot.hasClass('deleting')) {
                            _this._objects.deleteSnapshot.addClass('deleting');
                        }
                    } else {
                        if (_this._objects.deleteSnapshot.hasClass('deleting')) {
                            _this._objects.deleteSnapshot.removeClass('deleting');
                        }
                    }
                };

                // Auto-submit
                this._objects.uploadForm.find('input[type=file]').unbind('change').change(function() {
                    // Reset the value
                    if (!uploadingFlag) {
                        // Prepare the ajax data
                        var uploadFormData = {project.prefix}.tools._data.requestFsData;

                        // Implement our action, method, arguments and nonce
                        uploadFormData.action = '{project.prefix}_action';
                        uploadFormData.st_ajax_method      = 'uploadSnapshot';
                        uploadFormData.st_ajax_args        = [];
                        uploadFormData.st_ajax_nonce       = {project.prefix}.ajax_nonce;
                        uploadFormData.st_ajax_validate_fs = true;
                        
                        // Prepare the percent bar
                        _this._objects.uploadForm.ajaxForm({
                            dataType: 'json',
                            data: uploadFormData,
                            beforeSend: function () {
                                // Set the flag
                                setUploadingFlag(true);
                                
                                // Hide the controls
                                _this._objects.container.addClass('working').addClass('uploading');
                            
                                // Uploading
                                {project.prefix}_theme_manager.notify({project.prefix}.text_uploading);

                                // Set the upload percent
                                _this._objects.addSnapshot.html('0');
                            },
                            uploadProgress: function (event, position, total, percentComplete) {
                                // Prepare the actual percentage
                                var realPercent = parseInt(position * 100 / total, 10) + "";

                                // Update the upload status
                                _this._objects.addSnapshot.html(realPercent);
                                
                                // Finished upload
                                if (100 == realPercent) {
                                    // Working
                                    {project.prefix}_theme_manager.notify({project.prefix}.text_working);
                                    
                                    // Change the symbol to ellipsis
                                    _this._objects.addSnapshot.html('&#x2026;');
                                }
                            },
                            complete: function (xhr) {
                                // Uploaded and processing done
                                _this._objects.addSnapshot.html('+');
                                
                                // Get the message
                                var msg = xhr.responseJSON;
                                
                                // Could not upload the file, refused by server
                                if ("undefined" === typeof msg) {
                                    msg = {
                                        success: false,
                                        errorMessage: {project.prefix}.text_upload_failed
                                    };
                                }
                                
                                // Hide the controls
                                statusCheck.stop();
                                _this._objects.container.removeClass('uploading');
                                
                                // Set the flag
                                setUploadingFlag(false);
                                
                                // Reset the value
                                _this._objects.uploadForm.find('input[type=file]').val('');
                                
                                // The upload was successful
                                if (msg.success) {
                                    // Update the snapshots list
                                    if (msg.data.length) {
                                        {project.prefix}_theme_manager.slider.init(msg.data);
                                    } else {
                                        {project.prefix}_theme_manager.noSnapshots();
                                    }
                                    
                                    // Hide the message
                                    {project.prefix}_theme_manager.notify();
                                } else {
                                    // Prepare the message
                                    var errorList = msg.errorMessage + '<br/>';
                                    
                                    // Caught errors
                                    if ("object" === typeof msg.errorsCaught && null !== msg.errorsCaught && msg.errorsCaught.length) {
                                        // Go through the details
                                        $.each(msg.errorsCaught, function(k, v) {
                                            // Append the list item
                                            errorList += v + '<br/>';
                                        });
                                    }
                    
                                    // Show the errors
                                    {project.prefix}_theme_manager.notify(errorList, false);
                                }
                            }
                        });
                
                        // Send the form
                        _this._objects.uploadForm.submit();
                    }
                });
                    
                // Set the add snapshot action
                this._objects.addSnapshot.unbind('click').click(function(){
                    // Another task is running
                    if (statusCheck._status) {
                        return;
                    }
                    
                    // Check the upload flag
                    if (uploadingFlag) {
                        return;
                    } 
                    
                    // Request FileSystem access before the upload
                    {project.prefix}.tools.ajax(
                        'preUploadCheck',
                        [],
                        function(msg) {
                            if (msg.success) {
                                _this._objects.uploadForm.find('input[type=file]').click();
                            }
                        }
                    );
                });
                
                // Set the delete snapshot action
                this._objects.deleteSnapshot.unbind('click').click(function() {
                    // Another task is running
                    if (statusCheck._status) {
                        return;
                    }
                    
                    // Another delete action is running
                    if (deletingFlag) {
                        return;
                    }
                    
                    // Avoid accidental clicks
                    if (confirm({project.prefix}.text_confirm_delete)) {
                        // Set the flag
                        setDeletingFlag(true);

                        // Working
                        {project.prefix}_theme_manager.notify({project.prefix}.text_deleting);

                        // Hide the controls
                        _this._objects.container.addClass('working').addClass('deleting');

                        // On animation end
                        window.setTimeout(function(){
                            // Delete the snapshot
                            {project.prefix}.tools.ajax(
                                'deleteSnapshot',
                                [_this._slides[_this._slideCurrent].id],
                                function(msg) {
                                    // Hide the controls
                                    statusCheck.stop();
                                    _this._objects.container.removeClass('deleting');

                                    // Set the flag
                                    setDeletingFlag(false);

                                    // The upload was successful
                                    if (msg.success) {
                                        // Update the snapshots list
                                        if (msg.data.length) {
                                            {project.prefix}_theme_manager.slider.init(msg.data);
                                        } else {
                                            {project.prefix}_theme_manager.noSnapshots();
                                        }

                                        // Hide the message
                                        {project.prefix}_theme_manager.notify();
                                    } else {                                
                                        // Prepare the message
                                        var errorList = msg.errorMessage + '<br/>';

                                        // Caught errors
                                        if ("object" === typeof msg.errorsCaught && null !== msg.errorsCaught && msg.errorsCaught.length) {
                                            // Go through the details
                                            $.each(msg.errorsCaught, function(k, v) {
                                                // Append the list item
                                                errorList += v + '<br/>';
                                            });
                                        }

                                        // Show the errors
                                        {project.prefix}_theme_manager.notify(errorList, false);
                                    }
                                }
                            );
                        }, 1000);
                    }
                });
                
                // Set the install/uninstall action
                this._objects.install.unbind('click').click(function() {
                    // Another task is running
                    if (statusCheck._status) {
                        return;
                    }
                    
                    // Working
                    {project.prefix}_theme_manager.notify({project.prefix}.text_working);
                    
                    // Start the status checker
                    statusCheck.start(_this._slides[_this._slideCurrent].id)
                    
                    // Prepare the new status
                    var newStatus = !_this._slides[_this._slideCurrent].installed;
                    
                    // Start the action after the animation completes
                    window.setTimeout(function(){
                        // Start the installation process
                        {project.prefix}.tools.ajax(
                            // Set the action to install or uninstall
                            newStatus ? 'installSnapshot' : 'uninstallSnapshot',
                            
                            // For the current snapshot ID
                            [_this._slides[_this._slideCurrent].id], 
                            function(msg) {
                                // Stop the ticker
                                statusCheck.stop();
                                
                                // Success
                                if (msg.success) {
                                    // Mark all others as uninstalled by default
                                    $.each(_this._slides, function(slideId, slideObject) {
                                        _this._slides[slideId].installed = false
                                    });
                                    _this._objects.bullets.find('.bullet').removeClass('installed');
                                    
                                    // Mark the success
                                    _this._slides[_this._slideCurrent].installed = newStatus;
                                    
                                    // Set the bullet class
                                    if (newStatus) {
                                        _this._objects.bullets.find('.bullet[data-key="' + _this._slideCurrent + '"]').addClass('installed');
                                    }
                                    
                                    // Re-show the current slide
                                    _this._showSlide(_this._slideCurrent);
                                    
                                    // Show the success message
                                    {project.prefix}_theme_manager.notify(msg.data);
                                    
                                    // Hide the success message after 3s
                                    window.setTimeout(function() {
                                        {project.prefix}_theme_manager.notify();
                                    }, 3000);
                                } else {
                                    // Prepare the message
                                    var errorList = msg.errorMessage + '<br/>';
                                    
                                    // Caught errors
                                    if ("object" === typeof msg.errorsCaught && null !== msg.errorsCaught && msg.errorsCaught.length) {
                                        // Go through the details
                                        $.each(msg.errorsCaught, function(k, v) {
                                            // Append the list item
                                            errorList += v + '<br/>';
                                        });
                                    }
                    
                                    // Show the errors
                                    {project.prefix}_theme_manager.notify(errorList, false);
                                }
                            }
                        );
                    }, 600);
                });
                
                // Hide the elements
                if (isError) {
                    if (!this._objects.container.hasClass('is-error')) {
                        this._objects.container.addClass('is-error');
                    }
                } else {
                    if (this._objects.container.hasClass('is-error')) {
                        this._objects.container.removeClass('is-error');
                    }
                }
                
                // Set the first slide
                this._next();
            },
        },
        noSnapshots: function() {
            // Initialize the slider
            {project.prefix}_theme_manager.slider.init(
                [{
                    "id": 0,
                    "title": {project.prefix}.text_no_snapshots,
                    "description": {project.prefix}.text_no_snapshots_details,
                    "colors": ["#ff5733","#e00000"]
                }],
                true
            );
        },
        init: function() {
            // Get the snapshots
            {project.prefix}.tools.ajax('getSnapshots', {}, 
                function(msg) {
                    if (msg.success && msg.data.length) {
                        {project.prefix}_theme_manager.slider.init(msg.data);
                    } else {
                        {project.prefix}_theme_manager.noSnapshots();
                    }
                    
                    // Caught errors
                    if ("object" === typeof msg.errorsCaught && null !== msg.errorsCaught && msg.errorsCaught.length) {
                        // Prepare the error string
                        var errorList = '';
                        
                        // Go through the details
                        $.each(msg.errorsCaught, function(k, v) {
                            // Append the list item
                            errorList += v + '<br/>';
                        });
                        
                        // Show the errors
                        {project.prefix}_theme_manager.notify(errorList, false);
                    }
                    
                    // Show the loading bar
                    window.setTimeout(function(){
                        $('.container-holder').addClass('s1');
                    }, 1000);

                    // Drop-down the slider
                    window.setTimeout(function(){
                        $('.container-holder').addClass('s2');
                    }, 2500);
                },
                false
            );
        }
    };
    
    // Start the app
    {project.prefix}_theme_manager.init();
});

/*EOF*/