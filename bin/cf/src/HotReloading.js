let path = require('path');
let File = require('./File');

class HotReloading {
    /**
     *
     * @param {import('./CF')} cf
     */
    constructor(cf) {
        this.cf = cf;
    }

    record() {
        this.clean();

        if (!this.cf.config.hmr) {
            return;
        }

        this.hotFile().write(
            `${this.http()}://${this.cf.config.hmrOptions.host}:${this.port()}`
        );
    }

    hotFile() {
        return new File(path.join(this.cf.config.publicPath, 'hot'));
    }

    http() {
        return process.argv.includes('--https') ? 'https' : 'http';
    }

    port() {
        return process.argv.includes('--port')
            ? process.argv[process.argv.indexOf('--port') + 1]
            : this.cf.config.hmrOptions.port;
    }

    clean() {
        this.hotFile().delete();
    }

    /** @deprecated */
    static record() {
        return new HotReloading(global.CF).record();
    }

    /** @deprecated */
    static hotFile() {
        return new HotReloading(global.CF).hotFile();
    }

    /** @deprecated */
    static http() {
        return new HotReloading(global.CF).http();
    }

    /** @deprecated */
    static port() {
        return new HotReloading(global.CF).port();
    }

    /** @deprecated */
    static clean() {
        return new HotReloading(global.CF).clean();
    }
}

module.exports = HotReloading;
