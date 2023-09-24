class Extend {
    /**
     * Register the component.
     *
     * @param {string} name
     * @param {Component} component
     */
    register(name, component) {
        if (typeof component !== 'function') {
            component.name = () => name;

            return CF.registrar.install(component);
        }

        CF.registrar.install({
            name: () => name,

            register(...args) {
                this.args = args;
            },

            webpackConfig(config) {
                component.call(this, config, ...this.args);
            }
        });
    }
}

module.exports = Extend;
