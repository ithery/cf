export default class CF {
    constructor() {
        this.required = typeof this.required === 'undefined' ? [] : this.required;
        this.cssRequired = typeof this.cssRequired === 'undefined' ? [] : this.cssRequired;

        this.window = window;
        this.document = window.document;
        this.head = this.document.getElementsByTagName('head')[0];
        this.beforeInitCallback = [];
        this.afterInitCallback = [];
        this.config = window.capp;
        if (this.config.cssUrl) {
            this.config.cssUrl.forEach((item) => {
                this.required.push(item);
            });
        }
        if (this.config.jsUrl) {
            this.config.jsUrl.forEach((item) => {
                this.required.push(item);
            });
        }
    }

    debug(msg) {
        if (this.getConfig().debug) {
            window.console.log(msg);
        }
    }

    onBeforeInit(callback) {
        this.beforeInitCallback.push(callback);
        return this;
    }
    onAfterInit(callback) {
        this.afterInitCallback.push(callback);
        return this;
    }


    getConfig() {
        return this.config;
    }


    isUseRequireJs() {
        return this.getConfig().requireJs;
    }
    CFVersion() {
        return this.getConfig().CFVersion;
    }
    requireCss(url, callback) {
        if (!~this.cssRequired.indexOf(url)) {
            this.cssRequired.push(url);
            if (document.querySelector('link[href="' + url + '"],script[src="' + url + '"]') !== null) {
                return;
            }
            let string = '<link rel=\'stylesheet\' type=\'text/css\' href=\'' + url + '\' />';
            if ((document.readyState === 'loading' /* || mwd.readyState === 'interactive'*/) && !!window.CanvasRenderingContext2D && self === parent) {
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
    }
    requireJs(url, callback) {
        if (!~this.required.indexOf(url)) {
            this.required.push(url);
            if (document.querySelector('link[href="' + url + '"],script[src="' + url + '"]') !== null) {
                return;
            }
            let string = '<script type=\'text/javascript\'  src=\'' + url + '\'></script>';
            if ((document.readyState === 'loading' /* || mwd.readyState === 'interactive'*/) && !!window.CanvasRenderingContext2D && self === parent) {
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
                this.body.appendChild(el);
            }
        } else if (typeof (callback) === 'function') {
            callback();
        }
    }
    require(url, callback) {
        if (typeof url != 'string') {
            url = url[0];
        }

        if (!url) {
            return;
        }

        let toPush = url.trim();
        let t = 'js';

        let urlObject = new URL(toPush, document.baseURI);
        if (urlObject) {
            t = urlObject.pathname.split('.').pop();
        }

        if (t == 'js') {
            this.requireJs(toPush, callback);
        }
        if (t == 'css') {
            this.requireCss(toPush, callback);
        }
    }

    loadJQuery(callback) {
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
    }

    init() {
        let arrayJsUrl = this.getConfig().jsUrl;
        let arrayCssUrl = this.getConfig().cssUrl;
        this.beforeInitCallback.forEach(function (item) {
            item();
        });

        this.loadJQuery(() => {
            if (typeof arrayJsUrl !== 'undefined') {
                arrayJsUrl.forEach((item) => {
                    this.required.push(item);
                });
            }
            if (typeof arrayCssUrl !== 'undefined') {
                arrayCssUrl.forEach((item) => {
                    this.cssRequired.push(item);
                });
            }
        });
        this.afterInitCallback.forEach(function (item) {
            item();
        });
    }
}
