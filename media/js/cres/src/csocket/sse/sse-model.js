import { EventSourceConnection } from './EventSourceConnection';
import { authRequest } from './channel-auth';


const authMethods = [
    'created',
    'updated',
    'deleted',
    'restored',
    'trashed',
    'stopListening',
    'notification',
    'stopListeningNotification'
];

export class WaveModel {
    /**
     *
     * @param {string} name
     * @param {string} key
     * @param {*} connection
     * @param {*} options
     * @returns
     */
    constructor(
        name,
        key,
        connection,
        options
    ) {
        this.name = name;
        this.key = key;
        this.connection = connection;
        this.options = options;
        this.auth = null;
        this.channel = '';
        this.callbackMap = new Map();
        this.notificationCallbacks = {};


        const channelName = `${this.options.namespace}.${this.name}.${this.key}`;

        this.auth = authRequest(channelName, connection, this.options.authEndpoint);

        this.channel = `private-${channelName}`;

        const eventHandlerProxy = new Proxy(this, {
            get: (target, prop, receiver) => {
                const value = Reflect.get(target, prop, receiver);

                if (authMethods.includes(prop)) {
                    return (...args) => {
                        this.auth.after(() => value.apply(this, args));

                        return eventHandlerProxy;
                    };
                }

                return value;
            }
        });

        const notificationsListener = (data) => {
            if (this.notificationCallbacks[data.type]) {
                this.notificationCallbacks[data.type].forEach((callback) => callback(data));
            }
        };

        this.connection.subscribe(`${this.channel}.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated`, notificationsListener);

        return eventHandlerProxy;
    }


    listenEvent(model, event, callback) {
        const eventName = typeof event === 'string'
            ? event[0].toUpperCase() + event.slice(1)
            : model[0].toUpperCase() + model.slice(1);

        const listener = (data) => {
            typeof event === 'function' ? event(data.model) : callback(data.model);
        };

        this.callbackMap.set(typeof event === 'string' ? callback : event, listener);

        const modelClass = typeof event === 'string' ? model : this.name;

        this.connection.subscribe(`${this.channel}.${modelClass}${eventName}`, listener);

        return this;
    }

    created(model, callback) {
        if (typeof model === 'function') {
            return this.listenEvent('created', model);
        }

        return this.listenEvent(model, 'created', callback);
    }

    updated(model, callback) {
        if (typeof model === 'function') {
            return this.listenEvent('updated', model);
        }

        return this.listenEvent(model, 'updated', callback);
    }

    deleted(model, callback) {
        if (typeof model === 'function') {
            return this.listenEvent('deleted', model);
        }

        return this.listenEvent(model, 'deleted', callback);
    }

    restored(model, callback) {
        if (typeof model === 'function') {
            return this.listenEvent('restored', model);
        }

        return this.listenEvent(model, 'restored', callback);
    }

    trashed(model, callback) {
        if (typeof model === 'function') {
            return this.listenEvent('trashed', model);
        }

        return this.listenEvent(model, 'trashed', callback);
    }

    /**
     *
     * @param {string} model
     * @param {string|Function} event
     * @param {Function} callback
     * @returns
     */
    stopListening(model, event, callback) {
        const eventName = typeof event !== 'function' ? event[0].toUpperCase() + event.slice(1) : model[0].toUpperCase() + model.slice(1);

        const modelClass = typeof event === 'function' ? this.name : model;
        this.connection.removeListener(`${this.channel}.${modelClass}${eventName}`, this.callbackMap.get(typeof event === 'function' ? event : callback));

        return this;
    }

    notification(type, callback) {
        if (!this.notificationCallbacks[type]) {
            this.notificationCallbacks[type] = new Set();
        }

        this.notificationCallbacks[type].add(callback);

        return this;
    }

    stopListeningNotification(type, callback) {
        this.notificationCallbacks[type].delete(callback);

        return this;
    }
}
