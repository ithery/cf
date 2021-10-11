class When {
    register(condition, callback) {
        if (condition) {
            callback(CF.api);
        }
    }
}

module.exports = When;
