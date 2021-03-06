let collect = require('collect.js');
let path = require('path');
let File = require('./File');

class Manifest {
    /**
     * Create a new Manifest instance.
     *
     * @param {string} name
     */
    constructor(name = 'cf-manifest.json') {
        this.manifest = {};
        this.name = name;
    }

    /**
     * Get the underlying manifest collection.
     */
    get(file = null) {
        if (file) {
            return path.posix.join(
                this.cf.config.publicPath,
                this.manifest[this.normalizePath(file)]
            );
        }

        return collect(this.manifest).sortKeys().all();
    }

    /**
     * Add the given path to the manifest file.
     *
     * @param {string} filePath
     */
    add(filePath) {
        filePath = this.normalizePath(filePath);

        let original = filePath.replace(/\?id=\w{20}/, '');

        this.manifest[original] = filePath;

        return this;
    }

    /**
     * Add a new hashed key to the manifest.
     *
     * @param {string} file
     */
    hash(file) {
        let hash = new File(path.join(this.cf.config.publicPath, file)).version();

        let filePath = this.normalizePath(file);

        this.manifest[filePath] = filePath + '?id=' + hash;

        return this;
    }

    /**
     * Transform the Webpack stats into the shape we need.
     *
     * @param {object} stats
     */
    transform(stats) {
        this.flattenAssets(stats).forEach(this.add.bind(this));

        return this;
    }

    /**
     * Refresh the cf-manifest.js file.
     */
    refresh() {
        File.find(this.path()).makeDirectories().write(this.manifest);
    }

    /**
     * Retrieve the JSON output from the manifest file.
     */
    read() {
        return JSON.parse(File.find(this.path()).read());
    }

    /**
     * Get the path to the manifest file.
     */
    path() {
        return path.join(this.cf.config.publicPath, this.name);
    }

    /**
     * Flatten the generated stats assets into an array.
     *
     * @param {Object} stats
     */
    flattenAssets(stats) {
        let assets = Object.assign({}, stats.assetsByChunkName);

        // If there's a temporary cf.js chunk, we can safely remove it.
        if (assets.cf) {
            assets.cf = collect(assets.cf).except('cf.js').all();
        }

        return (
            collect(assets)
                .flatten()
                // Don't add hot updates to manifest
                .filter(name => name.indexOf('hot-update') === -1)
                .all()
        );
    }

    /**
     * Prepare the provided path for processing.
     *
     * @param {string} filePath
     */
    normalizePath(filePath) {
        if (
            this.cf.config.publicPath &&
            filePath.startsWith(this.cf.config.publicPath)
        ) {
            filePath = filePath.substring(this.cf.config.publicPath.length);
        }
        filePath = filePath.replace(/\\/g, '/');

        if (!filePath.startsWith('/')) {
            filePath = '/' + filePath;
        }

        return filePath;
    }

    get cf() {
        return global.CF;
    }
}

module.exports = Manifest;
