import fnToString from './var/fnToString';
import hasOwn from './var/hasOwn';
import getProto from './var/getProto';
import ObjectFunctionString from './var/ObjectFunctionString';

const isPlainObject = (obj) => {
    let proto, Ctor;

    // Detect obvious negatives
    // Use toString instead of jQuery.type to catch host objects
    if (!obj || toString.call(obj) !== '[object Object]') {
        return false;
    }

    proto = getProto(obj);

    // Objects with no prototype (e.g., `Object.create( null )`) are plain
    if (!proto) {
        return true;
    }

    // Objects with prototype are plain iff they were constructed by a global Object function
    Ctor = hasOwn.call(proto, 'constructor') && proto.constructor;
    return typeof Ctor === 'function' && fnToString.call(Ctor) === ObjectFunctionString;
};

export default isPlainObject;
