
let { Chunks } = require('./Chunks');
let buildConfig = require('./config');
let Paths = require('./Paths');
let Dependencies = require('./Dependencies');
let Dispatcher = require('./Dispatcher');
let WebpackConfig = require('./builder/WebpackConfig');
let { Resolver } = require('./Resolver');
let Dotenv = require('dotenv');
let HotReloading = require('./HotReloading');
let Manifest = require('./Manifest');
let Components = require('./components/Components');
let ComponentRegistrar = require('./components/ComponentRegistrar');
class CF {
    /** @type {CF|null} */
    static _instance = null;

     /**
     * Create a new instance.
     */
    constructor() {
        /** @type {ReturnType<buildConfig>} */
        this.config = buildConfig(this);
        this.chunks = new Chunks(this);
        this.paths = new Paths();
        this.manifest = new Manifest();
        this.components = new Components();
        this.dispatcher = new Dispatcher();
        this.webpackConfig = new WebpackConfig(this);
        this.hot = new HotReloading(this);
        this.resolver = new Resolver();
        this.registrar = new ComponentRegistrar(this);
         /** @type {Task[]} */
         this.tasks = [];


        /**
         * @internal
         * @type {boolean}
         **/
        this.initialized = false;

        this.booted = false;
    }

    /**
     * @internal
     */
     static get instance() {
        return CF._instance || (CF._instance = new CF());
    }


    /**
     * @internal
     */
     async build() {
        if (!this.booted) {
            // console.warn(
            //     'CF was not set up correctly. Please ensure you import or require cfnpm in your cf config.'
            // );

            this.boot();
        }

        return this.webpackConfig.build();
    }


    /**
     * @internal
     */
    boot() {
        if (this.booted) {
            return this;
        }

        this.booted = true;

        // Load .env
        Dotenv.config();

        // If we're using Laravel set the public path by default
        if (this.sees('laravel')) {
            this.config.publicPath = 'public';
        }

        this.listen('init', () => this.hot.record());
        this.makeCurrent();

        return this;
    }
    /**
     * @internal
     */
    async installDependencies() {
        await this.dispatch('internal:gather-dependencies');

        Dependencies.installQueued();
    }

    /**
     * @internal
     */
    init() {
        if (this.initialized) {
            return;
        }

        this.initialized = true;

        return this.dispatch('init', this);
    }

    /**
     * @returns {import("../types/index")}
     */
     get api() {
        if (!this._api) {
            this._api = this.registrar.installAll();

            // @ts-ignore
            this._api.inProduction = () => this.config.production;
        }

        // @ts-ignore
        return this._api;
    }
    /**
     * Determine if the given config item is truthy.
     *
     * @param {string} tool
     */
    isUsing(tool) {
        // @ts-ignore
        return !!this.config[tool];
    }

    /**
     * Determine if CF is executing in a production environment.
     */
    inProduction() {
        return this.config.production;
    }

    /**
     * Determine if CF should use HMR.
     */
    isHot() {
        return process.argv.includes('--hot');
    }

    /**
     * Determine if CF should watch files for changes.
     */
    isWatching() {
        return this.isHot() || process.argv.includes('--watch');
    }

    /**
     * Determine if polling is used for file watching
     */
    isPolling() {
        return this.isWatching() && process.argv.includes('--watch-poll');
    }

     /**
     * Determine if CF sees a particular tool or framework.
     *
     * @param {string} tool
     */
    sees(tool) {
        if (tool === 'cf') {
            return File.exists('./cf');
        }

        return false;
    }

    /**
     * Determine if the given npm package is installed.
     *
     * @param {string} npmPackage
     */
    seesNpmPackage(npmPackage) {
        try {
            require.resolve(npmPackage);

            return true;
        } catch (e) {
            return false;
        }
    }

    /**
     * Queue up a new task.
     *
     * @param {Task} task
     */
    addTask(task) {
        this.tasks.push(task);
    }

    /**
     * Listen for the given event.
     *
     * @param {string|string}   event
     * @param {import('./Dispatcher').Handler} callback
     */
    listen(event, callback) {
        this.dispatcher.listen(event, callback);
    }
    /**
     * Dispatch the given event.
     *
     * @param {string} event
     * @param {any | (() => any)}      [data]
     */
    async dispatch(event, data) {
        if (typeof data === 'function') {
            data = data();
        }

        return this.dispatcher.fire(event, data);
    }
    /**
     * @param {string} name
     * @internal
     */
    resolve(name) {
        return this.resolver.get(name);
    }

     /**
     * @internal
     */
      makeCurrent() {
        // Set up some globals

        // @ts-ignore
        global.Config = this.config;

        // @ts-ignore
        global.CF = this;

        // @ts-ignore
        global.webpackConfig = this.webpackConfig;

        this.chunks.makeCurrent();

        return this;
    }
}

module.exports = CF;
