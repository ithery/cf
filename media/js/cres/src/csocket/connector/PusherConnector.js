import { Connector } from './Connector';
import {
    PusherChannel,
    PusherPrivateChannel,
    PusherEncryptedPrivateChannel,
    PusherPresenceChannel
} from './../channel';
/* global Pusher */

/**
 * This class creates a connector to Pusher.
 */
export class PusherConnector extends Connector {
    constructor(options) {
        super(options);
    }

    /**
     * Create a fresh Pusher connection.
     */
    connect() {
        this.channels = {};

        if (typeof this.options.client !== 'undefined') {
            this.pusher = this.options.client;
        } else {
            if(typeof Pusher === 'undefined') {
                throw new Error('Pusher is not defined');
            }
            this.pusher = new Pusher(this.options.key, this.options);
        }
    }

    /**
     * Listen for an event on a channel instance.
     */
    listen(name, event, callback) {
        return this.channel(name).listen(event, callback);
    }

    /**
     * Get a channel instance by name.
     */
    channel(name) {
        if (!this.channels[name]) {
            this.channels[name] = new PusherChannel(this.pusher, name, this.options);
        }

        return this.channels[name];
    }

    /**
     * Get a private channel instance by name.
     */
    privateChannel(name) {
        if (!this.channels['private-' + name]) {
            this.channels['private-' + name] = new PusherPrivateChannel(this.pusher, 'private-' + name, this.options);
        }

        return this.channels['private-' + name];
    }

    /**
     * Get a private encrypted channel instance by name.
     */
    encryptedPrivateChannel(name) {
        if (!this.channels['private-encrypted-' + name]) {
            this.channels['private-encrypted-' + name] = new PusherEncryptedPrivateChannel(
                this.pusher,
                'private-encrypted-' + name,
                this.options
            );
        }

        return this.channels['private-encrypted-' + name];
    }

    /**
     * Get a presence channel instance by name.
     */
    presenceChannel(name) {
        if (!this.channels['presence-' + name]) {
            this.channels['presence-' + name] = new PusherPresenceChannel(
                this.pusher,
                'presence-' + name,
                this.options
            );
        }

        return this.channels['presence-' + name];
    }

    /**
     * Leave the given channel, as well as its private and presence variants.
     */
    leave(name) {
        let channels = [name, 'private-' + name, 'presence-' + name];

        channels.forEach((name, index) => {
            this.leaveChannel(name);
        });
    }

    /**
     * Leave the given channel.
     */
    leaveChannel(name) {
        if (this.channels[name]) {
            this.channels[name].unsubscribe();

            delete this.channels[name];
        }
    }

    /**
     * Get the socket ID for the connection.
     */
    socketId() {
        if(this.pusher && this.pusher.connection) {
            return this.pusher.connection.socket_id;
        }
        return null;
    }

    /**
     * Disconnect Pusher connection.
     */
    disconnect() {
        this.pusher.disconnect();
    }
}
