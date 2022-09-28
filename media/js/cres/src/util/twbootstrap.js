export const getVersion = function (pluginName) {
    if (!$) throw new Error('bootstrap-version-detect needs a jQuery instance');
    pluginName = pluginName || 'modal';
    var pluginFn = $.fn[pluginName];
    if (pluginFn) {
        if (pluginFn.VERSION) {
            return pluginFn.VERSION;
        }
        if (pluginName === 'modal') {
            // Bootstrap 2 doesn't use namespace on modal data (at least for now...)
            return pluginFn.toString().indexOf('bs.modal') === -1 ? '2.x' : '3.x';
        }
    }
    return '';
};

export const isVersion = function (version, pluginName) {
    version = typeof version === 'string' ? version : version + ''; // coerce to string
    var detectedVersion = bootstrapVersionDetect.getVersion(pluginName);
    return !! ( detectedVersion && ( detectedVersion.indexOf(version) === 0 ) );
};
