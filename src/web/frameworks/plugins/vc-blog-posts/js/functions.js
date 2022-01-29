jQuery && jQuery(document).ready(function(){
    var $ = jQuery;
    
    // {Plugin.getSlug}
    var {Plugin.getNameClass} = {
        init: function() {
            // Multi-instance
            $.each($('.{Plugin.getSlug} .swipe'), function(){
                // Get the children
                var frames = $(this).children('div');
                var thisSwipe = $(this);

                // No frames to iterate over
                if (!frames.length) {
                    return;
                }

                // Get the iterator
                var iterator = {
                    current: 0,
                    max: frames.length-1,
                    prev: function(){
                        var ci = iterator.current;
                        var slideWidth = frames.first().outerWidth();
                        var slidesPerSwipe = Math.round(thisSwipe.width() / slideWidth);
                        ci--;
                        if (ci < 0) {
                            ci = iterator.max - slidesPerSwipe + 1;
                        }
                        if (ci !== iterator.current && (iterator.max - iterator.current + 1) >= slidesPerSwipe) {
                            iterator.current = ci;
                            return true;
                        }
                        return false;
                    },
                    next: function(){
                        var ci = iterator.current;
                        var slideWidth = frames.first().outerWidth();
                        var slidesPerSwipe = Math.round(thisSwipe.width() / slideWidth);
                        ci++;
                        if (ci > iterator.max - slidesPerSwipe + 1) {
                            ci = 0;
                        }
                        if (ci !== iterator.current && (iterator.max - iterator.current + 1) >= slidesPerSwipe) {
                            iterator.current = ci;
                            return true;
                        }
                        return false;
                    },
                    animate: function() {
                        var slideWidth = frames.first().outerWidth();
                        for (var i = 0; i <= iterator.max; i++) {
                            var marginLeft = (i - iterator.current) * slideWidth;
                            $(frames[i]).stop().animate({marginLeft: marginLeft, left: 0, top: 0},{duration: 500});
                        }
                    }
                };

                // Create the navigation
                var navigation = $('<div class="swipe-navigation"></div>');
                var navLeft = $('<div class="left"><i class="icon-left-open-big">&lt;</i><div>');
                var navRight = $('<div class="right"><i class="icon-right-open-big">&gt;</i><div>');
                navLeft.appendTo(navigation);
                navRight.appendTo(navigation);

                // Left navigation button click event
                navLeft.click(function(){
                    if (iterator.prev()) {
                        iterator.animate();
                    }
                });

                // Right navigation button click event
                navRight.click(function(){
                    if (iterator.next()) {
                        iterator.animate();
                    }
                });

                // Make each slide draggable
                $.each(frames, function(){
                    $(this).draggable({
                        drag: function(e,ui) {
                            $.each(frames.not(e.target), function(){
                                $(this).css({
                                    left: ui.helper.position().left,
                                });
                            });
                        },
                        stop: function(e,ui) {
                            // Compute the number of slides per swipe
                            var slidesPerSwipe = Math.round(thisSwipe.width() / frames.first().outerWidth());

                            // Compute direction of travel
                            var direction = ui.helper.position().left > 0 ? 'prev' : 'next';

                            // Compute the steps
                            var steps = Math.abs(Math.round(ui.helper.position().left/$(e.target).outerWidth()));

                            // No steps, revert to no actual
                            if (steps > 0) {
                                for (var i = 1; i <= steps; i++) {
                                    // Do not allow overflowing (less than the first element or more than the last)
                                    if (('prev' !== direction || 0 !== iterator.current) && ('next' !== direction || iterator.current !== iterator.max - slidesPerSwipe + 1)) {
                                        iterator[direction]();
                                    }
                                }

                            }
                            iterator.animate();
                        }
                    });
                });

                // Append the navigation to the parent
                $(this).parent().append(navigation);

                // On resize, recreate the animation
                $(window).resize(iterator.animate);

                // Animate for the first time
                iterator.animate();
            });
        },
    };
    
    // Initialize
    {Plugin.getNameClass}.init();
    
    // Visual Composer Reload
    $(window).on('vc_reload', {Plugin.getNameClass}.init);
});

/*EOF*/