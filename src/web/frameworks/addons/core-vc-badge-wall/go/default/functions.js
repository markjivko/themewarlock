{if.core.useStoryline}
$.storyline.addFrame({
    selector: $('.st-vc-badge-wall'),
    onActive: function(c) {
        // Do something
        var degrees = c.percent.frameUnCentered > 0 ? -90 : 90;
        
        // Parallax
        $(this).css({
            backgroundPosition: '50% ' + ((100 + c.percent.frameUnCentered) / 2) + '%'
        });
        
        // Find anchors
        $(this).find('a').css({
            width: c.percent.frameVisible + '%',
            height: (70 * c.percent.frameVisible/100) + 'px',
            transform: c.percent.frameVisible == 100 ? 'none' : ('rotate(' + (degrees *(100 - c.percent.frameVisible)/100) + 'deg)'),
        });
    }
});
{/if.core.useStoryline}