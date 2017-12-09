

//** jQuery Scroll to Top Control script- (c) Dynamic Drive DHTML code library: http://www.dynamicdrive.com.
//** Available/ usage terms at http://www.dynamicdrive.com (March 30th, 09')
//** v1.1 (April 7th, 09'):
//** 1) Adds ability to scroll to an absolute position (from top of page) or specific element on the page instead.
//** 2) Fixes scroll animation not working in Opera. 

var capp_started_event_initialized = false;
var scrolltotop = {
//startline: Integer. Number of pixels from top of doc scrollbar is scrolled before showing control
//scrollto: Keyword (Integer, or "Scroll_to_Element_ID"). How far to scroll document up when control is clicked on (0=top).
    setting: {startline: 100, scrollto: 0, scrollduration: 1000, fadeduration: [500, 100]},
    controlHTML: '<img src="<?php echo curl::base(); ?>media/img/up.png" style="width:51px; height:42px" />', //HTML for control, which is auto wrapped in DIV w/ ID="topcontrol"
    controlattrs: {offsetx: 5, offsety: 5}, //offset of control relative to right/ bottom of window corner
    anchorkeyword: '#top', //Enter href value of HTML anchors on the page that should also act as "Scroll Up" links

    state: {isvisible: false, shouldvisible: false},
    scrollup: function () {
        if (!this.cssfixedsupport) //if control is positioned using JavaScript
            this.$control.css({opacity: 0}) //hide control immediately after clicking it
        var dest = isNaN(this.setting.scrollto) ? this.setting.scrollto : parseInt(this.setting.scrollto)
        if (typeof dest == "string" && jQuery('#' + dest).length == 1) //check element set by string exists
            dest = jQuery('#' + dest).offset().top
        else
            dest = 0
        this.$body.animate({scrollTop: dest}, this.setting.scrollduration);
    },
    keepfixed: function () {
        var $window = jQuery(window)
        var controlx = $window.scrollLeft() + $window.width() - this.$control.width() - this.controlattrs.offsetx
        var controly = $window.scrollTop() + $window.height() - this.$control.height() - this.controlattrs.offsety
        this.$control.css({left: controlx + 'px', top: controly + 'px'})
    },
    togglecontrol: function () {
        var scrolltop = jQuery(window).scrollTop()
        if (!this.cssfixedsupport)
            this.keepfixed()
        this.state.shouldvisible = (scrolltop >= this.setting.startline) ? true : false
        if (this.state.shouldvisible && !this.state.isvisible) {
            this.$control.stop().animate({opacity: 1}, this.setting.fadeduration[0])
            this.state.isvisible = true
        } else if (this.state.shouldvisible == false && this.state.isvisible) {
            this.$control.stop().animate({opacity: 0}, this.setting.fadeduration[1])
            this.state.isvisible = false
        }
    },
    init: function () {
        jQuery(document).ready(function ($) {
            var mainobj = scrolltotop
            var iebrws = document.all
            mainobj.cssfixedsupport = !iebrws || iebrws && document.compatMode == "CSS1Compat" && window.XMLHttpRequest //not IE or IE7+ browsers in standards mode
            mainobj.$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body')
            mainobj.$control = $('<div id="topcontrol">' + mainobj.controlHTML + '</div>')
                    .css({position: mainobj.cssfixedsupport ? 'fixed' : 'absolute', bottom: mainobj.controlattrs.offsety, right: mainobj.controlattrs.offsetx, opacity: 0, cursor: 'pointer'})
                    .attr({title: 'Scroll Back to Top'})
                    .click(function () {
                        mainobj.scrollup();
                        return false
                    })
                    .appendTo('body')
            if (document.all && !window.XMLHttpRequest && mainobj.$control.text() != '') //loose check for IE6 and below, plus whether control contains any text
                mainobj.$control.css({width: mainobj.$control.width()}) //IE6- seems to require an explicit width on a DIV containing text
            mainobj.togglecontrol()
            $('a[href="' + mainobj.anchorkeyword + '"]').click(function () {
                mainobj.scrollup()
                return false
            })
            $(window).bind('scroll resize', function (e) {
                mainobj.togglecontrol()
            })
        })
    }
}

