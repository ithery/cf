export default class Shimmer {
    /**
     * Constructor
     *
     * @param {HTMLElement} className
     * @param {Object} object
     */
    constructor(className, config = {}) {

        // all html elements
        const elements = className instanceof Element ? [className] : [].slice.call(document.querySelectorAll(className));
    }
}
