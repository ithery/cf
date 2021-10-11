class Then {
    /**
     * The API name for the component.
     */
    name() {
        return ['then', 'after'];
    }

    register(callback) {
        CF.listen('build', callback);

        return this;
    }
}

module.exports = Then;
