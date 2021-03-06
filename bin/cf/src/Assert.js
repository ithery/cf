let assert = require('assert');
let Dependencies = require('./Dependencies');
let File = require('./File');

class Assert {
    /**
     * Assert that the call the cf.js() is valid.
     *
     * @param {*} entry
     * @param {*} output
     */
    static js(entry, output) {
        assert(
            typeof entry === 'string' || Array.isArray(entry),
            'cf.js() is missing required parameter 1: entry'
        );

        assert(
            typeof output === 'string',
            'cf.js() is missing required parameter 2: output'
        );
    }

    /**
     * Assert that the calls to cf.sass() and cf.less() are valid.
     *
     * @param {string} type
     * @param {string} src
     * @param {string} output
     */
    static preprocessor(type, src, output) {
        assert(
            typeof src === 'string',
            `cf.${type}() is missing required parameter 1: src`
        );

        assert(
            typeof output === 'string',
            `cf.${type}() is missing required parameter 2: output`
        );
    }

    /**
     * Assert that calls to cf.combine() are valid.
     *
     * @param {string} src
     * @param {File}   output
     */
    static combine(src, output) {
        assert(
            typeof src === 'string' || Array.isArray(src),
            `cf.combine() requires a valid src string or array.`
        );

        assert(
            output.isFile(),
            'cf.combine() requires a full output file path as the second argument. Got ' +
                output.path()
        );
    }

    /**
     * Assert that the given file exists.
     *
     * @param {string} file
     */
    static exists(file) {
        assert(
            File.exists(file),
            `Whoops, you are trying to compile ${file}, but that file does not exist.`
        );
    }

    /**
     * Assert that the necessary dependencies are available.
     *
     * @deprecated
     * @param {Array}  list
     * @param {Boolean} abortOnComplete
     */
    static dependencies(dependencies, abortOnComplete = false) {
        if (process.env.NODE_ENV === 'test') {
            return;
        }

        new Dependencies(dependencies).install(abortOnComplete);
    }
}

module.exports = Assert;
