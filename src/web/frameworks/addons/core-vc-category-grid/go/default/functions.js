{if.core.useStoryline}
$.storyline.addFrame({
    selector: $('.st-vc-category-grid'),
    onEnter: function() {
        var swipe = $(this).find('.isotope');
        if (!swipe.hasClass('visible') && !swipe.data('animating')) {
            swipe.data('animating', true);
            window.setTimeout(function(){
                swipe.addClass('visible');
                swipe.data('animating', false);
            }, 800);
        }
    },
    onLeave: function(){
        $(this).find('.isotope').removeClass('visible');
    },
});
{/if.core.useStoryline}
