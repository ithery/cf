/**
 * @returns {Object}
 */
function defaultFadeConfig() {
    return {
        easing: 'linear',
        iterations: 1,
        direction: 'normal',
        fill: 'forwards',
        delay: 0,
        endDelay: 0
    };
}
/**
 * @param {HTMLElement} el
 * @param {number} durationInMs
 * @param {Object} config
 * @returns {Promise}
 */
export const fadeOut = async (el, durationInMs = 1000, config = defaultFadeConfig()) => {
    return new Promise((resolve, reject) => {
        const animation = el.animate(
            [
                { opacity: '1' },
                { opacity: '0.5', offset: 0.5 },
                { opacity: '0', offset: 1 }
            ],
            { duration: durationInMs, ...config }
        );
        animation.onfinish = () => resolve();
    });
}

/**
 * @param {HTMLElement} el
 * @param {number} durationInMs
 * @param {Object} config
 * @returns {Promise}
 */
export const fadeIn = async (el, durationInMs = 1000, config = defaultFadeConfig()) => {
    return new Promise((resolve) => {
        const animation = el.animate(
            [
                { opacity: '0' },
                { opacity: '0.5', offset: 0.5 },
                { opacity: '1', offset: 1 }
            ],
            { duration: durationInMs, ...config }
        );
        animation.onfinish = () => resolve();
    });
}
