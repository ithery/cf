import request from '../../util/request';


import SSEPrivateChannel from './sse-private-channel';
import { authRequest } from '../../sse/channel-auth';


export default class SSEPresenceChannel extends SSEPrivateChannel {
    constructor(connection, name, options) {
        super(connection, name, options);
        this.joined = false;
        this.joinRequest = authRequest(name, connection, this.options.endpoint + '/presence-channel-users')
            .after(() => {
                this.joined = true;
                return this.joined;
            });

        if (typeof window !== 'undefined') {
            window.addEventListener('beforeunload', () => this.unsubscribe());
        }
    }

    here(callback) {
        if (this.joined) {
            request(this.connection)
                .get(this.options.endpoint + '/presence-channel-users', { channel_name: this.name })
                .then((users) => callback(users));

            return this;
        }

        this.joinRequest.after((users) => callback(users));

        return this;
    }

    /**
     * Listen for someone joining the channel.
     */
    joining(callback) {
        this.listen('.join', callback);

        return this;
    }

    /**
     * Listen for someone leaving the channel.
     */
    leaving(callback) {
        this.listen('.leave', callback);

        return this;
    }

    unsubscribe() {
        this.joinRequest.after(() => {
            request(this.connection).delete(this.options.endpoint + '/presence-channel-users', { channel_name: this.name });
            super.unsubscribe();
        });
    }
}
