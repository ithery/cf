/**
 * Convert string from camel/snake case to kebab case. Borrowed from Alpine.js utils.
 *
 * @param {string} subject - subject to kebab
 * @returns {string}
 */
function kebabCase(subject) {
    return subject
        .replace(/([a-z])([A-Z])/g, '$1-$2')
        .replace(/[_\s]/, '-')
        .toLowerCase();
}

export function stylex(styles) {
    if (!styles || typeof styles !== 'object') {
        return '';
    }

    if (Array.isArray(styles)) {
        return styles.join(';') + ';';
    }

    let styleNames = '';
    for (const key of Object.keys(styles)) {
        const value = styles[key];
        const cssPropertyName = kebabCase(key);
        if (typeof value === 'string') {
            styleNames += `${cssPropertyName}:${value};`;
            continue;
        }

        if (typeof value === 'boolean') {
            styleNames += cssPropertyName;
            continue;
        }

        if (typeof value !== 'object' || value.length === 0) {
            continue;
        }

        const conditions = styles[key];

        for (const value of Object.keys(conditions)) {
            if (
                (typeof conditions[value] !== 'function' &&
                    conditions[value]) ||
                (typeof conditions[value] === 'function' && conditions[value]())
            ) {
                styleNames += `${cssPropertyName}:${value};`;
                break;
            }
        }
    }

    return styleNames;
}

export default stylex;
