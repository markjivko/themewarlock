{if.core.useStoryline}
$.storyline.addFrame({
    selector: $('.st-vc-swipe-blocks'),
    onActive: function(c){
        var degrees = c.percent.frameUnCentered > 0 ? -90 : 90;
        $(this).find('.author').css({
            width: (150 * c.percent.frameVisible/100) + 'px',
            height: (150 * c.percent.frameVisible/100) + 'px',
            transform: c.percent.frameVisible == 100 ? 'none' : ('rotate(' + (degrees *(100 - c.percent.frameVisible)/100) + 'deg)'),
        });
    },
});
{/if.core.useStoryline}
