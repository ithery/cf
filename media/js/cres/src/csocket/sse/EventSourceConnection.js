export class EventSourceConnection {
    constructor() {
        this.id = '';
        this.source = null;
        this.listeners = {};
        this.afterConnectCallbacks = [];
        this.reconnecting = false;
    }

    create(endpoint) {
        this.source = new EventSource(endpoint);

        this.source.addEventListener('connected', (event) => {
            this.id = event.data;

            if (!this.reconnecting) {
                this.afterConnectCallbacks.forEach(callback => callback(this.id));
            }

            this.reconnecting = false;

            console.log('SSE connected.');
        });

        this.source.addEventListener('error', (event) => {
            switch (event.target.readyState) {
                case EventSource.CONNECTING:
                    this.reconnecting = true;
                    console.log('SSE reconnecting...');
                    break;

                case EventSource.CLOSED:
                    console.log('SSE connection closed');
                    this.create(endpoint);
                    this.resubscribe();
                    break;
                default:
                    break;
            }
        }, false);
    }

    getId() {
        return this.id;
    }

    resubscribe() {
        for (let event in this.listeners) {
            this.listeners[event].forEach(listener => {
                this.source.addEventListener(event, listener);
            });
        }
    }

    /**
     *
     * @param {string} event
     * @param {Function} callback
     */
    subscribe(event, callback) {
        let listener = function (event) {
            callback(JSON.parse(event.data).data);
        };

        if (!this.listeners[event]) {
            this.listeners[event] = new Map();
        }

        this.listeners[event].set(callback, listener);

        this.source.addEventListener(event, listener);
    }

    /**
     *
     * @param {string} event
     */
    unsubscribe(event) {
        this.listeners[event].forEach(listener => {
            this.source.removeEventListener(event, listener);
        });

        delete this.listeners[event];
    }

    /**
     *
     * @param {string} event
     * @param {Function} callback
     * @returns
     */
    removeListener(event, callback) {
        if (!this.listeners[event] || !this.listeners[event].has(callback)) {
            return;
        }

        this.source.removeEventListener(event, this.listeners[event].get(callback));

        this.listeners[event].delete(callback);

        if (this.listeners[event].size === 0) {
            delete this.listeners[event];
        }
    }

    disconnect() {
        this.source.close();
    }
    /**
     *
     * @param {(connectionId) => void} callback
     */
    afterConnect(callback) {
        this.afterConnectCallbacks.push(callback);
    }
}
