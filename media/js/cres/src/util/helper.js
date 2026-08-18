

export const tap = (output, callback) => {
    callback(output);

    return output;
};
export const isJson = (str) => {
    try {
        JSON.parse(str);
    } catch (e) {
        return false;
    }
    return true;
};
export const isString = (str) => {
    return (str != null && typeof str.valueOf() === 'string');
};
export const isArray = (array) => {
    return Array.isArray(array);
};
export const isNumeric = (n) => {
    return !isNaN(parseFloat(n)) && isFinite(n);
}
export const isFunction = (func) => {
    return typeof func === 'function';
};
export const isPlainObject = (item) => {
    return item !== null && typeof item === 'object' && item.constructor === Object;
};
export const each = (objOrArray, callback) => {
    if (!objOrArray) {return;}
    if (isArray(objOrArray)) {
        objOrArray.forEach((val, index) => {
            callback(val, index, index);
        });
    } else {
        Object.entries(objOrArray).forEach(([key, val], index) => {
            callback(val, key, index);
        });
    }
};
export const map = (objOrArray, callback) => {
    let result = [];
    each(objOrArray, (val, key, index) => result.push(callback(val, key, index)));
    return result;
};
export const filter = (objOrArray, callback) => {
    let result = [];
    each(objOrArray, (val, key, index) => callback(val, key, index) && result.push(val));
    return result;
};
export const extend = (target, ...sources) => {
    const length = sources.length;
    if (length < 1 || target == null) {return target;}
    for (let i = 0; i < length; i++) {
        const source = sources[i];
        if (!isPlainObject(source)) {continue;}
        Object.keys(source).forEach((key) => {
            let desc = Object.getOwnPropertyDescriptor(source, key);
            if (desc.get || desc.set) {
                Object.defineProperty(target, key, desc);
            } else {
                target[key] = source[key];
            }
        });
    }
    return target;
};


export const deepMerge = (target, source) => {
    if (!source || typeof source !== 'object') {
        return target
    }
    if (!target || typeof target !== 'object') {
        target = Array.isArray(source) ? [] : {}
    }
    for (const key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
            const sourceValue = source[key]
            if (Array.isArray(sourceValue)) {
                target[key] = Array.isArray(target[key])
                    ? target[key].map((item, index) =>
                        sourceValue[index] ? deepMerge(item, sourceValue[index]) : item
                    ).concat(sourceValue.slice(target[key].length))
                    : [...sourceValue]
            } else if (sourceValue && typeof sourceValue === 'object') {
                target[key] = deepMerge(target[key], sourceValue);
            } else {
                target[key] = sourceValue
            }
        }
    }
    return target
}
