class ManifestPlugin {
    /**
     *
     * @param {import("../CF")} cf
     */
    constructor(cf) {
        this.cf = cf || global.CF;
    }

    /**
     * Apply the plugin.
     *
     * @param {import("webpack").Compiler} compiler
     */
    apply(compiler) {
        compiler.hooks.emit.tapAsync('ManifestPlugin', (curCompiler, callback) => {
            let stats = curCompiler.getStats().toJson();

            // Handle the creation of the cf-manifest.json file.
            this.cf.manifest.transform(stats).refresh();

            callback();
        });
    }
}

module.exports = ManifestPlugin;