scrolltotop.init();
jQuery(document).on('click', 'a.confirm', function (e) {
    var ahref = $(this).attr('href');
    var message = $(this).attr('data-confirm-message');
    var no_double = $(this).attr('data-no-double');
    var clicked = $(this).attr('data-clicked');
    var btn = jQuery(this);
    btn.attr('data-clicked', '1');
    if (no_double) {
        if (clicked == 1)
            return false;
    }

    if (!message) {
        message = "<?php echo clang::__('Are you sure').' ?'; ?>";
    } else {
        message = $.cresenity.base64.decode(message);
    }

    str_confirm = "<?php echo clang::__('OK'); ?>";
    str_cancel = "<?php echo clang::__('Cancel'); ?>";
    e.preventDefault();
    e.stopPropagation();
    bootbox.confirm(message, function (confirmed) {
        if (confirmed) {
            window.location.href = ahref;
        } else {
            btn.removeAttr('data-clicked');
        }
    });
    return false;
});
jQuery(document).on('click', 'input[type=submit].confirm', function (e) {

    var submitted = $(this).attr('data-submitted');
    var btn = jQuery(this);
    if (submitted == '1')
        return false;
    btn.attr('data-submitted', '1');
    var message = $(this).attr('data-confirm-message');
    if (!message) {
        message = "<?php echo clang::__('Are you sure').' ?'; ?>";
    } else {
        message = $.cresenity.base64.decode(message);
    }


    str_confirm = "<?php echo clang::__('OK'); ?>";
    str_cancel = "<?php echo clang::__('Cancel'); ?>";
    bootbox.confirm(message, str_cancel, str_confirm, function (confirmed) {
        if (confirmed) {
            jQuery(e.target).closest('form').submit();
        } else {
            btn.removeAttr('data-submitted');
        }
    });
    return false;
});
jQuery(document).ready(function () {
    jQuery("#toggle-subnavbar").click(function () {
        var cmd = jQuery("#toggle-subnavbar span").html();
        if (cmd == 'Hide') {
            jQuery('#subnavbar').slideUp('slow');
            jQuery("#toggle-subnavbar span").html('Show');
        } else {
            jQuery('#subnavbar').slideDown('slow');
            jQuery("#toggle-subnavbar span").html('Hide');
        }

    });
    jQuery("#toggle-fullscreen").click(function () {
        $.cresenity.fullscreen(document.documentElement);
    });
});
if (window.capp.have_clock) {


    $(document).ready(function () {
        $('#servertime').serverTime({
            ajaxFile: '<?php echo curl::base(); ?>cresenity/server_time',
            displayDateFormat: "yyyy-mm-dd HH:MM:ss"
        });
    });
}
/*
 cresenity.func.js
 */

