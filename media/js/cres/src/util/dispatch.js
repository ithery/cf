export function dispatch(eventName, params={}) {
    const event = new CustomEvent(eventName, {
        detail: params
    });

    window.dispatchEvent(event);

    return event;
}
