jQuery(document).ready(function () {
    $ = jQuery;
    
    // Check for an update 
    $('.revision.admin').click(function(){
        tools.performAjax('git/pull', {}, function (msg) {
            window.setTimeout(function(){
                window.location.href = window.location.href;
            }, 750);
        });
    }).tooltip();

    // Display the logo after the background has loaded
    window.setTimeout(function(){
        $('.logo.logo-dark').addClass('active');
    }, 500);
    
    // Navbar present
    if ($('nav .navbar-ribbon').length) {
        new Granim({
            element: 'nav .navbar-ribbon',
            name: 'navbar-gradient',
            direction: 'left-right',
            opacity: [1, 1],
            stateTransitionSpeed: 500,
            isPausedWhenNotInView: true,
            states : {
                "default-state": {
                    gradients: [
                        ['#e3e3e3', '#4494c3', '#73bf56'],
                        ['#73bf56', '#e3e3e3', '#4494c3'],
                        ['#4494c3', '#73bf56', '#e3e3e3'],
                    ],
                    transitionSpeed: 500,
                }
            }
        });
    }
    
    // Admins get access to Web Notifications
    if($('.revision').hasClass('admin')) {
        // Notification handler
        var notifHandler = {
            _messages: [],
            _working: false,
            _containerObject: null,
            start: function() {
                // Create container object
                notifHandler._containerObject = $('<div class="web-notif-container"></div>');

                // Append object to taskbar
                notifHandler._containerObject.insertAfter($('nav .navbar-collapse > ul:first-child'));

                // Get messages from server
                notifHandler.getServerMessages();
            },
            getServerMessages: function() {
                // Append messages
                tools.performAjax(
                    // Url for the Ajax controller
                    'utils/getNotifHandlerMessages', 
                    // No Arguments
                    {}, 
                    // Result
                    function(msg) {
                        if(typeof msg.status != "undefined") {
                            if("success" == msg.status) {
                                if("object" == typeof msg.result) {
                                    notifHandler.appendMessages(msg.result);
                                }
                            }
                        }
                    }, 
                    // No loading screen
                    false
                );
        
                // Set timeout for 10s
                window.setTimeout(function(){
                    notifHandler.getServerMessages();
                }, 10000); 
            },
            showNextMessage: function(forcedMode) {
                if(typeof forcedMode == 'undefined') {
                    forcedMode = false;
                }

                // Avoid message collision
                if(!forcedMode && notifHandler._working) {
                    return;
                }
                
                // We have messages to show
                if(notifHandler._messages.length > 0) {
                    notifHandler._working = true;
                    
                    // Get the first message - as "firstMessage" - from the top of "_messages"
                    var firstMessage = notifHandler._messages[0];
                    
                    // Remove it from "_messages" (by key)
                    notifHandler._messages = notifHandler._messages.slice(1);
                    
                    // Validate
                    do {
                        // Expecting object
                        if ("object" != typeof firstMessage || "undefined" == typeof firstMessage.length) {
                            break;
                        }
                        
                        // Expecting size 3
                        if (3 != firstMessage.length)  {
                            break;
                        }
                        
                        // Validate the status
                        if (!firstMessage[0].match(/^(warning|error|info)$/g)) {
                            break;
                        }
                        
                        // Validate the title and content
                        if ("string" != typeof firstMessage[1] || "string" != typeof firstMessage[2]) {
                            break;
                        }
                        
                        // Associate glyphicon with type of message
                        var glyphicon = 'glyphicon glyphicon-bell';
                        switch(firstMessage[0]) {
                            case 'error':
                                glyphicon = 'glyphicon glyphicon-remove-sign';
                                console && console.error && console.error('Theme Warlock: ' + $('<div>' + firstMessage[1] + ' - ' + firstMessage[2] + '</div>').text());
                                break;
                                
                            case 'warning':
                                glyphicon = 'glyphicon glyphicon-alert';
                                console && console.warn && console.warn('Theme Warlock: ' + $('<div>' + firstMessage[1] + ' - ' + firstMessage[2] + '</div>').text());
                                break;
                                
                            default:
                                console && console.info && console.info('Theme Warlock: ' + $('<div>' + firstMessage[1] + ' - ' + firstMessage[2] + '</div>').text());
                                break;
                                
                        };
                        
                        // Create the message HTML
                        var messageHTML = $(
                            '<div class="message message-' + firstMessage[0] + '">'
                                + '<span class="title">' + firstMessage[1] + '</br></span>'
                                + '<span class="' + glyphicon + '"></span>'
                                + '<span class="content">' + firstMessage[2] + '</span>'
                            + '</div>'
                        );
                
                        // Make it hidden
                        messageHTML.css({display: "none"});

                        // Append message to container
                        notifHandler._containerObject.append(messageHTML);

                        // Animate the message
                        messageHTML.fadeIn(200,function(){
                            window.setTimeout(function(){
                                messageHTML.fadeOut(200, function(){
                                    notifHandler.showNextMessage(true);
                                    messageHTML.remove();
                                });
                            }, 2000); 
                        });
                        
                        // Notifier not visible
                        if ($('nav').length == 0) {
                            // Fallback to the notifier instead
                            tools.notify(
                                '<span class="' + glyphicon + '"></span> <b>' + firstMessage[1] + '</b>: ' + firstMessage[2], 
                                firstMessage[0] == 'error' ? 'danger' : firstMessage[0],
                                4000
                            );
                        }
                        
                        // All went well
                        return;
                    } while (false);
                    
                    // Invalid message, skip to the next message
                    notifHandler.showNextMessage(true);
                } else {
                    notifHandler._working ? (notifHandler._working = false) : null;
                }
            },
            appendMessages: function(mess) {
                // Append messages 1 by 1 to notifHandler._messages
                for (var i = 0; i < mess.length; i++) {
                    notifHandler._messages.push(mess[i]);
                }
                
                // Try to show our message
                notifHandler.showNextMessage();
            }
        };
        
        // Launch the notification handler
        notifHandler.start();
    }
});

