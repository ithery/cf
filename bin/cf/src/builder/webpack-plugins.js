let CFDefinitionsPlugin = require('../webpackPlugins/CFDefinitionsPlugin');
let BuildCallbackPlugin = require('../webpackPlugins/BuildCallbackPlugin');
let CustomTasksPlugin = require('../webpackPlugins/CustomTasksPlugin');
let ManifestPlugin = require('../webpackPlugins/ManifestPlugin');
let MockEntryPlugin = require('../webpackPlugins/MockEntryPlugin');
let BuildOutputPlugin = require('../webpackPlugins/BuildOutputPlugin');
let WebpackBar = require('webpackbar');

/**
 *
 * @param {import("../CF")} cf
 */
module.exports = function (cf) {
    cf = cf || global.CF;

    let plugins = [];

    // If the user didn't declare any JS compilation, we still need to
    // use a temporary script to force a compile. This plugin will
    // handle the process of deleting the compiled script.
    if (!cf.bundlingJavaScript) {
        plugins.push(new MockEntryPlugin(cf));
    }

    // Activate support for CF_ .env definitions.
    plugins.push(
        new CFDefinitionsPlugin(cf.paths.root('.env'), {
            NODE_ENV: cf.inProduction()
                ? 'production'
                : process.env.NODE_ENV || 'development'
        })
    );

    // Handle the creation of the cf-manifest.json file.
    plugins.push(new ManifestPlugin(cf));

    // Handle all custom, non-webpack tasks.
    plugins.push(new CustomTasksPlugin(cf));

    // Notify the rest of our app when Webpack has finished its build.
    plugins.push(new BuildCallbackPlugin(stats => cf.dispatch('build', stats)));

    // Enable custom output when the Webpack build completes.
    plugins.push(
        new BuildOutputPlugin({
            clearConsole: cf.config.clearConsole,
            showRelated: true
        })
    );

    if (process.env.NODE_ENV !== 'test') {
        plugins.push(new WebpackBar({ name: 'CF' }));
    }

    return plugins;
};
