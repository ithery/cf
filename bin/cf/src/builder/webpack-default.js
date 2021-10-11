let path = require('path');
let TerserPlugin = require('terser-webpack-plugin');

/**
 *
 * @param {import("../CF")} cf
 */
module.exports = function (cf) {
    cf = cf || global.CF;
    return {
        context: cf.paths.root(),

        mode: cf.inProduction() ? 'production' : 'development',

        infrastructureLogging: cf.isWatching() ? { level: 'none' } : {},

        entry: {},

        output: {
            chunkFilename: '[name].[hash:5].js'
        },

        module: { rules: [] },

        plugins: [],

        resolve: {
            extensions: ['*', '.wasm', '.mjs', '.js', '.jsx', '.json'],
            roots: [path.resolve(cf.config.publicPath)]
        },

        stats: {
            preset: 'errors-warnings',
            performance: cf.inProduction()
        },

        performance: {
            hints: false
        },

        optimization: cf.inProduction()
            ? {
                  providedExports: true,
                  sideEffects: true,
                  usedExports: true,
                  minimizer: [new TerserPlugin(cf.config.terser)]
              }
            : {},

        devtool: cf.config.sourcemaps,

        devServer: {
            headers: {
                'Access-Control-Allow-Origin': '*'
            },
            static: path.resolve(cf.config.publicPath),
            historyApiFallback: true,
            compress: true,
            allowedHosts: 'auto'
        },

        watchOptions: {
            ignored: /node_modules/
        }
    };
};
