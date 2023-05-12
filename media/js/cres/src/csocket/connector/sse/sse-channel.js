import { Channel } from '../../channel/Channel';
import { EventFormatter } from '../../util/EventFormatter';


export default class SSEChannel extends Channel {
    /**
     * Create a new class instance.
     */
    constructor(connection, name, options) {
        super();

        this.events = [];
        /**
         * The event callbacks applied to the channel.
         */
        this.name = name;
        this.connection = connection;
        this.options = options;
        this.eventFormatter = new EventFormatter(this.options.namespace);
    }

    /**
     * Listen for an event on the channel instance.
     */
    listen(event, callback) {
        this.on(this.eventFormatter.format(event), callback);

        return this;
    }
    /**
     * Stop listening for an event on the channel instance.
     */
    stopListening(event) {
        const name = this.eventFormatter.format(event);
        this.connection.unsubscribe(`${this.name}.${name}`);
        this.events = this.events.filter(e => e !== name);

        return this;
    }

    /**
     * Bind the channel's socket to an event and store the callback.
     */
    on(event, callback) {
        if (!this.events.find(e => e === event)) {
            this.events.push(event);
        }
        this.connection.subscribe(`${this.name}.${event}`, callback);

        return this;
    }

    unsubscribe() {
        this.events.forEach(event => {
            this.connection.unsubscribe(`${this.name}.${event}`);
        });

        this.events = [];
    }

    subscribed(callback) {
        return callback();
    }

    error(callback) {
        return callback();
    }
}
