{if.core.useStoryline}
$.storyline.addFrame({
    selector: $('.st-vc-blog-posts'),
    onEnter: function() {
        var swipe = $(this).find('.swipe');
        if (!swipe.hasClass('visible') && !swipe.data('animating')) {
            swipe.data('animating', true);
            window.setTimeout(function(){
                swipe.addClass('visible');
                swipe.data('animating', false);
            }, 800);
        }
    },
    onLeave: function(){
        $(this).find('.swipe').removeClass('visible');
    },
});
{/if.core.useStoryline}