/**
 * Returns a promise that resolves when an element with a selector appears on the page for the first time.
 * Note: Use elementReadyRAF if this is too slow or unreliable.
 * @param {string} selector querySelector string
 * @return {Promise} promise onready
 */
export const elementReady = (selector) => {
    // eslint-disable-next-line no-unused-vars
    return new Promise((resolve, reject) => {
        // eslint-disable-next-line no-unused-vars
        const observer = new MutationObserver((mutations) => {
            const elements = document.querySelectorAll(selector);
            elements.forEach((element) => {
                if (!element.ready) {
                    element.ready = true;
                    observer.disconnect();
                    resolve(element);
                }
            });
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    });
};

/**
 * Calls the callback function whenever an element with the selector gets rendered
 * @param {string} selector querySelector string
 * @param {function} callback function to fire when an element gets rendered
 * @return {MutationObserver} the object that checks for the elements
 */
export const elementRendered = (selector, callback) => {
    const renderedElements = [];
    // eslint-disable-next-line no-unused-vars
    const observer = new MutationObserver((mutations) => {
        const elements = document.querySelectorAll(selector);
        elements.forEach((element) => {
            if (!renderedElements.includes(element)) {
                renderedElements.push(element);
                callback(element);
            }
        });
    });
    observer.observe(document.documentElement, { childList: true, subtree: true });
    return observer;
};
