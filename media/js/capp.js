/* eslint-disable no-underscore-dangle */
/* eslint-disable camelcase */
if (!Object.assign) {
    Object.assign = function assign(target, source) {
        let result = {};
        for (let i in target) {result[i] = target[i];}
        for (let i in source) {result[i] = source[i];}
        return result;
    };
}

// eslint-disable-next-line no-extend-native
String.prototype.contains = function (a) {
    return !!~this.indexOf(a);
};
// eslint-disable-next-line no-extend-native
String.prototype.toNumber = function () {
    let n = parseFloat(this);
    if (!isNaN(n)) {
        return n;
    }
    return 0;
};

let CF = function () {
    this.required = typeof this.required === 'undefined' ? [] : this.required;
    this.cssRequired = typeof this.cssRequired === 'undefined' ? [] : this.cssRequired;

    this.window = window;
    this.document = window.document;
    this.head = this.document.getElementsByTagName('head')[0];
    this.beforeInitCallback = [];
    this.afterInitCallback = [];

    this.onBeforeInit = function (callback) {
        this.beforeInitCallback.push(callback);
        return this;
    };
    this.onAfterInit = function (callback) {
        this.afterInitCallback.push(callback);
        return this;
    };


    this.getConfig = function () {
        return this.window.capp;
    };


    this.isUseRequireJs = function () {
        return this.getConfig().requireJs;
    };
    this.CFVersion = function () {
        return this.getConfig().CFVersion;
    };

    this.requireCss = function (url, callback) {
        if (!~this.cssRequired.indexOf(url)) {
            this.cssRequired.push(url);
            if (document.querySelector('link[href="' + url + '"],script[src="' + url + '"]') !== null) {
                return;
            }
            let string = '<link rel=\'stylesheet\' type=\'text/css\' href=\'' + url + '\' />';
            if ((document.readyState === 'loading'/* || mwd.readyState === 'interactive'*/) && !!window.CanvasRenderingContext2D && self === parent) {
                document.write(string);
            } else {
                let el;
                el = this.document.createElement('link');
                el.rel = 'stylesheet';
                el.type = 'text/css';
                el.href = url;
                // IE 6 & 7
                if (typeof (callback) === 'function') {
                    el.onload = callback;
                    el.onreadystatechange = function () {
                        if (this.readyState == 'complete') {
                            callback();
                        }
                    };
                }
                this.head.appendChild(el);
            }
        } else if (typeof (callback) === 'function') {
            callback();
        }
    };
    this.requireJs = function (url, callback) {
        if (!~this.required.indexOf(url)) {
            this.required.push(url);
            if (document.querySelector('link[href="' + url + '"],script[src="' + url + '"]') !== null) {
                return;
            }
            let string = '<script type=\'text/javascript\'  src=\'' + url + '\'></script>';
            if ((document.readyState === 'loading'/* || mwd.readyState === 'interactive'*/) && !!window.CanvasRenderingContext2D && self === parent) {
                document.write(string);
            } else {
                let el;
                el = this.document.createElement('script');
                el.src = url;
                el.setAttribute('type', 'text/javascript');
                // IE 6 & 7
                if (typeof (callback) === 'function') {
                    el.onload = callback;
                    el.onreadystatechange = function () {
                        if (this.readyState == 'complete') {
                            callback();
                        }
                    };
                }
                this.head.appendChild(el);
            }
        } else if (typeof (callback) === 'function') {
            callback();
        }
    };
    this.require = function (url, callback) {
        if (typeof url != 'string') {
            url = url[0];
        }

        if (!url) {
            return;
        }

        let toPush = url.trim();
        let t = 'js';

        let urlObject = new URL(toPush, document.baseURI);
        if(urlObject) {
            t = urlObject.pathname.split('.').pop();
        }

        if(t=='js') {
            this.requireJs(toPush, callback);
        }
        if(t=='css') {
            this.requireCss(toPush, callback);
        }
    };

    this.loadJQuery = function (callback) {
        if (typeof jQuery == 'undefined') {
            let fileref = this.document.createElement('script');
            fileref.setAttribute('type', 'text/javascript');
            fileref.setAttribute('src', this.getConfig().defaultJQueryUrl);
            // IE 6 & 7
            if (typeof (callback) === 'function') {
                fileref.onload = callback;
                fileref.onreadystatechange = function () {
                    if (this.readyState == 'complete') {
                        callback();
                    }
                };
            }
            this.head.appendChild(fileref);
        } else {
            callback();
        }
    };

    this.init = function () {
        let arrayJsUrl = this.getConfig().jsUrl;
        let arrayCssUrl = this.getConfig().cssUrl;
        this.beforeInitCallback.forEach(function (item) {
            item();
        });

        this.loadJQuery(() => {
            if (typeof arrayJsUrl !== 'undefined') {
                //todo add required for script already written in <script
                arrayJsUrl.forEach((item) => {
                    //console.log(item);
                    //if (document.querySelector('script[src="' + item + '"]') !== null) {
                    //document.querySelector('script[src="/media/js/plugins/form/jquery.form.js"]')
                    this.required.push(item);
                    //}
                });
            }
            if (typeof arrayCssUrl !== 'undefined') {
                arrayCssUrl.forEach((item) => {
                    //console.log(item);
                    //if (document.querySelector('script[src="' + item + '"]') !== null) {
                    //document.querySelector('script[src="/media/js/plugins/form/jquery.form.js"]')
                    this.cssRequired.push(item);
                    //}
                });
            }
        });

        this.afterInitCallback.forEach(function (item) {
            item();
        });
    };
};


