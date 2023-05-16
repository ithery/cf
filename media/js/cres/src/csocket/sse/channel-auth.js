import request from '../util/request';

import { EventSourceConnection } from './EventSourceConnection';


/**
 *
 * @param {string} channel
 * @param {EventSourceConnection} connection
 * @param {string} authEndpoint
 * @returns {{after: after, response: Promise<any>}
 */
export function authRequest(channel, connection, authEndpoint = '/cresenity/broadcast/auth') {
    let authorized = false;
    let afterAuthCallbacks = [];

    function after(callback) {
        if (authorized) {
            callback();

            return;
        }

        afterAuthCallbacks.push(callback);

        return this;
    }

    const response = request(connection).post(authEndpoint, { channel_name: channel }).then((response) => {
        authorized = true;

        afterAuthCallbacks.forEach((callback) => callback(response));

        afterAuthCallbacks = [];
    });

    return {
        after,
        response
    };
}
