import md5 from 'md5';
import fs from 'fs-extra';
import babel from 'rollup-plugin-babel';
import alias from '@rollup/plugin-alias';
import filesize from 'rollup-plugin-filesize';
import { terser } from 'rollup-plugin-terser';
import commonjs from '@rollup/plugin-commonjs';
import resolve from 'rollup-plugin-node-resolve';
import outputManifest from 'rollup-plugin-output-manifest';
import postcss from 'rollup-plugin-postcss';
import nodePolyfills from 'rollup-plugin-polyfill-node';
import serve from 'rollup-plugin-serve';
import livereload from 'rollup-plugin-livereload';
// eslint-disable-next-line no-process-env
const isProduction = process.env.NODE_ENV === 'production';

export default {
    input: 'src/index.js',
    output: {
        format: 'umd',
        sourcemap: true,
        name: 'Cresenity',
        file: isProduction ? 'dist/cres.js' : 'public/cres.js'
    },

    onwarn(warning, warn) {
        // suppress eval warnings
        if (warning.code === 'EVAL') {return;}
        // suppress circular dependency warnings
        if (warning.code === 'CIRCULAR_DEPENDENCY') {return;}
        warn(warning);
    },
    plugins: [

        resolve(),
        commonjs({
            // These npm packages still use common-js modules. Ugh.
            include: /node_modules\/(get-value|isobject|core-js|locutus|pusher-js|event-source-polyfill)/
        }),
        postcss({
            config: {
                path: './postcss.config.js'
            },
            extensions: ['.css'],
            extract: true,
            minimize: isProduction
            // modules: true,
        }),
        filesize(),
        isProduction && terser({
            mangle: false,
            compress: {
                // eslint-disable-next-line camelcase
                drop_debugger: false
            }
        }),
        babel({
            exclude: 'node_modules/**'
        }),
        alias({
            entries: [
                { find: '@', replacement: __dirname + '/src' }
            ]
        }),
        // Mimic Laravel Mix's mix-manifest file for auto-cache-busting.
        outputManifest({
            serialize() {
                const file = fs.readFileSync(__dirname + '/dist/cres.js', 'utf8');
                const hash = md5(file).substr(0, 20);

                return JSON.stringify({
                    '/cres.js': '/cres.js?id=' + hash
                });
            }
        }),
        nodePolyfills(),
        !isProduction && serve({
            contentBase: 'public'
        }),
        !isProduction && livereload({
            watch: 'public',
            port: 12345
        })
    ]
};
