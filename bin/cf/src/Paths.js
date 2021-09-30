let path = require('path');

class Paths {
    /**
     * Create a new Paths instance.
     */
    constructor() {
        // TODO: Refactor setup to allow removing this check
        if (process.env.NODE_ENV === 'test') {
            this.rootPath = path.resolve(__dirname, '../');
        } else {
            this.rootPath = process.cwd();
        }
        this.cresenityPath = process.cwd() + '/application/cresenity';
    }

    /**
     * Set the root path to resolve webpack.cf.js.
     *
     * @param {string} path
     */
    setRootPath(path) {
        this.rootPath = path;
        return this;
    }

    /**
     * Determine the path to the user's webpack.cf.js file.
     */
    cf() {
        const path = this.app(
            process.env && process.env.MIX_FILE ? process.env.MIX_FILE : 'webpack.cf'
        );

        try {
            require.resolve(`${path}.cjs`);

            return `${path}.cjs`;
        } catch (err) {
            //
        }

        return path;
    }

    /**
     * Determine the project root.
     *
     * @param {string|null} append
     */
    root(append = '') {
        return path.resolve(this.rootPath, append);
    }

    /**
     * Determine the project root.
     *
     * @param {string|null} append
     */
    app(append = '') {
        return path.resolve(this.cresenityPath, append);
    }
}

module.exports = Paths;
