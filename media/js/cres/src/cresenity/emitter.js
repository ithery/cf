
const emitter = {
    all: {},
    on(type, handler) {
        (this.all[type] || (this.all[type] = [])).push(handler);
    },
    off(type, handler) {
        if (this.all[type]) {
            this.all[type].splice(this.all[type].indexOf(handler) >>> 0, 1);
        }
    },
    async emit(type, evt) {
        (this.all[type] || []).slice().map(async function (handler) {
            await handler(evt);
        });
        (this.all['*'] || []).slice().map(async function (handler) {
            await handler(type, evt);
        });
    }
};


export default emitter;
