import {
    dispatch as dispatchWindowEvent
} from './util';
import { mergeOptions } from './util/config';
import pipeline from './util/pipeline';
class CF {
    constructor() {
        this.required = typeof this.required === 'undefined' ? [] : this.required;
        this.cssRequired = typeof this.cssRequired === 'undefined' ? [] : this.cssRequired;

        this.window = window;
        this.document = window.document;
        this.head = this.document.getElementsByTagName('head')[0];
        this.beforeInitCallback = [];
        this.afterInitCallback = [];
        let cappConfig = window.capp;
        if(typeof cappConfig == 'undefined') {
            cappConfig = {};
        }
        let defaultConfig = {
            baseUrl: '/',
            defaultJQueryUrl: 'https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js',
            haveScrollToTop: false,
            vscode: {
                liveReload: {
                    enable: false,
                    protocol: 'ws',
                    host: 'localhost',
                    port: 3717
                }
            },
            requireJs: false,
            CFVersion: '1.2',
            isProduction: false,
            react: {
                enable: false
            }

        };
        this.config = mergeOptions(defaultConfig, cappConfig);

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
                    el.onreadystatechange = () => {
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
                    el.onreadystatechange = () => {
                        if (this.readyState == 'complete') {
                            dispatchWindowEvent('cresenity:js:loaded', {
                                url:url,
                            });
                            callback();
                        }
                    };
                }
                this.document.body.appendChild(el);
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
    isProduction() {
        return this.config.environment == 'production';
    }
    loadReact(callback) {
        let afterReactLoaded = () => {
            dispatchWindowEvent('cresenity:react:loaded');
            callback();
        };
        const reactDevelopmentUrl = 'https://unpkg.com/react@17/umd/react.development.js';
        const reactDevelopmentDomUrl = 'https://unpkg.com/react-dom@17/umd/react-dom.development.js';

        const reactProductionUrl = 'https://unpkg.com/react@17/umd/react.production.min.js';
        const reactProductionDomUrl = 'https://unpkg.com/react-dom@17/umd/react-dom.production.min.js';

        let reactUrl = this.getConfig().isProduction ? reactProductionUrl : reactDevelopmentUrl;
        let reactDomUrl = this.getConfig().isProduction ? reactProductionDomUrl : reactDevelopmentDomUrl;
        let loadReactDom = () => {
            let fileref = this.document.createElement('script');
            fileref.setAttribute('type', 'text/javascript');
            fileref.setAttribute('src', reactDomUrl);
            // IE 6 & 7
            if (typeof (callback) === 'function') {
                fileref.onload = ()=>{
                    afterReactLoaded();
                };
            }
            this.head.appendChild(fileref);
        };
        let loadReactBase = () => {
            let fileref = this.document.createElement('script');
            fileref.setAttribute('type', 'text/javascript');
            fileref.setAttribute('src', reactUrl);
            // IE 6 & 7
            if (typeof (callback) === 'function') {
                fileref.onload = ()=>{
                    loadReactDom();
                };
            }
            this.head.appendChild(fileref);
        };
        if (typeof React == 'undefined') {
            loadReactBase();
        } else {
            afterReactLoaded();
        }
    }
    loadJQuery(callback) {
        const jqueryUrl = this.getConfig().defaultJQueryUrl;
        let afterJQueryLoaded = () => {
            this.required.push(jqueryUrl);
            dispatchWindowEvent('cresenity:jquery:loaded');
            callback();
        };
        if (typeof jQuery == 'undefined') {
            let fileref = this.document.createElement('script');
            fileref.setAttribute('type', 'text/javascript');

            fileref.setAttribute('src', jqueryUrl);
            // IE 6 & 7
            if (typeof (callback) === 'function') {
                fileref.onload = ()=>{
                    afterJQueryLoaded();
                };
                // fileref.onreadystatechange = () => {
                //     if (fileref.readyState == 'complete') {
                //         afterJQueryLoaded();
                //     }
                // };
            }
            this.head.appendChild(fileref);
        } else {
            afterJQueryLoaded();
        }
    }

    init() {
        this.beforeInitCallback.forEach((item) => {
            item();
        });

        //push all item already loaded by html in capp
        let arrayJsUrl = this.getConfig().jsUrl;
        let arrayCssUrl = this.getConfig().cssUrl;
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


        let resolver = this.getConfig().react.enable
            ? (callback) => {
                this.loadJQuery(this.loadReact(callback));
            }
            : (callback) => {
                this.loadJQuery(callback);
            };
        resolver(() => {
            this.afterInitCallback.forEach((item) => {
                item();
            });
        });
    }
}

let cf = new CF();

export default cf;
