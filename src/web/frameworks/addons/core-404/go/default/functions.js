if ("undefined" !== typeof particlesJS && $('body').hasClass('error404') && $('section.error-404').length) {
    // Add the particles container
    $('#primary').append('<div id="the-particles"></div>');
    
    // Get the section object
    var sectionObject = $('section.error-404');
    
    // Get the colors
    var colorA = sectionObject.attr('data-color-a');
    var colorB = sectionObject.attr('data-color-b');
    
    // Animation state
    var animation = true;
    if($('body').hasClass('compose-mode')) {
        animation = false;
    }
    
    // Loading particles
    particlesJS && particlesJS(
        'the-particles',
        {
            "particles": {
                "number": {
                    "value": 50,
                    "density": {
                        "enable": false,
                        "value_area": 300
                    }
                },
                "color": {
                    "value": colorA
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
                    "color": colorB,
                    "opacity": 0.5,
                    "width": 1
                },
                "move": {
                    "enable": animation,
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
                "detect_on": "canvas",
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