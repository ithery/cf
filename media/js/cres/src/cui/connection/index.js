import store from '@/cui/Store';
import componentStore from '../Store';
import { getCsrfToken } from '@/util';
import { showHtmlModal } from '../../util/window-util';

export default class Connection {
    onMessage(message, payload) {
        message.component.receiveMessage(message, payload);
    }

    onError(message, status) {
        message.component.messageSendFailed();

        return componentStore.onErrorCallback(status);
    }

    sendMessage(message) {
        let payload = message.payload();

        // eslint-disable-next-line no-underscore-dangle
        if (window.__testing_request_interceptor) {
            // eslint-disable-next-line no-underscore-dangle
            return window.__testing_request_interceptor(payload, this);
        }

        // Forward the query string for the ajax requests.
        fetch(
            `${window.capp.baseUrl}cresenity/component/message/${payload.fingerprint.name}`, {
                method: 'POST',
                body: JSON.stringify(payload),
                // This enables "cookies".
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'text/html, application/xhtml+xml',
                    'X-CSRF-TOKEN': getCsrfToken(),
                    'X-Socket-ID': this.getSocketId(),
                    'X-Cresenity': true,

                    // We'll set this explicitly to mitigate potential interference from ad-blockers/etc.
                    Referer: window.location.href
                }
            }
        )
            .then(response => {
                if (response.ok) {
                    response.text().then(response => {
                        if (this.isOutputFromDump(response)) {
                            this.onError(message);
                            showHtmlModal(response);
                        } else {
                            this.onMessage(message, JSON.parse(response));
                        }
                    });
                } else {
                    if (this.onError(message, response.status) === false) {return;}

                    if (response.status === 419) {
                        if (store.sessionHasExpired) {return;}

                        store.sessionHasExpired = true;

                        // eslint-disable-next-line no-alert
                        confirm(
                            'This page has expired due to inactivity.\nWould you like to refresh the page?'
                        ) && window.location.reload();
                    } else {
                        response.text().then(response => {
                            showHtmlModal(response);
                        });
                    }
                }
            })
            .catch(() => {
                this.onError(message);
            });
    }

    isOutputFromDump(output) {
        return !!output.match(/<script>Sfdump\(".+"\)<\/script>/);
    }

    getSocketId() {
        if (typeof window.Echo !== 'undefined') {
            return window.Echo.socketId();
        }
    }
}
