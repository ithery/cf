import SSEClient, { formatText } from './sse-client';
export class SSEManager {
    constructor(config) {
        this.defaultConfig = Object.assign(
            {
                format: formatText,
                sendCredentials: false
            },
            config
        );

        this.clients = null;
    }

    create(configOrURL) {
        let config;
        if (typeof configOrURL === 'object') {
            config = configOrURL;
        } else if (typeof configOrURL === 'string') {
            config = {
                url: configOrURL
            };
        } else {
            config = {};
        }

        const client = new SSEClient(Object.assign({}, this.defaultConfig, config));

        // If $clients is not null, then it's array that we should push this
        // client into for later cleanup in our mixin's beforeDestroy.
        if (this.clients !== null) {
            this.clients.push(client);
        }

        return client;
    }
}

export default SSEManager;
