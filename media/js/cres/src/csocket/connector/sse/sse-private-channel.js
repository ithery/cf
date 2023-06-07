import request from '../../util/request';
import { authRequest } from '../../sse/channel-auth';

import SSEChannel from './sse-channel';
import { Channel } from '../../channel/Channel';

export default class SSEPrivateChannel extends SSEChannel {
    constructor(connection, name, options) {
        super(connection, name, options);
        this.authorized = false;
        this.afterAuthCallbacks = {};
        this.whisperCallbacks = new Map();
        this.auth = authRequest(name, connection, this.options.authEndpoint);
    }

    whisper(eventName, data) {
        request(this.connection).post(this.options.endpoint + '/whisper', { channel_name: this.name, event_name: eventName, data });

        return this;
    }

    listenForWhisper(event, callback) {
        let listener = function (data) {
            callback(Array.isArray(data) && data.length === 1 && typeof data[0] !== 'object' ? data[0] : data);
        };

        this.whisperCallbacks.set(callback, listener);

        super.listenForWhisper(event, listener);

        return this;
    }

    stopListeningForWhisper(event, callback) {
        if (callback) {
            callback = this.whisperCallbacks.get(callback);
            this.whisperCallbacks.delete(callback);
        }

        super.stopListeningForWhisper(event, callback);

        return this;
    }

    on(event, callback) {
        this.auth.after(() => super.on(event, callback));

        return this;
    }
}
