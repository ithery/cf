function strlen(string) {
    //  discuss at: http://phpjs.org/functions/strlen/
    // original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // improved by: Sakimori
    // improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    //    input by: Kirk Strobeck
    // bugfixed by: Onno Marsman
    //  revised by: Brett Zamir (http://brett-zamir.me)
    //        note: May look like overkill, but in order to be truly faithful to handling all Unicode
    //        note: characters and to this function in PHP which does not count the number of bytes
    //        note: but counts the number of characters, something like this is really necessary.
    //   example 1: strlen('Kevin van Zonneveld');
    //   returns 1: 19
    //   example 2: ini_set('unicode.semantics', 'on');
    //   example 2: strlen('A\ud87e\udc04Z');
    //   returns 2: 3

    var str = string + ''
    var i = 0,
            chr = '',
            lgth = 0;

    if (!this.php_js || !this.php_js.ini || !this.php_js.ini['unicode.semantics'] || this.php_js.ini['unicode.semantics'].local_value.toLowerCase() !== 'on') {
        return string.length
    }

    var getWholeChar = function (str, i) {
        var code = str.charCodeAt(i);
        var next = '';
        var prev = '';
        if (0xD800 <= code && code <= 0xDBFF) {
            // High surrogate (could change last hex to 0xDB7F to treat high private surrogates as single characters)
            if (str.length <= (i + 1)) {
                throw 'High surrogate without following low surrogate'
            }
            next = str.charCodeAt(i + 1);
            if (0xDC00 > next || next > 0xDFFF) {
                throw 'High surrogate without following low surrogate'
            }
            return str.charAt(i) + str.charAt(i + 1)
        } else if (0xDC00 <= code && code <= 0xDFFF) {
            // Low surrogate
            if (i === 0) {
                throw 'Low surrogate without preceding high surrogate'
            }
            prev = str.charCodeAt(i - 1);
            if (0xD800 > prev || prev > 0xDBFF) {
                // (could change last hex to 0xDB7F to treat high private surrogates as single characters)
                throw 'Low surrogate without preceding high surrogate'
            }
            // We can pass over low surrogates now as the second component in a pair which we have already processed
            return false
        }
        return str.charAt(i)
    }

    for (i = 0, lgth = 0; i < str.length; i++) {
        if ((chr = getWholeChar(str, i)) === false) {
            continue;
        }
        // Adapt this line at the top of any loop, passing in the whole string and the current iteration and returning a variable to represent the individual character; purpose is to treat the first part of a surrogate pair as the whole character and then ignore the second part
        lgth++;
    }
    return lgth
}

//** jQuery Scroll to Top Control script- (c) Dynamic Drive DHTML code library: http://www.dynamicdrive.com.
//** Available/ usage terms at http://www.dynamicdrive.com (March 30th, 09')
//** v1.1 (April 7th, 09'):
//** 1) Adds ability to scroll to an absolute position (from top of page) or specific element on the page instead.
//** 2) Fixes scroll animation not working in Opera.


var capp_started_event_initialized = false;
var scrolltotop = {
    //startline: Integer. Number of pixels from top of doc scrollbar is scrolled before showing control
    //scrollto: Keyword (Integer, or "Scroll_to_Element_ID"). How far to scroll document up when control is clicked on (0=top).
    setting: {
        startline: 100,
        scrollto: 0,
        scrollduration: 1000,
        fadeduration: [500, 100]
    },
    controlHTML: '<img src="' + window.capp.base_url + 'media/img/up.png" style="width:51px; height:42px" />', //HTML for control, which is auto wrapped in DIV w/ ID="topcontrol"
    controlattrs: {
        offsetx: 5,
        offsety: 5
    }, //offset of control relative to right/ bottom of window corner
    anchorkeyword: '#top', //Enter href value of HTML anchors on the page that should also act as "Scroll Up" links

    state: {
        isvisible: false,
        shouldvisible: false
    },
    scrollup: function () {
        if (!this.cssfixedsupport) //if control is positioned using JavaScript
            this.$control.css({
                opacity: 0,
                zIndex: -1,
            }) //hide control immediately after clicking it
        var dest = isNaN(this.setting.scrollto) ? this.setting.scrollto : parseInt(this.setting.scrollto)
        if (typeof dest == "string" && jQuery('#' + dest).length == 1) {
            //check element set by string exists
            dest = jQuery('#' + dest).offset().top;
        } else {
            dest = 0;
        }

        this.$body.animate({
            scrollTop: dest
        }, this.setting.scrollduration);
    },
    keepfixed: function () {
        var $window = jQuery(window)
        var controlx = $window.scrollLeft() + $window.width() - this.$control.width() - this.controlattrs.offsetx
        var controly = $window.scrollTop() + $window.height() - this.$control.height() - this.controlattrs.offsety
        this.$control.css({
            left: controlx + 'px',
            top: controly + 'px'
        })
    },
    togglecontrol: function () {
        var scrolltop = jQuery(window).scrollTop()
        if (!this.cssfixedsupport) {
            this.keepfixed();
        }
        this.state.shouldvisible = (scrolltop >= this.setting.startline) ? true : false
        if (this.state.shouldvisible && !this.state.isvisible) {
            this.$control.stop().animate({
                opacity: 1,
                zIndex: 99999,
            }, this.setting.fadeduration[0])
            this.state.isvisible = true
        } else if (this.state.shouldvisible == false && this.state.isvisible) {
            this.$control.stop().animate({
                opacity: 0,
                zIndex: -1
            }, this.setting.fadeduration[1])
            this.state.isvisible = false
        }
    },
    init: function () {
        jQuery(document).ready(function ($) {
            var mainobj = scrolltotop;
            var iebrws = document.all;
            mainobj.cssfixedsupport = !iebrws || iebrws && document.compatMode == "CSS1Compat" && window.XMLHttpRequest;
            //not IE or IE7+ browsers in standards mode
            mainobj.$body = (window.opera) ? (document.compatMode == "CSS1Compat" ? $('html') : $('body')) : $('html,body');
            mainobj.$control = $('<div id="topcontrol">' + mainobj.controlHTML + '</div>')
                    .css({
                        position: mainobj.cssfixedsupport ? 'fixed' : 'absolute',
                        bottom: mainobj.controlattrs.offsety,
                        right: mainobj.controlattrs.offsetx,
                        opacity: 0,
                        cursor: 'pointer',
                        zIndex: 99999
                    })
                    .attr({
                        title: 'Scroll Back to Top'
                    })
                    .click(function () {
                        mainobj.scrollup();
                        return false
                    })
                    .appendTo('body')
            if (document.all && !window.XMLHttpRequest && mainobj.$control.text() != '') { //loose check for IE6 and below, plus whether control contains any text
                mainobj.$control.css({
                    width: mainobj.$control.width()
                }); //IE6- seems to require an explicit width on a DIV containing text
            }
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
if (typeof window.capp.have_scroll_to_top != 'undefined' && window.capp.have_scroll_to_top) {
    if (!document.getElementById('topcontrol')) {
        scrolltotop.init();
    }
}
var confirmInitialized = $('body').attr('data-confirm-initialized');
if (!confirmInitialized) {
    jQuery(document).on('click', 'a.confirm, button.confirm', function (e) {
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
            message = window.capp.label_confirm;
        } else {
            message = $.cresenity.base64.decode(message);
        }

        str_confirm = window.capp.label_ok;
        str_cancel = window.capp.label_cancel;
        e.preventDefault();
        e.stopPropagation();
        btn.off('click');
        bootbox.confirm(message, function (confirmed) {
            if (confirmed) {
                if (ahref) {
                    window.location.href = ahref;
                } else {
                    if (btn.attr('type') == 'submit') {
                        btn.closest('form').submit();
                    } else {
                        btn.on('click');
                    }

                }
            } else {
                btn.removeAttr('data-clicked');
            }
            setTimeout(function () {
                var modalExists = $('.modal:visible').length > 0;
                if (!modalExists) {
                    $('body').removeClass('modal-open');
                } else {
                    $('body').addClass('modal-open');
                }
            }, 750);
        });


        return false;
    });
    $('body').attr('data-confirm-initialized', '1');
}
var confirmSubmitInitialized = $('body').attr('data-confirm-submit-initialized');
if (!confirmSubmitInitialized) {
    jQuery(document).on('click', 'input[type=submit].confirm', function (e) {

        var submitted = $(this).attr('data-submitted');
        var btn = jQuery(this);
        if (submitted == '1')
            return false;
        btn.attr('data-submitted', '1');

        var message = $(this).attr('data-confirm-message');
        if (!message) {
            message = window.capp.label_confirm;
        } else {
            message = $.cresenity.base64.decode(message);
        }

        str_confirm = window.capp.label_ok;
        str_cancel = window.capp.label_cancel;
        bootbox.confirm(message, str_cancel, str_confirm, function (confirmed) {
            if (confirmed) {
                jQuery(e.target).closest('form').submit();
            } else {
                btn.removeAttr('data-submitted');
            }
        });


        return false;
    });
    $('body').attr('data-confirm-submit-initialized', '1');
}
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
            ajaxFile: window.capp.base_url + 'cresenity/server_time',
            displayDateFormat: "yyyy-mm-dd HH:MM:ss"
        });
    });
}
/*
 cresenity.func.js
 */
