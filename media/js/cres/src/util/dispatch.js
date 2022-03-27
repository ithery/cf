export function dispatch(eventName, params={}) {
    const event = new CustomEvent(eventName, params);

    window.dispatchEvent(event);

    return event;
}
