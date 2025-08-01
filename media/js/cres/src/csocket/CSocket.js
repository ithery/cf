import { PusherConnector, NullConnector, SSEConnector } from './connector';
/* global Vue,axios */

/**
 * This class is the primary API for interacting with broadcasting.
 */
export default class CSocket {
    /**
     * Create a new class instance.
     */
    constructor(options) {
        this.connector = null;
        this.options = options;
        this.connect();

        if (!this.options.withoutInterceptors) {
            this.registerInterceptors();
        }
    }

    /**
     * Get a channel instance by name.
     */
    channel(channel) {
        return this.connector.channel(channel);
    }

    /**
     * Create a new connection.
     */
    connect() {
        if (this.options.broadcaster == 'pusher') {
            this.connector = new PusherConnector(this.options);
        } else if (this.options.broadcaster == 'sse') {
            this.connector = new SSEConnector(this.options);
        } else if (this.options.broadcaster == 'null') {
            this.connector = new NullConnector(this.options);
        } else if (typeof this.options.broadcaster == 'function') {
            this.connector = new this.options.broadcaster(this.options);
        }
    }

    /**
     * Disconnect from the Echo server.
     */
    disconnect() {
        this.connector.disconnect();
    }

    /**
     * Get a presence channel instance by name.
     */
    join(channel) {
        return this.connector.presenceChannel(channel);
    }

    /**
     * Leave the given channel, as well as its private and presence variants.
     */
    leave(channel) {
        this.connector.leave(channel);
    }

    /**
     * Leave the given channel.
     */
    leaveChannel(channel) {
        this.connector.leaveChannel(channel);
    }

    /**
     * Listen for an event on a channel instance.
     */
    listen(channel, event, callback) {
        return this.connector.listen(channel, event, callback);
    }

    /**
     * Get a private channel instance by name.
     */
    private(channel) {
        return this.connector.privateChannel(channel);
    }

    /**
     * Get a private encrypted channel instance by name.
     */
    encryptedPrivate(channel) {
        return this.connector.encryptedPrivateChannel(channel);
    }

    /**
     * Get the Socket ID for the connection.
     */
    socketId() {
        return this.connector.socketId();
    }

    /**
     * Register 3rd party request interceptiors. These are used to automatically
     * send a connections socket id to a Laravel app with a X-Socket-Id header.
     */
    registerInterceptors() {
        if (typeof Vue === 'function' && Vue.http) {
            this.registerVueRequestInterceptor();
        }

        if (typeof axios === 'function') {
            this.registerAxiosRequestInterceptor();
        }

        if (typeof jQuery === 'function') {
            this.registerjQueryAjaxSetup();
        }
    }

    /**
     * Register a Vue HTTP interceptor to add the X-Socket-ID header.
     */
    registerVueRequestInterceptor() {
        Vue.http.interceptors.push((request, next) => {
            if (this.socketId()) {
                request.headers.set('X-Socket-ID', this.socketId());
            }

            next();
        });
    }

    /**
     * Register an Axios HTTP interceptor to add the X-Socket-ID header.
     */
    registerAxiosRequestInterceptor() {
        axios.interceptors.request.use((config) => {
            if (this.socketId()) {
                config.headers['X-Socket-Id'] = this.socketId();
            }

            return config;
        });
    }

    /**
     * Register jQuery AjaxPrefilter to add the X-Socket-ID header.
     */
    registerjQueryAjaxSetup() {
        if (typeof jQuery.ajax != 'undefined') {
            jQuery.ajaxPrefilter((options, originalOptions, xhr) => {
                if (this.socketId()) {
                    xhr.setRequestHeader('X-Socket-Id', this.socketId());
                }
            });
        }
    }
}
