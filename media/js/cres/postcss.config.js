// eslint-disable-next-line no-unused-vars,no-process-env
const isProduction = process.env.NODE_ENV === 'production';


module.exports = {
    plugins: {
        tailwindcss: {},
        autoprefixer: {},
        'postcss-url':{ url: 'inline' },
        'postcss-inline-base64': {
            baseDir: 'src/',
        },
        'postcss-import': {
            path: __dirname,
        }
    }
};
