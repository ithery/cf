import SSEManager from './sse/sse-manager';
export default class SSE {
    constructor() {

    }
    listen(url, onMessage) {
        let es = new window.EventSource(url);
        es.addEventListener('message', function (e) {
            onMessage(e);
        }, false);
        es.addEventListener('error', event => {
            if (event.readyState == EventSource.CLOSED) {
                console.log('SSE Connection Closed.');
            }
        }, false);
    }
}