;

var Cresenity = function () {

    var Base64 = function (cresenity) {
        this.cresenity = cresenity;
        this._keyStr = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=";
        this._utf8_encode = function (e) {
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
        };
        this._utf8_decode = function (e) {
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
        };
        this.encode = function (e) {
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
        };

        this.decode = function (e) {
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
        };

    }

    var Url = function (cresenity) {
        this.cresenity = cresenity;
        this.addQueryString = function (url, key, value) {
            key = encodeURI(key);
            value = encodeURI(value);
            var urlArray = url.split('?');
            var queryString = '';
            var baseUrl = urlArray[0];
            if (urlArray.length > 1) {
                queryString = urlArray[1];
            }
            var kvp = queryString.split('&');
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

            queryString = kvp.join('&');
            if (queryString.substr(0, 1) == '&')
                queryString = queryString.substr(1);
            return baseUrl + '?' + queryString;
        };
        this.replaceParam = function (url) {
            var available = true;
            while (available) {
                var matches = url.match(/{([\w]*)}/);
                if (matches != null) {
                    var key = matches[1];
                    var val = null;
                    if ($('#' + key).length > 0) {
                        var val = cresenity.value('#' + key);
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
    }

    this.url = new Url(this);
    this.base64 = new Base64(this);

    this.filesAdded = "";
    this.modalElements = [];
    this.loadJsCss = function (filename, filetype, callback) {
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
            if (typeof (callback) === 'function') {
                fileref.onload = cresenity.handleResponseCallback(callback);
                fileref.onreadystatechange = function () {
                    if (this.readyState == 'complete') {
                        cresenity.handleResponseCallback(callback);
                    }
                }
            }
            document.getElementsByTagName("head")[0].appendChild(fileref);
        }
    };
    this.removeJsCss = function (filename, filetype) {
        var targetelement = (filetype == "js") ? "script" : (filetype == "css") ? "link" : "none"; //determine element type to create nodelist from
        var targetattr = (filetype == "js") ? "src" : (filetype == "css") ? "href" : "none"; //determine corresponding attribute to test for
        var allsuspects = document.getElementsByTagName(targetelement);
        for (var i = allsuspects.length; i >= 0; i--) { //search backwards within nodelist for matching elements to remove
            if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1) {
                allsuspects[i].parentNode.removeChild(allsuspects[i]) //remove element by calling parentNode.removeChild()
            }
        }
    };
    this.handleResponse = function (data, callback) {
        if (data.css_require && data.css_require.length > 0) {
            for (var i = 0; i < data.css_require.length; i++) {
                cresenity.require(data.css_require[i], 'css');
            }
        }
        require(data.js_require, callback);


    };
    this.handleResponseCallback = function (callback) {
        cresenity.filesLoaded++;
        if (cresenity.filesLoaded == $.cresenity.filesNeeded) {
            callback();
        }
    };
    this.require = function (filename, filetype, callback) {
        if (cresenity.filesAdded.indexOf("[" + filename + "]") == -1) {
            cresenity.loadJsCss(filename, filetype, callback);
            cresenity.filesAdded += "[" + filename + "]" //List of files added in the form "[filename1],[filename2],etc"
        } else {
            cresenity.filesLoaded++;

            if (cresenity.filesLoaded == cresenity.filesNeeded) {
                callback();
            }
        }
    };
    this.reload = function (options) {
        var settings = $.extend({
            // These are the defaults.
            method: 'get',
            dataAddition: {},
            url: '/',
            onComplete: false,
        }, options);


        var method = settings.method;
        var selector = settings.selector;
        var xhr = jQuery(selector).data('xhr');
        if (xhr) {
            xhr.abort();
        }
        var dataAddition = settings.dataAddition;
        var url = settings.url;
        url = this.url.replaceParam(url);
        if (typeof dataAddition == 'undefined') {
            dataAddition = {};
        }

        $(selector).each(function () {
            var idTarget = $(this).attr('id');
            url = cresenity.url.addQueryString(url, 'capp_current_container_id', idTarget);


            (function (element) {
                cresenity.blockElement($(element));
                $(this).data('xhr', $.ajax({
                    type: method,
                    url: url,
                    dataType: 'json',
                    data: dataAddition,
                    success: function (data) {
                        cresenity.handleResponse(data, function () {
                            $(element).html(data.html);
                            if (data.js && data.js.length > 0) {
                                var script = cresenity.base64.decode(data.js);
                                eval(script);
                            }


                            if ($(element).find('.prettyprint').length > 0) {
                                window.prettyPrint && prettyPrint();
                            }
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        if (thrownError != 'abort') {
                            cresenity.message('error', 'Error, please call administrator... (' + thrownError + ')');
                        }

                    },
                    complete: function () {
                        $(element).data('xhr', false);
                        cresenity.unblockElement($(element));
                    }
                }));
            })(this);
        });

    };

    this.modal = function (options) {

        var settings = $.extend({
            // These are the defaults.
            haveHeader: false,
            haveFooter: false,
            headerText: '',
            onClose: false,
            footerAction: {}
        }, options);

        if (settings.title) {
            settings.haveHeader = true;
            settings.headerText = settings.title;
        }

        var modalContainer = jQuery('<div>').addClass('modal');

        if (settings.isSidebar) {
            modalContainer.addClass('sidebar');
            modalContainer.addClass(settings.sidebarMode);
        }
        var modalDialog = jQuery('<div>').addClass('modal-dialog modal-xl');
        var modalContent = jQuery('<div>').addClass('modal-content');

        var modalHeader = jQuery('<div>').addClass('modal-header');
        var modalTitle = jQuery('<div>').addClass('modal-title');
        var modalButtonClose = '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
        var modalBody = jQuery('<div>').addClass('modal-body');
        var modalFooter = jQuery('<div>').addClass('modal-footer');
        modalDialog.append(modalContent);
        modalContainer.append(modalDialog);
        if (settings.haveHeader) {
            modalTitle.html(settings.headerText);
            modalHeader.append(modalTitle).append(modalButtonClose);
            modalContent.append(modalHeader);
        }
        modalDialog.append(modalContent);
        if (settings.haveFooter) {
            modalContent.append(modalFooter);
        }
        modalContent.append(modalBody);
        $('body').append(modalContainer);

        modalContainer.on('hidden.bs.modal', function (e) {
            $(this).remove();
            cresenity.modalElements.pop();
            if (typeof settings.onClose == 'function') {
                settings.onClose(e);
            }
        });

        modalContainer.on('shown.bs.modal', function (e) {
            cresenity.modalElements.push($(this));
        });

        if (settings.message) {
            modalBody.append(settings.message);
        }
        if (settings.reload) {
            var reloadOptions = settings.reload;
            reloadOptions.selector = modalBody;
            cresenity.reload(reloadOptions);
        }

        modalContainer.modal();


    };
    this.closeDialog = function (options) {
        var modal = cresenity.modalElements.pop();
        modal.modal('hide');
    }
    this.ajaxSubmit = function (options) {
        var settings = $.extend({}, options);
        var selector = settings.selector;
        $(selector).each(function () {
            //don't do it again if still loading

            var formAjaxUrl = $(this).attr('action') || '';
            var formMethod = $(this).attr('method') || 'get';
            (function (element) {
                cresenity.blockElement($(element));
                var ajaxOptions = {
                    url: formAjaxUrl,
                    dataType: 'json',
                    type: formMethod,
                    complete: function () {
                        cresenity.unblockElement($(element));
                        if (typeof settings.onComplete == 'function') {
                            settings.onComplete();
                        }
                    },
                };
                $(element).ajaxSubmit(ajaxOptions);
            })(this);

        });
        //always return false to prevent submit
        return false;
    };
    this.message = function (type, message, alertType, callback) {
        alert_type = typeof alert_type !== 'undefined' ? alertType : 'notify';
        var container = $('#container');
        if (container.length == 0) {
            container = $('body');
        }
        if (alertType == 'bootbox') {

            if (typeof callback == 'undefined') {
                bootbox.alert(message);
            } else {
                bootbox.alert(message, callback);
            }
        }

        if (alertType == 'notify') {
            obj = $('<div>');
            container.prepend(obj);
            obj.addClass('notifications');
            obj.addClass('top-right');
            obj.notify({
                message: {
                    text: message
                },
                type: type
            }).show();
        }

    };

    this.blockPage = function () {
        $.blockUI({
            message: '<div class="sk-folding-cube sk-primary"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div><h5 style="color: #444">LOADING...</h5>',
            css: {
                backgroundColor: 'transparent',
                border: '0',
                zIndex: 9999999
            },
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8,
                zIndex: 9999990
            }
        });
    };
    this.unblockPage = function () {
        $.unblockUI();
    };
    this.blockElement = function (selector) {
        $(selector).block({
            message: '<div class="sk-wave sk-primary"><div class="sk-rect sk-rect1"></div> <div class="sk-rect sk-rect2"></div> <div class="sk-rect sk-rect3"></div> <div class="sk-rect sk-rect4"></div> <div class="sk-rect sk-rect5"></div></div>',
            css: {
                backgroundColor: 'transparent',
                border: '0'
            },
            overlayCSS: {
                backgroundColor: '#fff',
                opacity: 0.8
            }
        });

    };
    this.unblockElement = function (selector) {
        $(selector).unblock();
    };
};
if (!window.cresenity) {
    window.cresenity = new Cresenity();
}
(function ($, window, document, undefined) {
    $.cresenity = {
        _filesAdded: "",
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
            if ($.cresenity._filesloaded == $.cresenity._filesneeded) {
                callback();
            }
        },
        require: function (filename, filetype, callback) {
            if ($.cresenity._filesAdded.indexOf("[" + filename + "]") == -1) {
                $.cresenity._loadjscss(filename, filetype, callback);
                $.cresenity._filesAdded += "[" + filename + "]" //List of files added in the form "[filename1],[filename2],etc"
            } else {
                $.cresenity._filesloaded++;
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
            if (container.length == 0) {
                container = $('body');
            }
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
                    message: {
                        text: message
                    },
                    type: type
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
        base64: {
            _keyStr: "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",
            encode: function (e) {
                var t = "";
                var n,
                        r,
                        i,
                        s,
                        o,
                        u,
                        a;
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
            },
            decode: function (e) {
                var t = "";
                var n,
                        r,
                        i;
                var s,
                        o,
                        u,
                        a;
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
            },
            _utf8_encode: function (e) {
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
            },
            _utf8_decode: function (e) {
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
            }
        },
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
            if (typeof id_target == 'object') {
                return cresenity.reload(id_target);
            } else {
                var options = {};
                options.selector = '#' + id_target;
                options.url = url;
                options.method = method;
                options.dataAddition = data_addition;
                return cresenity.reload(options);
            }
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

            $.cresenity.blockElement(jQuery('#' + id_target));

            jQuery('#' + id_target).data('xhr', jQuery.ajax({
                type: method,
                url: url,
                dataType: 'json',
                data: data_addition,
                success: function (data) {
                    $.cresenity._handle_response(data, function () {
                        jQuery('#' + id_target).append(data.html);
                        jQuery('#' + id_target).find('#' + id_target + '-loading').remove();
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
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    if (thrownError != 'abort') {
                        $.cresenity.message('error', 'Error, please call administrator... (' + thrownError + ')');
                    }

                },
                complete: function () {
                    $.cresenity.unblockElement(jQuery('#' + id_target));
                }
            }));
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

            $.cresenity.blockElement(jQuery('#' + id_target));

            jQuery('#' + id_target).data('xhr', jQuery.ajax({
                type: method,
                url: url,
                dataType: 'json',
                data: data_addition,
                success: function (data) {

                    $.cresenity._handle_response(data, function () {
                        jQuery('#' + id_target).append(data.html);
                        jQuery('#' + id_target).find('#' + id_target + '-loading').remove();
                        var script = $.cresenity.base64.decode(data.js);
                        eval(script);
                        jQuery('#' + id_target).removeClass('loading');
                        jQuery('#' + id_target).data('xhr', false);
                        if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                            window.prettyPrint && prettyPrint();
                        }
                    });
                },
                error: function (obj, t, msg) {
                    if (msg != 'abort') {
                        $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                    }
                },
                complete: function () {
                    $.cresenity.unblockElement(jQuery('#' + id_target));
                }
            }));
        },
        prepend: function (id_target, url, method, data_addition) {

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

            $.cresenity.blockElement(jQuery('#' + id_target));

            jQuery('#' + id_target).data('xhr', jQuery.ajax({
                type: method,
                url: url,
                dataType: 'json',
                data: data_addition,
                success: function (data) {

                    $.cresenity._handle_response(data, function () {
                        jQuery('#' + id_target).prepend(data.html);
                        jQuery('#' + id_target).find('#' + id_target + '-loading').remove();
                        var script = $.cresenity.base64.decode(data.js);
                        eval(script);
                        jQuery('#' + id_target).removeClass('loading');
                        jQuery('#' + id_target).data('xhr', false);
                        if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                            window.prettyPrint && prettyPrint();
                        }
                    });
                },
                error: function (obj, t, msg) {
                    if (msg != 'abort') {
                        $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                    }
                },
                complete: function () {
                    $.cresenity.unblockElement(jQuery('#' + id_target));
                }
            }));
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
                        success: function (data) {
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
                        },
                        error: function (obj, t, msg) {
                            if (msg != 'abort') {
                                $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                            }
                            $('#' + id_target + '-loading').remove();
                        }
                    }));
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
                    title: document.title,
                },
                url: url,
                dataType: 'json',
                success: function (data) {
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
                },
                error: function (obj, t, msg) {
                    if (msg != 'abort') {
                        $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                    }
                }
            }));
        },
        show_dialog: function (id_target, url, method, options, data_addition) {
            if (typeof id_target == 'object') {
                return cresenity.modal(id_target);
            } else {
                options.selector = '#' + id_target;
                options.reload = {};
                options.reload.method = method;
                options.reload.dataAddition = data_addition;
                options.reload.url = url;

                return cresenity.modal(options);
            }
            var title = options;
            if (typeof options != 'object') {
                options = {};
                options.title = title;
            }
            var settings = $.extend({
                // These are the defaults.
                title: 'Data',
                isSidebar: false,
                haveFooter: false,
                onComplete: false
            }, options);

            title = settings.title;
            if (title) {
                settings.haveHeader = true;

            }
            var bootstrapVersion = $.fn.tooltip.Constructor.VERSION;
            if (typeof bootstrapVersion == 'undefined') {
                bootstrapVersion = '2';
            }

            if (window.capp.bootstrap >= '3.3') {

                if (!title) {
                    title = 'Dialog';
                }
                if (typeof data_addition == 'undefined')
                    data_addition = {};
                var _dialog_html = "<div class='modal fade'>"
                        + "<div class='modal-dialog'>"
                        + "<div class='modal-content'>"
                        + "<div class='modal-header'>"
                        + "<button type='button' class='close' data-dismiss='modal' aria-label='Close'><span aria-hidden='true'>�</span></button>"
                        + "<h4 class='modal-title'></h4>"
                        + "</div>"
                        + "<div class='modal-body loading'>"
                        + "</div>"

                        + "</div>"
                        + "</div>"
                        + "</div>";

                var selection = jQuery('#' + id_target);

                var handle;
                var dialog_is_remove = false;
                if (selection.length == 0) {
                    selection = jQuery('<div/>').attr('id', id_target);
                    dialog_is_remove = true;
                }
                url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', id_target);
                if (!selection.is(".modal-body")) {
                    var parent = modalContainer;
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
                    success: function (data) {
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
                    },
                    error: function (obj, t, msg) {
                        if (msg != 'abort') {
                            $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                        }
                    }
                }));

            } else {
                // do Old show_dialog

                if (!title)
                    title = 'Dialog';
                if (typeof data_addition == 'undefined')
                    data_addition = {};

                var modalContainer = jQuery('<div>').addClass('modal capp-modal ').css('display', 'none');
                if (settings.isSidebar) {
                    modalContainer.addClass('sidebar');

                }
                var modalDialog = jQuery('<div>').addClass('modal-dialog');
                var modalContent = jQuery('<div>').addClass('modal-content animated bounceInRight');

                var modalHeader = jQuery('<div>').addClass('modal-header');
                var modalTitle = jQuery('<h3>').addClass('modal-title');
                var modalButtonClose = '<a class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></a>';
                var modalBody = jQuery('<div>').addClass('modal-body loading');
                var modalFooter = jQuery('<div>').addClass('modal-footer');
                modalDialog.append(modalContent);
                modalContainer.append(modalDialog);
                if (settings.haveHeader) {
                    modalTitle.html(title);
                    modalHeader.append(modalTitle).append(modalButtonClose);
                    modalContent.append(modalHeader);
                }
                modalDialog.append(modalContent);
                if (settings.haveFooter) {
                    modalContent.append(modalFooter);
                }
                modalContent.append(modalBody);

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
                    var parent = modalContainer;
                    jQuery(".modal-header a.close", parent).text(unescape("%D7")).click(function (event) {
                        event.preventDefault();
                        if (dialog_is_remove) {
                            jQuery(this).parents(".modal").find(".modal-body").closest('.modal').hide(400, function () {
                                handle.closest('.modal').remove();
                            });
                            handle.closest('.modal').prev(".modal-backdrop").hide(400, function () {
                                handle.closest('.modal').prev(".modal-backdrop").remove();
                                var modalExists = $('.modal:visible').length > 0;
                                if (!modalExists) {
                                    $('body').removeClass('modal-open');
                                }

                            });
                        } else {
                            handle.closest('.modal').prev(".modal-backdrop").hide(400);
                            jQuery(this).parents(".modal").find(".modal-body").closest('.modal').hide(400, function () {
                                var modalExists = $('.modal:visible').length > 0;
                                if (!modalExists) {
                                    $('body').removeClass('modal-open');
                                }

                            });
                        }
                    });
                    jQuery(document).on('click', '[data-dismiss="modal"]', function (event) {
                        event.preventDefault();
                        if (dialog_is_remove) {
                            jQuery(this).parents(".modal").find(".modal-body").closest('.modal').hide(400, function () {
                                handle.closest('.modal').remove();
                            });
                            handle.closest('.modal').prev(".modal-backdrop").hide(400, function () {
                                handle.closest('.modal').prev(".modal-backdrop").remove();
                                var modalExists = $('.modal:visible').length > 0;
                                if (!modalExists) {
                                    $('body').removeClass('modal-open');
                                }

                            });
                        } else {
                            handle.closest('.modal').prev(".modal-backdrop").hide(400);
                            jQuery(this).parents(".modal").find(".modal-body").closest('.modal').hide(400, function () {
                                var modalExists = $('.modal:visible').length > 0;
                                if (!modalExists) {
                                    $('body').removeClass('modal-open');
                                }

                            });
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
                    $('body').addClass('modal-open');
                    handle.addClass("opened").closest('.modal').show();
                    if (!$.fn.modal.Constructor.VERSION) {
                    }
                }
                handle.data('xhr', jQuery.ajax({
                    type: method,
                    url: url,
                    dataType: 'json',
                    data: data_addition,
                    success: function (data) {

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
                    },
                    error: function (obj, t, msg) {
                        if (msg != 'abort') {
                            $.cresenity.message('error', 'Error, please call administrator... (' + msg + ')');
                        }
                    }
                }));
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

        },
        blockPage: function () {
            cresenity.blockPage();
        },
        unblockPage: function () {
            cresenity.unblockPage();
        },
        blockElement: function (selector) {
            cresenity.blockElement(selector);

        },
        unblockElement: function (selector) {
            cresenity.unblockElement(selector);
        },
    }
    String.prototype.format_currency = function () {
        return $.cresenity.format_currency(this)
    };
    String.prototype.unformat_currency = function () {
        return $.cresenity.unformat_currency(this);
    };
})(this.jQuery, window, document);

