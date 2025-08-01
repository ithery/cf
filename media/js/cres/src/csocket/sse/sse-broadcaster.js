import { EventSourceConnection } from './EventSourceConnection';

import { SSEModel } from './sse-model';


export class SSEBroadcaster {
    constructor(options) {
        this.models = {};
        this.options = {
            endpoint: '/cresenity/sse',
            authEndpoint: '/cresenity/broadcast/auth',
            namespace: 'App.Models'
        };
        this.options = { ...this.options, ...options };
        this.connection = new EventSourceConnection();
        this.connection.create(this.options.endpoint);
    }

    /**
     *
     * @param {string} model
     * @param {string} key
     * @returns {SSEModel}
     */
    model(model, key) {
        const index = `${model}.${String(key)}`;

        if (!this.models[index]) {
            const { authEndpoint, namespace } = this.options;
            this.models[index] = new SSEModel(model, key, this.connection, { authEndpoint, namespace });
        }

        return this.models[index];
    }
}
