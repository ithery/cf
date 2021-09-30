class Before {
    /**
     * Register the component.
     *
     * @param  {Function} callback
     * @return {void}
     */
    register(callback) {
        CF.listen('init', callback);
    }
}

module.exports = Before;