var appValidation;
appValidation = {

    implicitRules: ['Required', 'Confirmed'],

    /**
     * Initialize app validations.
     */
    init: function () {
        if ($.validator) {
            // Disable class rules and attribute rules
            $.validator.classRuleSettings = {};
            $.validator.attributeRules = function () {
                this.rules = {}
            };

            $.validator.dataRules = this.arrayRules;
            $.validator.prototype.arrayRulesCache = {};
            // Register validations methods
            this.setupValidations();
        }
    },

    arrayRules: function (element) {

        var rules = {},
                validator = $.data(element.form, "validator"),
                cache = validator.arrayRulesCache;

        // Is not an Array
        if (element.name.indexOf('[') === -1) {
            return rules;
        }

        if (!(element.name in cache)) {
            cache[element.name] = {};
        }

        $.each(validator.settings.rules, function (name, tmpRules) {
            if (name in cache[element.name]) {
                $.extend(rules, cache[element.name][name]);
            } else {
                cache[element.name][name] = {};
                var nameRegExp = appValidation.helpers.regexFromWildcard(name);
                if (element.name.match(nameRegExp)) {
                    var newRules = $.validator.normalizeRule(tmpRules) || {};
                    cache[element.name][name] = newRules;
                    $.extend(rules, newRules);
                }
            }
        });

        return rules;
    },

    setupValidations: function () {

        /**
         * Create JQueryValidation check to validate Laravel rules.
         */

        $.validator.addMethod("appValidation", function (value, element, params) {
            var validator = this;
            var validated = true;
            var previous = this.previousValue(element);

            // put Implicit rules in front
            var rules = [];
            $.each(params, function (i, param) {
                if (param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1) {
                    rules.unshift(param);
                } else {
                    rules.push(param);
                }
            });

            $.each(rules, function (i, param) {
                var implicit = param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1;
                var rule = param[0];
                var message = param[2];

                if (!implicit && validator.optional(element)) {
                    validated = "dependency-mismatch";
                    return false;
                }

                if (appValidation.methods[rule] !== undefined) {
                    validated = appValidation.methods[rule].call(validator, value, element, param[1], function (valid) {
                        validator.settings.messages[element.name].appValidationRemote = previous.originalMessage;
                        if (valid) {
                            var submitted = validator.formSubmitted;
                            validator.prepareElement(element);
                            validator.formSubmitted = submitted;
                            validator.successList.push(element);
                            delete validator.invalid[element.name];
                            validator.showErrors();
                        } else {
                            var errors = {};
                            errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message;
                            validator.invalid[element.name] = true;
                            validator.showErrors(errors);
                        }
                        validator.showErrors(validator.errorMap);
                        previous.valid = valid;
                    });
                } else {
                    validated = false;
                }

                if (validated !== true) {
                    if (!validator.settings.messages[element.name]) {
                        validator.settings.messages[element.name] = {};
                    }
                    validator.settings.messages[element.name].appValidation = message;
                    return false;
                }

            });
            return validated;

        }, '');

        /**
         * Create JQueryValidation check to validate Remote Laravel rules.
         */
        $.validator.addMethod("appValidationRemote", function (value, element, params) {
            var implicit = false,
                    check = params[0][1],
                    attribute = element.name,
                    token = check[1],
                    validateAll = check[2];

            $.each(params, function (i, parameters) {
                implicit = implicit || parameters[3];
            });

            if (!implicit && this.optional(element)) {
                return "dependency-mismatch";
            }

            var previous = this.previousValue(element),
                    validator,
                    data;

            if (!this.settings.messages[element.name]) {
                this.settings.messages[element.name] = {};
            }
            previous.originalMessage = this.settings.messages[element.name].appValidationRemote;
            this.settings.messages[element.name].appValidationRemote = previous.message;

            var param = typeof param === "string" && {
                url: param
            }
            || param;

            if (appValidation.helpers.arrayEquals(previous.old, value) || previous.old === value) {
                return previous.valid;
            }

            previous.old = value;
            validator = this;
            this.startRequest(element);

            data = $(validator.currentForm).serializeArray();

            data.push({
                'name': '_jsvalidation',
                'value': attribute
            });

            data.push({
                'name': '_jsvalidation_validate_all',
                'value': validateAll
            });

            var formMethod = $(validator.currentForm).attr('method');
            if ($(validator.currentForm).find('input[name="_method"]').length) {
                formMethod = $(validator.currentForm).find('input[name="_method"]').val();
            }

            $.ajax($.extend(true, {
                mode: "abort",
                port: "validate" + element.name,
                dataType: "json",
                data: data,
                context: validator.currentForm,
                url: $(validator.currentForm).attr('remote-validation-url'),
                type: formMethod,

                beforeSend: function (xhr) {
                    if ($(validator.currentForm).attr('method').toLowerCase() !== 'get' && token) {
                        return xhr.setRequestHeader('X-XSRF-TOKEN', token);
                    }
                }
            }, param)).always(function (response, textStatus) {
                var errors,
                        message,
                        submitted,
                        valid;

                if (textStatus === 'error') {
                    valid = false;
                    response = appValidation.helpers.parseErrorResponse(response);
                } else if (textStatus === 'success') {
                    valid = response === true || response === "true";
                } else {
                    return;
                }

                validator.settings.messages[element.name].appValidationRemote = previous.originalMessage;

                if (valid) {
                    submitted = validator.formSubmitted;
                    validator.prepareElement(element);
                    validator.formSubmitted = submitted;
                    validator.successList.push(element);
                    delete validator.invalid[element.name];
                    validator.showErrors();
                } else {
                    errors = {};
                    message = response || validator.defaultMessage(element, "remote");
                    errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message[0];
                    validator.invalid[element.name] = true;
                    validator.showErrors(errors);
                }
                validator.showErrors(validator.errorMap);
                previous.valid = valid;
                validator.stopRequest(element, valid);
            });
            return "pending";
        }, '');
    }
};

