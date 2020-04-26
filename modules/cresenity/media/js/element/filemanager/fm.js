


var CFileManager = function (options) {
    this.settings = $.extend({
        selector: '.capp-fm',

    }, options);

    this.callback = {};


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


}

