/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

import Base64 from "./module/Base64";
import Url from "./module/Url";

import Util from "./module/Util";
import CF from "./CF";
import ScrollToTop from "./module/ScrollToTop";
import CUI from "./cui";
import { dispatch, cfDirectives } from '@/util'

export default class Cresenity {
    constructor() {
        this.cf = new CF();
        this.base64 = new Base64();
        this.url = new Url();
        this.scrollToTop = new ScrollToTop();
        this.callback = {};
        this.filesAdded = [];
        this.ui = new CUI();

        this.dispatch('cf:ui:available')

    }

    dispatch(eventName) {
        dispatch(eventName)
    }
    haveCallback(name) {
        return typeof this.callback[name] == 'function';
    };
    doCallback(name, ...args) {
        if (this.haveCallback(name)) {
            this.callback[name](...args);
        }
    };

    setCallback(name, cb) {
        this.callback[name] = cb;
    };

    isUsingRequireJs() {
        return (typeof capp.requireJs !== "undefined") ? capp.requireJs : true;
    }

    normalizeRequireJs() {
        if (!this.isUsingRequireJs()) {

            if (typeof define === 'function' && define.amd) {

                window.define = undefined;

            }
        }
    }
    isJson(text) {
        if (typeof text == 'string') {
            return (/^[\],:{}\s]*$/.test(text.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, '')));
        }
        return false;
    };

    loadJs(filename, callback) {
        var fileref = document.createElement('script');
        fileref.setAttribute("type", "text/javascript");
        fileref.setAttribute("src", filename);
        // IE 6 & 7
        if (typeof(callback) === 'function') {
            fileref.onload = callback;
            fileref.onreadystatechange = function() {
                if (this.readyState == 'complete') {
                    callback();
                }
            }
        }
        document.getElementsByTagName("head")[0].appendChild(fileref);

    };

    loadJsCss(filename, filetype, callback) {
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
            if (typeof(callback) === 'function') {
                fileref.onload = cresenity.handleResponseCallback(callback);
                fileref.onreadystatechange = function() {
                    if (this.readyState == 'complete') {
                        cresenity.handleResponseCallback(callback);
                    }
                }
            }
            document.getElementsByTagName("head")[0].appendChild(fileref);
        }
    };
    require(filename, filetype, callback) {
        if (this.filesAdded.indexOf("[" + filename + "]") == -1) {
            this.loadJsCss(filename, filetype, callback);
            this.filesAdded += "[" + filename + "]" //List of files added in the form "[filename1],[filename2],etc"
        } else {
            cresenity.filesLoaded++;

            if (cresenity.filesLoaded == cresenity.filesNeeded) {
                callback();
            }
        }
    };

    handleResponse(data, callback) {
        if (data.css_require && data.css_require.length > 0) {
            for (var i = 0; i < data.css_require.length; i++) {
                cresenity.require(data.css_require[i], 'css');
            }
        }
        if (data.js_require && data.js_require.length > 0) {
            for (var i = 0; i < data.js_require.length; i++) {
                cresenity.require(data.js_require[i], 'js');
            }
        }
        callback();


    };
    handleResponseCallback(callback) {
        cresenity.filesLoaded++;
        if (cresenity.filesLoaded == $.cresenity.filesNeeded) {
            callback();
        }
    };

    reload(options) {
        let settings = Util.extend({
            // These are the defaults.
            method: 'get',
            dataAddition: {},
            url: '/',
            reloadType: 'reload',
            onComplete: false,
            onSuccess: false,
            onBlock: false,
            onUnblock: false,
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

        (function(settings) {
            $(selector).each(function() {
                var idTarget = $(this).attr('id');
                url = cresenity.url.addQueryString(url, 'capp_current_container_id', idTarget);


                (function(element) {

                    if (typeof settings.onBlock == 'function') {
                        settings.onBlock();
                    } else {
                        cresenity.blockElement($(element));
                    }

                    $(element).data('xhr', $.ajax({
                        type: method,
                        url: url,
                        dataType: 'json',
                        data: dataAddition,
                        success: function(data) {

                            cresenity.doCallback('onReloadSuccess', data);
                            cresenity.handleResponse(data, function() {

                                switch (settings.reloadType) {
                                    case 'after':
                                        $(element).after(data.html);
                                        break;
                                    case 'before':
                                        $(element).before(data.html);
                                        break;
                                    case 'append':
                                        $(element).append(data.html);
                                        break;
                                    case 'prepend':
                                        $(element).prepend(data.html);
                                        break;
                                    default:
                                        $(element).html(data.html);
                                        break;
                                }

                                if (data.js && data.js.length > 0) {
                                    var script = cresenity.base64.decode(data.js);
                                    eval(script);
                                }


                                if ($(element).find('.prettyprint').length > 0) {
                                    window.prettyPrint && prettyPrint();
                                }
                                if (typeof settings.onSuccess == 'function') {
                                    settings.onSuccess(data);
                                }
                            });
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            if (thrownError != 'abort') {
                                cresenity.message('error', 'Error, please call administrator... (' + thrownError + ')');
                            }

                        },
                        complete: function() {
                            $(element).data('xhr', false);
                            if (typeof settings.onBlock == 'function') {
                                settings.onUnblock();
                            } else {
                                cresenity.unblockElement($(element));
                            }

                            if (typeof settings.onComplete == 'function') {
                                settings.onComplete();
                            }
                        }
                    }));
                })(this);
            });
        })(settings);

    };

    append(options) {
        options.reloadType = 'append';
        this.reload(options);
    };
    prepend(options) {
        options.reloadType = 'prepend';
        this.reload(options);
    };
    after(options) {
        options.reloadType = 'after';
        this.reload(options);
    };
    before(options) {
        options.reloadType = 'before';
        this.reload(options);
    };
    confirm(options) {
        var settings = $.extend({
            // These are the defaults.
            method: 'get',
            dataAddition: {},
            message: 'Are you sure?',
            onConfirmed: false,
        }, options);
        bootbox.confirm(settings.message, settings.onConfirmed);

    };
    modal(options) {

        var settings = $.extend({
            // These are the defaults.
            haveHeader: false,
            haveFooter: false,
            headerText: '',
            backdrop: 'static',
            modalClass: false,
            onClose: false,
            appendTo: false,
            footerAction: {}
        }, options);

        if (settings.title) {
            settings.haveHeader = true;
            settings.headerText = settings.title;
        }

        let modalContainer = jQuery('<div>').addClass('modal');

        if (settings.modalClass) {
            modalContainer.addClass(settings.modalClass);
        }

        if (settings.isSidebar) {
            modalContainer.addClass('sidebar');
            modalContainer.addClass(settings.sidebarMode);
        }
        if (settings.isFull) {
            modalContainer.addClass('sidebar full');
        }
        var modalDialog = jQuery('<div>').addClass('modal-dialog modal-xl');
        var modalContent = jQuery('<div>').addClass('modal-content');

        var modalHeader = jQuery('<div>').addClass('modal-header');
        var modalTitle = jQuery('<div>').addClass('modal-title');
        var modalButtonClose = jQuery('<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        modalButtonClose.click(function(e) {
            modalButtonClose.closest('.modal').modal('hide');
        });
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

        var appendTo = settings.appendTo;
        if (typeof appendTo == 'undefined' || !appendTo) {
            appendTo = $('body');
        }

        modalContainer.appendTo(appendTo);
        modalContainer.addClass('capp-modal');
        modalContainer.on('hidden.bs.modal', function(e) {

            if (cresenity.modalElements.length > 0) {
                var lastModal = cresenity.modalElements[cresenity.modalElements.length - 1];
                if (lastModal && lastModal.get(0) === $(e.target).get(0)) {
                    (function(modal) {
                        var Next = function() {
                            this.isRunning = false;
                            this.callback = (delay) => {
                                if (typeof delay == 'undefined') {
                                    delay = 0;
                                }
                                if (typeof parseInt(delay) == 'NaN') {
                                    delay = 0;
                                }

                                setTimeout(function() {

                                    $(modal).remove();
                                    cresenity.modalElements.pop();



                                    var modalExists = $('.modal:visible').length > 0;
                                    if (!modalExists) {
                                        $('body').removeClass('modal-open');
                                    } else {
                                        if (!$('body').hasClass('modal-open')) {
                                            $('body').addClass('modal-open');
                                        }

                                    }


                                }, delay);
                                this.isRunning = true;
                            }
                        }
                        next = new Next();
                        if (typeof settings.onClose == 'function') {
                            settings.onClose(e, next.callback);
                        }
                        if (!next.isRunning) {

                            next.callback();
                        }
                    })(lastModal);
                }
            }

        });

        modalContainer.on('shown.bs.modal', function(e) {
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

        modalContainer.modal({
            backdrop: settings.backdrop
        });

        return modalContainer;
    };

    closeLastModal(options) {
        if (cresenity.modalElements.length > 0) {
            var lastModal = cresenity.modalElements[cresenity.modalElements.length - 1];

            lastModal.modal('hide');
        }
    }
    closeDialog(options) {
        this.closeLastModal(options);
    }
    ajax(options) {
        var settings = $.extend({
            block: true,
            url: window.location.href,
            method: 'post',
        }, options);
        var dataAddition = settings.dataAddition;
        var url = settings.url;
        url = this.url.replaceParam(url);
        if (typeof dataAddition == 'undefined') {
            dataAddition = {};
        }
        if (settings.block) {
            cresenity.blockPage();
        }

        var validationIsValid = true;
        var ajaxOptions = {
            url: url,
            dataType: 'json',
            data: dataAddition,
            type: settings.method,

            success: function(response) {
                var onSuccess = function() {};
                var onError = function(errMessage) {
                    cresenity.showError(errMessage)
                };
                if (typeof settings.onSuccess == 'function' && validationIsValid) {
                    onSuccess = settings.onSuccess;
                }
                if (typeof settings.onError == 'function' && validationIsValid) {
                    onError = settings.onError;
                }

                if (validationIsValid) {
                    if (settings.handleJsonResponse == true) {
                        cresenity.handleJsonResponse(response, onSuccess, onError);
                    } else {
                        onSuccess(response);
                    }

                }
            },
            error: function(xhr, ajaxOptions, thrownError) {
                if (thrownError != 'abort') {
                    console.log(thrownError);
                    cresenity.showError(thrownError);
                }

            },

            complete: function() {
                if (settings.block) {
                    cresenity.unblockPage();
                }

                if (typeof settings.onComplete == 'function' && validationIsValid) {
                    settings.onComplete();
                }
            },
        };

        return $.ajax(ajaxOptions);

    };
    ajaxSubmit(options) {
        var settings = $.extend({}, options);
        var selector = settings.selector;
        $(selector).each(function() {
            //don't do it again if still loading

            var formAjaxUrl = $(this).attr('action') || '';
            var formMethod = $(this).attr('method') || 'get';
            (function(element) {
                cresenity.blockElement($(element));
                var validationIsValid = true;
                var ajaxOptions = {
                    url: formAjaxUrl,
                    dataType: 'json',
                    type: formMethod,
                    beforeSubmit: function() {
                        if (typeof $(element).validate == 'function') {
                            validationIsValid = $(element).validate().form();
                            return validationIsValid;
                        }
                        return true;
                    },
                    success: function(response) {
                        var onSuccess = function() {};
                        var onError = function(errMessage) {
                            cresenity.showError(errMessage)
                        };

                        haveOnSuccess = false;
                        if (typeof settings.onSuccess == 'function' && validationIsValid) {
                            onSuccess = settings.onSuccess;
                            haveOnSuccess = true;
                        }
                        if (typeof settings.onError == 'function' && validationIsValid) {
                            onError = settings.onError;
                        }

                        if (validationIsValid) {
                            if (settings.handleJsonResponse == true && haveOnSuccess) {
                                cresenity.handleJsonResponse(response, onSuccess, onError);
                            } else {
                                onSuccess(response);
                            }

                        }
                    },

                    complete: function() {
                        cresenity.unblockElement($(element));

                        if (typeof settings.onComplete == 'function' && validationIsValid) {
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

    debug(message) {
        if (this.cf.getConfig().debug) {
            console.log(message);
        }
    }
    message(type, message, alertType, callback) {
        this.debug(message);
        alertType = typeof alertType !== 'undefined' ? alertType : 'notify';
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

    blockPage(options) {
        var settings = $.extend({
            innerMessage: '<div class="sk-folding-cube sk-primary"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div><h5 style="color: #444">LOADING...</h5>',
        }, options);
        $.blockUI({
            message: settings.innerMessage,
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

    scrollTo(element, container) {
        if (typeof container == 'undefined') {
            container = document.body;
        }
        $(container).animate({
            scrollTop: $(element).offset().top - ($(container).offset().top + $(container).scrollTop())
        });
    };

    formatCurrency(rp) {
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
    }
    unblockPage() {
        $.unblockUI();
    };
    blockElement(selector, options) {
        var settings = $.extend({
            innerMessage: '<div class="sk-wave sk-primary"><div class="sk-rect sk-rect1"></div> <div class="sk-rect sk-rect2"></div> <div class="sk-rect sk-rect3"></div> <div class="sk-rect sk-rect4"></div> <div class="sk-rect sk-rect5"></div></div>',
        }, options);

        $(selector).block({
            message: settings.innerMessage,
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
    unblockElement(selector) {
        $(selector).unblock();
    };

    value(elm) {
        elm = jQuery(elm);
        if (elm.length == 0) {
            return null;
        }
        if (elm.attr('type') == 'checkbox') {
            if (!elm.is(':checked')) {
                return null;
            }
        }
        if (elm.attr('type') == 'radio') {
            if (!elm.is(':checked')) {
                return null;
            }
        }
        if (typeof elm.val() != 'undefined') {
            return elm.val();
        }
        if (typeof elm.attr('value') != 'undefined') {
            return elm.attr('value');
        }
        return elm.html();
    };


    initConfirm() {

        var confirmInitialized = $('body').attr('data-confirm-initialized');
        if (!confirmInitialized) {
            jQuery(document).on('click', 'a.confirm, button.confirm', function(e) {
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
                bootbox.confirm({
                    className: "capp-modal-confirm",
                    message: message,
                    callback: function(confirmed) {
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
                        setTimeout(function() {
                            var modalExists = $('.modal:visible').length > 0;
                            if (!modalExists) {
                                $('body').removeClass('modal-open');
                            } else {
                                $('body').addClass('modal-open');
                            }
                        }, 750);
                    }
                });


                return false;
            });
            $('body').attr('data-confirm-initialized', '1');
        }
        var confirmSubmitInitialized = $('body').attr('data-confirm-submit-initialized');
        if (!confirmSubmitInitialized) {
            jQuery(document).on('click', 'input[type=submit].confirm', function(e) {

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
                bootbox.confirm(message, str_cancel, str_confirm, function(confirmed) {
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
        jQuery(document).ready(function() {
            jQuery("#toggle-subnavbar").click(function() {
                var cmd = jQuery("#toggle-subnavbar span").html();
                if (cmd == 'Hide') {
                    jQuery('#subnavbar').slideUp('slow');
                    jQuery("#toggle-subnavbar span").html('Show');
                } else {
                    jQuery('#subnavbar').slideDown('slow');
                    jQuery("#toggle-subnavbar span").html('Hide');
                }

            });
            jQuery("#toggle-fullscreen").click(function() {
                $.cresenity.fullscreen(document.documentElement);
            });
        });

    }

    initClock() {
        if (!!this.cf.getConfig().haveClock) {
            $(document).ready(function() {
                $('#servertime').serverTime({
                    ajaxFile: window.capp.base_url + 'cresenity/server_time',
                    displayDateFormat: "yyyy-mm-dd HH:MM:ss"
                });
            });
        }
    }
    init() {
        this.cf.onBeforeInit(() => {
            this.normalizeRequireJs();
        });
        this.cf.onAfterInit(() => {
            if (!!this.cf.getConfig().haveScrollToTop) {
                if (!document.getElementById('topcontrol')) {
                    this.scrollToTop.init();
                }
            }
            this.initConfirm();

        });


        this.cf.init();


    }

    downloadProgress(options) {
        let settings = $.extend({
            // These are the defaults.
            method: 'get',
            dataAddition: {},
            url: '/',
            onComplete: false,
            onSuccess: false,
            onBlock: false,
            onUnblock: false,
        }, options);


        var method = settings.method;

        var xhr = jQuery(window).data('cappXhrProgress');
        if (xhr) {
            xhr.abort();
        }

        var dataAddition = settings.dataAddition;
        var url = settings.url;
        url = this.url.replaceParam(url);
        if (typeof dataAddition == 'undefined') {
            dataAddition = {};
        }

        (function(settings) {

            (function(element) {
                if (typeof settings.onBlock == 'function') {
                    settings.onBlock();
                } else {
                    cresenity.blockPage();
                }

                $(element).data('xhr', $.ajax({
                    type: method,
                    url: url,
                    dataType: 'json',
                    data: dataAddition,
                    success: function(response) {

                        cresenity.handleJsonResponse(response, function(data) {
                            var progressUrl = data.progressUrl;
                            var progressContainer = $('<div>').addClass('progress-container');

                            var interval = setInterval(function() {
                                $.ajax({
                                    type: method,
                                    url: progressUrl,
                                    dataType: 'json',
                                    success: function(response) {
                                        cresenity.handleJsonResponse(response, function(data) {
                                            if (data.state == 'DONE') {
                                                progressContainer.find('.progress-container-status').empty();
                                                var innerStatus = $('<div>');

                                                var innerStatusLabel = $('<label>', { class: 'mb-3 d-block' }).append("Your file is ready");
                                                var linkDownload = $('<a>', { target: '_blank', href: data.fileUrl, class: 'btn btn-primary' }).append("Download");
                                                var linkClose = $('<a>', { href: 'javascript:;', class: 'btn btn-primary ml-3' }).append("Close");

                                                innerStatus.append(innerStatusLabel);
                                                innerStatus.append(linkDownload);
                                                innerStatus.append(linkClose);

                                                progressContainer.find('.progress-container-status').append(innerStatus);
                                                linkClose.click(function() {
                                                    cresenity.closeLastModal();
                                                })
                                                clearInterval(interval);
                                            }
                                        });
                                    }
                                });
                            }, 3000);

                            var innerStatus = $('<div>');
                            var innerStatusLabel = $('<label>', { class: 'mb-4' }).append("Please Wait...");
                            var innerStatusAnimation = $('<div>').append('<div class="sk-fading-circle sk-primary"><div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div></div>');
                            var innerStatusAction = $('<div>', { class: 'text-center my-3' });
                            var innerStatusCancelButton = $('<button>', { class: 'btn btn-primary' }).append('Cancel');
                            innerStatusAction.append(innerStatusCancelButton);
                            innerStatus.append(innerStatusLabel);
                            innerStatus.append(innerStatusAnimation);
                            innerStatus.append(innerStatusAction);
                            progressContainer.append($('<div>').addClass('progress-container-status').append(innerStatus));

                            innerStatusCancelButton.click(function() {
                                clearInterval(interval);
                                cresenity.closeLastModal();
                            });


                            cresenity.modal({
                                message: progressContainer,
                                modalClass: 'modal-download-progress'
                            })
                        });
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        if (thrownError != 'abort') {
                            cresenity.message('error', 'Error, please call administrator... (' + thrownError + ')');
                        }

                    },
                    complete: function() {
                        $(element).data('xhr', false);
                        if (typeof settings.onBlock == 'function') {
                            settings.onUnblock();
                        } else {
                            cresenity.unblockPage();
                        }

                        if (typeof settings.onComplete == 'function') {
                            settings.onComplete();
                        }
                    }
                }));
            })(this);

        })(settings);

    };
}