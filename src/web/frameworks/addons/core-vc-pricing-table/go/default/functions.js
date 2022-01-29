{if.core.useStoryline}
$.storyline.addFrame({
    selector: $('.st-vc-pricing-table'),
    onActive: function(c) {
        // Stop when user is interacting with table
        if ($(this).is(':hover')) {
            return;
        }

        // Get the rows
        var rows = $(this).find('[data-role="table"] > .row > .col');

        // Valid number of rows found
        if (rows.length) {
            // Compute the scroll percent
            var percent = 100 - ((100 + c.percent.frameUnCentered) / 2); // 0 to 100

            // Get the row index
            var rowIndex = parseInt((rows.length - 1) * percent / 100, 10);

            // Simulate a hover event
            rows.eq(rowIndex).mouseover();
        }
    },
});
{/if.core.useStoryline}
