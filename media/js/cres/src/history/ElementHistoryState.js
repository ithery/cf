/**
 * Per-element helper for syncing a slice of UI state (e.g. the folder currently
 * open in a file manager, the tab currently active in a tab strip, ...) with
 * the browser's history, so back/forward works the way users expect without
 * a full page reload. Built on top of Cresenity's History.js port (see
 * ./core.js), which is already wired up in Cresenity.js#initHistory() and
 * dispatches a 'cresenity:history:statechange' window event on every
 * pushState/replaceState/popstate.
 *
 * Every instance is scoped to a `namespace` (e.g. `filemanager:${elementId}`)
 * so several elements on the same page can each keep their own history state
 * on the same entry without clobbering one another -- push()/replace() only
 * ever touch this namespace's key, merging over whatever the others already
 * stored there.
 *
 * Note this only covers same-page back/forward navigation. A hard page load
 * (deep link, refresh) never carries history.state with it, so the initial
 * value of whatever you're syncing still has to be read from the URL itself
 * (query string, path, ...) by the caller.
 *
 * @example
 * let history = new ElementHistoryState('filemanager:' + this.element.id);
 * history.onChange((state) => this.goTo(state ? state.path : this.initialPath, false));
 * // ... when the user navigates to a new folder:
 * history.push({path: newDir}, cresenity.url.addQueryString(location.href, 'path', newDir));
 */
export default class ElementHistoryState {
    /**
     * @param {string} namespace unique key for this element instance
     */
    constructor(namespace) {
        this.namespace = namespace;
        this.listeners = [];

        window.addEventListener('cresenity:history:statechange', () => {
            let state = this.getState();

            this.listeners.forEach((callback) => callback(state));
        });
    }

    /**
     * The state this namespace last pushed/replaced onto the current history
     * entry, or `undefined` if this namespace hasn't written to it (e.g. the
     * page just loaded, or the user navigated back past its first entry).
     *
     * @return {object|undefined}
     */
    getState() {
        return window.History.getState().data[this.namespace];
    }

    /**
     * Adds a new browser history entry (i.e. a new back-button stop).
     *
     * @param {object} state arbitrary serializable data for this namespace
     * @param {string} [url] full url to navigate to, defaults to the current url
     */
    push(state, url) {
        this.write('pushState', state, url);
    }

    /**
     * Updates the current browser history entry in place, without adding a
     * new back-button stop.
     *
     * @param {object} state
     * @param {string} [url]
     */
    replace(state, url) {
        this.write('replaceState', state, url);
    }

    /**
     * @param {function(object|undefined): void} callback invoked with this
     *        namespace's state whenever the user navigates back/forward
     */
    onChange(callback) {
        this.listeners.push(callback);
    }

    write(method, state, url) {
        let data = Object.assign({}, window.History.getState().data, { [this.namespace]: state });

        window.History[method](data, null, url || window.location.href);
    }
}
