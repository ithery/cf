class Override {
    register(callback) {
        CF.listen('configReadyForUser', callback);

        return this;
    }
}

module.exports = Override;
