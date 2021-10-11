const { assertSupportedNodeVersion } = require('../src/Engine');

module.exports = async () => {
    assertSupportedNodeVersion();

    const cf = require('../src/CF').instance;

    require(cf.paths.cf());

    await cf.installDependencies();
    await cf.init();

    return cf.build();
};
