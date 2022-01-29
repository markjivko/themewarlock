/**
 * {project.destProjectName} Common Tools
 * 
 * {utils.common.copyright}
 */
if ("object" === typeof {project.prefix}) {
    {project.prefix}.tools = {
        
        _objects: {},
        
        _data: {},
        
        /**
         * AJAX method that handles nonce validation and, optionally, 
         * WP_FileSystem request dialogs
         * 
         * @param string   methodName AJAX Method name
         * @param object   methodArgs (optional) Method arguments associative 
         * array; default <b>{}</b>
         * @param function callback   (optional) Function to call when the AJAX 
         * request completes; default <b>empty</b>
         * @param boolean  validateFs (optional) Requst filesystem credentials 
         * for this AJAX call; set to <b>false</b> when <b>$wp_filesytem</b> is
         * not required in the backend method; default <b>true</b>
         */
        ajax: function(methodName, methodArgs, callback, validateFs) {
            var $ = jQuery, _this = this;

            // Prepare the request dialog object
            if ("undefined" === typeof _this._objects.requestFsDialog) {
                _this._objects.requestFsDialog = $('#request-filesystem-credentials-dialog');
            }
            
            // Set the defaults
            if ("undefined" === typeof methodName) {
                return;
            }
            if ("object" !== typeof methodArgs) {
                methodArgs = [];
            }
            if ("function" !== typeof callback) {
                callback = null;
            }
            if ("undefined" === typeof validateFs) {
                validateFs = true;
            }
            
            // Prepare the request data
            if (validateFs && "undefined" === typeof _this._data.requestFsData) {
                _this._data.requestFsData = {};
            }

            // Dialog found
            if (validateFs && _this._objects.requestFsDialog.length) {
                // Prepare the notification area
                _this._objects.requestFsDialogNotificationArea = $('<div></div>');

                // Prepend it to the dialog
                _this._objects.requestFsDialogNotificationArea.insertAfter(_this._objects.requestFsDialog.find('.request-filesystem-credentials-form > h2'));

                // Close action
                _this._objects.requestFsDialog.find('[data-js-action="close"]').click(function(){
                    // Hide the dialog
                    $('body').removeClass('modal-open');
                    _this._objects.requestFsDialog.hide();
                });
            }

            // Prepare the ajax data
            var ajaxData = validateFs ? _this._data.requestFsData : {};

            // Implement our action, method, arguments and nonce
            ajaxData.action = '{project.prefix}_action';
            ajaxData.st_ajax_method      = methodName;
            ajaxData.st_ajax_args        = methodArgs;
            ajaxData.st_ajax_nonce       = {project.prefix}.ajax_nonce;
            ajaxData.st_ajax_validate_fs = validateFs;

            // Perform the request
            $.ajax({
                type: "POST",
                url: {project.prefix}.ajax_url,
                data: ajaxData,
                dataType: "JSON"
            }).success(function (msg) {
                // FileSystem failure
                if (validateFs && !msg.success && "undefined" !== typeof msg.errorCode && 'unable_to_connect_to_filesystem' === msg.errorCode && _this._objects.requestFsDialog.length) {
                    // Open modal
                    !$('body').hasClass('modal-open') && $('body').addClass('modal-open');

                    // Show the dialog
                    _this._objects.requestFsDialog.show();

                    // Send the request form by AJAX
                    _this._objects.requestFsDialog.find('form').unbind('submit').submit(function(e) {
                        e.preventDefault();

                        // Remove the notifications
                        _this._objects.requestFsDialogNotificationArea.attr('class', '').html('');

                        // Hide the dialog
                        $('body').removeClass('modal-open');
                        _this._objects.requestFsDialog.hide();

                        // Prepare the form data
                        var formData = {};
                        $.each($(this).serializeArray(), function(k,v){
                            formData[v.name] = v.value;
                        });

                        // Store this data for later
                        _this._data.requestFsData = formData;

                        // Preserve our action, method, arguments and nonce
                        formData.action = ajaxData.action;
                        formData.st_ajax_method      = ajaxData.st_ajax_method;
                        formData.st_ajax_args        = ajaxData.st_ajax_args;
                        formData.st_ajax_nonce       = ajaxData.st_ajax_nonce;
                        formData.st_ajax_validate_fs = ajaxData.st_ajax_validate_fs;

                        // Send a new request
                        $.ajax({
                            type: "POST",
                            url: {project.prefix}.ajax_url,
                            data: formData,
                            dataType: "JSON"
                        }).success(function(msg){
                            if (!msg.success) {
                                // Show the dialog
                                !$('body').hasClass('modal-open') && $('body').addClass('modal-open');
                                _this._objects.requestFsDialog.show();

                                // FileSystem Request failed
                                _this._objects.requestFsDialogNotificationArea.attr('class', 'notice notice-alt notice-error').html('<p>' + msg.errorMessage + '</p>');
                            } else {
                                // Execute the callback
                                if ("function" === typeof callback) {
                                    callback(msg);
                                }
                            }
                        });

                        // Stop here
                        return false;
                    });
                } else {
                    if ("function" === typeof callback) {
                        callback(msg);
                    }
                }
            });
        },
    };
}

