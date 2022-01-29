/**
 * {Addon.projectName} functionality
 * 
 * {utils.common.copyright}
 */
// jQuery UI Touch Punch 0.2.3, Copyright 2011–2014, Dave Furfero
!function(a){function f(a,b){if(!(a.originalEvent.touches.length>1)){a.preventDefault();var c=a.originalEvent.changedTouches[0],d=document.createEvent("MouseEvents");d.initMouseEvent(b,!0,!0,window,1,c.screenX,c.screenY,c.clientX,c.clientY,!1,!1,!1,!1,0,null),a.target.dispatchEvent(d)}}if(a.support.touch="ontouchend"in document,a.support.touch){var e,b=a.ui.mouse.prototype,c=b._mouseInit,d=b._mouseDestroy;b._touchStart=function(a){var b=this;!e&&b._mouseCapture(a.originalEvent.changedTouches[0])&&(e=!0,b._touchMoved=!1,f(a,"mouseover"),f(a,"mousemove"),f(a,"mousedown"))},b._touchMove=function(a){e&&(this._touchMoved=!0,f(a,"mousemove"))},b._touchEnd=function(a){e&&(f(a,"mouseup"),f(a,"mouseout"),this._touchMoved||f(a,"click"),e=!1)},b._mouseInit=function(){var b=this;b.element.bind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),c.call(b)},b._mouseDestroy=function(){var b=this;b.element.unbind({touchstart:a.proxy(b,"_touchStart"),touchmove:a.proxy(b,"_touchMove"),touchend:a.proxy(b,"_touchEnd")}),d.call(b)}}}(jQuery);

// DOM Ready
jQuery && jQuery(document).ready(function(){
    "use strict";
    var $ = jQuery;
    
    // Global functions - PHP ports
    var intval = function (mixed_var, base) {var tmp;var type = typeof(mixed_var);if (type === 'boolean') {return +mixed_var;} else if (type === 'string') {tmp = parseInt(mixed_var, base || 10);return (isNaN(tmp) || !isFinite(tmp)) ? 0 : tmp;} else if (type === 'number' && isFinite(mixed_var)) {return mixed_var | 0;} else {return 0;}};
    
    // Define the main handler
    var {project.prefix}_handler = function() {
        var _this = this;
        
        // Store the workers
        this.workers = {};
        
        /**
         * Store the worker
         */
        this.addWorker = function(name, el) {
            if ("string" === typeof name && "function" === typeof el) {
                this.workers[name] = el;
            }
        };
        
        // Initiate the project
        this.init = function() {
            // Go through the events
            $.each(this.workers, function(name, el) {
                if("function" === typeof el) {
                    el(name, _this);
                }
            });
        };
        
    };
    
    // Prepare the handler
    var {project.prefix}_instance = new {project.prefix}_handler();
    
    // #{project.prefix}-bootstrap-init worker
    {project.prefix}_instance.addWorker("#{project.prefix}-bootstrap-init", function(addonName, _this) {
        // Stylize all buttons
        $('#content').find('button,input[type="submit"],input[type="button"],#comments .comment-reply-link,.post-edit-link,.vc_inline-link').not('.btn').addClass('btn');
        
        // Stylize all input fields
        $('#content').find('textarea,input[type="text"],input[type="search"],input[type="url"],input[type="password"],input[type="email"],input[type="number"]').not('.form-control').addClass('form-control');
    });
    
    // #Workers#
    
{if.core.useStoryline}
    // #{project.prefix}-storyline-init worker
    {project.prefix}_instance.addWorker("#{project.prefix}-storyline-init", function(addonName, _this) {
        $.storyline.run({
            buildMenu: {project.prefix}.storyline_build_menu,
{if.core.debugging}
            guide: true,
{/if.core.debugging}
        });
    });
    
    // Show the storyline elements for screenshots
    if(/\bThemeWarlock\-Screenshot\b/g.test(navigator.userAgent)) {
        $('body').addClass('compose-mode');
    }
{/if.core.useStoryline}
    
    // Initialize
    {project.prefix}_instance.init();
    
});

/*EOF*/