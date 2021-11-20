import { EventFormatter } from '../../util';
import { Channel } from './../Channel';

/**
 * This class represents a Pusher channel.
 */
export class PusherChannel extends Channel {
    /**
     * Create a new class instance.
     */
    constructor(pusher, name, options) {
        super();

        this.name = name;
        this.pusher = pusher;
        this.options = options;
        this.eventFormatter = new EventFormatter(this.options.namespace);
        this.subscription = null;
        this.subscribe();
    }

    /**
     * Subscribe to a Pusher channel.
     */
    subscribe() {
        this.subscription = this.pusher.subscribe(this.name);
    }

    /**
     * Unsubscribe from a Pusher channel.
     */
    unsubscribe() {
        this.pusher.unsubscribe(this.name);
    }

    /**
     * Listen for an event on the channel instance.
     */
    listen(event, callback) {
        this.on(this.eventFormatter.format(event), callback);

        return this;
    }

    /**
     * Listen for all events on the channel instance.
     */
    listenToAll(callback) {
        this.subscription.bind_global((event, data) => {
            if (event.startsWith('pusher:')) {
                return;
            }

            let namespace = this.options.namespace.replace(/\./g, '\\');

            let formattedEvent = event.startsWith(namespace) ? event.substring(namespace.length + 1) : '.' + event;

            callback(formattedEvent, data);
        });

        return this;
    }

    /**
     * Stop listening for an event on the channel instance.
     */
    stopListening(event, callback) {
        if (callback) {
            this.subscription.unbind(this.eventFormatter.format(event), callback);
        } else {
            this.subscription.unbind(this.eventFormatter.format(event));
        }

        return this;
    }

    /**
     * Stop listening for all events on the channel instance.
     */
    stopListeningToAll(callback) {
        if (callback) {
            this.subscription.unbind_global(callback);
        } else {
            this.subscription.unbind_global();
        }

        return this;
    }

    /**
     * Register a callback to be called anytime a subscription succeeds.
     */
    subscribed(callback) {
        this.on('pusher:subscription_succeeded', () => {
            callback();
        });

        return this;
    }

    /**
     * Register a callback to be called anytime a subscription error occurs.
     */
    error(callback) {
        this.on('pusher:subscription_error', (status) => {
            callback(status);
        });

        return this;
    }

    /**
     * Bind a channel to an event.
     */
    on(event, callback) {
        this.subscription.bind(event, callback);

        return this;
    }
}