/**
 * jQuery Form Plugin
 * version: 3.51.0
 * Requires jQuery v1.5 or later
 * Copyright (c) 2014 M. Alsup
 * Examples and documentation at: http://malsup.com/jquery/form/
 * Project repository: https://github.com/malsup/form
 * Dual licensed under the MIT and GPL licenses.
 * https://github.com/malsup/form#copyright-and-license
 */
!function(e){"use strict";"function"==typeof define&&define.amd?define(["jquery"],e):e("undefined"!=typeof jQuery?jQuery:window.Zepto)}(function(e){"use strict";var t={};t.fileapi=void 0!==e("<input type='file'/>").get(0).files,t.formdata=void 0!==window.FormData;var r=!!e.fn.prop;function a(t){var r=t.data;t.isDefaultPrevented()||(t.preventDefault(),e(t.target).ajaxSubmit(r))}function n(t){var r=t.target,a=e(r);if(!a.is("[type=submit],[type=image]")){var n=a.closest("[type=submit]");if(0===n.length)return;r=n[0]}var i=this;if(i.clk=r,"image"==r.type)if(void 0!==t.offsetX)i.clk_x=t.offsetX,i.clk_y=t.offsetY;else if("function"==typeof e.fn.offset){var o=a.offset();i.clk_x=t.pageX-o.left,i.clk_y=t.pageY-o.top}else i.clk_x=t.pageX-r.offsetLeft,i.clk_y=t.pageY-r.offsetTop;setTimeout(function(){i.clk=i.clk_x=i.clk_y=null},100)}function i(){if(e.fn.ajaxSubmit.debug){var t="[jquery.form] "+Array.prototype.join.call(arguments,"");window.console&&window.console.log?window.console.log(t):window.opera&&window.opera.postError&&window.opera.postError(t)}}e.fn.attr2=function(){if(!r)return this.attr.apply(this,arguments);var e=this.prop.apply(this,arguments);return e&&e.jquery||"string"==typeof e?e:this.attr.apply(this,arguments)},e.fn.ajaxSubmit=function(a){if(!this.length)return i("ajaxSubmit: skipping submit process - no element selected"),this;var n,o,s,u=this;"function"==typeof a?a={success:a}:void 0===a&&(a={}),n=a.type||this.attr2("method"),(s=(s="string"==typeof(o=a.url||this.attr2("action"))?e.trim(o):"")||window.location.href||"")&&(s=(s.match(/^([^#]+)/)||[])[1]),a=e.extend(!0,{url:s,success:e.ajaxSettings.success,type:n||e.ajaxSettings.type,iframeSrc:/^https/i.test(window.location.href||"")?"javascript:false":"about:blank"},a);var c={};if(this.trigger("form-pre-serialize",[this,a,c]),c.veto)return i("ajaxSubmit: submit vetoed via form-pre-serialize trigger"),this;if(a.beforeSerialize&&!1===a.beforeSerialize(this,a))return i("ajaxSubmit: submit aborted via beforeSerialize callback"),this;var l=a.traditional;void 0===l&&(l=e.ajaxSettings.traditional);var f,m=[],p=this.formToArray(a.semantic,m);if(a.data&&(a.extraData=a.data,f=e.param(a.data,l)),a.beforeSubmit&&!1===a.beforeSubmit(p,this,a))return i("ajaxSubmit: submit aborted via beforeSubmit callback"),this;if(this.trigger("form-submit-validate",[p,this,a,c]),c.veto)return i("ajaxSubmit: submit vetoed via form-submit-validate trigger"),this;var d=e.param(p,l);f&&(d=d?d+"&"+f:f),"GET"==a.type.toUpperCase()?(a.url+=(a.url.indexOf("?")>=0?"&":"?")+d,a.data=null):a.data=d;var h=[];if(a.resetForm&&h.push(function(){u.resetForm()}),a.clearForm&&h.push(function(){u.clearForm(a.includeHidden)}),!a.dataType&&a.target){var v=a.success||function(){};h.push(function(t){var r=a.replaceTarget?"replaceWith":"html";e(a.target)[r](t).each(v,arguments)})}else a.success&&h.push(a.success);if(a.success=function(e,t,r){for(var n=a.context||this,i=0,o=h.length;i<o;i++)h[i].apply(n,[e,t,r||u,u])},a.error){var g=a.error;a.error=function(e,t,r){var n=a.context||this;g.apply(n,[e,t,r,u])}}if(a.complete){var x=a.complete;a.complete=function(e,t){var r=a.context||this;x.apply(r,[e,t,u])}}var b=e("input[type=file]:enabled",this).filter(function(){return""!==e(this).val()}).length>0,y="multipart/form-data",T=u.attr("enctype")==y||u.attr("encoding")==y,j=t.fileapi&&t.formdata;i("fileAPI :"+j);var w,S=(b||T)&&!j;!1!==a.iframe&&(a.iframe||S)?a.closeKeepAlive?e.get(a.closeKeepAlive,function(){w=k(p)}):w=k(p):w=(b||T)&&j?function(t){for(var r=new FormData,i=0;i<t.length;i++)r.append(t[i].name,t[i].value);if(a.extraData){var o=function(t){var r,n,i=e.param(t,a.traditional).split("&"),o=i.length,s=[];for(r=0;r<o;r++)i[r]=i[r].replace(/\+/g," "),n=i[r].split("="),s.push([decodeURIComponent(n[0]),decodeURIComponent(n[1])]);return s}(a.extraData);for(i=0;i<o.length;i++)o[i]&&r.append(o[i][0],o[i][1])}a.data=null;var s=e.extend(!0,{},e.ajaxSettings,a,{contentType:!1,processData:!1,cache:!1,type:n||"POST"});a.uploadProgress&&(s.xhr=function(){var t=e.ajaxSettings.xhr();return t.upload&&t.upload.addEventListener("progress",function(e){var t=0,r=e.loaded||e.position,n=e.total;e.lengthComputable&&(t=Math.ceil(r/n*100)),a.uploadProgress(e,r,n,t)},!1),t});s.data=null;var u=s.beforeSend;return s.beforeSend=function(e,t){a.formData?t.data=a.formData:t.data=r,u&&u.call(this,e,t)},e.ajax(s)}(p):e.ajax(a),u.removeData("jqxhr").data("jqxhr",w);for(var D=0;D<m.length;D++)m[D]=null;return this.trigger("form-submit-notify",[this,a]),this;function k(t){var o,s,c,l,f,p,d,h,v,g,x,b,y=u[0],T=e.Deferred();if(T.abort=function(e){h.abort(e)},t)for(s=0;s<m.length;s++)o=e(m[s]),r?o.prop("disabled",!1):o.removeAttr("disabled");if((c=e.extend(!0,{},e.ajaxSettings,a)).context=c.context||c,f="jqFormIO"+(new Date).getTime(),c.iframeTarget?(g=(p=e(c.iframeTarget)).attr2("name"))?f=g:p.attr2("name",f):(p=e('<iframe name="'+f+'" src="'+c.iframeSrc+'" />')).css({position:"absolute",top:"-1000px",left:"-1000px"}),d=p[0],h={aborted:0,responseText:null,responseXML:null,status:0,statusText:"n/a",getAllResponseHeaders:function(){},getResponseHeader:function(){},setRequestHeader:function(){},abort:function(t){var r="timeout"===t?"timeout":"aborted";i("aborting upload... "+r),this.aborted=1;try{d.contentWindow.document.execCommand&&d.contentWindow.document.execCommand("Stop")}catch(e){}p.attr("src",c.iframeSrc),h.error=r,c.error&&c.error.call(c.context,h,r,t),l&&e.event.trigger("ajaxError",[h,c,r]),c.complete&&c.complete.call(c.context,h,r)}},(l=c.global)&&0==e.active++&&e.event.trigger("ajaxStart"),l&&e.event.trigger("ajaxSend",[h,c]),c.beforeSend&&!1===c.beforeSend.call(c.context,h,c))return c.global&&e.active--,T.reject(),T;if(h.aborted)return T.reject(),T;(v=y.clk)&&(g=v.name)&&!v.disabled&&(c.extraData=c.extraData||{},c.extraData[g]=v.value,"image"==v.type&&(c.extraData[g+".x"]=y.clk_x,c.extraData[g+".y"]=y.clk_y));var j=1,w=2;function S(e){var t=null;try{e.contentWindow&&(t=e.contentWindow.document)}catch(e){i("cannot get iframe.contentWindow document: "+e)}if(t)return t;try{t=e.contentDocument?e.contentDocument:e.document}catch(r){i("cannot get iframe.contentDocument: "+r),t=e.document}return t}var D=e("meta[name=csrf-token]").attr("content"),k=e("meta[name=csrf-param]").attr("content");function A(){var t=u.attr2("target"),r=u.attr2("action"),a=u.attr("enctype")||u.attr("encoding")||"multipart/form-data";y.setAttribute("target",f),n&&!/post/i.test(n)||y.setAttribute("method","POST"),r!=c.url&&y.setAttribute("action",c.url),c.skipEncodingOverride||n&&!/post/i.test(n)||u.attr({encoding:"multipart/form-data",enctype:"multipart/form-data"}),c.timeout&&(b=setTimeout(function(){x=!0,O(j)},c.timeout));var o=[];try{if(c.extraData)for(var s in c.extraData)c.extraData.hasOwnProperty(s)&&(e.isPlainObject(c.extraData[s])&&c.extraData[s].hasOwnProperty("name")&&c.extraData[s].hasOwnProperty("value")?o.push(e('<input type="hidden" name="'+c.extraData[s].name+'">').val(c.extraData[s].value).appendTo(y)[0]):o.push(e('<input type="hidden" name="'+s+'">').val(c.extraData[s]).appendTo(y)[0]));c.iframeTarget||p.appendTo("body"),d.attachEvent?d.attachEvent("onload",O):d.addEventListener("load",O,!1),setTimeout(function e(){try{var t=S(d).readyState;i("state = "+t),t&&"uninitialized"==t.toLowerCase()&&setTimeout(e,50)}catch(e){i("Server abort: ",e," (",e.name,")"),O(w),b&&clearTimeout(b),b=void 0}},15);try{y.submit()}catch(e){document.createElement("form").submit.apply(y)}}finally{y.setAttribute("action",r),y.setAttribute("enctype",a),t?y.setAttribute("target",t):u.removeAttr("target"),e(o).remove()}}k&&D&&(c.extraData=c.extraData||{},c.extraData[k]=D),c.forceSync?A():setTimeout(A,10);var L,E,M,F=50;function O(t){if(!h.aborted&&!M){if((E=S(d))||(i("cannot access response document"),t=w),t===j&&h)return h.abort("timeout"),void T.reject(h,"timeout");if(t==w&&h)return h.abort("server abort"),void T.reject(h,"error","server abort");if(E&&E.location.href!=c.iframeSrc||x){d.detachEvent?d.detachEvent("onload",O):d.removeEventListener("load",O,!1);var r,a="success";try{if(x)throw"timeout";var n="xml"==c.dataType||E.XMLDocument||e.isXMLDoc(E);if(i("isXml="+n),!n&&window.opera&&(null===E.body||!E.body.innerHTML)&&--F)return i("requeing onLoad callback, DOM not available"),void setTimeout(O,250);var o=E.body?E.body:E.documentElement;h.responseText=o?o.innerHTML:null,h.responseXML=E.XMLDocument?E.XMLDocument:E,n&&(c.dataType="xml"),h.getResponseHeader=function(e){return{"content-type":c.dataType}[e.toLowerCase()]},o&&(h.status=Number(o.getAttribute("status"))||h.status,h.statusText=o.getAttribute("statusText")||h.statusText);var s=(c.dataType||"").toLowerCase(),u=/(json|script|text)/.test(s);if(u||c.textarea){var f=E.getElementsByTagName("textarea")[0];if(f)h.responseText=f.value,h.status=Number(f.getAttribute("status"))||h.status,h.statusText=f.getAttribute("statusText")||h.statusText;else if(u){var m=E.getElementsByTagName("pre")[0],v=E.getElementsByTagName("body")[0];m?h.responseText=m.textContent?m.textContent:m.innerText:v&&(h.responseText=v.textContent?v.textContent:v.innerText)}}else"xml"==s&&!h.responseXML&&h.responseText&&(h.responseXML=X(h.responseText));try{L=_(h,s,c)}catch(e){a="parsererror",h.error=r=e||a}}catch(e){i("error caught: ",e),a="error",h.error=r=e||a}h.aborted&&(i("upload aborted"),a=null),h.status&&(a=h.status>=200&&h.status<300||304===h.status?"success":"error"),"success"===a?(c.success&&c.success.call(c.context,L,"success",h),T.resolve(h.responseText,"success",h),l&&e.event.trigger("ajaxSuccess",[h,c])):a&&(void 0===r&&(r=h.statusText),c.error&&c.error.call(c.context,h,a,r),T.reject(h,"error",r),l&&e.event.trigger("ajaxError",[h,c,r])),l&&e.event.trigger("ajaxComplete",[h,c]),l&&!--e.active&&e.event.trigger("ajaxStop"),c.complete&&c.complete.call(c.context,h,a),M=!0,c.timeout&&clearTimeout(b),setTimeout(function(){c.iframeTarget?p.attr("src",c.iframeSrc):p.remove(),h.responseXML=null},100)}}}var X=e.parseXML||function(e,t){return window.ActiveXObject?((t=new ActiveXObject("Microsoft.XMLDOM")).async="false",t.loadXML(e)):t=(new DOMParser).parseFromString(e,"text/xml"),t&&t.documentElement&&"parsererror"!=t.documentElement.nodeName?t:null},C=e.parseJSON||function(e){return window.eval("("+e+")")},_=function(t,r,a){var n=t.getResponseHeader("content-type")||"",i="xml"===r||!r&&n.indexOf("xml")>=0,o=i?t.responseXML:t.responseText;return i&&"parsererror"===o.documentElement.nodeName&&e.error&&e.error("parsererror"),a&&a.dataFilter&&(o=a.dataFilter(o,r)),"string"==typeof o&&("json"===r||!r&&n.indexOf("json")>=0?o=C(o):("script"===r||!r&&n.indexOf("javascript")>=0)&&e.globalEval(o)),o};return T}},e.fn.ajaxForm=function(t){if((t=t||{}).delegation=t.delegation&&e.isFunction(e.fn.on),!t.delegation&&0===this.length){var r={s:this.selector,c:this.context};return!e.isReady&&r.s?(i("DOM not ready, queuing ajaxForm"),e(function(){e(r.s,r.c).ajaxForm(t)}),this):(i("terminating; zero elements found by selector"+(e.isReady?"":" (DOM not ready)")),this)}return t.delegation?(e(document).off("submit.form-plugin",this.selector,a).off("click.form-plugin",this.selector,n).on("submit.form-plugin",this.selector,t,a).on("click.form-plugin",this.selector,t,n),this):this.ajaxFormUnbind().bind("submit.form-plugin",t,a).bind("click.form-plugin",t,n)},e.fn.ajaxFormUnbind=function(){return this.unbind("submit.form-plugin click.form-plugin")},e.fn.formToArray=function(r,a){var n=[];if(0===this.length)return n;var i,o,s,u,c,l,f,m,p=this[0],d=this.attr("id"),h=r?p.getElementsByTagName("*"):p.elements;if(h&&!/MSIE [678]/.test(navigator.userAgent)&&(h=e(h).get()),d&&(i=e(':input[form="'+d+'"]').get()).length&&(h=(h||[]).concat(i)),!h||!h.length)return n;for(o=0,f=h.length;o<f;o++)if((u=(l=h[o]).name)&&!l.disabled)if(r&&p.clk&&"image"==l.type)p.clk==l&&(n.push({name:u,value:e(l).val(),type:l.type}),n.push({name:u+".x",value:p.clk_x},{name:u+".y",value:p.clk_y}));else if((c=e.fieldValue(l,!0))&&c.constructor==Array)for(a&&a.push(l),s=0,m=c.length;s<m;s++)n.push({name:u,value:c[s]});else if(t.fileapi&&"file"==l.type){a&&a.push(l);var v=l.files;if(v.length)for(s=0;s<v.length;s++)n.push({name:u,value:v[s],type:l.type});else n.push({name:u,value:"",type:l.type})}else null!=c&&(a&&a.push(l),n.push({name:u,value:c,type:l.type,required:l.required}));if(!r&&p.clk){var g=e(p.clk),x=g[0];(u=x.name)&&!x.disabled&&"image"==x.type&&(n.push({name:u,value:g.val()}),n.push({name:u+".x",value:p.clk_x},{name:u+".y",value:p.clk_y}))}return n},e.fn.formSerialize=function(t){return e.param(this.formToArray(t))},e.fn.fieldSerialize=function(t){var r=[];return this.each(function(){var a=this.name;if(a){var n=e.fieldValue(this,t);if(n&&n.constructor==Array)for(var i=0,o=n.length;i<o;i++)r.push({name:a,value:n[i]});else null!=n&&r.push({name:this.name,value:n})}}),e.param(r)},e.fn.fieldValue=function(t){for(var r=[],a=0,n=this.length;a<n;a++){var i=this[a],o=e.fieldValue(i,t);null==o||o.constructor==Array&&!o.length||(o.constructor==Array?e.merge(r,o):r.push(o))}return r},e.fieldValue=function(t,r){var a=t.name,n=t.type,i=t.tagName.toLowerCase();if(void 0===r&&(r=!0),r&&(!a||t.disabled||"reset"==n||"button"==n||("checkbox"==n||"radio"==n)&&!t.checked||("submit"==n||"image"==n)&&t.form&&t.form.clk!=t||"select"==i&&-1==t.selectedIndex))return null;if("select"==i){var o=t.selectedIndex;if(o<0)return null;for(var s=[],u=t.options,c="select-one"==n,l=c?o+1:u.length,f=c?o:0;f<l;f++){var m=u[f];if(m.selected){var p=m.value;if(p||(p=m.attributes&&m.attributes.value&&!m.attributes.value.specified?m.text:m.value),c)return p;s.push(p)}}return s}return e(t).val()},e.fn.clearForm=function(t){return this.each(function(){e("input,select,textarea",this).clearFields(t)})},e.fn.clearFields=e.fn.clearInputs=function(t){var r=/^(?:color|date|datetime|email|month|number|password|range|search|tel|text|time|url|week)$/i;return this.each(function(){var a=this.type,n=this.tagName.toLowerCase();r.test(a)||"textarea"==n?this.value="":"checkbox"==a||"radio"==a?this.checked=!1:"select"==n?this.selectedIndex=-1:"file"==a?/MSIE/.test(navigator.userAgent)?e(this).replaceWith(e(this).clone(!0)):e(this).val(""):t&&(!0===t&&/hidden/.test(a)||"string"==typeof t&&e(this).is(t))&&(this.value="")})},e.fn.resetForm=function(){return this.each(function(){("function"==typeof this.reset||"object"==typeof this.reset&&!this.reset.nodeType)&&this.reset()})},e.fn.enable=function(e){return void 0===e&&(e=!0),this.each(function(){this.disabled=!e})},e.fn.selected=function(t){return void 0===t&&(t=!0),this.each(function(){var r=this.type;if("checkbox"==r||"radio"==r)this.checked=t;else if("option"==this.tagName.toLowerCase()){var a=e(this).parent("select");t&&a[0]&&"select-one"==a[0].type&&a.find("option").selected(!1),this.selected=t}})},e.fn.ajaxSubmit.debug=!1});

/*EOF*/