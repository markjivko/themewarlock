    $.each($('.st-vc-vendor-revslider-wrapper'), function(k, v) {
        // Prepare the prefix
        var beforeObject = $(v).find('.st-vc-vendor-revslider-before');
        if (!beforeObject.length) {
            beforeObject = $('<div class="st-vc-vendor-revslider-before"></div>');
            $(v).children('div').prepend(beforeObject);
        }
        
        // Prepare the suffix
        var afterObject = $(v).find('.st-vc-vendor-revslider-after');
        if (!afterObject.length) {
            afterObject = $('<div class="st-vc-vendor-revslider-after"></div>');
            $(v).children('div').append(afterObject);
        }

        // Get the dimensions
        var width = afterObject.width();
        var height = 150;

        // Prepare the canvas
        var canvas = $('<canvas></canvas>').attr('width', width).attr('height', height);

        // Prepare the context
        var ctx = canvas[0].getContext('2d');

        // Draw the white triangle
        ctx.beginPath();
        
        // Top-right
        ctx.moveTo(width, 0);  
        
        // Bottom-left
        ctx.lineTo(0, height);
        
        // Bottom-right
        ctx.lineTo(width, height);
        
        // Background color
        ctx.fillStyle='#ffffff';
        
        // Fill the path
        ctx.fill();
        
        // Close the triangle
        ctx.closePath();

        // Append the canvas to the container
        afterObject.append(canvas);
    });
{if.core.useStoryline}
// Add the storyline frame
$.storyline.addFrame({
    selector: $('.st-vc-vendor-revslider-wrapper'),
    onActive: function(c) {
        $(this).find('.st-vc-vendor-revslider-after').css({
            height: (c.percent.frameUnCentered <= 0 ? -c.percent.frameUnCentered : 0) / 100 * 150 + 'px',
        });
    },
    showInMenu: false
});
{/if.core.useStoryline}