let CUploader = function (options) {
    this.settings = $.extend({
        // These are the defaults.
        imgElement: null,
        uploadUrl: null,
        inputElement: null,
        accept: null,
        pageType: null,
        onUploadSuccess: null
    }, options);

    this.filename = '';
    this.uploaded = false;
    this.url = '';
    this.mimeType = '';
    this.size = 0;
    this.fileObject = null;
    this.isUploading = false;
    this.objectType = 'file';


    this.imgElement = this.settings.imgElement;
    this.previewElement = this.settings.previewElement;
    this.inputElement = this.settings.inputElement;
    this.uploadUrl = this.settings.uploadUrl;
    if (this.imgElement) {
        this.objectType = 'image';
    }

    this.trigger = function () {
        (function (cUploader) {
            let inputTemp = $('.capp-input-file-temporary');
            if (inputTemp.length == 0) {
                inputTemp = $('<input />');
                inputTemp.addClass('capp-input-file-temporary');
                inputTemp.attr('type', 'file').attr('name', 'capp-input-file-temporary').css('display', 'none');
                if (typeof this.settings.accept != 'undefined' && this.settings.accept != null) {
                    inputTemp.attr('accept', this.settings.accept);
                }
                $('body').append(inputTemp);
                inputTemp.change(function (e) {
                    $.each(e.target.files, function (i, file) {
                        cUploader.setFileObject(file);
                        cUploader.uploadFile();
                    });
                    inputTemp.remove();
                });
            }
            inputTemp.trigger('click');
        }(this));
        return false;
    };


    this.uploadFile = function () {
        (function (cUploader) {
            cUploader.isUploading = true;
            cUploader.showLoading();
            let ajaxData = new FormData();
            ajaxData.append('files[]', cUploader.fileObject);

            //console.log('try to upload ' + cUploader.fileObject.name);

            $.ajax({
                url: cUploader.settings.uploadUrl,
                type: 'post',
                data: ajaxData,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                xhr: function () {
                    let myXhr = $.ajaxSettings.xhr();
                    if (myXhr.upload) {
                        myXhr.upload.addEventListener('progress', function (e) {
                            let total = e.total;
                            let loaded = e.loaded;
                            let percent = loaded * 100 / total;
                            if (cUploader.getProgressBar()) {
                                cUploader.getProgressBar().css('width', percent + '%');
                                cUploader.getProgressBarInfo().html(cUploader.getSizeFormatted(loaded) + ' / ' + cUploader.getSizeFormatted(total));
                            }
                        }, false);
                    }
                    return myXhr;
                },
                complete: function () {
                    cUploader.isUploading = false;
                    cUploader.hideLoading();
                },
                success: function (response) {
                    if (response.errCode == 0) {
                        let responseData = response.data;
                        if (responseData.files) {
                            for (let i in responseData.files) {
                                let data = responseData.files[i];

                                cUploader.setUrl(data.url);
                                cUploader.setInput(data.fileId);
                                if (typeof cUploader.settings.onUploadSuccess == 'function') {
                                    cUploader.settings.onUploadSuccess(data);
                                }
                            }
                        }
                    } else {
                        window.cresenity.showError(response.errMessage);
                    }
                },
                error: function () {
                    // Log the error, show an alert, whatever works for you
                }
            });
        }(this));
    };


    this.setFileObject = function (fileObject) {
        this.fileObject = fileObject;
        this.setMimeType(fileObject.type);
        this.setFilename(fileObject.name);
        this.setSize(fileObject.size);
    };
    this.setInput = function (fileId) {
        if (this.inputElement) {
            this.inputElement.val(fileId);
        }
    };
    this.setFilename = function (filename) {
        this.filename = filename;
    };
    this.getFilename = function () {
        return this.filename;
    };
    this.setSize = function (size) {
        this.size = size;
    };
    this.getSize = function () {
        return this.size;
    };
    this.getSizeFormatted = function (size) {
        if (typeof size == 'undefined') {
            size = this.size;
        }
        let sizeStr = '';
        let sizeKB = size / 1024;
        if (parseInt(sizeKB) > 1024) {
            let sizeMB = sizeKB / 1024;
            if (parseInt(sizeMB) > 1024) {
                let sizeGB = sizeMB / 1024;
                sizeStr = sizeGB.toFixed(2) + ' GB';
            } else {
                sizeStr = sizeMB.toFixed(2) + ' MB';
            }
        } else {
            sizeStr = sizeKB.toFixed(2) + ' KB';
        }
        return sizeStr;
    };

    this.setMimeType = function (mimeType) {
        this.mimeType = mimeType;
    };
    this.getMimeType = function () {
        return this.mimeType;
    };

    this.setUrl = function (url) {
        this.url = url;
        this.uploaded = true;
        switch (this.getMimeType()) {
            case 'image/png':
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/gif':
                this.objectType = 'image';
                break;
            case 'video/mp4':
            case 'video/avi':
                this.objectType = 'video';
                break;
        }

        if (this.objectType == 'image' && this.imgElement) {
            this.imgElement.attr('src', url);
        }

        if (this.objectType == 'image' && this.previewElement) {
            let imgPreview = $('<img>', { class: 'img-fluid w-100 media-preview', src: url });
            this.previewElement.prepend(imgPreview);
        }
        if (this.objectType == 'video' && this.previewElement) {
            let videoPreviewControl = $('<video>', { controls: '', width: '100%', height: '100%', class: 'w-100 media-preview' });
            let videoSource = $('<source>', { src: url });
            videoPreviewControl.append(videoSource);
            this.previewElement.prepend(videoPreviewControl);
        }
        if (this.objectType == 'file' && this.previewElement) {
            let filePreview = $('<i>', { class: 'media-preview far fa-file fa-10x text-center' });
            this.previewElement.prepend(filePreview);
        }
    };

    this.getUrl = function () {
        return this.url;
    };

    this.showLoading = () => {
        $.blockUI({
            message: '<div class="dc-progress-upload-container" style="max-width:400px"><div class="progress" style="height: 6px;"><div class="progress-bar" style="width: 0%;"></div></div><div class="text-center mt-3 progress-bar-info"></div><h6 style="color: #444">LOADING...</h6></div>',
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
    this.hideLoading = function () {
        $.unblockUI();
    };
    this.getProgressBar = function () {
        return $('.dc-progress-upload-container .progress-bar');
    };

    this.getProgressBarInfo = function () {
        return $('.dc-progress-upload-container .progress-bar-info');
    };
};

let CBlocker = function () {

};


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

    let str = string + '';
    let i = 0, lgth = 0;

    if (!this.php_js || !this.php_js.ini || !this.php_js.ini['unicode.semantics'] || this.php_js.ini['unicode.semantics'].local_value.toLowerCase() !== 'on') {
        return string.length;
    }

    let getWholeChar = function (str, i) {
        let code = str.charCodeAt(i);
        let next = '';
        let prev = '';
        if (code >= 0xD800 && code <= 0xDBFF) {
            // High surrogate (could change last hex to 0xDB7F to treat high private surrogates as single characters)
            if (str.length <= (i + 1)) {
                throw 'High surrogate without following low surrogate';
            }
            next = str.charCodeAt(i + 1);
            if (next < 0xDC00 || next > 0xDFFF) {
                throw 'High surrogate without following low surrogate';
            }
            return str.charAt(i) + str.charAt(i + 1);
        } else if (code >= 0xDC00 && code <= 0xDFFF) {
            // Low surrogate
            if (i === 0) {
                throw 'Low surrogate without preceding high surrogate';
            }
            prev = str.charCodeAt(i - 1);
            if (prev < 0xD800 || prev > 0xDBFF) {
                // (could change last hex to 0xDB7F to treat high private surrogates as single characters)
                throw 'Low surrogate without preceding high surrogate';
            }
            // We can pass over low surrogates now as the second component in a pair which we have already processed
            return false;
        }
        return str.charAt(i);
    };

    for (i = 0, lgth = 0; i < str.length; i++) {
        if ((getWholeChar(str, i)) === false) {
            continue;
        }
        // Adapt this line at the top of any loop, passing in the whole string and the current iteration and returning a variable to represent the individual character; purpose is to treat the first part of a surrogate pair as the whole character and then ignore the second part
        lgth++;
    }
    return lgth;
}
let Cresenity = function () {
    let ScrollToTop = function () {
        //startline: Integer. Number of pixels from top of doc scrollbar is scrolled before showing control
        //scrollto: Keyword (Integer, or "Scroll_to_Element_ID"). How far to scroll document up when control is clicked on (0=top).
        this.setting = {
            startline: 100,
            scrollto: 0,
            scrollduration: 1000,
            fadeduration: [500, 100]
        };
        this.controlHTML = '<img src="' + window.capp.base_url + 'media/img/up.png" style="width:51px; height:42px" />'; //HTML for control, which is auto wrapped in DIV w/ ID="topcontrol"
        this.controlattrs = {
            offsetx: 5,
            offsety: 5
        }; //offset of control relative to right/ bottom of window corner
        this.anchorkeyword = '#top'; //Enter href value of HTML anchors on the page that should also act as "Scroll Up" links

        this.state = {
            isvisible: false,
            shouldvisible: false
        };
        this.scrollup = function () {
            if (!this.cssfixedsupport) {
                //if control is positioned using JavaScript
                this.$control.css({
                    opacity: 0,
                    zIndex: -1
                });
            } //hide control immediately after clicking it
            let dest = isNaN(this.setting.scrollto) ? this.setting.scrollto : parseInt(this.setting.scrollto);
            if (typeof dest == 'string' && jQuery('#' + dest).length == 1) {
                //check element set by string exists
                dest = jQuery('#' + dest).offset().top;
            } else {
                dest = 0;
            }

            this.$body.animate({
                scrollTop: dest
            }, this.setting.scrollduration);
        };
        this.keepfixed = function () {
            let $window = jQuery(window);
            let controlx = $window.scrollLeft() + $window.width() - this.$control.width() - this.controlattrs.offsetx;
            let controly = $window.scrollTop() + $window.height() - this.$control.height() - this.controlattrs.offsety;
            this.$control.css({
                left: controlx + 'px',
                top: controly + 'px'
            });
        };
        this.togglecontrol = function () {
            let scrolltop = jQuery(window).scrollTop();
            if (!this.cssfixedsupport) {
                this.keepfixed();
            }
            this.state.shouldvisible = (scrolltop >= this.setting.startline) ? true : false;
            if (this.state.shouldvisible && !this.state.isvisible) {
                this.$control.stop().animate({
                    opacity: 1,
                    zIndex: 99999
                }, this.setting.fadeduration[0]);
                this.state.isvisible = true;
            } else if (this.state.shouldvisible == false && this.state.isvisible) {
                this.$control.stop().animate({
                    opacity: 0,
                    zIndex: -1
                }, this.setting.fadeduration[1]);
                this.state.isvisible = false;
            }
        };
        this.init = function () {
            jQuery(document).ready(($) => {
                let mainobj = this;
                let iebrws = document.all;
                mainobj.cssfixedsupport = !iebrws || iebrws && document.compatMode == 'CSS1Compat' && window.XMLHttpRequest;
                //not IE or IE7+ browsers in standards mode
                mainobj.$body = (window.opera) ? (document.compatMode == 'CSS1Compat' ? $('html') : $('body')) : $('html,body');
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
                        return false;
                    })
                    .appendTo('body');
                if (document.all && !window.XMLHttpRequest && mainobj.$control.text() != '') { //loose check for IE6 and below, plus whether control contains any text
                    mainobj.$control.css({
                        width: mainobj.$control.width()
                    }); //IE6- seems to require an explicit width on a DIV containing text
                }
                mainobj.togglecontrol();
                $('a[href="' + mainobj.anchorkeyword + '"]').click(function () {
                    mainobj.scrollup();
                    return false;
                });
                $(window).bind('scroll resize', function () {
                    mainobj.togglecontrol();
                });
            });
        };
    };

    let Base64 = function (cresenity) {
        this.cresenity = cresenity;
        this._keyStr = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
        this._utf8_encode = function (e) {
            e = e.replace(/rn/g, 'n');
            let t = '';
            for (let n = 0; n < e.length; n++) {
                let r = e.charCodeAt(n);
                if (r < 128) {
                    t = t + String.fromCharCode(r);
                } else if (r > 127 && r < 2048) {
                    t = t + String.fromCharCode(r >> 6 | 192);
                    t = t + String.fromCharCode(r & 63 | 128);
                } else {
                    t = t + String.fromCharCode(r >> 12 | 224);
                    t = t + String.fromCharCode(r >> 6 & 63 | 128);
                    t = t + String.fromCharCode(r & 63 | 128);
                }
            }
            return t;
        };
        this._utf8_decode = function (e) {
            let t = '';
            let n = 0;
            let r = 0;
            let c3 = 0;
            let c2 = 0;
            while (n < e.length) {
                r = e.charCodeAt(n);
                if (r < 128) {
                    t = t + String.fromCharCode(r);
                    n++;
                } else if (r > 191 && r < 224) {
                    c2 = e.charCodeAt(n + 1);
                    t = t + String.fromCharCode((r & 31) << 6 | c2 & 63);
                    n = n + 2;
                } else {
                    c2 = e.charCodeAt(n + 1);
                    c3 = e.charCodeAt(n + 2);
                    t = t + String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
                    n = n + 3;
                }
            }
            return t;
        };
        this.encode = function (e) {
            let t = '';
            let n, r, i, s, o, u, a;
            let f = 0;
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
                    u = a = 64;
                } else if (isNaN(i)) {
                    a = 64;
                }
                t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a);
            }
            return t;
        };


        this.decode = function (e) {
            let t = '';
            let n, r, i;
            let s, o, u, a;
            let f = 0;
            e = e.replace(/[^A-Za-z0-9+/=]/g, '');
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
                    t = t + String.fromCharCode(r);
                }
                if (a != 64) {
                    t = t + String.fromCharCode(i);
                }
            }
            t = this._utf8_decode(t);
            return t;
        };
    };

    let Url = function (cresenity) {
        this.cresenity = cresenity;
        this.addQueryString = function (url, key, value) {
            key = encodeURI(key);
            value = encodeURI(value);
            let urlArray = url.split('?');
            let queryString = '';
            let baseUrl = urlArray[0];
            if (urlArray.length > 1) {
                queryString = urlArray[1];
            }
            let kvp = queryString.split('&');
            let i = kvp.length;
            let x;
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
            if (queryString.substr(0, 1) == '&') {queryString = queryString.substr(1);}
            return baseUrl + '?' + queryString;
        };
        this.replaceParam = function (url) {
            let available = true;
            while (available) {
                let matches = url.match(/{([\w]*)}/);
                if (matches != null) {
                    let key = matches[1];
                    let val = null;
                    if ($('#' + key).length > 0) {
                        val = cresenity.value('#' + key);
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
        };
    };

    this.url = new Url(this);
    this.base64 = new Base64(this);

    this.cf = new CF();

    this.filesAdded = '';
    this.modalElements = [];
    this.callback = {};
    this.loadJs = function (filename, callback) {
        let fileref = document.createElement('script');
        fileref.setAttribute('type', 'text/javascript');
        fileref.setAttribute('src', filename);
        // IE 6 & 7
        if (typeof (callback) === 'function') {
            fileref.onload = callback;
            fileref.onreadystatechange = function () {
                if (this.readyState == 'complete') {
                    callback();
                }
            };
        }
        document.getElementsByTagName('head')[0].appendChild(fileref);
    };
    this.haveCallback = (name) => {
        return typeof this.callback[name] == 'function';
    };
    this.doCallback = (name, ...args) => {
        if (this.haveCallback(name)) {
            this.callback[name](...args);
        }
    };

    this.setCallback = (name, cb) => {
        this.callback[name] = cb;
    };

    this.isUsingRequireJs = function () {
        return (typeof window.capp.requireJs !== 'undefined') ? window.capp.requireJs : true;
    };
    this.normalizeRequireJs = function () {
        if (!this.isUsingRequireJs()) {
            if (typeof window.define === 'function' && window.define.amd) {
                window.define = undefined;
            }
        }
    };
    this.isJson = function (text) {
        if (typeof text == 'string') {
            return (/^[\],:{}\s]*$/.test(text.replace(/\\["\\\/bfnrtu]/g, '@').replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').replace(/(?:^|:|,)(?:\s*\[)+/g, '')));
        }
        return false;
    };

    this.removeJsCss = function (filename, filetype) {
        let targetelement = (filetype == 'js') ? 'script' : (filetype == 'css') ? 'link' : 'none'; //determine element type to create nodelist from
        let targetattr = (filetype == 'js') ? 'src' : (filetype == 'css') ? 'href' : 'none'; //determine corresponding attribute to test for
        let allsuspects = document.getElementsByTagName(targetelement);
        for (let i = allsuspects.length; i >= 0; i--) { //search backwards within nodelist for matching elements to remove
            if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1) {
                allsuspects[i].parentNode.removeChild(allsuspects[i]); //remove element by calling parentNode.removeChild()
            }
        }
    };


    this.handleResponse = function (data, callback) {
        if (data.css_require && data.css_require.length > 0) {
            for (let i = 0; i < data.css_require.length; i++) {
                window.cresenity.cf.require(data.css_require[i], 'css');
            }
        }
        callback();
    };

    this.handleJsonResponse = function (response, onSuccess, onError) {
        let errMessage = 'Unexpected error happen, please relogin ro refresh this page';
        if (typeof onError == 'string') {
            errMessage = onError;
        }

        if (response.errCode == 0) {
            if (typeof onSuccess == 'function') {
                onSuccess(response.data);
            }
        } else {
            if (typeof response.errMessage != 'undefined') {
                errMessage = response.errMessage;
            }
            if (typeof onError == 'function') {
                onError(errMessage);
            } else {
                window.cresenity.showError(errMessage);
            }
        }
    };

    this.showError = function (message) {
        if(window.toastr) {
            return window.toastr.error(message, 'Error', {
                positionClass: 'toast-top-right',
                closeButton: true,
                progressBar: true,
                preventDuplicates: false,
                newestOnTop: false

            });
        }
        return window.cresenity.message('error', message);
    };
    this.require = function (filename, filetype, callback) {
        if (window.cresenity.filesAdded.indexOf('[' + filename + ']') == -1) {
            window.cresenity.loadJsCss(filename, filetype, callback);
            window.cresenity.filesAdded = window.cresenity.filesAdded + ('[' + filename + ']'); //List of files added in the form "[filename1],[filename2],etc"
        } else {
            window.cresenity.filesLoaded++;

            if (window.cresenity.filesLoaded == window.cresenity.filesNeeded) {
                callback();
            }
        }
    };
    this.reload = function (options) {
        let targetOptions = {};
        if(options && options.selector) {
            let target = $(options.selector);
            if(target.attr('data-url')) {
                targetOptions.url = target.attr('data-url');
            }
            if(target.attr('data-method')) {
                targetOptions.method = target.attr('data-method');
            }
            if(target.attr('data-block-html')) {
                targetOptions.blockHtml = target.attr('data-block-html');
            }
            if(target.attr('data-block-type')) {
                targetOptions.blockType = target.attr('data-block-type');
            }
            if(target.attr('data-data-addition')) {
                targetOptions.dataAddition = JSON.parse(target.attr('data-data-addition'));
            }
        }

        let settings = $.extend({
            // These are the defaults.
            method: 'get',
            dataAddition: {},
            url: '/',
            reloadType: 'reload',
            onComplete: false,
            onSuccess: false,
            onBlock: false,
            blockHtml: false,
            blockType: 'default',
            onUnblock: false
        }, targetOptions, options);


        let method = settings.method;
        let selector = settings.selector;


        let blockOptions = {
            blockType: settings.blockType
        };
        if(settings.blockHtml) {
            blockOptions.innerMessage=settings.blockHtml;
        }
        let xhr = jQuery(selector).data('xhr');
        if (xhr) {
            xhr.abort();
        }
        let dataAddition = settings.dataAddition;
        let url = settings.url;
        if(url) {
            url = this.url.replaceParam(url);
        }
        if (typeof dataAddition == 'undefined') {
            dataAddition = {};
        }


        $(selector).each((index, element) => {
            let idTarget = $(element).attr('id');
            url = window.cresenity.url.addQueryString(url, 'capp_current_container_id', idTarget);


            if (typeof settings.onBlock == 'function') {
                settings.onBlock($(element));
            } else {
                window.cresenity.blockElement($(element), blockOptions);
            }

            $(element).data('xhr', $.ajax({
                type: method,
                url: url,
                dataType: 'json',
                data: dataAddition,
                success: function (data) {
                    window.cresenity.doCallback('onReloadSuccess', data);
                    window.cresenity.handleResponse(data, function () {
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
                            let script = window.cresenity.base64.decode(data.js);
                            eval(script);
                        }


                        if ($(element).find('.prettyprint').length > 0) {
                            window.prettyPrint && window.prettyPrint();
                        }
                        if (typeof settings.onSuccess == 'function') {
                            settings.onSuccess(data);
                        }
                    });
                },
                error: (xhr, ajaxOptions, thrownError) => {
                    if (thrownError != 'abort') {
                        window.cresenity.message('error', 'Error, please call administrator... (' + thrownError + ')');
                    }
                },
                complete: function () {
                    $(element).data('xhr', false);
                    if (typeof settings.onBlock == 'function') {
                        settings.onUnblock($(element));
                    } else {
                        window.cresenity.unblockElement($(element));
                    }

                    if (typeof settings.onComplete == 'function') {
                        settings.onComplete();
                    }
                }
            }));
        });
    };
    this.append = function (options) {
        options.reloadType = 'append';
        this.reload(options);
    };
    this.prepend = function (options) {
        options.reloadType = 'prepend';
        this.reload(options);
    };
    this.after = function (options) {
        options.reloadType = 'after';
        this.reload(options);
    };
    this.before = function (options) {
        options.reloadType = 'before';
        this.reload(options);
    };
    this.confirm = function (options) {
        let settings = $.extend({
            // These are the defaults.
            method: 'get',
            dataAddition: {},
            message: 'Are you sure?',
            onConfirmed: false
        }, options);
        window.bootbox.confirm(settings.message, settings.onConfirmed);
    };
    this.modal = function (options) {
        let settings = $.extend({
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
        let modalDialog = jQuery('<div>').addClass('modal-dialog modal-xl');
        let modalContent = jQuery('<div>').addClass('modal-content');

        let modalHeader = jQuery('<div>').addClass('modal-header');
        let modalTitle = jQuery('<div>').addClass('modal-title');
        let modalButtonClose = jQuery('<button type="button" class="close" aria-label="Close"><span aria-hidden="true">&times;</span></button>');
        modalButtonClose.click(() => {
            modalButtonClose.closest('.modal').modal('hide');
        });
        let modalBody = jQuery('<div>').addClass('modal-body');
        let modalFooter = jQuery('<div>').addClass('modal-footer');
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

        let appendTo = settings.appendTo;
        if (typeof appendTo == 'undefined' || !appendTo) {
            appendTo = $('body');
        }
        modalContainer.appendTo(appendTo);
        modalContainer.addClass('capp-modal');
        modalContainer.on('hidden.bs.modal', function (e) {
            if (window.cresenity.modalElements.length > 0) {
                let lastModal = window.cresenity.modalElements[window.cresenity.modalElements.length - 1];
                if (lastModal && lastModal.get(0) === $(e.target).get(0)) {
                    (function (modal) {
                        let Next = function () {
                            this.isRunning = false;
                            this.callback = (delay) => {
                                if (typeof delay == 'undefined') {
                                    delay = 0;
                                }
                                if (isNaN(parseInt(delay))) {
                                    delay = 0;
                                }

                                setTimeout(function () {
                                    $(modal).remove();
                                    window.cresenity.modalElements.pop();


                                    let modalExists = $('.modal:visible').length > 0;
                                    if (!modalExists) {
                                        $('body').removeClass('modal-open');
                                    } else if (!$('body').hasClass('modal-open')) {
                                        $('body').addClass('modal-open');
                                    }
                                }, delay);
                                this.isRunning = true;
                            };
                        };
                        let next = new Next();
                        if (typeof settings.onClose == 'function') {
                            settings.onClose(e, next.callback);
                        }
                        if (!next.isRunning) {
                            next.callback();
                        }
                    }(lastModal));
                }
            }
        });

        modalContainer.on('shown.bs.modal', function () {
            window.cresenity.modalElements.push($(this));
        });

        if (settings.message) {
            modalBody.append(settings.message);
        }
        if (settings.reload) {
            let reloadOptions = settings.reload;
            reloadOptions.selector = modalBody;
            window.cresenity.reload(reloadOptions);
        }

        modalContainer.modal({
            backdrop: settings.backdrop
        });

        return modalContainer;
    };
    this.closeLastModal = function () {
        if (window.cresenity.modalElements.length > 0) {
            let lastModal = window.cresenity.modalElements[window.cresenity.modalElements.length - 1];

            lastModal.modal('hide');
        }
    };
    this.closeDialog = function () {
        this.closeLastModal();
    };
    this.ajax = function (options) {
        let settings = $.extend({
            block: true,
            url: window.location.href,
            method: 'post'
        }, options);
        let dataAddition = settings.dataAddition;
        let url = settings.url;
        url = this.url.replaceParam(url);
        if (typeof dataAddition == 'undefined') {
            dataAddition = {};
        }
        if (settings.block) {
            window.cresenity.blockPage();
        }

        let validationIsValid = true;
        let ajaxOptions = {
            url: url,
            dataType: 'json',
            data: dataAddition,
            type: settings.method,

            success: function (response) {
                let onSuccess = function () { };
                let onError = function (errMessage) {
                    window.cresenity.showError(errMessage);
                };
                if (typeof settings.onSuccess == 'function' && validationIsValid) {
                    onSuccess = settings.onSuccess;
                }
                if (typeof settings.onError == 'function' && validationIsValid) {
                    onError = settings.onError;
                }

                if (validationIsValid) {
                    if (settings.handleJsonResponse == true) {
                        window.cresenity.handleJsonResponse(response, onSuccess, onError);
                    } else {
                        onSuccess(response);
                    }
                }
            },
            error: function (xhr, ajaxOptions, thrownError) {
                if (thrownError != 'abort') {
                    //console.log(thrownError);
                    window.cresenity.showError(thrownError);
                }
            },

            complete: function () {
                if (settings.block) {
                    window.cresenity.unblockPage();
                }

                if (typeof settings.onComplete == 'function' && validationIsValid) {
                    settings.onComplete();
                }
            }
        };

        return $.ajax(ajaxOptions);
    };
    this.ajaxSubmit = function (options) {
        let settings = $.extend({}, options);
        let selector = settings.selector;
        $(selector).each(function () {
            //don't do it again if still loading
            let formAjaxUrl = $(this).attr('action') || '';
            let formMethod = $(this).attr('method') || 'get';
            (function (element) {
                window.cresenity.blockElement($(element));
                let validationIsValid = true;
                let ajaxOptions = {
                    url: formAjaxUrl,
                    dataType: 'json',
                    type: formMethod,
                    beforeSubmit: function () {
                        if (typeof $(element).validate == 'function') {
                            validationIsValid = $(element).validate().form();
                            return validationIsValid;
                        }
                        return true;
                    },
                    success: function (response) {
                        let onSuccess = () => { };
                        let onError = function (errMessage) {
                            window.cresenity.showError(errMessage);
                        };

                        let haveOnSuccess = false;
                        if (typeof settings.onSuccess == 'function' && validationIsValid) {
                            onSuccess = settings.onSuccess;
                            haveOnSuccess = true;
                        }
                        if (typeof settings.onError == 'function' && validationIsValid) {
                            onError = settings.onError;
                        }

                        if (validationIsValid) {
                            if (settings.handleJsonResponse == true && haveOnSuccess) {
                                window.cresenity.handleJsonResponse(response, onSuccess, onError);
                            } else {
                                onSuccess(response);
                            }
                        }
                    },

                    complete: function () {
                        window.cresenity.unblockElement($(element));

                        if (typeof settings.onComplete == 'function' && validationIsValid) {
                            settings.onComplete();
                        }
                    }
                };
                $(element).ajaxSubmit(ajaxOptions);
            }(this));
        });
        //always return false to prevent submit
        return false;
    };
    this.message = function (type, message, alertType, callback) {
        alertType = typeof alertType !== 'undefined' ? alertType : 'notify';
        let container = $('#container');
        if (container.length == 0) {
            container = $('body');
        }
        if (alertType == 'bootbox') {
            if (typeof callback == 'undefined') {
                window.bootbox.alert(message);
            } else {
                window.bootbox.alert(message, callback);
            }
        }

        if (alertType == 'notify') {
            let obj = $('<div>');
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

    this.scrollTo = function (element, container) {
        if (typeof container == 'undefined') {
            container = document.body;
        }
        $(container).animate({
            scrollTop: $(element).offset().top - ($(container).offset().top + $(container).scrollTop())
        });
    };

    this.formatCurrency = function (rp) {
        rp = '' + rp;
        let rupiah = '';
        let vfloat = '';
        let ds = window.capp.decimal_separator;
        let ts = window.capp.thousand_separator;
        let dd = window.capp.decimal_digit;
        dd = parseInt(dd);
        let minus_str = '';
        if (rp.indexOf('-') >= 0) {
            minus_str = rp.substring(rp.indexOf('-'), 1);
            rp = rp.substring(rp.indexOf('-') + 1);
        }

        if (rp.indexOf('.') >= 0) {
            vfloat = rp.substring(rp.indexOf('.'));
            rp = rp.substring(0, rp.indexOf('.'));
        }
        let p = rp.length;
        while (p > 3) {
            rupiah = ts + rp.substring(p - 3) + rupiah;
            let l = rp.length - 3;
            rp = rp.substring(0, l);
            p = rp.length;
        }
        rupiah = rp + rupiah;
        vfloat = vfloat.replace('.', ds);
        if (vfloat.length > dd) {vfloat = vfloat.substring(0, dd + 1);}
        return minus_str + rupiah + vfloat;
    };

    this.getStyles = (selector, only, except) => {
        // the map to return with requested styles and values as KVP
        let product = {};

        // the style object from the DOM element we need to iterate through
        let style;

        // recycle the name of the style attribute
        let name;

        let element = $(selector);

        // if it's a limited list, no need to run through the entire style object
        if (only && only instanceof Array) {
            for (let i = 0, l = only.length; i < l; i++) {
                // since we have the name already, just return via built-in .css method
                name = only[i];
                product[name] = element.css(name);
            }
        } else if (element.length) {
            // otherwise, we need to get everything
            let dom = element.get(0);

            // standards
            if (window.getComputedStyle) {
                // convenience methods to turn css case ('background-image') to camel ('backgroundImage')
                let pattern = /\-([a-z])/g;
                let uc = function (a, b) {
                    return b.toUpperCase();
                };
                let camelize = function (string) {
                    return string.replace(pattern, uc);
                };

                // make sure we're getting a good reference
                if (style = window.getComputedStyle(dom, null)) {
                    let camel, value;
                    // opera doesn't give back style.length - use truthy since a 0 length may as well be skipped anyways
                    if (style.length) {
                        for (let i = 0, l = style.length; i < l; i++) {
                            name = style[i];
                            camel = camelize(name);
                            value = style.getPropertyValue(name);
                            product[camel] = value;
                        }
                    } else {
                        // opera
                        for (name in style) {
                            camel = camelize(name);
                            value = style.getPropertyValue(name) || style[name];
                            product[camel] = value;
                        }
                    }
                }
            } else if (style = dom.currentStyle) {
                // IE - first try currentStyle, then normal style object - don't bother with runtimeStyle
                for (name in style) {
                    product[name] = style[name];
                }
            } else if (style = dom.style) {
                for (name in style) {
                    if (typeof style[name] != 'function') {
                        product[name] = style[name];
                    }
                }
            }
        }

        // remove any styles specified...
        // be careful on blacklist - sometimes vendor-specific values aren't obvious but will be visible...  e.g., excepting 'color' will still let '-webkit-text-fill-color' through, which will in fact color the text
        if (except && except instanceof Array) {
            for (let i = 0, l = except.length; i < l; i++) {
                name = except[i];
                delete product[name];
            }
        }

        // one way out so we can process blacklist in one spot
        return product;
    };

    this.createPlaceholderElement = (selector, root, depth) => {
        depth = parseInt(depth);
        if(!Number.isInteger(depth)) {
            depth = 0;
        }
        let element = $(selector);
        if(element.length==0) {
            return null;
        }

        root = root || element;
        let newElement = element.clone().empty();
        newElement.removeAttr('id');
        newElement.removeAttr('data-block-html');
        newElement.removeClass();


        if(!(element.is(':visible'))) {
            return null;
        }


        let styles = this.getStyles(element);


        if(depth>0) {
            //newElement.addClass('remove-after');
            //newElement.addClass('remove-before');
            if(element.children(':visible:not(:empty)').length==0) {
                let relativeY = element.offset().top - root.offset().top;
                let relativeX = element.offset().left - root.offset().left;
                styles.width=''+element.outerWidth()+'px';
                styles.height=''+ (element.outerHeight() - 8)+'px';
                styles.position='absolute';
                styles.top=''+(relativeY + 4)+'px';
                styles.left=''+relativeX+'px';
                styles.backgroundColor='#ced4da';
            }
        }

        styles.border = '0';
        styles.borderRadius = '0';
        styles.overflow = 'hidden';

        switch(element.prop('tagName').toLowerCase()) {
            case 'ul':
                //styles['listStyle']='none';
                styles.padding='0px';
                break;
            case 'li':
                styles.listStyle='none';
                //styles['color']='#ced4da';
                /*
                styles['height']='10px';
                styles['marginBottom']='7.5px';
                styles['backgroundColor']='#ced4da';
                    */
                break;
        }
        if(depth==0) {
            styles.position = 'relative';
        }
        newElement.css(styles);
        if(depth==0) {
            newElement.addClass('capp-ph-item');
            newElement.attr('style', function (i, s) { return (s || '') + 'margin: 0 !important;'; });
        }
        element.children().each((idx, item) => {
            let newChild = this.createPlaceholderElement(item, root, depth+1);
            if(newChild) {
                newElement.append(newChild);
            }
        });
        return newElement;
    };

    this.blockPage = function (options) {
        let settings = $.extend({
            innerMessage: '<div class="sk-folding-cube sk-primary"><div class="sk-cube1 sk-cube"></div><div class="sk-cube2 sk-cube"></div><div class="sk-cube4 sk-cube"></div><div class="sk-cube3 sk-cube"></div></div><h5 style="color: #444">LOADING...</h5>'
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

    this.unblockPage = function () {
        $.unblockUI();
    };
    this.blockElement = (selector, options) => {
        let settings = $.extend({
            innerMessage: '<div class="sk-wave sk-primary"><div class="sk-rect sk-rect1"></div> <div class="sk-rect sk-rect2"></div> <div class="sk-rect sk-rect3"></div> <div class="sk-rect sk-rect4"></div> <div class="sk-rect sk-rect5"></div></div>',
            blockType: 'default'
        }, options);

        let blockMessage = settings.innerMessage;
        let blockType = settings.blockType || 'default';
        let cssConfig = {
            backgroundColor: 'transparent',
            border: '0'
        };


        let overlayCssConfig = {
            backgroundColor: '#fff',
            opacity: 0.8
        };

        if(blockType=='shimmer') {
            blockMessage = this.createPlaceholderElement($(selector));
            cssConfig.width='100%';
            cssConfig.top='0px';
            overlayCssConfig.opacity = 1;
        }

        $(selector).block({
            message: blockMessage,
            css: cssConfig,
            overlayCSS: overlayCssConfig
        });
    };
    this.unblockElement = function (selector) {
        $(selector).unblock();
    };

    this.value = function (elm) {
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


    this.initConfirm = function () {
        let confirmInitialized = $('body').attr('data-confirm-initialized');
        if (!confirmInitialized) {
            jQuery(document).on('click', 'a.confirm, button.confirm', function (e) {
                let ahref = $(this).attr('href');
                let message = $(this).attr('data-confirm-message');
                let no_double = $(this).attr('data-no-double');
                let clicked = $(this).attr('data-clicked');


                let btn = jQuery(this);
                btn.attr('data-clicked', '1');
                if (no_double) {
                    if (clicked == 1) {return false;}
                }

                if (!message) {
                    message = window.capp.label_confirm;
                } else {
                    message = window.cresenity.base64.decode(message);
                }


                e.preventDefault();
                e.stopPropagation();
                btn.off('click');
                window.bootbox.confirm({
                    className: 'capp-modal-confirm',
                    message: message,
                    callback: function (confirmed) {
                        if (confirmed) {
                            if (ahref) {
                                window.location.href = ahref;
                            } else if (btn.attr('type') == 'submit') {
                                btn.closest('form').submit();
                            } else {
                                btn.on('click');
                            }
                        } else {
                            btn.removeAttr('data-clicked');
                        }
                        setTimeout(function () {
                            let modalExists = $('.modal:visible').length > 0;
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
        let confirmSubmitInitialized = $('body').attr('data-confirm-submit-initialized');
        if (!confirmSubmitInitialized) {
            jQuery(document).on('click', 'input[type=submit].confirm', function (e) {
                let submitted = $(this).attr('data-submitted');
                let btn = jQuery(this);
                if (submitted == '1') {return false;}
                btn.attr('data-submitted', '1');

                let message = $(this).attr('data-confirm-message');
                if (!message) {
                    message = window.capp.label_confirm;
                } else {
                    message = window.cresenity.base64.decode(message);
                }

                window.bootbox.confirm({
                    className: 'capp-modal-confirm',
                    message: message,
                    callback: function (confirmed) {
                        if (confirmed) {
                            jQuery(e.target).closest('form').submit();
                        } else {
                            btn.removeAttr('data-submitted');
                        }
                    }
                });


                return false;
            });
            $('body').attr('data-confirm-submit-initialized', '1');
        }
        jQuery(document).ready(function () {
            jQuery('#toggle-subnavbar').click(function () {
                let cmd = jQuery('#toggle-subnavbar span').html();
                if (cmd == 'Hide') {
                    jQuery('#subnavbar').slideUp('slow');
                    jQuery('#toggle-subnavbar span').html('Show');
                } else {
                    jQuery('#subnavbar').slideDown('slow');
                    jQuery('#toggle-subnavbar span').html('Hide');
                }
            });
            jQuery('#toggle-fullscreen').click(function () {
                window.cresenity.fullscreen(document.documentElement);
            });
        });
    };
    this.initReload = function () {
        let reloadInitialized = $('body').attr('data-reload-initialized');
        if (!reloadInitialized) {
            (function (cresenity) {
                $('.capp-reload').each((idx, item) => {
                    if(!$(item).hasClass('capp-reloaded')) {
                        let reloadOptions = {};
                        reloadOptions.selector = $(item);
                        cresenity.reload(reloadOptions);
                        $(item).addClass('capp-reloaded');
                    }
                });
                $('body').attr('data-reload-initialized', '1');
            }(this));
        }
    };
    this.init = function () {
        this.cf.onBeforeInit(() => {
            this.normalizeRequireJs();
        });
        this.cf.onAfterInit(() => {
            if (this.cf.getConfig().haveScrollToTop) {
                if (!document.getElementById('topcontrol')) {
                    (new ScrollToTop()).init();
                }
            }
            this.initConfirm();
            this.initReload();
        });


        this.cf.init();
    };

    this.downloadProgress = function (options) {
        let settings = $.extend({
            // These are the defaults.
            method: 'get',
            dataAddition: {},
            url: '/',
            onComplete: false,
            onSuccess: false,
            onBlock: false,
            onUnblock: false
        }, options);


        let method = settings.method;

        let xhr = jQuery(window).data('cappXhrProgress');
        if (xhr) {
            xhr.abort();
        }

        let dataAddition = settings.dataAddition;
        let url = settings.url;
        url = this.url.replaceParam(url);
        if (typeof dataAddition == 'undefined') {
            dataAddition = {};
        }

        (function (settings) {
            (function (element) {
                if (typeof settings.onBlock == 'function') {
                    settings.onBlock();
                } else {
                    window.cresenity.blockPage();
                }

                $(element).data('xhr', $.ajax({
                    type: method,
                    url: url,
                    dataType: 'json',
                    data: dataAddition,
                    success: function (response) {
                        window.cresenity.handleJsonResponse(response, function (data) {
                            let progressUrl = data.progressUrl;
                            let progressContainer = $('<div>').addClass('progress-container');

                            const interval = setInterval(function () {
                                $.ajax({
                                    type: method,
                                    url: progressUrl,
                                    dataType: 'json',
                                    success: function (response) {
                                        window.cresenity.handleJsonResponse(response, function (data) {
                                            if (data.state == 'DONE') {
                                                progressContainer.find('.progress-container-status').empty();
                                                let innerStatus = $('<div>');

                                                let innerStatusLabel = $('<label>', { class: 'mb-3 d-block' }).append('Your file is ready');
                                                let linkDownload = $('<a>', { target: '_blank', href: data.fileUrl, class: 'btn btn-primary' }).append('Download');
                                                let linkClose = $('<a>', { href: 'javascript:;', class: 'btn btn-primary ml-3' }).append('Close');

                                                innerStatus.append(innerStatusLabel);
                                                innerStatus.append(linkDownload);
                                                innerStatus.append(linkClose);

                                                progressContainer.find('.progress-container-status').append(innerStatus);
                                                linkClose.click(() => {
                                                    window.cresenity.closeLastModal();
                                                });
                                                clearInterval(interval);
                                            }
                                        });
                                    }
                                });
                            }, 3000);

                            let innerStatus = $('<div>');
                            let innerStatusLabel = $('<label>', { class: 'mb-4' }).append('Please Wait...');
                            let innerStatusAnimation = $('<div>').append('<div class="sk-fading-circle sk-primary"><div class="sk-circle1 sk-circle"></div><div class="sk-circle2 sk-circle"></div><div class="sk-circle3 sk-circle"></div><div class="sk-circle4 sk-circle"></div><div class="sk-circle5 sk-circle"></div><div class="sk-circle6 sk-circle"></div><div class="sk-circle7 sk-circle"></div><div class="sk-circle8 sk-circle"></div><div class="sk-circle9 sk-circle"></div><div class="sk-circle10 sk-circle"></div><div class="sk-circle11 sk-circle"></div><div class="sk-circle12 sk-circle"></div></div>');
                            let innerStatusAction = $('<div>', { class: 'text-center my-3' });
                            let innerStatusCancelButton = $('<button>', { class: 'btn btn-primary' }).append('Cancel');
                            innerStatusAction.append(innerStatusCancelButton);
                            innerStatus.append(innerStatusLabel);
                            innerStatus.append(innerStatusAnimation);
                            innerStatus.append(innerStatusAction);
                            progressContainer.append($('<div>').addClass('progress-container-status').append(innerStatus));

                            innerStatusCancelButton.click(function () {
                                clearInterval(interval);
                                window.cresenity.closeLastModal();
                            });


                            window.cresenity.modal({
                                message: progressContainer,
                                modalClass: 'modal-download-progress'
                            });
                        });
                    },
                    error: function (xhr, ajaxOptions, thrownError) {
                        if (thrownError != 'abort') {
                            window.cresenity.message('error', 'Error, please call administrator... (' + thrownError + ')');
                        }
                    },
                    complete: function () {
                        $(element).data('xhr', false);
                        if (typeof settings.onBlock == 'function') {
                            settings.onUnblock();
                        } else {
                            window.cresenity.unblockPage();
                        }

                        if (typeof settings.onComplete == 'function') {
                            settings.onComplete();
                        }
                    }
                }));
            }(this));
        }(settings));
    };
};
if (!window.cresenity) {
    window.cresenity = new Cresenity();
    window.cresenity.init();
}
(function ($, window, document) {
    $.cresenity = {
        _filesAdded: '',
        _loadjscss: function (filename, filetype, callback) {
            let fileref = false;
            if (filetype == 'js') { //if filename is a external JavaScript file
                fileref = document.createElement('script');
                fileref.setAttribute('type', 'text/javascript');
                fileref.setAttribute('src', filename);
            } else if (filetype == 'css') { //if filename is an external CSS file
                fileref = document.createElement('link');
                fileref.setAttribute('rel', 'stylesheet');
                fileref.setAttribute('type', 'text/css');
                fileref.setAttribute('href', filename);
            }
            if (fileref !== false) {
                //fileref.onload = callback;
                // IE 6 & 7
                fileref.onload = $.cresenity._handle_response_callback(callback);
                if (typeof (callback) === 'function') {
                    fileref.onreadystatechange = function () {
                        if (this.readyState == 'complete') {
                            $.cresenity._handle_response_callback(callback);
                        }
                    };
                }
                document.getElementsByTagName('head')[0].appendChild(fileref);
            }
        },
        _removejscss: function (filename, filetype) {
            let targetelement = (filetype == 'js') ? 'script' : (filetype == 'css') ? 'link' : 'none'; //determine element type to create nodelist from
            let targetattr = (filetype == 'js') ? 'src' : (filetype == 'css') ? 'href' : 'none'; //determine corresponding attribute to test for
            let allsuspects = document.getElementsByTagName(targetelement);
            for (let i = allsuspects.length; i >= 0; i--) { //search backwards within nodelist for matching elements to remove
                if (allsuspects[i] && allsuspects[i].getAttribute(targetattr) != null && allsuspects[i].getAttribute(targetattr).indexOf(filename) != -1) {
                    allsuspects[i].parentNode.removeChild(allsuspects[i]); //remove element by calling parentNode.removeChild()
                }
            }
        },
        _handle_response: function (data, callback) {
            if (data.css_require && data.css_require.length > 0) {
                for (let i = 0; i < data.css_require.length; i++) {
                    $.cresenity.require(data.css_require[i], 'css');
                }
            }
            require(data.js_require, callback);
            return;
        },
        _handle_response_callback: function (callback) {
            $.cresenity._filesloaded++;
            if ($.cresenity._filesloaded == $.cresenity._filesneeded) {
                callback();
            }
        },
        require: function (filename, filetype, callback) {
            if ($.cresenity._filesAdded.indexOf('[' + filename + ']') == -1) {
                $.cresenity._loadjscss(filename, filetype, callback);
                $.cresenity._filesAdded += '[' + filename + ']'; //List of files added in the form "[filename1],[filename2],etc"
            } else {
                $.cresenity._filesloaded++;
                if ($.cresenity._filesloaded == $.cresenity._filesneeded) {
                    callback();
                }
            }
        },
        days_between: function (date1, date2) {
            // The number of milliseconds in one day
            let ONE_DAY = 1000 * 60 * 60 * 24;

            // Convert both dates to milliseconds
            let date1_ms = date1.getTime();
            let date2_ms = date2.getTime();

            // Calculate the difference in milliseconds
            let difference_ms = Math.abs(date1_ms - date2_ms);

            // Convert back to days and return
            return Math.round(difference_ms / ONE_DAY);
        },
        set_confirm: function (selector) {
            $(selector).click(function (e) {
                let ahref = $(this).attr('href');
                e.preventDefault();
                e.stopPropagation();
                window.bootbox.confirm('Are you sure?', function (confirmed) {
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
            let div_content = $('body #' + dlg_id + ' #' + dlg_id + '_content');
            if (div_content.length) {
                $('body #' + dlg_id + ' #' + dlg_id + '_header h3').html(title);
                return div_content;
            }
            if (title == 'undefined') {title = '';}
            if (!title) {title = '';}
            //not exists create the modal div
            let div = $('<div>').attr('id', dlg_id);
            let btnClose = '<a href="' + 'javascript:;' + '" class="close" data-dismiss="modal">&times;</a>';
            btnClose = '';
            div.append('<div class="modal-header" id="' + dlg_id + '_header">' + btnClose + '<h3>' + title + '</h3></div>');
            div_content = $('<div class="modal-body" id="' + dlg_id + '_content"></div>');
            div.append(div_content);
            let btn_close = $('<a id="' + dlg_id + '_close">').addClass('btn').attr('href', 'javascript:void(0)');
            btn_close.append('<i class="icon icon-close"></i> Close');
            btn_close.click(function () {
                $('#' + dlg_id + '').modal('hide');
                $('#' + dlg_id + '').remove();
            });
            let div_footer = $('<div class="modal-footer" id="suspended_dlg_footer"></div>');
            div_footer.append(btn_close);
            div.append(div_footer);
            div.css('overflow', 'hidden');
            div.addClass('modal');
            // stick the modal right at the bottom of the main body out of the way
            $('body').append(div);
            return div_content;
        },
        message: function (type, message, alert_type, callback) {
            alert_type = typeof alert_type !== 'undefined' ? alert_type : 'notify';
            let container = $('#container');
            if (container.length == 0) {
                container = $('body');
            }
            if (alert_type == 'bootbox') {
                if (typeof callback == 'undefined') {
                    window.bootbox.alert(message);
                } else {
                    window.bootbox.alert(message, callback);
                }
            }

            if (alert_type == 'notify') {
                let obj = $('<div>');
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
            rp = '' + rp;
            let rupiah = '';
            let vfloat = '';
            let ds = window.capp.decimal_separator;
            let ts = window.capp.thousand_separator;
            let dd = window.capp.decimal_digit;
            dd = parseInt(dd);
            let minus_str = '';
            if (rp.indexOf('-') >= 0) {
                minus_str = rp.substring(rp.indexOf('-'), 1);
                rp = rp.substring(rp.indexOf('-') + 1);
            }

            if (rp.indexOf('.') >= 0) {
                vfloat = rp.substring(rp.indexOf('.'));
                rp = rp.substring(0, rp.indexOf('.'));
            }
            let p = rp.length;
            while (p > 3) {
                rupiah = ts + rp.substring(p - 3) + rupiah;
                let l = rp.length - 3;
                rp = rp.substring(0, l);
                p = rp.length;
            }
            rupiah = rp + rupiah;
            vfloat = vfloat.replace('.', ds);
            if (vfloat.length > dd) {vfloat = vfloat.substring(0, dd + 1);}
            return minus_str + rupiah + vfloat;
        },
        replace_all: function (string, find, replace) {
            let escapedFind = find.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, '\\$1');
            return string.replace(new RegExp(escapedFind, 'g'), replace);
        },
        format_currency: function (rp) {
            return $.cresenity.thousand_separator(rp);
        },
        unformat_currency: function (rp) {
            if (typeof rp == 'undefined') {
                rp = '';
            }
            let ds = window.capp.decimal_separator;
            let ts = window.capp.thousand_separator;
            let last3 = rp.substr(rp.length - 3);
            let char_last3 = last3.charAt(0);
            if (char_last3 != ts) {
                rp = this.replace_all(rp, ts, '');
            }

            rp = rp.replace(ds, '.');
            return rp;
        },
        base64: {
            _keyStr: 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=',
            encode: function (e) {
                let t = '';
                let n,
                    r,
                    i,
                    s,
                    o,
                    u,
                    a;
                let f = 0;
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
                        u = a = 64;
                    } else if (isNaN(i)) {
                        a = 64;
                    }
                    t = t + this._keyStr.charAt(s) + this._keyStr.charAt(o) + this._keyStr.charAt(u) + this._keyStr.charAt(a);
                }
                return t;
            },
            decode: function (e) {
                let t = '';
                let n,
                    r,
                    i;
                let s,
                    o,
                    u,
                    a;
                let f = 0;
                e = e.replace(/[^A-Za-z0-9+/=]/g, '');
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
                        t = t + String.fromCharCode(r);
                    }
                    if (a != 64) {
                        t = t + String.fromCharCode(i);
                    }
                }
                t = this._utf8_decode(t);
                return t;
            },
            _utf8_encode: function (e) {
                e = e.replace(/rn/g, 'n');
                let t = '';
                for (let n = 0; n < e.length; n++) {
                    let r = e.charCodeAt(n);
                    if (r < 128) {
                        t = t + String.fromCharCode(r);
                    } else if (r > 127 && r < 2048) {
                        t = t + String.fromCharCode(r >> 6 | 192);
                        t = t + String.fromCharCode(r & 63 | 128);
                    } else {
                        t = t + String.fromCharCode(r >> 12 | 224);
                        t = t + String.fromCharCode(r >> 6 & 63 | 128);
                        t = t + String.fromCharCode(r & 63 | 128);
                    }
                }
                return t;
            },
            _utf8_decode: function (e) {
                let t = '';
                let n = 0;
                let r = 0;
                let c3 = 0;
                let c2 = 0;
                while (n < e.length) {
                    r = e.charCodeAt(n);
                    if (r < 128) {
                        t = t + String.fromCharCode(r);
                        n++;
                    } else if (r > 191 && r < 224) {
                        c2 = e.charCodeAt(n + 1);
                        t = t + String.fromCharCode((r & 31) << 6 | c2 & 63);
                        n = n + 2;
                    } else {
                        c2 = e.charCodeAt(n + 1);
                        c3 = e.charCodeAt(n + 2);
                        t = t + String.fromCharCode((r & 15) << 12 | (c2 & 63) << 6 | c3 & 63);
                        n = n + 3;
                    }
                }
                return t;
            }
        },
        url: {
            add_query_string: function (url, key, value) {
                key = encodeURI(key);
                value = encodeURI(value);
                let url_array = url.split('?');
                let query_string = '';
                let base_url = url_array[0];
                if (url_array.length > 1) {query_string = url_array[1];}
                let kvp = query_string.split('&');
                let i = kvp.length;
                let x;
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
                if (query_string.substr(0, 1) == '&') {query_string = query_string.substr(1);}
                return base_url + '?' + query_string;
            },
            replace_param: function (url) {
                let available = true;
                while (available) {
                    let matches = url.match(/{([\w]*)}/);
                    if (matches != null) {
                        let key = matches[1];
                        let val = null;
                        if ($('#' + key).length > 0) {
                            val = $.cresenity.value('#' + key);
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
                return window.cresenity.reload(id_target);
            }
            let options = {};
            options.selector = '#' + id_target;
            options.url = url;
            options.method = method;
            options.dataAddition = data_addition;
            return window.cresenity.reload(options);
        },
        append: function (id_target, url, method, data_addition) {
            if (!method) {method = 'get';}
            let xhr = jQuery('#' + id_target).data('xhr');
            url = $.cresenity.url.replace_param(url);
            if (typeof data_addition == 'undefined') {data_addition = {};}
            url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', id_target);
            if (window.capp.bootstrap >= '3.3') {
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').addClass('loading'));
            } else {
                jQuery('#' + id_target).addClass('loading');
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').css('text-align', 'center').css('margin-top', '100px').css('margin-bottom', '100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')));
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
                        let script = $.cresenity.base64.decode(data.js);
                        eval(script);
                        jQuery('#' + id_target).removeClass('loading');
                        jQuery('#' + id_target).data('xhr', false);
                        if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                            window.prettyPrint && window.prettyPrint();
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
            if (!method) {method = 'get';}
            let xhr = jQuery('#' + id_target).data('xhr');
            url = $.cresenity.url.replace_param(url);
            if (typeof data_addition == 'undefined') {data_addition = {};}
            url = $.cresenity.url.add_query_string(url, 'capp_current_container_id', id_target);
            if (window.capp.bootstrap >= '3.3') {
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').addClass('loading'));
            } else {
                jQuery('#' + id_target).addClass('loading');
                jQuery('#' + id_target).append(jQuery('<div>').attr('id', id_target + '-loading').css('text-align', 'center').css('margin-top', '100px').css('margin-bottom', '100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x')));
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
                        let script = $.cresenity.base64.decode(data.js);
                        eval(script);
                        jQuery('#' + id_target).removeClass('loading');
                        jQuery('#' + id_target).data('xhr', false);
                        if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                            window.prettyPrint && window.prettyPrint();
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
        // eslint-disable-next-line max-params
        show_tooltip: function (id_target, url, method, text, toggle, position, title, data_addition) {
            if (typeof title == 'undefined') {title = '';}
            if (typeof position == 'undefined') {position = 'auto';}
            if (typeof data_addition == 'undefined') {data_addition = {};}
            if (typeof text == 'undefined') {text = ' ';}
            let _tooltip_html = '<div class="popover" id="popover' + id_target + '" role="tooltip"><div class="arrow"></div><h3 class="popover-title"></h3><div class="popover-content" id="popover-content' + id_target + '"></div></div>';
            let selection = jQuery('#' + id_target);
            let handle;
            let html_content = text;
            let parent = $(selection.html());
            let close_button = '<a id=\'closetooltip' + id_target + '\' class=\'close\' style=\'margin-left:10px;\'>X</a>';
            if (typeof jQuery('#popover' + id_target).html() == 'undefined') {
                if (url.length > 0) {
                    text = jQuery('<div>').attr('id', id_target + '-loading').css('text-align', 'center').css('margin-top', '100px').css('margin-bottom', '100px').append(jQuery('<i>').addClass('icon icon-repeat icon-spin icon-4x'));
                }
                if (position == 'auto' || position == '') {
                    $('#' + id_target).popover({
                        container: 'body',
                        title: close_button + title,
                        html: true,
                        trigger: 'manual',
                        content: text,
                        selector: true,
                        template: _tooltip_html
                    });
                } else {
                    $('#' + id_target).popover({
                        container: 'body',
                        title: close_button + title,
                        html: true,
                        trigger: 'manual',
                        content: text,
                        placement: position,
                        selector: true,
                        template: _tooltip_html
                    });
                }
                $('#' + id_target).popover('show');
                handle = $('.tooltip-inner', parent);
                if (url.length > 0) {
                    let xhr = handle.data('xhr');
                    if (xhr) {xhr.abort();}
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
                                        trigger: 'manual',
                                        content: html_content + data.html,
                                        selector: true,
                                        template: _tooltip_html
                                    });
                                } else {
                                    $('#' + id_target).popover({
                                        animation: false,
                                        title: close_button + title,
                                        html: true,
                                        trigger: 'manual',
                                        content: html_content + data.html,
                                        placement: position,
                                        selector: true,
                                        template: _tooltip_html
                                    });
                                }
                                $('#' + id_target).popover('show');
                                if (data.js && data.js.length > 0) {
                                    let script = $.cresenity.base64.decode(data.js);
                                    eval(script);
                                }
                                $('#closetooltip' + id_target).on('click', function () {
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
                    $('#closetooltip' + id_target).on('click', function () {
                        $('#' + id_target).popover('destroy');
                    });
                }
            } else if (toggle == '1') {
                $('#' + id_target).popover('destroy');
            }
        },
        show_real_notification: function (id_target, url) {
            let selection = jQuery('#' + id_target);
            url = $.cresenity.url.replace_param(url);
            let handle;
            if (selection.length == 0) {
                selection = jQuery('<div/>').attr('id', id_target);
            }
            handle = selection;
            handle.data('xhr', jQuery.ajax({
                type: 'post',
                data: {
                    title: document.title
                },
                url: url,
                dataType: 'json',
                success: function (data) {
                    $.cresenity._handle_response(data, function () {
                        jQuery('#' + id_target).html(data.html);
                        if (data.js && data.js.length > 0) {
                            let script = $.cresenity.base64.decode(data.js);
                            eval(script);
                        }
                        jQuery('#' + id_target).removeClass('loading');
                        jQuery('#' + id_target).data('xhr', false);
                        if (jQuery('#' + id_target).find('.prettyprint').length > 0) {
                            window.prettyPrint && window.prettyPrint();
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
                return window.cresenity.modal(id_target);
            }
            options.selector = '#' + id_target;
            options.reload = {};
            options.reload.method = method;
            options.reload.dataAddition = data_addition;
            options.reload.url = url;

            return window.cresenity.modal(options);
        },
        value: function (elm) {
            elm = jQuery(elm);
            if (elm.length == 0) {return null;}
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
            if (elm.val() != 'undefined') {
                return elm.val();
            }
            if (elm.attr('value') != 'undefined') {
                return elm.attr('value');
            }
            return elm.html();
        },
        dialog: {
            alert: function (message) {
                $.fn.dialog2.helpers.alert(message, {});
            },
            prompt: function (message) {
                $.fn.dialog2.helpers.prompt(message, {});
            },
            confirm: function (message) {
                $.fn.dialog2.helpers.confirm(message, {});
            },
            show: function (selector, options) {
                $(selector).dialog2(options);
            }
        },

        fullscreen: function (element) {
            if (!$('body').hasClass('full-screen')) {
                $('body').addClass('full-screen');
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
                $('body').removeClass('full-screen');
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
            window.cresenity.blockPage();
        },
        unblockPage: function () {
            window.cresenity.unblockPage();
        },
        blockElement: function (selector) {
            window.cresenity.blockElement(selector);
        },
        unblockElement: function (selector) {
            window.cresenity.unblockElement(selector);
        }
    };
    String.prototype.format_currency = function () {
        return $.cresenity.format_currency(this);
    };
    String.prototype.unformat_currency = function () {
        return $.cresenity.unformat_currency(this);
    };
}(this.jQuery, window, document));

let appValidation;
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
                this.rules = {};
            };

            $.validator.dataRules = this.arrayRules;
            $.validator.prototype.arrayRulesCache = {};
            // Register validations methods
            this.setupValidations();
        }
    },

    arrayRules: function (element) {
        let rules = {},
            validator = $.data(element.form, 'validator'),
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
                let nameRegExp = appValidation.helpers.regexFromWildcard(name);
                if (element.name.match(nameRegExp)) {
                    let newRules = $.validator.normalizeRule(tmpRules) || {};
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


        $.validator.addMethod('appValidation', function (value, element, params) {
            let validator = this;
            let validated = true;
            let previous = this.previousValue(element);

            // put Implicit rules in front
            let rules = [];
            $.each(params, function (i, param) {
                if (param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1) {
                    rules.unshift(param);
                } else {
                    rules.push(param);
                }
            });

            $.each(rules, function (i, param) {
                let implicit = param[3] || appValidation.implicitRules.indexOf(param[0]) !== -1;
                let rule = param[0];
                let message = param[2];

                if (!implicit && validator.optional(element)) {
                    validated = 'dependency-mismatch';
                    return false;
                }

                if (appValidation.methods[rule] !== undefined) {
                    validated = appValidation.methods[rule].call(validator, value, element, param[1], function (valid) {
                        validator.settings.messages[element.name].appValidationRemote = previous.originalMessage;
                        if (valid) {
                            let submitted = validator.formSubmitted;
                            validator.prepareElement(element);
                            validator.formSubmitted = submitted;
                            validator.successList.push(element);
                            delete validator.invalid[element.name];
                            validator.showErrors();
                        } else {
                            let errors = {};
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
        $.validator.addMethod('appValidationRemote', function (value, element, params) {
            let implicit = false,
                check = params[0][1],
                attribute = element.name,
                token = check[1],
                validateAll = check[2];

            $.each(params, function (i, parameters) {
                implicit = implicit || parameters[3];
            });

            if (!implicit && this.optional(element)) {
                return 'dependency-mismatch';
            }

            let previous = this.previousValue(element),
                validator,
                data;

            if (!this.settings.messages[element.name]) {
                this.settings.messages[element.name] = {};
            }
            previous.originalMessage = this.settings.messages[element.name].appValidationRemote;
            this.settings.messages[element.name].appValidationRemote = previous.message;

            let param = typeof params === 'string' && {
                url: params
            } || params;

            if (appValidation.helpers.arrayEquals(previous.old, value) || previous.old === value) {
                return previous.valid;
            }

            previous.old = value;
            validator = this;
            this.startRequest(element);

            data = $(validator.currentForm).serializeArray();

            data.push({
                name: '_jsvalidation',
                value: attribute
            });

            data.push({
                name: '_jsvalidation_validate_all',
                value: validateAll
            });

            let formMethod = $(validator.currentForm).attr('method');
            if ($(validator.currentForm).find('input[name="_method"]').length) {
                formMethod = $(validator.currentForm).find('input[name="_method"]').val();
            }

            $.ajax($.extend(true, {
                mode: 'abort',
                port: 'validate' + element.name,
                dataType: 'json',
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
                let errors,
                    message,
                    submitted,
                    valid;

                if (textStatus === 'error') {
                    valid = false;
                    response = appValidation.helpers.parseErrorResponse(response);
                } else if (textStatus === 'success') {
                    valid = response === true || response === 'true';
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
                    message = response || validator.defaultMessage(element, 'remote');
                    errors[element.name] = previous.message = $.isFunction(message) ? message(value) : message[0];
                    validator.invalid[element.name] = true;
                    validator.showErrors(errors);
                }
                validator.showErrors(validator.errorMap);
                previous.valid = valid;
                validator.stopRequest(element, valid);
            });
            return 'pending';
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
            let FileName = fieldObj.value;
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
            let selector = [];
            if (!$.isArray(names)) {
                names = [names];
            }
            for (let i = 0; i < names.length; i++) {
                selector.push('[name=\'' + names[i] + '\']');
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
            let found = false;
            if (typeof rules === 'string') {
                rules = [rules];
            }

            let validator = $.data(element.form, 'validator');
            let listRules = [];
            let cache = validator.arrayRulesCache;
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
                    let _rules = objRules.appValidation;
                    for (let i = 0; i < _rules.length; i++) {
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
            let found;
            $.each($.validator.staticRules(element), function (key, rules) {
                if (key === 'appValidation') {
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
            let timeValue = false;
            let fmt = new DateFormatter();

            if ($.type(format) === 'object') {
                let dateRule = this.getAppValidation('DateFormat', format);
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
            let fmt = new DateFormatter();
            return fmt.guessDate(value, format);
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
            return strtotime(text, now);
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
            return is_numeric(mixed_var);
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
            let el = validator.findByName(name);

            if (el[0] !== undefined && validator.settings.onfocusout) {
                let event = 'blur';
                if (el[0].tagName === 'SELECT' ||
                    el[0].tagName === 'OPTION' ||
                    el[0].type === 'checkbox' ||
                    el[0].type === 'radio') {
                    event = 'click';
                }

                let ruleName = '.validate-appValidation';
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
            let newResponse = ['Whoops, looks like something went wrong.'];
            if ('responseText' in response) {
                let errorMsg = response.responseText.match(/<h1\s*>(.*)<\/h1\s*>/i);
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
            return str.replace(/[\-\[\]\/\{\}\(\)\*\+\?\.\\\^\$\|]/g, '\\$&');
        },

        /**
         * Generate RegExp from wildcard attributes.
         *
         * @param name
         * @returns {RegExp}
         */
        regexFromWildcard: function (name) {
            let nameParts = name.split('[*]');
            if (nameParts.length === 1) {
                nameParts.push('');
            }
            let regexpParts = nameParts.map(function (currentValue, index) {
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
            let timezones = {
                africa: [
                    'abidjan',
                    'accra',
                    'addis_ababa',
                    'algiers',
                    'asmara',
                    'bamako',
                    'bangui',
                    'banjul',
                    'bissau',
                    'blantyre',
                    'brazzaville',
                    'bujumbura',
                    'cairo',
                    'casablanca',
                    'ceuta',
                    'conakry',
                    'dakar',
                    'dar_es_salaam',
                    'djibouti',
                    'douala',
                    'el_aaiun',
                    'freetown',
                    'gaborone',
                    'harare',
                    'johannesburg',
                    'juba',
                    'kampala',
                    'khartoum',
                    'kigali',
                    'kinshasa',
                    'lagos',
                    'libreville',
                    'lome',
                    'luanda',
                    'lubumbashi',
                    'lusaka',
                    'malabo',
                    'maputo',
                    'maseru',
                    'mbabane',
                    'mogadishu',
                    'monrovia',
                    'nairobi',
                    'ndjamena',
                    'niamey',
                    'nouakchott',
                    'ouagadougou',
                    'porto-novo',
                    'sao_tome',
                    'tripoli',
                    'tunis',
                    'windhoek'
                ],
                america: [
                    'adak',
                    'anchorage',
                    'anguilla',
                    'antigua',
                    'araguaina',
                    'argentina\/buenos_aires',
                    'argentina\/catamarca',
                    'argentina\/cordoba',
                    'argentina\/jujuy',
                    'argentina\/la_rioja',
                    'argentina\/mendoza',
                    'argentina\/rio_gallegos',
                    'argentina\/salta',
                    'argentina\/san_juan',
                    'argentina\/san_luis',
                    'argentina\/tucuman',
                    'argentina\/ushuaia',
                    'aruba',
                    'asuncion',
                    'atikokan',
                    'bahia',
                    'bahia_banderas',
                    'barbados',
                    'belem',
                    'belize',
                    'blanc-sablon',
                    'boa_vista',
                    'bogota',
                    'boise',
                    'cambridge_bay',
                    'campo_grande',
                    'cancun',
                    'caracas',
                    'cayenne',
                    'cayman',
                    'chicago',
                    'chihuahua',
                    'costa_rica',
                    'creston',
                    'cuiaba',
                    'curacao',
                    'danmarkshavn',
                    'dawson',
                    'dawson_creek',
                    'denver',
                    'detroit',
                    'dominica',
                    'edmonton',
                    'eirunepe',
                    'el_salvador',
                    'fortaleza',
                    'glace_bay',
                    'godthab',
                    'goose_bay',
                    'grand_turk',
                    'grenada',
                    'guadeloupe',
                    'guatemala',
                    'guayaquil',
                    'guyana',
                    'halifax',
                    'havana',
                    'hermosillo',
                    'indiana\/indianapolis',
                    'indiana\/knox',
                    'indiana\/marengo',
                    'indiana\/petersburg',
                    'indiana\/tell_city',
                    'indiana\/vevay',
                    'indiana\/vincennes',
                    'indiana\/winamac',
                    'inuvik',
                    'iqaluit',
                    'jamaica',
                    'juneau',
                    'kentucky\/louisville',
                    'kentucky\/monticello',
                    'kralendijk',
                    'la_paz',
                    'lima',
                    'los_angeles',
                    'lower_princes',
                    'maceio',
                    'managua',
                    'manaus',
                    'marigot',
                    'martinique',
                    'matamoros',
                    'mazatlan',
                    'menominee',
                    'merida',
                    'metlakatla',
                    'mexico_city',
                    'miquelon',
                    'moncton',
                    'monterrey',
                    'montevideo',
                    'montreal',
                    'montserrat',
                    'nassau',
                    'new_york',
                    'nipigon',
                    'nome',
                    'noronha',
                    'north_dakota\/beulah',
                    'north_dakota\/center',
                    'north_dakota\/new_salem',
                    'ojinaga',
                    'panama',
                    'pangnirtung',
                    'paramaribo',
                    'phoenix',
                    'port-au-prince',
                    'port_of_spain',
                    'porto_velho',
                    'puerto_rico',
                    'rainy_river',
                    'rankin_inlet',
                    'recife',
                    'regina',
                    'resolute',
                    'rio_branco',
                    'santa_isabel',
                    'santarem',
                    'santiago',
                    'santo_domingo',
                    'sao_paulo',
                    'scoresbysund',
                    'shiprock',
                    'sitka',
                    'st_barthelemy',
                    'st_johns',
                    'st_kitts',
                    'st_lucia',
                    'st_thomas',
                    'st_vincent',
                    'swift_current',
                    'tegucigalpa',
                    'thule',
                    'thunder_bay',
                    'tijuana',
                    'toronto',
                    'tortola',
                    'vancouver',
                    'whitehorse',
                    'winnipeg',
                    'yakutat',
                    'yellowknife'
                ],
                antarctica: [
                    'casey',
                    'davis',
                    'dumontdurville',
                    'macquarie',
                    'mawson',
                    'mcmurdo',
                    'palmer',
                    'rothera',
                    'south_pole',
                    'syowa',
                    'vostok'
                ],
                arctic: [
                    'longyearbyen'
                ],
                asia: [
                    'aden',
                    'almaty',
                    'amman',
                    'anadyr',
                    'aqtau',
                    'aqtobe',
                    'ashgabat',
                    'baghdad',
                    'bahrain',
                    'baku',
                    'bangkok',
                    'beirut',
                    'bishkek',
                    'brunei',
                    'choibalsan',
                    'chongqing',
                    'colombo',
                    'damascus',
                    'dhaka',
                    'dili',
                    'dubai',
                    'dushanbe',
                    'gaza',
                    'harbin',
                    'hebron',
                    'ho_chi_minh',
                    'hong_kong',
                    'hovd',
                    'irkutsk',
                    'jakarta',
                    'jayapura',
                    'jerusalem',
                    'kabul',
                    'kamchatka',
                    'karachi',
                    'kashgar',
                    'kathmandu',
                    'khandyga',
                    'kolkata',
                    'krasnoyarsk',
                    'kuala_lumpur',
                    'kuching',
                    'kuwait',
                    'macau',
                    'magadan',
                    'makassar',
                    'manila',
                    'muscat',
                    'nicosia',
                    'novokuznetsk',
                    'novosibirsk',
                    'omsk',
                    'oral',
                    'phnom_penh',
                    'pontianak',
                    'pyongyang',
                    'qatar',
                    'qyzylorda',
                    'rangoon',
                    'riyadh',
                    'sakhalin',
                    'samarkand',
                    'seoul',
                    'shanghai',
                    'singapore',
                    'taipei',
                    'tashkent',
                    'tbilisi',
                    'tehran',
                    'thimphu',
                    'tokyo',
                    'ulaanbaatar',
                    'urumqi',
                    'ust-nera',
                    'vientiane',
                    'vladivostok',
                    'yakutsk',
                    'yekaterinburg',
                    'yerevan'
                ],
                atlantic: [
                    'azores',
                    'bermuda',
                    'canary',
                    'cape_verde',
                    'faroe',
                    'madeira',
                    'reykjavik',
                    'south_georgia',
                    'st_helena',
                    'stanley'
                ],
                australia: [
                    'adelaide',
                    'brisbane',
                    'broken_hill',
                    'currie',
                    'darwin',
                    'eucla',
                    'hobart',
                    'lindeman',
                    'lord_howe',
                    'melbourne',
                    'perth',
                    'sydney'
                ],
                europe: [
                    'amsterdam',
                    'andorra',
                    'athens',
                    'belgrade',
                    'berlin',
                    'bratislava',
                    'brussels',
                    'bucharest',
                    'budapest',
                    'busingen',
                    'chisinau',
                    'copenhagen',
                    'dublin',
                    'gibraltar',
                    'guernsey',
                    'helsinki',
                    'isle_of_man',
                    'istanbul',
                    'jersey',
                    'kaliningrad',
                    'kiev',
                    'lisbon',
                    'ljubljana',
                    'london',
                    'luxembourg',
                    'madrid',
                    'malta',
                    'mariehamn',
                    'minsk',
                    'monaco',
                    'moscow',
                    'oslo',
                    'paris',
                    'podgorica',
                    'prague',
                    'riga',
                    'rome',
                    'samara',
                    'san_marino',
                    'sarajevo',
                    'simferopol',
                    'skopje',
                    'sofia',
                    'stockholm',
                    'tallinn',
                    'tirane',
                    'uzhgorod',
                    'vaduz',
                    'vatican',
                    'vienna',
                    'vilnius',
                    'volgograd',
                    'warsaw',
                    'zagreb',
                    'zaporozhye',
                    'zurich'
                ],
                indian: [
                    'antananarivo',
                    'chagos',
                    'christmas',
                    'cocos',
                    'comoro',
                    'kerguelen',
                    'mahe',
                    'maldives',
                    'mauritius',
                    'mayotte',
                    'reunion'
                ],
                pacific: [
                    'apia',
                    'auckland',
                    'chatham',
                    'chuuk',
                    'easter',
                    'efate',
                    'enderbury',
                    'fakaofo',
                    'fiji',
                    'funafuti',
                    'galapagos',
                    'gambier',
                    'guadalcanal',
                    'guam',
                    'honolulu',
                    'johnston',
                    'kiritimati',
                    'kosrae',
                    'kwajalein',
                    'majuro',
                    'marquesas',
                    'midway',
                    'nauru',
                    'niue',
                    'norfolk',
                    'noumea',
                    'pago_pago',
                    'palau',
                    'pitcairn',
                    'pohnpei',
                    'port_moresby',
                    'rarotonga',
                    'saipan',
                    'tahiti',
                    'tarawa',
                    'tongatapu',
                    'wake',
                    'wallis'
                ],
                utc: [
                    ''
                ]
            };

            let tzparts = value.split('/', 2);
            let continent = tzparts[0].toLowerCase();
            let city = '';
            if (tzparts[1]) {
                city = tzparts[1].toLowerCase();
            }

            return (continent in timezones && (timezones[continent].length === 0 || timezones[continent].indexOf(city) !== -1));
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
            let validator = this,
                required = false;
            let currentObject = this;
            $.each(params, function (i, param) {
                let target = appValidation.helpers.dependentElement(
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
            let validator = this,
                required = true;
            let currentObject = this;
            $.each(params, function (i, param) {
                let target = appValidation.helpers.dependentElement(
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
            let validator = this,
                required = false;
            let currentObject = this;
            $.each(params, function (i, param) {
                let target = appValidation.helpers.dependentElement(
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
            let validator = this,
                required = true,
                currentObject = this;
            $.each(params, function (i, param) {
                let target = appValidation.helpers.dependentElement(
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
            let target = appValidation.helpers.dependentElement(
                this, element, params[0]);
            if (target !== undefined) {
                let val = String(this.elementValue(target));
                if (typeof val !== 'undefined') {
                    let data = params.slice(1);
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
            let target = appValidation.helpers.dependentElement(
                this, element, params[0]);
            if (target !== undefined) {
                let val = String(this.elementValue(target));
                if (typeof val !== 'undefined') {
                    let data = params.slice(1);
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
            let target = appValidation.helpers.dependentElement(
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
            let elements = this.elements();
            let found = false;
            let nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);
            for (let i = 0; i < elements.length; i++) {
                let targetName = elements[i].name;
                if (targetName.match(nameRegExp)) {
                    let equals = appValidation.methods.Same.call(this, value, element, [targetName]);
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

            let elements = this.elements();
            let found = false;
            let nameRegExp = appValidation.helpers.regexFromWildcard(params[0]);
            for (let i = 0; i < elements.length; i++) {
                let targetName = elements[i].name;
                if (targetName !== element.name && targetName.match(nameRegExp)) {
                    let equals = appValidation.methods.Same.call(this, value, element, [targetName]);
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
            let regex = new RegExp('^(?:(yes|on|1|true))$', 'i');
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
            let regex = new RegExp('^(?:(true|false|1|0))$', 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute is an integer.
         *
         * @return {boolean}
         */
        Integer: function (value) {
            let regex = new RegExp('^(?:-?\\d+)$', 'i');
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
            if ($.isArray(value) && appValidation.helpers.hasRules(element, 'Array')) {
                let diff = appValidation.helpers.arrayDiff(value, params);
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
            let lowerParams = $.map(params, function (item) {
                return item.toLowerCase();
            });
            let fileinfo = appValidation.helpers.fileinfo(element);
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
            let lowerParams = $.map(params, function (item) {
                return item.toLowerCase();
            });
            let fileinfo = appValidation.helpers.fileinfo(element);
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

            let fr = new FileReader();
            fr.onload = function () {
                let img = new Image();
                img.onload = function () {
                    let height = parseFloat(img.naturalHeight);
                    let width = parseFloat(img.naturalWidth);
                    let ratio = width / height;
                    let notValid = ((params.width) && parseFloat(params.width !== width)) ||
                        ((params.min_width) && parseFloat(params.min_width) > width) ||
                        ((params.max_width) && parseFloat(params.max_width) < width) ||
                        ((params.height) && parseFloat(params.height) !== height) ||
                        ((params.min_height) && parseFloat(params.min_height) > height) ||
                        ((params.max_height) && parseFloat(params.max_height) < height) ||
                        ((params.ratio) && ratio !== parseFloat(eval(params.ratio)));
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

            let regex = new RegExp('^(?:^[a-z\u00E0-\u00FC]+$)$', 'i');
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
            let regex = new RegExp('^(?:^[a-z0-9\u00E0-\u00FC]+$)$', 'i');
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
            let regex = new RegExp('^(?:^[a-z0-9\u00E0-\u00FC_-]+$)$', 'i');
            return regex.test(value);
        },
        /**
         * Validate that an attribute passes a regular expression check.
         *
         * @return {boolean}
         */
        Regex: function (value, element, params) {
            let invalidModifiers = ['x', 's', 'u', 'X', 'U', 'A'];
            // Converting php regular expression
            let phpReg = new RegExp('^(?:\/)(.*\\\/?[^\/]*|[^\/]*)(?:\/)([gmixXsuUAJ]*)?$');
            let matches = params[0].match(phpReg);
            if (matches === null) {
                return false;
            }
            // checking modifiers
            let php_modifiers = [];
            if (matches[2] !== undefined) {
                php_modifiers = matches[2].split('');
                for (let i = 0; i < php_modifiers.length < i; i++) {
                    if (invalidModifiers.indexOf(php_modifiers[i]) !== -1) {
                        return true;
                    }
                }
            }
            let regex = new RegExp('^(?:' + matches[1] + ')$', php_modifiers.join());
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
            let timeCompare = parseFloat(params);
            if (isNaN(timeCompare)) {
                let target = appValidation.helpers.dependentElement(this, element, params);
                if (target === undefined) {
                    return false;
                }
                timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
            }

            let timeValue = appValidation.helpers.parseTime(value, element);
            return (timeValue !== false && timeValue < timeCompare);
        },
        /**
         * Validate the date is after a given date.
         *
         * @return {boolean}
         */
        After: function (value, element, params) {
            let timeCompare = parseFloat(params);
            if (isNaN(timeCompare)) {
                let target = appValidation.helpers.dependentElement(this, element, params);
                if (target === undefined) {
                    return false;
                }
                timeCompare = appValidation.helpers.parseTime(this.elementValue(target), target);
            }

            let timeValue = appValidation.helpers.parseTime(value, element);
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
            let result = true;
            try {
                JSON.parse(value);
            } catch (e) {
                result = false;
            }
            return result;
        }
    }
});


if (!window.cresenity) {
    window.cresenity = new Cresenity();

    window.cresenity.init();
}