$(function () {
    appValidation.init();
});

/*!
 * CApp Javascript Validation
 * Reference https://github.com/proengsoft/laravel-jsvalidation
 * Helper functions used by validators
 *
 */

$.extend(true, appValidation, {

    helpers: {

        /**
         * Numeric rules
         */
        numericRules: ['Integer', 'Numeric'],

        /**
         * Gets the file information from file input.
         *
         * @param fieldObj
         * @param index
         * @returns {{file: *, extension: string, size: number}}
         */
        fileinfo: function (fieldObj, index) {
            var FileName = fieldObj.value;
            index = typeof index !== 'undefined' ? index : 0;
            if (fieldObj.files !== null) {
                if (typeof fieldObj.files[index] !== 'undefined') {
                    return {
                        file: FileName,
                        extension: FileName.substr(FileName.lastIndexOf('.') + 1),
                        size: fieldObj.files[index].size / 1024,
                        type: fieldObj.files[index].type
                    };
                }
            }
            return false;
        },

        /**
         * Gets the selectors for th specified field names.
         *
         * @param names
         * @returns {string}
         */
        selector: function (names) {
            var selector = [];
            if (!$.isArray(names)) {
                names = [names];
            }
            for (var i = 0; i < names.length; i++) {
                selector.push("[name='" + names[i] + "']");
            }
            return selector.join();
        },

        /**
         * Check if element has numeric rules.
         *
         * @param element
         * @returns {boolean}
         */
        hasNumericRules: function (element) {
            return this.hasRules(element, this.numericRules);
        },

        /**
         * Check if element has passed rules.
         *
         * @param element
         * @param rules
         * @returns {boolean}
         */
        hasRules: function (element, rules) {

            var found = false;
            if (typeof rules === 'string') {
                rules = [rules];
            }

            var validator = $.data(element.form, "validator");
            var listRules = [];
            var cache = validator.arrayRulesCache;
            if (element.name in cache) {
                $.each(cache[element.name], function (index, arrayRule) {
                    listRules.push(arrayRule);
                });
            }
            if (element.name in validator.settings.rules) {
                listRules.push(validator.settings.rules[element.name]);
            }
            $.each(listRules, function (index, objRules) {
                if ('appValidation' in objRules) {
                    var _rules = objRules.appValidation;
                    for (var i = 0; i < _rules.length; i++) {
                        if ($.inArray(_rules[i][0], rules) !== -1) {
                            found = true;
                            return false;
                        }
                    }
                }
            });

            return found;
        },

        /**
         * Return the string length using PHP function.
         * http://php.net/manual/en/function.strlen.php
         * http://phpjs.org/functions/strlen/
         *
         * @param string
         */
        strlen: function (string) {
            return strlen(string);
        },

        /**
         * Get the size of the object depending of his type.
         *
         * @param obj
         * @param element
         * @param value
         * @returns int
         */
        getSize: function getSize(obj, element, value) {

            if (this.hasNumericRules(element) && this.is_numeric(value)) {
                return parseFloat(value);
            } else if ($.isArray(value)) {
                return parseFloat(value.length);
            } else if (element.type === 'file') {
                return parseFloat(Math.floor(this.fileinfo(element).size));
            }

            return parseFloat(this.strlen(value));
        },

        /**
         * Return specified rule from element.
         *
         * @param rule
         * @param element
         * @returns object
         */
        getAppValidation: function (rule, element) {

            var found = undefined;
            $.each($.validator.staticRules(element), function (key, rules) {
                if (key === "appValidation") {
                    $.each(rules, function (i, value) {
                        if (value[0] === rule) {
                            found = value;
                        }
                    });
                }
            });

            return found;
        },

        /**
         * Return he timestamp of value passed using format or default format in element.
         *
         * @param value
         * @param format
         * @returns {boolean|int}
         */
        parseTime: function (value, format) {

            var timeValue = false;
            var fmt = new DateFormatter();

            if ($.type(format) === 'object') {
                var dateRule = this.getAppValidation('DateFormat', format);
                if (dateRule !== undefined) {
                    format = dateRule[1][0];
                } else {
                    format = null;
                }
            }

            if (format == null) {
                timeValue = this.strtotime(value);
            } else {
                timeValue = fmt.parseDate(value, format);
                if (timeValue) {
                    timeValue = Math.round((timeValue.getTime() / 1000));
                }
            }

            return timeValue;
        },

        /**
         * This method allows you to intelligently guess the date by closely matching the specific format.
         *
         * @param value
         * @param format
         * @returns {Date}
         */
        guessDate: function (value, format) {
            var fmt = new DateFormatter();
            return fmt.guessDate(value, format)
        },

        /**
         * Returns Unix timestamp based on PHP function strototime.
         * http://php.net/manual/es/function.strtotime.php
         * http://phpjs.org/functions/strtotime/
         *
         * @param text
         * @param now
         * @returns {*}
         */
        strtotime: function (text, now) {
            return strtotime(text, now)
        },

        /**
         * Returns if value is numeric.
         * http://php.net/manual/es/var.is_numeric.php
         * http://phpjs.org/functions/is_numeric/
         *
         * @param mixed_var
         * @returns {*}
         */
        is_numeric: function (mixed_var) {
            return is_numeric(mixed_var)
        },

        /**
         * Returns Array diff based on PHP function array_diff.
         * http://php.net/manual/es/function.array_diff.php
         * http://phpjs.org/functions/array_diff/
         *
         * @param arr1
         * @param arr2
         * @returns {*}
         */
        arrayDiff: function (arr1, arr2) {
            return array_diff(arr1, arr2);
        },

        /**
         * Check whether two arrays are equal to one another.
         *
         * @param arr1
         * @param arr2
         * @returns {*}
         */
        arrayEquals: function (arr1, arr2) {
            if (!$.isArray(arr1) || !$.isArray(arr2)) {
                return false;
            }

            if (arr1.length !== arr2.length) {
                return false;
            }

            return $.isEmptyObject(this.arrayDiff(arr1, arr2));
        },

        /**
         * Makes element dependant from other.
         *
         * @param validator
         * @param element
         * @param name
         * @returns {*}
         */
        dependentElement: function (validator, element, name) {

            var el = validator.findByName(name);

            if (el[0] !== undefined && validator.settings.onfocusout) {
                var event = 'blur';
                if (el[0].tagName === 'SELECT' ||
                        el[0].tagName === 'OPTION' ||
                        el[0].type === 'checkbox' ||
                        el[0].type === 'radio') {
                    event = 'click';
                }

                var ruleName = '.validate-appValidation';
                el.off(ruleName)
                        .off(event + ruleName + '-' + element.name)
                        .on(event + ruleName + '-' + element.name, function () {
                            $(element).valid();
                        });
            }

            return el[0];
        },

        /**
         * Parses error Ajax response and gets the message.
         *
         * @param response
         * @returns {string[]}
         */
        parseErrorResponse: function (response) {
            var newResponse = ['Whoops, looks like something went wrong.'];
            if ('responseText' in response) {
                var errorMsg = response.responseText.match(/<h1\s*>(.*)<\/h1\s*>/i);
                if ($.isArray(errorMsg)) {
                    newResponse = [errorMsg[1]];
                }
            }
            return newResponse;
        },

        /**
         * Escape string to use as Regular Expression.
         *
         * @param str
         * @returns string
         */
        escapeRegExp: function (str) {
            return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, "\\$&");
        },

        /**
         * Generate RegExp from wildcard attributes.
         *
         * @param name
         * @returns {RegExp}
         */
        regexFromWildcard: function (name) {
            var nameParts = name.split("[*]");
            if (nameParts.length === 1) {
                nameParts.push('');
            }
            var regexpParts = nameParts.map(function (currentValue, index) {
                if (index % 2 === 0) {
                    currentValue = currentValue + '[';
                } else {
                    currentValue = ']' + currentValue;
                }

                return appValidation.helpers.escapeRegExp(currentValue);
            });

            return new RegExp('^' + regexpParts.join('.*') + '$');
        }
    }
});


