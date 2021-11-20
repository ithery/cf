import { Connector } from './Connector';
import { NullChannel, NullPrivateChannel, NullPresenceChannel } from './../channel';

/**
 * This class creates a null connector.
 */
export class NullConnector extends Connector {
    constructor(options) {
        super(options);

        this.channels = {};
    }
    /**
     * Create a fresh connection.
     */
    connect() {
        //
    }

    /**
     * Listen for an event on a channel instance.
     */
    listen(name, event, callback) {
        return new NullChannel();
    }

    /**
     * Get a channel instance by name.
     */
    channel(name) {
        return new NullChannel();
    }

    /**
     * Get a private channel instance by name.
     */
    privateChannel(name) {
        return new NullPrivateChannel();
    }

    /**
     * Get a presence channel instance by name.
     */
    presenceChannel(name) {
        return new NullPresenceChannel();
    }

    /**
     * Leave the given channel, as well as its private and presence variants.
     */
    leave(name) {
        //
    }

    /**
     * Leave the given channel.
     */
    leaveChannel(name) {
        //
    }

    /**
     * Get the socket ID for the connection.
     */
    socketId() {
        return 'fake-socket-id';
    }

    /**
     * Disconnect the connection.
     */
    disconnect() {
        //
    }
}
