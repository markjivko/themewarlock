jQuery && jQuery(document).ready(function(){
    var $ = jQuery;
    
    // Global functions - PHP ports
    var intval = function (mixed_var, base) {var tmp;var type = typeof(mixed_var);if (type === 'boolean') {return +mixed_var;} else if (type === 'string') {tmp = parseInt(mixed_var, base || 10);return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;} else if (type === 'number' && isFinite(mixed_var)) {return mixed_var | 0;} else {return 0;}};
    
    // {Plugin.getSlug}
    var {Plugin.getNameClass} = {
        init: function() {
            // Multi-instance
            $.each($('.{Plugin.getSlug}'), function() {
                // Tweets div
                var tweetsDiv = $(this).find('.tweets');

                // Get the animation speed
                var animationSpeed = tweetsDiv.attr('data-speed') ? intval(tweetsDiv.attr('data-speed')) : 2500;
                animationSpeed = animationSpeed < 500 ? 500 : (animationSpeed > 10000 ? 10000: animationSpeed);
                
                // Get the tweets
                tweetsDiv.html('<h2>' + tweetsDiv.attr('data-loading') + '</h2>');
                $.ajax({
                    method: "POST",
                    url: tweetsDiv.attr('data-action'),
                    data: {
                        username: tweetsDiv.attr('data-username'),
                        cache_hours: tweetsDiv.attr('data-cache'),
                        tweets_count: tweetsDiv.attr('data-count'),
                        ignore_replies: 'true' == tweetsDiv.attr('data-ignore-replies'),
                        include_retweets: 'true' == tweetsDiv.attr('data-include-retweets'),
                        style_timestamp: 'true' == tweetsDiv.attr('data-style-timestamp'),
                    },
                    dataType: "JSON",
                    timeout: 180000,
                    cache: false,
                }).success(function(tweets) {
                    if (tweets.length) {
                        // Prepare the tweet elements and navigation elements
                        var tweetElements = $('<div class="elements row no-gutters align-items-center h-100"></div>'), navigationElements = $('<div class="navigation"></div>');

                        // Parse the tweets
                        for (var i in tweets) {
                            if (tweets.hasOwnProperty(i)) {
                                var tw = $('<div class="tw">' + tweets[i].desc + '<div class="time"><a href="http://twitter.com/' + tweetsDiv.attr('data-username') + '" target="_blank" class="icon-s-twitter"></a>' + tweets[i].time + '</div></div>');
                                var nav = $('<i data-target="' + i + '">*</i>');

                                // Append to tweet elements
                                tweetElements.append(tw);

                                // Append to navigation elements
                                navigationElements.append(nav);
                            }
                        }

                        // Navigation actions
                        navigationElements.children().on('click', function(){
                            // Store this
                            var navElement = $(this);

                            // Make other elements inactive
                            tweetElements.children().fadeOut(500);
                            navigationElements.children().removeClass('active');

                            // Make the current element active
                            navElement.addClass('active');

                            window.setTimeout(function(){
                                tweetElements.children(':nth-child(' + (intval(navElement.attr('data-target')) + 1) + ')').fadeIn();
                            }, 510);
                        });

                        // Click on the first button
                        navigationElements.children().first().click();

                        var iterator = {
                            next: function() {
                                // Get the index
                                var index = navigationElements.children('.active').index();

                                // Increment
                                if (++index >= tweets.length) {
                                    index = 0;
                                }
                                navigationElements.children(':nth-child(' + (index+1) + ')').click();
                            },
                            run: function() {
                                if (!tweetsDiv.is(":hover")) {
                                    iterator.next();
                                }
                                window.setTimeout(iterator.run, animationSpeed);
                            },
                        };
                        window.setTimeout(iterator.run, animationSpeed);

                        // Prepend the items
                        tweetsDiv.html('').prepend(tweetElements).prepend(navigationElements);
                        return;
                    }
                    tweetsDiv.html('<h2>' + tweetsDiv.attr('data-no-tweets') + '</h2>');
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