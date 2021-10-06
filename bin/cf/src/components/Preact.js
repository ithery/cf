class Preact {
    /**
     * Required dependencies for the component.
     */
    dependencies() {
        return ['babel-preset-preact'];
    }

    register() {
        if (
            arguments.length === 2 &&
            typeof arguments[0] === 'string' &&
            typeof arguments[1] === 'string'
        ) {
            throw new Error(
                'cf.preact() is now a feature flag. Use cf.js(source, destination).preact() instead'
            );
        }
    }

    /**
     * Babel config to be merged with CF's defaults.
     */
    babelConfig() {
        return {
            presets: ['preact']
        };
    }
}

module.exports = Preact;
