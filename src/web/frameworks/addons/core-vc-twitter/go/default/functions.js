{if.core.useStoryline}
$.storyline.addFrame({
    selector: $('.st-vc-twitter'),
    onActive: function(c) {
        $(this).css({
            backgroundPosition: '50% ' + ((100 + c.percent.frameUnCentered) / 2) + '%'
        });
    }
});
{/if.core.useStoryline}