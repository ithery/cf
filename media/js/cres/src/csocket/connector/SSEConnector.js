import { Connector } from './Connector';

import { EventSourceConnection } from '../sse/EventSourceConnection';

import SSEChannel from './sse/sse-channel';
import SSEPrivateChannel from './sse/sse-private-channel';
import SSEPresenceChannel from './sse/sse-presence-channel';

export class SSEConnector extends Connector {
    constructor(options) {
        super({ endpoint: '/cresenity/sse', ...options });
        this.connection = null;
        this.channels = [];
    }

    connect() {
        this.connection = new EventSourceConnection();
        this.connection.create(this.options.endpoint);
    }

    channel(channel) {
        if (!this.channels[channel]) {
            this.channels[channel] = new SSEChannel(this.connection, channel, this.options);
        }

        return this.channels[channel];
    }

    presenceChannel(channel) {
        if (!this.channels['presence-' + channel]) {
            this.channels['presence-' + channel] = new SSEPresenceChannel(this.connection, 'presence-' + channel, this.options);
        }

        return this.channels['presence-' + channel];
    }

    privateChannel(channel) {
        if (!this.channels['private-' + channel]) {
            this.channels['private-' + channel] = new SSEPrivateChannel(this.connection, 'private-' + channel, this.options);
        }

        return this.channels['private-' + channel];
    }

    disconnect() {
        this.connection.disconnect();
    }

    leave(channel) {
        let channels = [channel, 'private-' + channel, 'presence-' + channel];

        channels.forEach((name) => {
            this.leaveChannel(name);
        });
    }

    /**
     * Leave the given channel.
     */
    leaveChannel(channel) {
        if (this.channels[channel]) {
            this.channels[channel].unsubscribe();

            delete this.channels[channel];
        }
    }

    socketId() {
        return this.connection.getId();
    }
}
