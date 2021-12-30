/**
 * Performs right-to-left composition, combining multiple functions into a single function.
 * @function compose
 * @param {...Function} args
 * @returns {Function}
 */
export const compose = (...fns) => x => fns.reduceRight((output, fn) => {
    console.log(fn);
    return fn(output);
}, x, fns);

/**
  * Performs left-to-right composition, combining multiple functions into a single function. Sometimes called `sequence`. Right-to-left composition is typically known as `compose`.
  * @function pipe
  * @param {...Function} args
  * @returns {Function}
  */
export const pipe = (...fns) => x => fns.reduce((output, fn) => fn(output), x);

/** Runs the given function with the supplied object, then returns the object.
  * @function tap
  * @param {Function} f
  * @returns {*}
  */
export const tap = f => x => {
    f(x);
    return x;
};

/** Logs the given label and a provided object to the console, the returns the object.
  * @function trace
  * @param {String} label
  * @returns {Function}
  */
export const trace = label => tap(console.log.bind(console, label + ':' || ''));

export default {
    compose,
    pipe,
    tap,
    trace
};
