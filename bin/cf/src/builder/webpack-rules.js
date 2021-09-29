/**
 *
 * @param {import("../CF")} cf
 */
 module.exports = function (cf) {
    cf = cf || global.CF;
    let rules = [];

    // Add support for loading HTML files.
    rules.push({
        test: /\.html$/,
        resourceQuery: { not: [/\?vue/i] },
        use: [{ loader: cf.resolve('html-loader') }]
    });

    if (Config.imgLoaderOptions) {
        // Add support for loading images.
        rules.push({
            // only include svg that doesn't have font in the path or file name by using negative lookahead
            test: /(\.(png|jpe?g|gif|webp)$|^((?!font).)*\.svg$)/,
            use: [
                {
                    loader: cf.resolve('file-loader'),
                    options: {
                        name: path => {
                            if (!/node_modules|bower_components/.test(path)) {
                                return (
                                    cf.config.fileLoaderDirs.images +
                                    '/[name].[ext]?[hash]'
                                );
                            }

                            return (
                                cf.config.fileLoaderDirs.images +
                                '/vendor/' +
                                path
                                    .replace(/\\/g, '/')
                                    .replace(
                                        /((.*(node_modules|bower_components))|images|image|img|assets)\//g,
                                        ''
                                    ) +
                                '?[hash]'
                            );
                        },
                        publicPath: cf.config.resourceRoot
                    }
                },

                {
                    loader: cf.resolve('img-loader'),
                    options: cf.config.imgLoaderOptions
                }
            ]
        });
    }

    // Add support for loading fonts.
    rules.push({
        test: /(\.(woff2?|ttf|eot|otf)$|font.*\.svg$)/,
        use: [
            {
                loader: cf.resolve('file-loader'),
                options: {
                    name: path => {
                        if (!/node_modules|bower_components/.test(path)) {
                            return (
                                cf.config.fileLoaderDirs.fonts + '/[name].[ext]?[hash]'
                            );
                        }

                        return (
                            cf.config.fileLoaderDirs.fonts +
                            '/vendor/' +
                            path
                                .replace(/\\/g, '/')
                                .replace(
                                    /((.*(node_modules|bower_components))|fonts|font|assets)\//g,
                                    ''
                                ) +
                            '?[hash]'
                        );
                    },
                    publicPath: cf.config.resourceRoot
                }
            }
        ]
    });

    // Add support for loading cursor files.
    rules.push({
        test: /\.(cur|ani)$/,
        use: [
            {
                loader: cf.resolve('file-loader'),
                options: {
                    name: '[name].[ext]?[hash]',
                    publicPath: cf.config.resourceRoot
                }
            }
        ]
    });

    return rules;
};
