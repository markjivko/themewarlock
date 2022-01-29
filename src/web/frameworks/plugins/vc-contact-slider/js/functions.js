jQuery && jQuery(document).ready(function(){
    var $ = jQuery;
    
    // {Plugin.getSlug}
    var {Plugin.getNameClass} = {
        init: function() {
            // Go through all the contact sliders
            $.each($('[data-role="{project.destDir}-contact-slider-form"]'), function(k, v) {
                var sliderFormObject = $(v);

                // Get the map object
                var mapObject = sliderFormObject.find('[data-role="{project.destDir}-contact-slider-map"][data-map-address]');
                if (mapObject.length && "undefined" !== typeof google) {
                    // Remove the warning
                    mapObject.html('');

                    // Find the Latitude/Longitude
                    (new google.maps.Geocoder()).geocode({
                        'address': mapObject.attr('data-map-address')
                    }, function(results, status) {
                        var colorLighter = mapObject.attr('data-map-color-lighter');
                        var colorDarker = mapObject.attr('data-map-color-darker');
                        var colorComplement = mapObject.attr('data-map-color-complement');
                        if(status == google.maps.GeocoderStatus.OK) {
                            var styledMapType = new google.maps.StyledMapType([
                                {
                                    "featureType": "administrative",
                                    "elementType": "labels.text.fill",
                                    "stylers": [
                                        {
                                            "color": colorDarker
                                        }
                                    ]
                                },
                                {
                                    "featureType": "landscape",
                                    "elementType": "all",
                                    "stylers": [
                                        {
                                            "hue": colorLighter
                                        }
                                    ]
                                },
                                {
                                    "featureType": "poi",
                                    "elementType": "all",
                                    "stylers": [
                                        {
                                            "visibility": "off"
                                        }
                                    ]
                                },
                                {
                                    "featureType": "road",
                                    "elementType": "all",
                                    "stylers": [
                                        {
                                            "saturation": -100
                                        },
                                        {
                                            "lightness": 45
                                        }
                                    ]
                                },
                                {
                                    "featureType": "road.highway",
                                    "elementType": "all",
                                    "stylers": [
                                        {
                                            "hue": colorComplement,

                                        },
                                        {
                                            "saturation": 50,
                                        },
                                        {
                                            "visibility": "simplified"
                                        }
                                    ]
                                },
                                {
                                    "featureType": "road.arterial",
                                    "elementType": "labels.icon",
                                    "stylers": [
                                        {
                                            "visibility": "off"
                                        }
                                    ]
                                },
                                {
                                    "featureType": "transit",
                                    "elementType": "all",
                                    "stylers": [
                                        {
                                            "visibility": "off"
                                        }
                                    ]
                                }
                            ]);

                            // Map IDs
                            var mapTypeIds = ['roadmap', 'satellite', 'hybrid', 'terrain'];

                            // Valid colors supplied
                            if (colorLighter !== colorComplement) {
                                mapTypeIds[mapTypeIds.length] = 'styled_map';
                            }

                            // Prepare the Map
                            var map = new google.maps.Map(mapObject[0], { 
                                mapTypeControlOptions: {
                                    mapTypeIds: mapTypeIds
                                },
                                zoom: 15
                            });

                            // Associate the styled map with the MapTypeId and set it to display
                            if (colorLighter !== colorComplement) {
                                map.mapTypes.set('styled_map', styledMapType);
                                map.setMapTypeId('styled_map');
                            }

                            // Set the location
                            new google.maps.Marker({
                                position: results[0].geometry.location,
                                map: map
                            });

                            // Center the map
                            map.setCenter(results[0].geometry.location);
                        }
                    });
                }

                // Toggle button
                var toggleButton = $('<span class="btn btn-info float-right" data-role="contact-toggle"></span>');
                toggleButton.html(sliderFormObject.attr('data-button-text'));

                // Toggle button - On-click event
                toggleButton.click(function(){
                    // Toggle it
                    sliderFormObject.hasClass('active') ? sliderFormObject.removeClass('active') : sliderFormObject.addClass('active');
                });

                // This add-on was placed in the header
                do {
                    if ($(this).parents('aside').hasClass('sidebar-header')) {
                        // Inside of our theme
                        var siblingSocialAnnex = $(this).parents('aside').siblings('[data-role="{project.destDir}-header-social-accounts"]').find('[data-role="annex"]');

                        // Annex found
                        if (siblingSocialAnnex.length) {
                            // Move the button to the social accounts annex
                            toggleButton.appendTo(siblingSocialAnnex);

                            // Stop here
                            break;
                        }
                    }

                    // Move the button to the end of the form
                    toggleButton.insertAfter(sliderFormObject);
                } while(false);

                // AJAX Form
                sliderFormObject.find('form').submit(function(e) {
                    e.preventDefault();
                    
                    // Store the form object
                    var formObject = $(this);

                    // Show the reCaptcha
                    if (formObject.find('.g-recaptcha').length && !formObject.find('.g-recaptcha').is(':visible')) {
                        formObject.find('.g-recaptcha').slideDown();
                        formObject.find('.initial-input').slideUp();

                        // Stop here
                        return false;
                    }

                    // Prepare the form data
                    var formData = formObject.serializeArray().reduce(function(obj, item) {
                        obj[item.name] = item.value;
                        return obj;
                    }, {});

                    /**
                     * Display an error message in the form
                     */
                    var showErrorMessage = function(text) {
                        // Reset the form
                        if (formObject.find('.g-recaptcha').length) {
                            formObject.find('.g-recaptcha').slideUp();
                        }
                        formObject.find('.initial-input').slideDown();

                        // Prepare the error object
                        var errorMessageObject = $('<span class="error-message"><span class="text"></span></span>');

                        // Previous error still present
                        if (formObject.find('.error-message').length) {
                            // Point to the already appended object
                            errorMessageObject = formObject.find('.error-message');
                        } else {
                            // Add the object
                            formObject.prepend(errorMessageObject);
                        }

                        // Set the text
                        errorMessageObject.find('.text').html(text);

                        // Auto-close after 5 seconds
                        window.setTimeout(function() {
                            errorMessageObject.slideUp(function(){
                                $(this).remove();
                            });
                        }, 5000);
                    };

                    // Perform the request
                    $.ajax({
                        method: "POST",
                        url: {project.prefix}_contact_slider.ajax_url,
                        data: formData,
                        dataType: "JSON",
                        timeout: 180000,
                        cache: false,
                    }).success(function(msg) {
                        if ("undefined" !== msg.status) {
                            if (msg.status) {
                                // Replace the form with the success message
                                formObject.replaceWith('<span class="form-success">' + sliderFormObject.attr('data-thank-you-text') + '</span>');
                            } else {
                                // Append the error message
                                showErrorMessage(msg.message);

                                // Reload the recaptcha
                                "undefined" !== typeof grecaptcha && grecaptcha.reset()
                            }
                        } else {
                            showErrorMessage('AJAX failed. Please try again.');
                        }
                    }).error(function(msg) {
                        showErrorMessage('Server connection failed. Please try again.');
                        "undefined" !== typeof grecaptcha && grecaptcha.reset()
                    });

                    // Prevent propagation
                    return false;
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