;
(function ($, window, document, undefined)
{
    $.cresenity = {
        _filesadded: "",
        _loadjscss: function (filename, filetype, callback) {
            if (filetype == "js") { //if filename is a external JavaScript file
                var fileref = document.createElement('script')
                fileref.setAttribute("type", "text/javascript")
                fileref.setAttribute("src", filename)
            } else if (filetype == "css") { //if filename is an external CSS file
                var fileref = document.createElement("link")
                fileref.setAttribute("rel", "stylesheet")
                fileref.setAttribute("type", "text/css")
                fileref.setAttribute("href", filename)
            }
            if (typeof fileref != "undefined") {
                //fileref.onload = callback;
                // IE 6 & 7
                fileref.onload = $.cresenity._handle_response_callback(callback);
                if (typeof (callback) === 'function') {
                    fileref.onreadystatechange = function () {

                        if (this.readyState == 'complete') {
                            $.cresenity._handle_response_callback(callback);
                        }
                    }
                }
                document.getElementsByTagName("head")[0].appendChild(fileref);
            }
        },
        _removejscss: function (filename, filetype) {
            var targetelement = (filetype == "js") ? "script" : (filetype == "css") ? "link" : "none"; //determine element type to create nodelist from
            var targetattr = (filetype == "js") ? "src" : (filetype == "css") ? "href" : "none"; //determine corresponding attribute to test for
            var allsuspects = document.getElementsByTagName(targetelement);
            for (var i = allsuspects.length; i >= 0; i--) { //search backwards within nodelist for matching elements to remove
                if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1) {
                    allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
                }
            }
        },
        _handle_response: function (data, callback) {



            if (data.css_require && data.css_require.length > 0) {
                for (var i = 0; i < data.css_require.length; i++) {
                    $.cresenity.require(data.css_require[i], 'css');
                }
            }
            require(data.js_require, callback);
            return;
            $.cresenity._filesloaded = 0;
            $.cresenity._filesneeded = 0;
            if (data.css_require && data.css_require.length > 0)
                $.cresenity._filesneeded += data.css_require.length;
            if (data.js_require && data.js_require.length > 0)
                $.cresenity._filesneeded += data.js_require.length;
            //console.log('needed:'+$.cresenity._filesneeded);
            if (data.css_require && data.css_require.length > 0) {
                for (var i = 0; i < data.css_require.length; i++) {
                    $.cresenity.require(data.css_require[i], 'css', callback);
                }
            }
            if (data.js_require && data.js_require.length > 0) {
                for (var i = 0; i < data.js_require.length; i++) {
                    $.cresenity.require(data.js_require[i], 'js', callback);
                }
            }
            if ($.cresenity._filesloaded == $.cresenity._filesneeded) {
                callback();
            }
        },
        _handle_response_callback: function (callback) {
            $.cresenity._filesloaded++;
            //console.log('dynamic loaded:'+$.cresenity._filesloaded);
            if ($.cresenity._filesloaded == $.cresenity._filesneeded) {
                callback();
            }
        },
        require: function (filename, filetype, callback) {
            if ($.cresenity._filesadded.indexOf("[" + filename + "]") == -1) {
                $.cresenity._loadjscss(filename, filetype, callback);
                $.cresenity._filesadded += "[" + filename + "]" //List of files added in the form "[filename1],[filename2],etc"
            } else {
                $.cresenity._filesloaded++;
                //console.log('already loaded:'+$.cresenity._filesloaded);
                if ($.cresenity._filesloaded == $.cresenity._filesneeded) {
                    callback();
                }
            }
        },
        days_between: function (date1, date2) {

            // The number of milliseconds in one day
            var ONE_DAY = 1000 * 60 * 60 * 24

            // Convert both dates to milliseconds
            var date1_ms = date1.getTime()
            var date2_ms = date2.getTime()

            // Calculate the difference in milliseconds
            var difference_ms = Math.abs(date1_ms - date2_ms)

            // Convert back to days and return
            return Math.round(difference_ms / ONE_DAY)

        },
        set_confirm: function (selector) {
            $(selector).click(function (e) {
                var ahref = $(this).attr('href');
                e.preventDefault();
                e.stopPropagation();
                bootbox.confirm("Are you sure?", function (confirmed) {
                    if (confirmed) {
                        window.location.href = ahref;
                    }
                });
            });
        },
        is_number: function (n) {
            return !isNaN(parseFloat(n)) && isFinite(n);
        },
        get_dialog: function (dlg_id, title) {

            var div_content = $('body #' + dlg_id + ' #' + dlg_id + '_content');
            if (div_content.length) {
                $('body #' + dlg_id + ' #' + dlg_id + '_header h3').html(title);
                return div_content;
            }
            if (title == "undefined")
                title = "";
            if (!title)
                title = "";
            //not exists create the modal div
            var div = $('<div>').attr('id', dlg_id);
            var btnClose = '<a href="' + 'javascript:;' + '" class="close" data-dismiss="modal">&times;</a>';
            btnClose = '';
            div.append('<div class="modal-header" id="' + dlg_id + '_header">' + btnClose + '<h3>' + title + '</h3></div>')
            div_content = $('<div class="modal-body" id="' + dlg_id + '_content"></div>');
            div.append(div_content);
            var btn_close = $('<a id="' + dlg_id + '_close">').addClass('btn').attr('href', 'javascript:void(0)');
            btn_close.append('<i class="icon icon-close"></i> Close');
            btn_close.click(function () {

                $('#' + dlg_id + '').modal('hide');
                $('#' + dlg_id + '').remove();
            });
            div_footer = $('<div class="modal-footer" id="suspended_dlg_footer"></div>');
            div_footer.append(btn_close);
            div.append(div_footer);
            div.css('overflow', 'hidden');
            div.addClass('modal');
            // stick the modal right at the bottom of the main body out of the way
            $("body").append(div);
            return div_content;
        },
        message: function (type, message, alert_type, callback) {
            alert_type = typeof alert_type !== 'undefined' ? alert_type : 'notify';
            var container = $('#container');
            if (alert_type == 'bootbox') {

                if (typeof callback == 'undefined') {
                    bootbox.alert(message);
                } else {
                    bootbox.alert(message, callback);
                }
            }

            if (alert_type == 'notify') {
                obj = $('<div>');
                container.prepend(obj);
                obj.addClass('notifications');
                obj.addClass('top-right');
                obj.notify({
                    'message': {text: message},
                    'type': type
                }).show();
            }

        },
        thousand_separator: function (rp) {

            rp = "" + rp;
            var rupiah = "";
            var vfloat = "";
            var ds = window.capp.decimal_separator;
            var ts = window.capp.thousand_separator;
            var dd = window.capp.decimal_digit;
            var dd = parseInt(dd);
            var minus_str = "";
            if (rp.indexOf("-") >= 0) {
                minus_str = rp.substring(rp.indexOf("-"), 1);
                rp = rp.substring(rp.indexOf("-") + 1);
            }

            if (rp.indexOf(".") >= 0) {
                vfloat = rp.substring(rp.indexOf("."));
                rp = rp.substring(0, rp.indexOf("."));
            }
            p = rp.length;
            while (p > 3) {
                rupiah = ts + rp.substring(p - 3) + rupiah;
                l = rp.length - 3;
                rp = rp.substring(0, l);
                p = rp.length;
            }
            rupiah = rp + rupiah;
            vfloat = vfloat.replace('.', ds);
            if (vfloat.length > dd)
                vfloat = vfloat.substring(0, dd + 1);
            return minus_str + rupiah + vfloat;
        },
        replace_all: function (string, find, replace) {
            escaped_find = find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "\\$1");
            return string.replace(new RegExp(escaped_find, 'g'), replace);
        },
        format_currency: function (rp) {
            return $.cresenity.thousand_separator(rp);
        },
        unformat_currency: function (rp) {
            if (typeof rp == "undefined") {
                rp = '';
            }
            var ds = window.capp.decimal_separator;
            var ts = window.capp.thousand_separator;
            var last3 = rp.substr(rp.length - 3);
            var char_last3 = last3.charAt(0);
            if (char_last3 != ts) {
                rp = this.replace_all(rp, ts, '');
            }



            rp = rp.replace(ds, ".");
            return rp;
        },
        base64: {_keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=", encode: function (e) {
                var t = "";
                var n, r, i, s, o, u, a;
                var f = 0;
                e = this._utf8_encode(e);
                while (f < e.length) {
                    n = e.charCodeAt(f++);
                    r = e.charCodeAt(f++);
                    i = e.charCodeAt(f++);
                    s = n >> 2;
                    o = (n & 3) << 4 | r >> 4;
                    u = (r & 15) << 2 | i >> 6;
                    a = i & 63;
                    if (isNaN(r)) {
                        u = a = 64
                    } else if (isNaN(i)) {
                        a = 64
                    }
                    t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a)
                }
                return t
            }, decode: function (e) {
                var t = "";
                var n, r, i;
                var s, o, u, a;
                var f = 0;
                e = e.replace(/[^A-Za-z0-9+/=]/g, "");
                while (f < e.length) {
                    s = this._keyStr.indexOf(e.charAt(f++));
                    o = this._keyStr.indexOf(e.charAt(f++));
                    u = this._keyStr.indexOf(e.charAt(f++));
                    a = this._keyStr.indexOf(e.charAt(f++));
                    n = s << 2 | o >> 4;
                    r = (o & 15) << 4 | u >> 2;
                    i = (u & 3) << 6 | a;
                    t = t + String.fromCharCode(n);
                    if (u != 64) {
                        t = t + String.fromCharCode(r)
                    }
                    if (a != 64) {
                        t = t + String.fromCharCode(i)
                    }
                }
                t = this._utf8_decode(t);
                return t
            }, _utf8_encode: function (e) {
                e = e.replace(/rn/g, "n");
                var t = "";
                for (var n = 0; n < e.length; n++) {
                    var r = e.charCodeAt(n);
                    if (r < 128) {
                        t += String.fromCharCode(r)
                    } else if (r > 127 && r < 2048) {
                        t += String.fromCharCode(r >> 6 | 192);
                        t += String.fromCharCode(r & 63 | 128)
                    } else {
                        t += String.fromCharCode(r >> 12 | 224);
                        t += String.fromCharCode(r >> 6 & 63 | 128);
                        t += String.fromCharCode(r & 63 | 128)
                    }
                }
                return t
            }, _utf8_decode: function (e) {
                var t = "";
                var n = 0;
                var r = c1 = c2 = 0;
                while (n < e.length) {
                    r = e.charCodeAt(n);
                    if (r < 128) {
                        t += String.fromCharCode(r);
                        n++
                    } else if (r > 191 && r < 224) {
                        c2 = e.charCodeAt(n + 1);
                        t += String.fromCharCode((r & 31) << 6 | c2 & 63);
                        n += 2
                    } else {
                        c2 = e.charCodeAt(n + 1);
                        c3 = e.charCodeAt(n + 2);
                        t += String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
                        n += 3
                    }
                }
                return t
            }},
        url: {
            add_query_string: function (url, key, value) {
                key = encodeURI(key);
                value = encodeURI(value);
                var url_array = url.split('?');
                var query_string = '';
                var base_url = url_array[0];
                if (url_array.length > 1)
                    query_string = url_array[1];
                var kvp = query_string.split('&');
                var i = kvp.length;
                var x;
                while (i--) {
                    x = kvp[i].split('=');
                    if (x[0] == key) {
                        x[1] = value;
                        kvp[i] = x.join('=');
                        break;
                    }
                }

                if (i < 0) {
                    kvp[kvp.length] = [key, value].join('=');
                }


                query_string = kvp.join('&');
                if (query_string.substr(0, 1) == '&')
                    query_string = query_string.substr(1);
                return base_url + '?' + query_string;
            },
            replace_param: function (url) {
                var available = true;
                while (available) {
                    matches = url.match(/{([\w]*)}/);
                    if (matches != null) {
                        var key = matches[1];
                        var val = null;
                        if ($('#' + key).length > 0) {
                            var val = $.cresenity.value('#' + key);
                        }

                        if (val == null) {
                            val = key;
                        }

                        url = url.replace('{' + key + '}', val);
                    } else {
                        available = false;
                    }

                }
                return url;
            }
        },
        reload: function (id_target, url, method, data_addition) {

            if (!method)
                method = "get";
            var xhr = jQuery('#' + id_target).data('xhr');
            if (xhr)
                xhr.abort();
            url = $.cresenity.url.replace_param(url);
            if (typeof data_addition == 'undefined')
                data_addition = {};
            url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', id_target);
            if (window.capp.bootstrap >= 3.3) {
                jQuery('#' + id_target).empty();
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').addClass('loading'));
            } else {
                jQuery('#' + id_target).addClass('loading');
                jQuery('#' + id_target).empty();
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').css('text-align', 'center').css('margin-top', '100px').css('margin-bottom', '100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')))
            }


            jQuery('#' + id_target).data('xhr', jQuery.ajax({
                type: method,
                url: url,
                dataType: 'json',
                data: data_addition,
            }).done(function (data) {

                $.cresenity._handle_response(data, function () {
                    jQuery('#' + id_target).html(data.html);
                    if (data.js && data.js.length > 0) {
                        var script = $.cresenity.base64.decode(data.js);
                        eval(script);
                    }

                    jQuery('#' + id_target).removeClass('loading');
                    jQuery('#' + id_target).data('xhr', false);
                    if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                        window.prettyPrint && prettyPrint();
                    }
                });
            }).error(function (obj, t, msg) {
                if (msg != 'abort') {
                    $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                }
            })
                    );
        },
        append: function (id_target, url, method, data_addition) {

            if (!method)
                method = "get";
            var xhr = jQuery('#' + id_target).data('xhr');
            url = $.cresenity.url.replace_param(url);
            if (typeof data_addition == 'undefined')
                data_addition = {};
            url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', id_target);
            if (window.capp.bootstrap >= '3.3') {

                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').addClass('loading'));
            } else {

                jQuery('#' + id_target).addClass('loading');
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').css('text-align', 'center').css('margin-top', '100px').css('margin-bottom', '100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')))

            }



            jQuery('#' + id_target).data('xhr', jQuery.ajax({
                type: method,
                url: url,
                dataType: 'json',
                data: data_addition,
            }).done(function (data) {

                $.cresenity._handle_response(data, function () {
                    jQuery('#' + id_target).append(data.html);
                    jQuery('#' + id_target).find('#' + id_target + '-loading').remove();
                    var script = $.cresenity.base64.decode(data.js);
                    console.log(script);
                    eval(script);
                    jQuery('#' + id_target).removeClass('loading');
                    jQuery('#' + id_target).data('xhr', false);
                    if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                        window.prettyPrint && prettyPrint();
                    }
                });
            }).error(function (obj, t, msg) {
                if (msg != 'abort') {
                    $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                }
            })
                    );
        },
        show_tooltip: function (id_target, url, method, text, toggle, position, title, data_addition) {
            if (typeof title == 'undefined')
                title = '';
            if (typeof position == 'undefined')
                position = 'auto';
            if (typeof data_addition == 'undefined')
                data_addition = {};
            if (typeof text == 'undefined')
                text = ' ';
            var _tooltip_html = '<div class="popover" id="popover' + id_target + '" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content" id="popover-content' + id_target + '"></div></div>';
            var selection = jQuery('#' + id_target);
            var handle;
            var html_content = text;
            var parent = $(jQuery('#' + id_target).html());
            var close_button = "<a id='closetooltip" + id_target + "' class='close' style='margin-left:10px;'>X</a>";
            if (typeof jQuery('#popover' + id_target).html() == 'undefined') {
                if (url.length > 0) {
                    text = jQuery('<div>').attr('id', id_target + '-loading').css('text-align', 'center').css('margin-top', '100px').css('margin-bottom', '100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x'));
                }
                if (position == 'auto' || position == '') {
                    $('#' + id_target).popover({
                        container: 'body',
                        title: close_button + title,
                        html: true,
                        trigger: "manual",
                        content: text,
                        selector: true,
                        template: _tooltip_html,
                    });
                } else {
                    $('#' + id_target).popover({
                        container: 'body',
                        title: close_button + title,
                        html: true,
                        trigger: "manual",
                        content: text,
                        placement: position,
                        selector: true,
                        template: _tooltip_html,
                    });
                }
                $('#' + id_target).popover('show');
                handle = $('.tooltip-inner', parent);
                if (url.length > 0) {
                    var xhr = handle.data('xhr');
                    if (xhr)
                        xhr.abort();
                    url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', '#tooltip_' + id_target);
                    handle.data('xhr', jQuery.ajax({
                        type: method,
                        url: url,
                        dataType: 'json',
                        data: data_addition,
                    }).done(function (data) {
                        $.cresenity._handle_response(data, function () {
                            $('#' + id_target).popover('destroy');
                            if (position == 'auto' || position == '') {
                                $('#' + id_target).popover({
                                    animation: false,
                                    title: close_button + title,
                                    html: true,
                                    trigger: "manual",
                                    content: html_content + data.html,
                                    selector: true,
                                    template: _tooltip_html,
                                });
                            } else {
                                $('#' + id_target).popover({
                                    animation: false,
                                    title: close_button + title,
                                    html: true,
                                    trigger: "manual",
                                    content: html_content + data.html,
                                    placement: position,
                                    selector: true,
                                    template: _tooltip_html,
                                });
                            }
                            $('#' + id_target).popover('show');
                            if (data.js && data.js.length > 0) {
                                var script = $.cresenity.base64.decode(data.js);
                                eval(script);
                            }
                            $("#closetooltip" + id_target).on("click", function () {
                                $('#' + id_target).popover('destroy');
                            });
                        });
                    }).error(function (obj, t, msg) {
                        if (msg != 'abort') {
                            $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                        }
                        $('#' + id_target + '-loading').remove();
                    })
                            );
                } else {
                    $("#closetooltip" + id_target).on("click", function () {
                        $('#' + id_target).popover('destroy');
                    });
                }

            } else {
                if (toggle == "1") {
                    $('#' + id_target).popover('destroy');
                }
            }

        },
        show_real_notification: function (id_target, url) {
            var selection = jQuery('#' + id_target);
            url = $.cresenity.url.replace_param(url);
            var handle;
            if (selection.length == 0) {
                selection = jQuery('<div/>').attr('id', id_target);
            }
            handle = selection;
            handle.data('xhr', jQuery.ajax({
                type: 'post',
                data: {
                    'title': document.title,
                },
                url: url,
                dataType: 'json',
            }).done(function (data) {
                $.cresenity._handle_response(data, function () {
                    jQuery('#' + id_target).html(data.html);
                    if (data.js && data.js.length > 0) {
                        var script = $.cresenity.base64.decode(data.js);
                        eval(script);
                    }
                    jQuery('#' + id_target).removeClass('loading');
                    jQuery('#' + id_target).data('xhr', false);
                    if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                        window.prettyPrint && prettyPrint();
                    }
                });
            }).error(function (obj, t, msg) {
                if (msg != 'abort') {
                    $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                }
            })
                    );
        },
        show_dialog: function (id_target, url, method, title, data_addition) {

            if (window.capp.bootstrap >= '3.3') {

                if (!title)
                    title = 'Dialog';
                if (typeof data_addition == 'undefined')
                    data_addition = {};

                var _dialog_html = "<div class='modal fade'>"
                        + "<div class='modal-dialog'>"
                        + "<div class='modal-content'>"
                        + "<div class='modal-header'>"
                        + "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>×</span></button>"
                        + "<h4 class='modal-title'></h4>"
                        + "</div>"
                        + "<div class='modal-body loading'>"
                        + "</div>"

                        + "</div>"
                        + "</div>"
                        + "</div>";
                var selection = jQuery('#' + id_target);
                console.log(selection);
                var handle;
                var dialog_is_remove = false;
                if (selection.length == 0) {
                    selection = jQuery('<div/>').attr('id', id_target);
                    dialog_is_remove = true;
                }
                url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', id_target);
                if (!selection.is(".modal-body")) {
                    var parent = $(_dialog_html);
                    parent.attr('id', id_target + '_modal');
                    jQuery(".modal-header .close[data-dismiss='modal']", parent).click(function (event) {
                        event.preventDefault();
                        if (dialog_is_remove) {
                            jQuery(this).parents(".modal").remove();
                        } else {
                            jQuery(this).parents(".modal").removeClass('in').hide();
                        }
                    });

                    jQuery("body").append(parent);
                    jQuery(".modal-header .modal-title", parent).html(title);
                    handle = $(".modal-body", parent);
                    if (selection.is("div") && selection.length == 1) {
                        handle.replaceWith(selection);
                        selection.addClass("modal-body").show();
                        handle = selection;
                    }
// If not, append current selection to dialog body
                    else {
                        handle.append(selection);
                    }
                } else {
                    handle = selection;
                }
                if (!method)
                    method = "get";
                var xhr = handle.data('xhr');
                if (xhr)
                    xhr.abort();

                url = $.cresenity.url.replace_param(url);
                jQuery('#' + id_target).empty();
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').addClass('loading'));
                if (!handle.is(".opened")) {
                    handle.parents('.modal').addClass("in").show();
                }
                handle.data('xhr', jQuery.ajax({
                    type: method,
                    url: url,
                    dataType: 'json',
                    data: data_addition,
                }).done(function (data) {
                    $.cresenity._handle_response(data, function () {
                        jQuery('#' + id_target).html(data.html);
                        if (data.js && data.js.length > 0) {
                            var script = $.cresenity.base64.decode(data.js);
                            eval(script);
                        }
                        jQuery('#' + id_target).removeClass('loading');
                        jQuery('#' + id_target).data('xhr', false);
                        if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                            window.prettyPrint && prettyPrint();
                        }
                        if (data.title) {
                            jQuery('#' + id_target + '').parent().find('.modal-header .modal-title').html(data.title);
                        }
                    });
                }).error(function (obj, t, msg) {
                    if (msg != 'abort') {
                        $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                    }
                })
                        );

            } else {
                // do Old show_dialog

                if (!title)
                    title = 'Dialog';
                if (typeof data_addition == 'undefined')
                    data_addition = {};

                var _dialog_html = "<div class='modal' style=\"display: none;\" >" +
                        "<div class='modal-header loading'>" +
                        "<a href='#' class='close'></a>" +
                        "<span class='loader'></span><h3></h3>" +
                        "</div>" +
                        "<div class='modal-body'>" +
                        "</div>" +
                        "<div class='modal-footer'>" +
                        "</div>" +
                        "</div>";


                var selection = jQuery('#' + id_target);
                var handle;
                var dialog_is_remove = false;
                if (selection.length == 0) {
                    selection = jQuery('<div/>').attr('id', id_target);
                    dialog_is_remove = true;
                }



                url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', id_target);
                if (!selection.is(".modal-body")) {
                    var overlay = $('<div class="modal-backdrop"></div>').hide();
                    var parent = $(_dialog_html);
                    jQuery(".modal-header a.close", parent).text(unescape("%D7")).click(function (event) {
                        event.preventDefault();
                        if (dialog_is_remove) {
                            handle.parent().prev(".modal-backdrop").remove();
                            jQuery(this).parents(".modal").find(".modal-body").parent().remove();
                        } else {
                            handle.parent().prev(".modal-backdrop").hide();
                            jQuery(this).parents(".modal").find(".modal-body").parent().hide();
                        }
                    });
                    jQuery("body").append(overlay).append(parent);
                    jQuery(".modal-header h3", parent).html(title);
                    handle = $(".modal-body", parent);
// Create dialog body from current jquery selection
// If specified body is a div element and only one element is 
// specified, make it the new modal dialog body
// Allows us to do something like this 
// $('<div id="foo"></div>').dialog2(); $("#foo").dialog2("open");
                    if (selection.is("div") && selection.length == 1) {
                        handle.replaceWith(selection);
                        selection.addClass("modal-body").show();
                        handle = selection;
                    }
// If not, append current selection to dialog body
                    else {
                        handle.append(selection);
                    }
                } else {
                    handle = selection;
                }
                if (!method)
                    method = "get";
                var xhr = handle.data('xhr');
                if (xhr)
                    xhr.abort();

                url = $.cresenity.url.replace_param(url);
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').css('text-align', 'center').css('margin-top', '100px').css('margin-bottom', '100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')))
                if (!handle.is(".opened")) {
                    overlay.show();
                    handle.addClass("opened").parent().show();
                }
                handle.data('xhr', jQuery.ajax({
                    type: method,
                    url: url,
                    dataType: 'json',
                    data: data_addition,
                }).done(function (data) {

                    $.cresenity._handle_response(data, function () {
                        jQuery('#' + id_target).html(data.html);
                        if (data.js && data.js.length > 0) {
                            var script = $.cresenity.base64.decode(data.js);
                            eval(script);
                        }
                        jQuery('#' + id_target).removeClass('loading');
                        jQuery('#' + id_target).data('xhr', false);
                        if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                            window.prettyPrint && prettyPrint();
                        }
                        if (data.title) {
                            jQuery('#' + id_target + '').parent().find('.modal-header h4').html(data.title);
                        }

                    });
                }).error(function (obj, t, msg) {
                    if (msg != 'abort') {
                        $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                    }
                })
                        );
            } // old show dialog
        },
        value: function (elm) {
            elm = jQuery(elm);
            if (elm.length == 0)
                return null;
            if (elm.attr('type') == 'checkbox') {

                if (!elm.is(':checked')) {
                    return null;
                }
            }
            if (elm.val() != 'undefined') {
                return elm.val();
            }
            if (elm.attr('value') != 'undefined') {


                return elm.attr('value');
            }
            return elm.html();
        },
        dialog: {
            alert: function (message, options) {
                $.fn.dialog2.helpers.alert(message, {});
            },
            prompt: function (message, options) {
                $.fn.dialog2.helpers.prompt(message, {});
            },
            confirm: function (message, options) {
                $.fn.dialog2.helpers.confirm(message, {});
            },
            show: function (selector, options) {

                $(selector).dialog2(options);
            }
        },

        fullscreen: function (element) {


            if (!$('body').hasClass("full-screen")) {

                $('body').addClass("full-screen");
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.webkitRequestFullscreen) {
                    element.webkitRequestFullscreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }

            } else {

                $('body').removeClass("full-screen");
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }

            }

        }
    }
    String.prototype.format_currency = function () {
        return $.cresenity.format_currency(this)
    };
    String.prototype.unformat_currency = function () {
        return $.cresenity.unformat_currency(this);
    };
})(this.jQuery, window, document);
