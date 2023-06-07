import { EventSourcePolyfill } from 'event-source-polyfill';

export const formatText = (e) => e.data;

export const formatJSON = (e) => JSON.parse(e.data);

export default class SSEClient {
    constructor(config) {
        this.handlers = {};
        this.listeners = {};
        this.source = null;

        if (config.format) {
            if (typeof config.format === 'string') {
                if (config.format === 'plain') {
                    this.format = formatText;
                } else if (config.format === 'json') {
                    this.format = formatJSON;
                } else {
                    this.format = formatText;
                }
            } else if (typeof config.format === 'function') {
                this.format = config.format;
            } else {
                this.format = formatText;
            }
        } else {
            this.format = formatText;
        }

        if (config.handlers) {
            for (const event in config.handlers) {
                this.on(event, config.handlers[event]);
            }
        }

        this.url = config.url;
        this.withCredentials = !!config.withCredentials;
        this.polyfillOptions = config.polyfillOptions || {};
        this.forcePolyfill = !!config.forcePolyfill;
    }

    get source() {
        return this.source;
    }

    connect() {
        if (this.forcePolyfill) {
            this.source = new EventSourcePolyfill(
                this.url,
                Object.assign({}, this.polyfillOptions, {
                    withCredentials: this.withCredentials
                })
            );
        } else {
            this.source = new window.EventSource(this.url, {
                withCredentials: this.withCredentials
            });
        }

        return new Promise((resolve, reject) => {
            this.source.onopen = () => {
                // Add event listeners that were added before we connected
                for (let event in this.listeners) {
                    this.source.addEventListener(event, this.listeners[event]);
                }

                this.source.onerror = null;

                resolve(this);
            };

            this.source.onerror = reject;
        });
    }

    disconnect() {
        if (this.source !== null) {
            this.source.close();
            this.source = null;
        }
    }

    on(event, handler) {
        if (!event) {
            // Default "event-less" event
            event = 'message';
        }

        if (!this.listeners[event]) {
            this.create(event);
        }

        this.handlers[event].push(handler);

        return this;
    }

    once(event, handler) {
        this.on(event, (e) => {
            this.off(event, handler);

            handler(e);
        });

        return this;
    }

    off(event, handler) {
        if (!this.handlers[event]) {
            // no handlers registered for event
            return this;
        }

        const idx = this.handlers[event].indexOf(handler);
        if (idx === -1) {
            // handler not registered for event
            return this;
        }

        // remove handler from event
        this.handlers[event].splice(idx, 1);

        if (this.handlers[event].length === 0) {
            // remove listener since no handlers exist
            this.source.removeEventListener(event, this.listeners[event]);
            delete this.handlers[event];
            delete this.listeners[event];
        }

        return this;
    }

    create(event) {
        this.handlers[event] = [];

        this.listeners[event] = (message) => {
            let data;

            try {
                data = this.format(message);
            } catch (err) {
                if (typeof this.source.onerror === 'function') {
                    this.source.onerror(err);
                }
                return;
            }

            this.handlers[event].forEach((handler) => handler(data, message.lastEventId));
        };

        if (this.source) {
            this.source.addEventListener(event, this.listeners[event]);
        }
    }
}
