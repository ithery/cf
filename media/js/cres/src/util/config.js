/**
* Merge the DEFAULT_SETTINGS with the user defined options if specified
* @param {Object} options The user defined options
*/
export const mergeOptions = (initialOptions, customOptions) => {
    const merged = customOptions;
    for(const prop in initialOptions) {
        if(merged.hasOwnProperty(prop)) {
            if(initialOptions[prop] !== null && initialOptions[prop].constructor === Object) {
                merged[prop] = mergeOptions(initialOptions[prop], merged[prop]);
            }
        } else {
            merged[prop] = initialOptions[prop];
        }
    }
    return merged;
};