/*!
 * references from https://github.com/proengsoft/laravel-jsvalidation
 * Timezone Helper functions used by validators
 *
 */

$.extend(true, appValidation, {

    helpers: {

        /**
         * Check if the specified timezone is valid.
         *
         * @param value
         * @returns {boolean}
         */
        isTimezone: function (value) {

            var timezones = {
                "africa": [
                    "abidjan",
                    "accra",
                    "addis_ababa",
                    "algiers",
                    "asmara",
                    "bamako",
                    "bangui",
                    "banjul",
                    "bissau",
                    "blantyre",
                    "brazzaville",
                    "bujumbura",
                    "cairo",
                    "casablanca",
                    "ceuta",
                    "conakry",
                    "dakar",
                    "dar_es_salaam",
                    "djibouti",
                    "douala",
                    "el_aaiun",
                    "freetown",
                    "gaborone",
                    "harare",
                    "johannesburg",
                    "juba",
                    "kampala",
                    "khartoum",
                    "kigali",
                    "kinshasa",
                    "lagos",
                    "libreville",
                    "lome",
                    "luanda",
                    "lubumbashi",
                    "lusaka",
                    "malabo",
                    "maputo",
                    "maseru",
                    "mbabane",
                    "mogadishu",
                    "monrovia",
                    "nairobi",
                    "ndjamena",
                    "niamey",
                    "nouakchott",
                    "ouagadougou",
                    "porto-novo",
                    "sao_tome",
                    "tripoli",
                    "tunis",
                    "windhoek"
                ],
                "america": [
                    "adak",
                    "anchorage",
                    "anguilla",
                    "antigua",
                    "araguaina",
                    "argentina\/buenos_aires",
                    "argentina\/catamarca",
                    "argentina\/cordoba",
                    "argentina\/jujuy",
                    "argentina\/la_rioja",
                    "argentina\/mendoza",
                    "argentina\/rio_gallegos",
                    "argentina\/salta",
                    "argentina\/san_juan",
                    "argentina\/san_luis",
                    "argentina\/tucuman",
                    "argentina\/ushuaia",
                    "aruba",
                    "asuncion",
                    "atikokan",
                    "bahia",
                    "bahia_banderas",
                    "barbados",
                    "belem",
                    "belize",
                    "blanc-sablon",
                    "boa_vista",
                    "bogota",
                    "boise",
                    "cambridge_bay",
                    "campo_grande",
                    "cancun",
                    "caracas",
                    "cayenne",
                    "cayman",
                    "chicago",
                    "chihuahua",
                    "costa_rica",
                    "creston",
                    "cuiaba",
                    "curacao",
                    "danmarkshavn",
                    "dawson",
                    "dawson_creek",
                    "denver",
                    "detroit",
                    "dominica",
                    "edmonton",
                    "eirunepe",
                    "el_salvador",
                    "fortaleza",
                    "glace_bay",
                    "godthab",
                    "goose_bay",
                    "grand_turk",
                    "grenada",
                    "guadeloupe",
                    "guatemala",
                    "guayaquil",
                    "guyana",
                    "halifax",
                    "havana",
                    "hermosillo",
                    "indiana\/indianapolis",
                    "indiana\/knox",
                    "indiana\/marengo",
                    "indiana\/petersburg",
                    "indiana\/tell_city",
                    "indiana\/vevay",
                    "indiana\/vincennes",
                    "indiana\/winamac",
                    "inuvik",
                    "iqaluit",
                    "jamaica",
                    "juneau",
                    "kentucky\/louisville",
                    "kentucky\/monticello",
                    "kralendijk",
                    "la_paz",
                    "lima",
                    "los_angeles",
                    "lower_princes",
                    "maceio",
                    "managua",
                    "manaus",
                    "marigot",
                    "martinique",
                    "matamoros",
                    "mazatlan",
                    "menominee",
                    "merida",
                    "metlakatla",
                    "mexico_city",
                    "miquelon",
                    "moncton",
                    "monterrey",
                    "montevideo",
                    "montreal",
                    "montserrat",
                    "nassau",
                    "new_york",
                    "nipigon",
                    "nome",
                    "noronha",
                    "north_dakota\/beulah",
                    "north_dakota\/center",
                    "north_dakota\/new_salem",
                    "ojinaga",
                    "panama",
                    "pangnirtung",
                    "paramaribo",
                    "phoenix",
                    "port-au-prince",
                    "port_of_spain",
                    "porto_velho",
                    "puerto_rico",
                    "rainy_river",
                    "rankin_inlet",
                    "recife",
                    "regina",
                    "resolute",
                    "rio_branco",
                    "santa_isabel",
                    "santarem",
                    "santiago",
                    "santo_domingo",
                    "sao_paulo",
                    "scoresbysund",
                    "shiprock",
                    "sitka",
                    "st_barthelemy",
                    "st_johns",
                    "st_kitts",
                    "st_lucia",
                    "st_thomas",
                    "st_vincent",
                    "swift_current",
                    "tegucigalpa",
                    "thule",
                    "thunder_bay",
                    "tijuana",
                    "toronto",
                    "tortola",
                    "vancouver",
                    "whitehorse",
                    "winnipeg",
                    "yakutat",
                    "yellowknife"
                ],
                "antarctica": [
                    "casey",
                    "davis",
                    "dumontdurville",
                    "macquarie",
                    "mawson",
                    "mcmurdo",
                    "palmer",
                    "rothera",
                    "south_pole",
                    "syowa",
                    "vostok"
                ],
                "arctic": [
                    "longyearbyen"
                ],
                "asia": [
                    "aden",
                    "almaty",
                    "amman",
                    "anadyr",
                    "aqtau",
                    "aqtobe",
                    "ashgabat",
                    "baghdad",
                    "bahrain",
                    "baku",
                    "bangkok",
                    "beirut",
                    "bishkek",
                    "brunei",
                    "choibalsan",
                    "chongqing",
                    "colombo",
                    "damascus",
                    "dhaka",
                    "dili",
                    "dubai",
                    "dushanbe",
                    "gaza",
                    "harbin",
                    "hebron",
                    "ho_chi_minh",
                    "hong_kong",
                    "hovd",
                    "irkutsk",
                    "jakarta",
                    "jayapura",
                    "jerusalem",
                    "kabul",
                    "kamchatka",
                    "karachi",
                    "kashgar",
                    "kathmandu",
                    "khandyga",
                    "kolkata",
                    "krasnoyarsk",
                    "kuala_lumpur",
                    "kuching",
                    "kuwait",
                    "macau",
                    "magadan",
                    "makassar",
                    "manila",
                    "muscat",
                    "nicosia",
                    "novokuznetsk",
                    "novosibirsk",
                    "omsk",
                    "oral",
                    "phnom_penh",
                    "pontianak",
                    "pyongyang",
                    "qatar",
                    "qyzylorda",
                    "rangoon",
                    "riyadh",
                    "sakhalin",
                    "samarkand",
                    "seoul",
                    "shanghai",
                    "singapore",
                    "taipei",
                    "tashkent",
                    "tbilisi",
                    "tehran",
                    "thimphu",
                    "tokyo",
                    "ulaanbaatar",
                    "urumqi",
                    "ust-nera",
                    "vientiane",
                    "vladivostok",
                    "yakutsk",
                    "yekaterinburg",
                    "yerevan"
                ],
                "atlantic": [
                    "azores",
                    "bermuda",
                    "canary",
                    "cape_verde",
                    "faroe",
                    "madeira",
                    "reykjavik",
                    "south_georgia",
                    "st_helena",
                    "stanley"
                ],
                "australia": [
                    "adelaide",
                    "brisbane",
                    "broken_hill",
                    "currie",
                    "darwin",
                    "eucla",
                    "hobart",
                    "lindeman",
                    "lord_howe",
                    "melbourne",
                    "perth",
                    "sydney"
                ],
                "europe": [
                    "amsterdam",
                    "andorra",
                    "athens",
                    "belgrade",
                    "berlin",
                    "bratislava",
                    "brussels",
                    "bucharest",
                    "budapest",
                    "busingen",
                    "chisinau",
                    "copenhagen",
                    "dublin",
                    "gibraltar",
                    "guernsey",
                    "helsinki",
                    "isle_of_man",
                    "istanbul",
                    "jersey",
                    "kaliningrad",
                    "kiev",
                    "lisbon",
                    "ljubljana",
                    "london",
                    "luxembourg",
                    "madrid",
                    "malta",
                    "mariehamn",
                    "minsk",
                    "monaco",
                    "moscow",
                    "oslo",
                    "paris",
                    "podgorica",
                    "prague",
                    "riga",
                    "rome",
                    "samara",
                    "san_marino",
                    "sarajevo",
                    "simferopol",
                    "skopje",
                    "sofia",
                    "stockholm",
                    "tallinn",
                    "tirane",
                    "uzhgorod",
                    "vaduz",
                    "vatican",
                    "vienna",
                    "vilnius",
                    "volgograd",
                    "warsaw",
                    "zagreb",
                    "zaporozhye",
                    "zurich"
                ],
                "indian": [
                    "antananarivo",
                    "chagos",
                    "christmas",
                    "cocos",
                    "comoro",
                    "kerguelen",
                    "mahe",
                    "maldives",
                    "mauritius",
                    "mayotte",
                    "reunion"
                ],
                "pacific": [
                    "apia",
                    "auckland",
                    "chatham",
                    "chuuk",
                    "easter",
                    "efate",
                    "enderbury",
                    "fakaofo",
                    "fiji",
                    "funafuti",
                    "galapagos",
                    "gambier",
                    "guadalcanal",
                    "guam",
                    "honolulu",
                    "johnston",
                    "kiritimati",
                    "kosrae",
                    "kwajalein",
                    "majuro",
                    "marquesas",
                    "midway",
                    "nauru",
                    "niue",
                    "norfolk",
                    "noumea",
                    "pago_pago",
                    "palau",
                    "pitcairn",
                    "pohnpei",
                    "port_moresby",
                    "rarotonga",
                    "saipan",
                    "tahiti",
                    "tarawa",
                    "tongatapu",
                    "wake",
                    "wallis"
                ],
                "utc": [
                    ""
                ]
            };

            var tzparts = value.split('/', 2);
            var continent = tzparts[0].toLowerCase();
            var city = '';
            if (tzparts[1]) {
                city = tzparts[1].toLowerCase();
            }

            return (continent in timezones && (timezones[continent].length === 0 || timezones[continent].indexOf(city) !== -1))
        }
    }
});

