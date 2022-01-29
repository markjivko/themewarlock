    // Prepare the menu object
    var menuObject = $('header[data-role="banner"]');
    
    // Floating menu feature on
    if (menuObject.find('nav').hasClass('is-floating')) {
        // Prepare the page object
        var pageObject = $('#page');
        var contentObject = $('#content');

        // Calculate the menu offset
        var menuOffsetTop = menuObject.offset().top - ($('body').hasClass('admin-bar') ? 32: 0);

        // Prepare the floating-menu toggle tools
        var tools = {
            lastAction: null,
            hide: function () {
                // Prevent multiple calls
                if ('hide' == tools.lastAction) {
                    return;
                }

                // Relative positioned element
                menuObject.css({width: 'auto'});
                if (pageObject.hasClass('floating-menu')) {
                    pageObject.removeClass('floating-menu');
                }

                // Store the last action
                tools.lastAction = 'hide';
            },
            show: function () {
                // Hide for mobile devices
                if ($(window).width() < 992) {
                    return tools.hide();
                }

                // Auto-adjust the width
                if (contentObject.width() != menuObject.width()) {
                    menuObject.width(contentObject.width());
                }

                // Prevent multiple calls
                if ('show' == tools.lastAction) {
                    return;
                }

                // Floating bar, static positioning
                if (!pageObject.hasClass('floating-menu')) {
                    pageObject.addClass('floating-menu');
                }

                // Store the last action
                tools.lastAction = 'show';
            }
        };

        // Check for changes regularly
        var adjustMenu = function () {
            // Over the initial menu offset
            if ($(window).scrollTop() >= menuOffsetTop) {
                tools.show();
            } else {
                tools.hide();
            }
        };
        
        // Call the menu adjustment
        $(window).resize(adjustMenu);
        $(window).scroll(adjustMenu);
    }