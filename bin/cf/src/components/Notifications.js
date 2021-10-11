let AutomaticComponent = require('./AutomaticComponent');

class Notifications extends AutomaticComponent {
    /**
     * webpack plugins to be appended to the master config.
     */
    webpackPlugins() {
        if (CF.isUsing('notifications')) {
            let WebpackNotifierPlugin = require('webpack-notifier');

            return new WebpackNotifierPlugin({
                appID: 'CF NPM',

                title: 'CF NPM',
                alwaysNotify: Config.notifications.onSuccess,
                timeout: false,
                hint: process.platform === 'linux' ? 'int:transient:1' : undefined,
                contentImage: CF.paths.root('media/img/favico.png')
            });
        }
    }
}

module.exports = Notifications;