// Perform an AJAX request
var tools = {
    base64: {
        // private property
        _keyStr : "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",

        // public method for encoding
        encode : function (input) {
            var output = "";
            var chr1, chr2, chr3, enc1, enc2, enc3, enc4;
            var i = 0;

            input = tools.base64._utf8_encode(input);

            while (i < input.length) {

                chr1 = input.charCodeAt(i++);
                chr2 = input.charCodeAt(i++);
                chr3 = input.charCodeAt(i++);

                enc1 = chr1 >> 2;
                enc2 = ((chr1 & 3) << 4) | (chr2 >> 4);
                enc3 = ((chr2 & 15) << 2) | (chr3 >> 6);
                enc4 = chr3 & 63;

                if (isNaN(chr2)) {
                    enc3 = enc4 = 64;
                } else if (isNaN(chr3)) {
                    enc4 = 64;
                }

                output = output +
                tools.base64._keyStr.charAt(enc1) + tools.base64._keyStr.charAt(enc2) +
                tools.base64._keyStr.charAt(enc3) + tools.base64._keyStr.charAt(enc4);
            }

            return output;
        },

        // public method for decoding
        decode : function (input) {
            var output = "";
            var chr1, chr2, chr3;
            var enc1, enc2, enc3, enc4;
            var i = 0;

            input = input.replace(/[^A-Za-z0-9\+\/\=]/g, "");

            while (i < input.length) {

                enc1 = tools.base64._keyStr.indexOf(input.charAt(i++));
                enc2 = tools.base64._keyStr.indexOf(input.charAt(i++));
                enc3 = tools.base64._keyStr.indexOf(input.charAt(i++));
                enc4 = tools.base64._keyStr.indexOf(input.charAt(i++));

                chr1 = (enc1 << 2) | (enc2 >> 4);
                chr2 = ((enc2 & 15) << 4) | (enc3 >> 2);
                chr3 = ((enc3 & 3) << 6) | enc4;

                output = output + String.fromCharCode(chr1);

                if (enc3 != 64) {
                    output = output + String.fromCharCode(chr2);
                }
                if (enc4 != 64) {
                    output = output + String.fromCharCode(chr3);
                }

            }

            output = tools.base64._utf8_decode(output);

            return output;

        },

        // private method for UTF-8 encoding
        _utf8_encode : function (string) {
            string = string.replace(/\r\n/g,"\n");
            var utftext = "";

            for (var n = 0; n < string.length; n++) {

                var c = string.charCodeAt(n);

                if (c < 128) {
                    utftext += String.fromCharCode(c);
                }
                else if((c > 127) && (c < 2048)) {
                    utftext += String.fromCharCode((c >> 6) | 192);
                    utftext += String.fromCharCode((c & 63) | 128);
                }
                else {
                    utftext += String.fromCharCode((c >> 12) | 224);
                    utftext += String.fromCharCode(((c >> 6) & 63) | 128);
                    utftext += String.fromCharCode((c & 63) | 128);
                }

            }

            return utftext;
        },

        // private method for UTF-8 decoding
        _utf8_decode : function (utftext) {
            var string = "";
            var i = 0;
            var c = c1 = c2 = 0;

            while ( i < utftext.length ) {

                c = utftext.charCodeAt(i);

                if (c < 128) {
                    string += String.fromCharCode(c);
                    i++;
                }
                else if((c > 191) && (c < 224)) {
                    c2 = utftext.charCodeAt(i+1);
                    string += String.fromCharCode(((c & 31) << 6) | (c2 & 63));
                    i += 2;
                }
                else {
                    c2 = utftext.charCodeAt(i+1);
                    c3 = utftext.charCodeAt(i+2);
                    string += String.fromCharCode(((c & 15) << 12) | ((c2 & 63) << 6) | (c3 & 63));
                    i += 3;
                }

            }
            return string;
        }
    },
    json: {
        prettyPrint: function (obj) {
            var jsonLine = /^( *)("[\w ]+": )?("[^"]*"|[\w.+-]*)?([,[{])?$/mg;
            var replacer = function (match, pIndent, pKey, pVal, pEnd) {
                var key = '<span class=json-key>';
                var val = '<span class=json-value>';
                var str = '<span class=json-string>';
                var r = pIndent || '';
                if (pKey)
                    r = r + key + pKey.replace(/([":]| +$)/g, '') + '</span>: ';
                if (pVal)
                    r = r + (pVal[0] == '"' ? str : val) + pVal + '</span>';
                return r + (pEnd || '');
            };
            
            // All done
            return JSON.stringify(obj, null, 3)
                .replace(/&/g, '&amp;').replace(/\\"/g, '&quot;')
                .replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(jsonLine, replacer);
        }
    },
    php: {
        ucwords: function(str) {
            return (str + '').replace(/^(.)|\s+(.)/g, function ($1) {
                return $1.toUpperCase();
            });
        },
        ucfirst: function(str) {
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        strip_tags: function(input, allowed) {
            allowed = (((allowed || '') + '').toLowerCase().match(/<[a-z][a-z0-9]*>/g) || []).join('');
            var tags = /<\/?([a-z][a-z0-9]*)\b[^>]*>/gi;
            var commentsAndPhpTags = /<!--[\s\S]*?-->|<\?(?:php)?[\s\S]*?\?>/gi;
            return input.replace(commentsAndPhpTags, '').replace(tags, function ($0, $1) {
                return allowed.indexOf('<' + $1.toLowerCase() + '>') > -1 ? $0 : '';
            });
        },
        htmlentities: function(str) {
            return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
        },
        // Remove the first value from a numeric array
        removeFirst: function(array, value) {
            // Prepare the result
            var arrayFiltered = [];
            
            // First match flag
            var arrayFirstMatch = false;
            
            // Go through the array
            $.each(array, function(k, v) {
                if (v === value && !arrayFirstMatch) {
                    arrayFirstMatch = true;
                } else {
                    // Re-ordered array
                    arrayFiltered[arrayFiltered.length] = v;
                }
            });
            
            // All done
            return arrayFiltered;
        }
    },
    // Handle the notification
    notify: function(text, type, timeout) {
        // Default type
        if ("undefined" === typeof type) {
            type = "success";
        }
        
        // Default timeout
        if ("undefined" === typeof timeout) {
            timeout = 2000;
        }
        
        // No message to show
        if ("undefined" === typeof text) {
            return;
        }
        
        // Prepare the content
        var content = '<div class="alert alert-__TYPE__ alert-dismissible" role="alert">'
            + '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
            + '__TEXT__'
            + '</div>';

        // Get the content
        content = $(content.replace('__TYPE__', type).replace('__TEXT__', text));

        // Fade out
        content.css({display:"none"});

        // Display the notification
        $('#message-area').append(content);

        // Fade in
        content.slideDown();

        // Auto-close
        window.setTimeout(function(){
            if (!content.is(':hover')) {
                content.slideUp(function(){content.alert('close');});
            }
        }, timeout);
    },
    isLoading: function() {
        return 'true' === $('body').attr('data-loading');
    },
    loading: {
        // Store the objects
        _objects: {},
        
        // Initialize
        _init: function() {
            if (0 === Object.keys(this._objects).length) {
                this._objects = {
                    loading: $('body > .loading'),
                    paragraph: $('body > .loading > span > p'),
                    body: $('body'),
                };
            
                // Get the number of particles
                var numberOfParticles = Math.floor(parseInt(this._objects.body.width(), 10) / 10) + 10;
            
                // Loading particles
                particlesJS && particlesJS(
                    'loading-particles',
                    {
                        "particles": {
                            "number": {
                                "value":  numberOfParticles,
                                "density": {
                                    "enable": false,
                                    "value_area": 600
                                }
                            },
                            "color": {
                                "value": "#34cf3d"
                            },
                            "shape": {
                                "type": "circle",
                                "stroke": {
                                    "width": 1,
                                    "color": "#ffffff"
                                },
                                "polygon": {
                                    "nb_sides": 5
                                }
                            },
                            "opacity": {
                                "value": 0.5,
                                "random": false,
                                "anim": {
                                    "enable": false,
                                    "speed": 1,
                                    "opacity_min": 0.1,
                                    "sync": false
                                }
                            },
                            "size": {
                                "value": 4,
                                "random": true,
                                "anim": {
                                    "enable": true,
                                    "speed": 40,
                                    "size_min": 0.1,
                                    "sync": false
                                }
                            },
                            "line_linked": {
                                "enable": true,
                                "distance": 150,
                                "color": "#1391c5",
                                "opacity": 0.5,
                                "width": 1
                            },
                            "move": {
                                "enable": false,
                                "speed": 10,
                                "direction": "none",
                                "random": true,
                                "straight": false,
                                "out_mode": "out",
                                "bounce": false,
                                "attract": {
                                    "enable": false,
                                    "rotateX": 600,
                                    "rotateY": 1200
                                }
                            }
                        },
                        "interactivity": {
                            "detect_on": "window",
                            "events": {
                                "onhover": {
                                    "enable": true,
                                    "mode": "bubble"
                                },
                                "onclick": {
                                    "enable": true,
                                    "mode": "repulse"
                                },
                                "resize": true
                            },
                            "modes": {
                                "grab": {
                                    "distance": 400,
                                    "line_linked": {
                                        "opacity": 1
                                    }
                                },
                                "bubble": {
                                    "distance": 210,
                                    "size": 30,
                                    "duration": 2,
                                    "opacity": 0.7,
                                    "speed": 3
                                },
                                "repulse": {
                                    "distance": 200,
                                    "duration": 0.4
                                },
                                "push": {
                                    "particles_nb": 4
                                },
                                "remove": {
                                    "particles_nb": 2
                                }
                            }
                        },
                        "retina_detect": false
                    }
                );
            }
        },
        // Show the loading animation
        show: function() {
            // Initialize
            this._init();
            
            // Remove the text
            tools.loading.print('Loading...');
            
            // Show the loading div
            tools.loading._objects.loading.css({display: 'block'});

            // Particle.js active
            if (pJSDom && pJSDom[0]) {
                if (!pJSDom[0].pJS.particles.move.enable) {
                    pJSDom[0].pJS.particles.move.enable = true;
                    pJSDom[0].pJS.fn.particlesRefresh();
                }
            }

            // Fade in
            window.setTimeout(function(){
                tools.loading._objects.body.css({'overflow': 'hidden'}).attr('data-loading', 'true');
            }, 200);
        },
        // Hide the loading animation
        hide: function() {
            // Initialize
            this._init();

            // Particle.js active
            if (pJSDom && pJSDom[0]) {
                pJSDom[0].pJS.particles.move.enable = false;
            }
            
            // Hide the loading div
            tools.loading._objects.loading.css({display: 'none'});
            
            // Fade out
            window.setTimeout(function(){
                tools.loading._objects.body.css({'overflow': 'auto'}).attr('data-loading', 'false');
            }, 200);
            
            // Remove the text
            tools.loading.print('Loading...');
        },
        // Set a text on the loading animation
        print: function(text) {
            // Initialize
            this._init();
            
            // Change the text
            if (text !== tools.loading._objects.paragraph.text()) {
                tools.loading._objects.paragraph.html(text);
                console && console.log('Theme Warlock: ' + $('<div>' + text + '</div>').text());
            }
        },
    },
    performAjaxAllowed: true,
    performAjax: function (url, data, callback, loading, progressCallback) {
        // User needs to log back in
        if (!tools.performAjaxAllowed) {
            return;
        }
        
        // Default callback
        if ("undefined" == typeof(callback)) {
            callback = function(msg) {
                console && console.log(msg);
            };
        }

        // Default loading
        if ("undefined" === typeof loading) {
            loading = true;
        }

        // Prepare the loading
        loading && tools.loading.show();
        
        // Prepare the structure
        var ajaxStructure = {
            method: "POST",
            url: "/ajax/" + url,
            data: data,
            dataType: "JSON",
            timeout: 180000,
            cache: false,
        };
        
        // Callback
        if ("function" == typeof progressCallback) {
            if ("object" == typeof data) {
                // Don't process data
                ajaxStructure.processData = false;
                
                // Assume data is a file
                ajaxStructure.contentType = false;
                
                // No cache
                ajaxStructure.cache = false;
                
                // Type
                ajaxStructure.type = 'POST';

                // Listener
                ajaxStructure.xhr = function(){
                    // get the native XmlHttpRequest object
                    var xhr = $.ajaxSettings.xhr() ;
                    
                    // set the onprogress event handler
                    xhr.upload.onprogress = function(evt){ 
                        progressCallback(evt.loaded/evt.total*100) 
                    };
                    
                    // set the onload event handler
                    xhr.upload.onload = function(){
                        progressCallback(100);
                    };
                    
                    // return the customized object
                    return xhr ;
                };
                
                // Enable the progress bar
                progressCallback(0);
            }
        }
        
        // Perform the request
        $.ajax(ajaxStructure).success(function (msg) {
            // Close the loading
            loading && tools.loading.hide();
            
            // Prepare the text
            var text = "Success!";
            
            // Get the new text
            if (msg.content && msg.content.length) {
                text = msg.content;
            }
            
            // Maybe in the status
            if (!msg.content.length && typeof msg.result == "string" && msg.result.length) {
                text = msg.result;
            }
            
            // Set the notification handler
            if (loading) {
               tools.notify(text, msg.status == "success" ? "success" : "warning");
            }
            
            // Launch the callback
            callback(msg);
        }).error(function (msg) {
            // Close the loading
            loading && tools.loading.hide();
            
            // Login time-out
            if (440 == msg.status) {
                // Stop other AJAX calls
                tools.performAjaxAllowed = false;
                
                // Warn the user
                tools.notify("Session expired! Please log in to continue...", "info");
                
                // Refresh in 2 seconds
                window.setTimeout(function(){
                    window.location.href = window.location.href;
                }, 2000);
            } else {
                // Get the result in JSON format
                var msgJson = msg.responseJSON;
            
                // Prepare the text
                var text = '';

                // Result given
                if ("string" === typeof msgJson.result && msgJson.result.length) {
                    text += msgJson.result + ' ';
                }
                
                // Get the new text
                if ("string" === typeof msgJson.content && msgJson.content.length) {
                    text += '<i>' + msgJson.content + '</i>';
                }
                
                // Log the event
                console && console.warn(msg.status + " (" + msg.statusText + ") at " + url + ": " + text);
                
                // Custom text
                if ('PROJECT_LOCKED_FOR_EXPORT' === msgJson.result) {
                    text = 'Project locked for export';
                }
            
                // Set the notification handler
                tools.notify(text, "danger");

                // Not allowed to edit this project (working on another one)
                if ('EDIT_TOKEN_EXPIRED' === msgJson.result) {
                    window.location.href = '/';
                } else if ('PROJECT_LOCKED_FOR_EXPORT' === msgJson.result) {
                    if (window.location.pathname.length > 0) {
                        window.setTimeout(function(){
                            window.location.href = '/';
                        }, 2000);
                    }
                } else {
                    // Launch the callback
                    callback(msg);
                }
            }
        });
    }
};
$.fn.serializeAssoc = function () {
    var data = {};
    
    // Initialize the checkboxes
    this.find('input:checkbox').map(function() {
        var checkboxName = this.name.replace(/\[\]$/g, '');
        if ("undefined" == typeof data[checkboxName]) {
            // Initialize for jQuery AJAX to work
            data[checkboxName] = null;
        }
    });
    
    $.each(this.serializeArray(), function (key, obj) {
        var a = obj.name.match(/(.*?)\[(.*?)\]/);
        if (a !== null) {
            var subName = a[1];
            var subKey = a[2];

            if (!data[subName]) {
                data[subName] = [];
            }

            if (!subKey.length) {
                subKey = data[subName].length;
            }

            if (data[subName][subKey]) {
                if ($.isArray(data[subName][subKey])) {
                    data[subName][subKey].push(obj.value);
                } else {
                    data[subName][subKey] = [];
                    data[subName][subKey].push(obj.value);
                }
            } else {
                data[subName][subKey] = obj.value;
            }
        } else {
            if (data[obj.name]) {
                if ($.isArray(data[obj.name])) {
                    data[obj.name].push(obj.value);
                } else {
                    data[obj.name] = [];
                    data[obj.name].push(obj.value);
                }
            } else {
                data[obj.name] = obj.value;
            }
        }
    });

    // All done
    return data;
};

/*EOF*/