/*!
 * Methods that implement CApp Validations
 */

$.extend(true, appValidation, {

    methods: {

        helpers: appValidation.helpers,
        jsRemoteTimer: 0,
        /**
         * "Validate" optional attributes.
         * Always returns true, just lets us put sometimes in rules.
         *
         * @return {boolean}
         */
        Sometimes: function () {
            return true;
        },
        /**
         * Bail This is the default behaivour os JSValidation.
         * Always returns true, just lets us put sometimes in rules.
         *
         * @return {boolean}
         */
        Bail: function () {
            return true;
        },
        /**
         * "Indicate" validation should pass if value is null.
         * Always returns true, just lets us put "nullable" in rules.
         *
         * @return {boolean}
         */
        Nullable: function () {
            return true;
        },
        /**
         * Validate the given attribute is filled if it is present.
         */
        Filled: function (value, element) {
            return $.validator.methods.required.call(this, value, element, true);
        },
        /**
         * Validate that a required attribute exists.
         */
        Required: function (value, element) {
            return $.validator.methods.required.call(this, value, element);
        },
        /**
         * Validate that an attribute exists when any other attribute exists.
         *
         * @return {boolean}
         */
        RequiredWith: function (value, element, params) {
            var validator = this,
                    required = false;
            var currentObject = this;
            $.each(params, function (i, param) {
                var target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                required = required || (
                        target !== undefined &&
                        $.validator.methods.required.call(
                                validator,
                                currentObject.elementValue(target),
                                target, true));
            });
            if (required) {
                return $.validator.methods.required.call(this, value, element, true);
            }
            return true;
        },
        /**
         * Validate that an attribute exists when all other attribute exists.
         *
         * @return {boolean}
         */
        RequiredWithAll: function (value, element, params) {
            var validator = this,
                    required = true;
            var currentObject = this;
            $.each(params, function (i, param) {
                var target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                required = required && (
                        target !== undefined &&
                        $.validator.methods.required.call(
                                validator,
                                currentObject.elementValue(target),
                                target, true));
            });
            if (required) {
                return $.validator.methods.required.call(this, value, element, true);
            }
            return true;
        },
        /**
         * Validate that an attribute exists when any other attribute does not exists.
         *
         * @return {boolean}
         */
        RequiredWithout: function (value, element, params) {
            var validator = this,
                    required = false;
            var currentObject = this;
            $.each(params, function (i, param) {
                var target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                required = required ||
                        target === undefined ||
                        !$.validator.methods.required.call(
                                validator,
                                currentObject.elementValue(target),
                                target, true);
            });
            if (required) {
                return $.validator.methods.required.call(this, value, element, true);
            }
            return true;
        },
        /**
         * Validate that an attribute exists when all other attribute does not exists.
         *
         * @return {boolean}
         */
        RequiredWithoutAll: function (value, element, params) {
            var validator = this,
                    required = true,
                    currentObject = this;
            $.each(params, function (i, param) {
                var target = appValidation.helpers.dependentElement(
                        currentObject, element, param);
                required = required && (
                        target === undefined ||
                        !$.validator.methods.required.call(
                                validator,
                                currentObject.elementValue(target),
                                target, true));
            });
            if (required) {
                return $.validator.methods.required.call(this, value, element, true);
            }
            return true;
        },
        /**
         * Validate that an attribute exists when another attribute has a given value.
         *
         * @return {boolean}
         */
        RequiredIf: function (value, element, params) {

            var target = appValidation.helpers.dependentElement(
                    this, element, params[0]);
            if (target !== undefined) {
                var val = String(this.elementValue(target));
                if (typeof val !== 'undefined') {
                    var data = params.slice(1);
                    if ($.inArray(val, data) !== -1) {
                        return $.validator.methods.required.call(
                                this, value, element, true);
                    }
                }
            }

            return true;
        },
        /**
         * Validate that an attribute exists when another
         * attribute does not have a given value.
         *
         * @return {boolean}
         */
        RequiredUnless: function (value, element, params) {

            var target = appValidation.helpers.dependentElement(
                    this, element, params[0]);
            if (target !== undefined) {
                var val = String(this.elementValue(target));
                if (typeof val !== 'undefined') {
                    var data = params.slice(1);
                    if ($.inArray(val, data) !== -1) {
                        return true;
                    }
                }
            }

            return $.validator.methods.required.call(
                    this, value, element, true);
        },
        /**
         * Validate that an attribute has a matching confirmation.
         *
         * @return {boolean}
         */
        Confirmed: function (value, element, params) {
            return appValidation.methods.Same.call(this, value, element, params);
        },
        /**
         * Validate that two attributes match.
         *
         * @return {boolean}
         */
        Same: function (value, element, params) {

            var target = appValidation.helpers.dependentElement(
                    this, element, params[0]);
            if (target !== undefined) {
                return String(value) === String(this.elementValue(target));
            }
            return false;
        },
        /**
         * Validate that the values of an attribute is in another attribute.
         *
         * @param value
         * @param element
         * @param params
         * @returns {boolean}
         * @constructor
         */
        InArray: function (value, element, params) {
            if (typeof params[0] === 'undefined') {
                return false;
            }
            var elements = this.elements();
            var found = false;
            var nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);
            for (var i = 0; i < elements.length; i++) {
                var targetName = elements[i].name;
                if (targetName.match(nameRegExp)) {
                    var equals = appValidation.methods.Same.call(this, value, element, [targetName]);
                    found = found || equals;
                }
            }

            return found;
        },
        /**
         * Validate an attribute is unique among other values.
         *
         * @param value
         * @param element
         * @param params
         * @returns {boolean}
         */
        Distinct: function (value, element, params) {
            if (typeof params[0] === 'undefined') {
                return false;
            }

            var elements = this.elements();
            var found = false;
            var nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);
            for (var i = 0; i < elements.length; i++) {
                var targetName = elements[i].name;
                if (targetName !== element.name && targetName.match(nameRegExp)) {
                    var equals = appValidation.methods.Same.call(this, value, element, [targetName]);
                    found = found || equals;
                }
            }

            return !found;
        },
        /**
         * Validate that an attribute is different from another attribute.
         *
         * @return {boolean}
         */
        Different: function (value, element, params) {
            return !appValidation.methods.Same.call(this, value, element, params);
        },
        /**
         * Validate that an attribute was "accepted".
         * This validation rule implies the attribute is "required".
         *
         * @return {boolean}
         */
        Accepted: function (value) {
            var regex = new RegExp("^(?:(yes|on|1|true))$", 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute is an array.
         *
         * @param value
         * @param element
         */
        Array: function (value, element) {
            if (element.name.indexOf('[') !== -1 && element.name.indexOf(']') !== -1) {
                return true;
            }

            return $.isArray(value);
        },
        /**
         * Validate that an attribute is a boolean.
         *
         * @return {boolean}
         */
        Boolean: function (value) {
            var regex = new RegExp("^(?:(true|false|1|0))$", 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute is an integer.
         *
         * @return {boolean}
         */
        Integer: function (value) {
            var regex = new RegExp("^(?:-?\\d+)$", 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute is numeric.
         */
        Numeric: function (value, element) {
            return $.validator.methods.number.call(this, value, element, true);
        },
        /**
         * Validate that an attribute is a string.
         *
         * @return {boolean}
         */
        String: function (value) {
            return typeof value === 'string';
        },
        /**
         * The field under validation must be numeric and must have an exact length of value.
         */
        Digits: function (value, element, params) {
            return (
                    $.validator.methods.number.call(this, value, element, true) &&
                    value.length === parseInt(params, 10));
        },
        /**
         * The field under validation must have a length between the given min and max.
         */
        DigitsBetween: function (value, element, params) {
            return ($.validator.methods.number.call(this, value, element, true)
                    && value.length >= parseFloat(params[0]) && value.length <= parseFloat(params[1]));
        },
        /**
         * Validate the size of an attribute.
         *
         * @return {boolean}
         */
        Size: function (value, element, params) {
            return appValidation.helpers.getSize(this, element, value) === parseFloat(params[0]);
        },
        /**
         * Validate the size of an attribute is between a set of values.
         *
         * @return {boolean}
         */
        Between: function (value, element, params) {
            return (appValidation.helpers.getSize(this, element, value) >= parseFloat(params[0]) &&
                    appValidation.helpers.getSize(this, element, value) <= parseFloat(params[1]));
        },
        /**
         * Validate the size of an attribute is greater than a minimum value.
         *
         * @return {boolean}
         */
        Min: function (value, element, params) {
            return appValidation.helpers.getSize(this, element, value) >= parseFloat(params[0]);
        },
        /**
         * Validate the size of an attribute is less than a maximum value.
         *
         * @return {boolean}
         */
        Max: function (value, element, params) {
            return appValidation.helpers.getSize(this, element, value) <= parseFloat(params[0]);
        },
        /**
         * Validate an attribute is contained within a list of values.
         *
         * @return {boolean}
         */
        In: function (value, element, params) {
            if ($.isArray(value) && appValidation.helpers.hasRules(element, "Array")) {
                var diff = appValidation.helpers.arrayDiff(value, params);
                return Object.keys(diff).length === 0;
            }
            return params.indexOf(value.toString()) !== -1;
        },
        /**
         * Validate an attribute is not contained within a list of values.
         *
         * @return {boolean}
         */
        NotIn: function (value, element, params) {
            return params.indexOf(value.toString()) === -1;
        },
        /**
         * Validate that an attribute is a valid IP.
         *
         * @return {boolean}
         */
        Ip: function (value) {
            return /^(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)\.(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i.test(value) ||
                    /^((([0-9A-Fa-f]{1,4}:){7}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}:[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){5}:([0-9A-Fa-f]{1,4}:)?[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){4}:([0-9A-Fa-f]{1,4}:){0,2}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){3}:([0-9A-Fa-f]{1,4}:){0,3}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){2}:([0-9A-Fa-f]{1,4}:){0,4}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){6}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(([0-9A-Fa-f]{1,4}:){0,5}:((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|(::([0-9A-Fa-f]{1,4}:){0,5}((\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b)\.){3}(\b((25[0-5])|(1\d{2})|(2[0-4]\d)|(\d{1,2}))\b))|([0-9A-Fa-f]{1,4}::([0-9A-Fa-f]{1,4}:){0,5}[0-9A-Fa-f]{1,4})|(::([0-9A-Fa-f]{1,4}:){0,6}[0-9A-Fa-f]{1,4})|(([0-9A-Fa-f]{1,4}:){1,7}:))$/i.test(value);
        },
        /**
         * Validate that an attribute is a valid e-mail address.
         */
        Email: function (value, element) {
            return $.validator.methods.email.call(this, value, element, true);
        },
        /**
         * Validate that an attribute is a valid URL.
         */
        Url: function (value, element) {
            return $.validator.methods.url.call(this, value, element, true);
        },
        /**
         * The field under validation must be a successfully uploaded file.
         *
         * @return {boolean}
         */
        File: function (value, element) {
            if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                return true;
            }
            if ('files' in element) {
                return (element.files.length > 0);
            }
            return false;
        },
        /**
         * Validate the MIME type of a file upload attribute is in a set of MIME types.
         *
         * @return {boolean}
         */
        Mimes: function (value, element, params) {
            if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                return true;
            }
            var lowerParams = $.map(params, function (item) {
                return item.toLowerCase();
            });
            var fileinfo = appValidation.helpers.fileinfo(element);
            return (fileinfo !== false && lowerParams.indexOf(fileinfo.extension.toLowerCase()) !== -1);
        },
        /**
         * The file under validation must match one of the given MIME types.
         *
         * @return {boolean}
         */
        Mimetypes: function (value, element, params) {
            if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                return true;
            }
            var lowerParams = $.map(params, function (item) {
                return item.toLowerCase();
            });
            var fileinfo = appValidation.helpers.fileinfo(element);
            if (fileinfo === false) {
                return false;
            }
            return (lowerParams.indexOf(fileinfo.type.toLowerCase()) !== -1);
        },
        /**
         * Validate the MIME type of a file upload attribute is in a set of MIME types.
         */
        Image: function (value, element) {
            return appValidation.methods.Mimes.call(this, value, element, [
                'jpg', 'png', 'gif', 'bmp', 'svg', 'jpeg'
            ]);
        },
        /**
         * Validate dimensions of Image.
         *
         * @return {boolean|string}
         */
        Dimensions: function (value, element, params, callback) {
            if (!window.File || !window.FileReader || !window.FileList || !window.Blob) {
                return true;
            }
            if (element.files === null || typeof element.files[0] === 'undefined') {
                return false;
            }

            var fr = new FileReader;
            fr.onload = function () {
                var img = new Image();
                img.onload = function () {
                    var height = parseFloat(img.naturalHeight);
                    var width = parseFloat(img.naturalWidth);
                    var ratio = width / height;
                    var notValid = ((params['width']) && parseFloat(params['width'] !== width)) ||
                            ((params['min_width']) && parseFloat(params['min_width']) > width) ||
                            ((params['max_width']) && parseFloat(params['max_width']) < width) ||
                            ((params['height']) && parseFloat(params['height']) !== height) ||
                            ((params['min_height']) && parseFloat(params['min_height']) > height) ||
                            ((params['max_height']) && parseFloat(params['max_height']) < height) ||
                            ((params['ratio']) && ratio !== parseFloat(eval(params['ratio'])));
                    callback(!notValid);
                };
                img.onerror = function () {
                    callback(false);
                };
                img.src = fr.result;
            };
            fr.readAsDataURL(element.files[0]);
            return 'pending';
        },
        /**
         * Validate that an attribute contains only alphabetic characters.
         *
         * @return {boolean}
         */
        Alpha: function (value) {
            if (typeof value !== 'string') {
                return false;
            }

            var regex = new RegExp("^(?:^[a-z\u00E0-\u00FC]+$)$", 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute contains only alpha-numeric characters.
         *
         * @return {boolean}
         */
        AlphaNum: function (value) {
            if (typeof value !== 'string') {
                return false;
            }
            var regex = new RegExp("^(?:^[a-z0-9\u00E0-\u00FC]+$)$", 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute contains only alphabetic characters.
         *
         * @return {boolean}
         */
        AlphaDash: function (value) {
            if (typeof value !== 'string') {
                return false;
            }
            var regex = new RegExp("^(?:^[a-z0-9\u00E0-\u00FC_-]+$)$", 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute passes a regular expression check.
         *
         * @return {boolean}
         */
        Regex: function (value, element, params) {
            var invalidModifiers = ['x', 's', 'u', 'X', 'U', 'A'];
            // Converting php regular expression
            var phpReg = new RegExp('^(?:\/)(.*\\\/?[^\/]*|[^\/]*)(?:\/)([gmixXsuUAJ]*)?$');
            var matches = params[0].match(phpReg);
            if (matches === null) {
                return false;
            }
            // checking modifiers
            var php_modifiers = [];
            if (matches[2] !== undefined) {
                php_modifiers = matches[2].split('');
                for (var i = 0; i < php_modifiers.length < i; i++) {
                    if (invalidModifiers.indexOf(php_modifiers[i]) !== -1) {
                        return true;
                    }
                }
            }
            var regex = new RegExp("^(?:" + matches[1] + ")$", php_modifiers.join());
            return regex.test(value);
        },
        /**
         * Validate that an attribute is a valid date.
         *
         * @return {boolean}
         */
        Date: function (value) {
            return (appValidation.helpers.strtotime(value) !== false);
        },
        /**
         * Validate that an attribute matches a date format.
         *
         * @return {boolean}
         */
        DateFormat: function (value, element, params) {
            return appValidation.helpers.parseTime(value, params[0]) !== false;
        },
        /**
         * Validate the date is before a given date.
         *
         * @return {boolean}
         */
        Before: function (value, element, params) {

            var timeCompare = parseFloat(params);
            if (isNaN(timeCompare)) {
                var target = appValidation.helpers.dependentElement(this, element, params);
                if (target === undefined) {
                    return false;
                }
                timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
            }

            var timeValue = appValidation.helpers.parseTime(value, element);
            return (timeValue !== false && timeValue < timeCompare);
        },
        /**
         * Validate the date is after a given date.
         *
         * @return {boolean}
         */
        After: function (value, element, params) {
            var timeCompare = parseFloat(params);
            if (isNaN(timeCompare)) {
                var target = appValidation.helpers.dependentElement(this, element, params);
                if (target === undefined) {
                    return false;
                }
                timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
            }

            var timeValue = appValidation.helpers.parseTime(value, element);
            return (timeValue !== false && timeValue > timeCompare);
        },
        /**
         * Validate that an attribute is a valid date.
         */
        Timezone: function (value) {
            return appValidation.helpers.isTimezone(value);
        },
        /**
         * Validate the attribute is a valid JSON string.
         *
         * @param  value
         * @return bool
         */
        Json: function (value) {
            var result = true;
            try {
                JSON.parse(value);
            } catch (e) {
                result = false;
            }
            return result;
        }
    }
});
