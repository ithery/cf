import { Channel } from './../Channel';

/**
 * This class represents a null channel.
 */
export class NullChannel extends Channel {
    /**
     * Subscribe to a channel.
     */
    subscribe() {
        //
    }

    /**
     * Unsubscribe from a channel.
     */
    unsubscribe() {
        //
    }

    /**
     * Listen for an event on the channel instance.
     */
    listen(event, callback) {
        return this;
    }

    /**
     * Stop listening for an event on the channel instance.
     */
    stopListening(event, callback) {
        return this;
    }

    /**
     * Register a callback to be called anytime a subscription succeeds.
     */
    subscribed(callback) {
        return this;
    }

    /**
     * Register a callback to be called anytime an error occurs.
     */
    error(callback) {
        return this;
    }

    /**
     * Bind a channel to an event.
     */
    on(event, callback) {
        return this;
    }
}
