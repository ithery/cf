export const mergeOptions = (...args) => {
    let i, obj;
    let out = {};
    for (i = 0; i < args.length; i++) {
        obj = args[i];
        if (!obj) {
            continue;
        }
        for (var key in obj) {
            if (obj.hasOwnProperty(key)) {
                if (typeof obj[key] === 'object') {
                    mergeOptions(out[key], obj[key]);
                } else {
                    out[key] = obj[key];
                }
            }
        }
    }
    return out;
};
