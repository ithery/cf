import { NativeEventSource, EventSourcePolyfill } from 'event-source-polyfill';


if (typeof window.EventSource === 'undefined') {
    window.EventSource = NativeEventSource || EventSourcePolyfill;
}
