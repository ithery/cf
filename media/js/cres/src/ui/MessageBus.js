
export default class MessageBus {
    constructor() {
        this.listeners = {};
    }

    register(name, callback) {
        if (!this.listeners[name]) {
            this.listeners[name] = [];
        }

        this.listeners[name].push(callback);
    }

    unregister(name, callback) {
        if(!callback) {
            this.listeners[name] = [];
        }
        const index = this.listeners[name].indexOf(callback);
        if (index > -1) {
            this.listeners[name].splice(index, 1);
        }
    }

    call(name, ...params) {
        (this.listeners[name] || []).forEach(callback => {
            callback(...params);
        });
    }

    has(name) {
        return Object.keys(this.listeners).includes(name);
    }
}
