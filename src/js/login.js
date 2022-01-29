jQuery(document).ready(function(){
    var $ = jQuery;
    
    // Prepare the elements
    var elements = $(
        '<div class="elements">'+
            '<label for="username" class="sr-only">E-mail address</label>' + 
            '<input type="email" id="username" name="username" class="form-control" placeholder="E-mail address" required autofocus value="' + loginBinome.username + '">' +
            '<label for="password" class="sr-only">Password</label>' +
            '<input type="password" id="password" name="password" class="form-control" placeholder="Password" required value="' + loginBinome.password + '">' +
            '<button class="btn btn-lg btn-success btn-block" type="submit">Log in</button>' + 
        '</div>'
    ).css({display: 'none'});
    
    // Append them to the form
    $('.form-signin').append(elements);
    
    // Prepare the signin
    var quotes = {
        object: $('.form-signin-heading'),
        get: function() {
            // Hide the quote
            quotes.object.slideUp(400, function(){
                // Get a quote from the server
                tools.performAjax('quote/get', {}, function(msg) {
                    if ("success" == msg.status) {
                        // Set the quote
                        quotes.object.html(msg.result['text']);
                        
                        // Set the author
                        quotes.object.attr('data-original-title', msg.result['author']);
                        
                        // Enable the tooltip
                        quotes.object.tooltip();
                        
                        // Show the quote
                        quotes.object.slideDown();
                    }
                }, false);
            });
        },
        init: function() {
            quotes.object.click(function(){
                // Get a new quote
                quotes.get();
                
                // Hide the elements
                elements.slideUp(1000);
            });
        }
    };
    
    // Initialize the quotes
    quotes.init();
    
    // Get the first quote
    quotes.get();
    
    // Enable the tooltip
    $('.logo').click(function() {
        if (!quotes.object.is(':visible')) {
            // Show the quotes
            quotes.object.slideDown();
        } else {
            // Hide the quotes
            quotes.object.slideUp();
            
            // Show the elements
            if (!elements.is(':visible')) {
                elements.slideDown(1500);
            }
        }
    });
    
    // Show the elements
    window.setTimeout(function(){
        elements.slideDown(1500);
    }, 1500);
});
