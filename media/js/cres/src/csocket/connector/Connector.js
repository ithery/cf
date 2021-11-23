export class Connector {
    /**
     * Create a new class instance.
     */
    constructor(options) {
        this.options=null;
        /**
         * Default connector options.
         */
        this.defaultOptions = {
            auth: {
                headers: {}
            },
            authEndpoint: window.capp.baseUrl + 'cresenity/broadcast/auth',
            broadcaster: 'pusher',
            csrfToken: null,
            host: null,
            key: null,
            namespace: 'App.Events'
        };

        this.setOptions(options);
        this.connect();
    }

    /**
     * Merge the custom options with the defaults.
     */
    setOptions(options) {
        this.options = Object.assign(this.defaultOptions, options);

        if (this.csrfToken()) {
            this.options.auth.headers['X-CSRF-TOKEN'] = this.csrfToken();
        }

        return options;
    }

    /**
     * Extract the CSRF token from the page.
     */
    csrfToken() {
        let selector;

        if (typeof window !== 'undefined' && window.capp && window.capp.csrfToken) {
            return window.capp.csrfToken;
        } else if (this.options.csrfToken) {
            return this.options.csrfToken;
        } else if (
            typeof document !== 'undefined' &&
            typeof document.querySelector === 'function' &&
            (selector = document.querySelector('meta[name="csrf-token"]'))
        ) {
            return selector.getAttribute('content');
        }

        return null;
    }
}
