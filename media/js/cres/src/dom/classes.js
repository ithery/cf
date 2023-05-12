import stripAndCollapse from '../core/stripAndCollapse';
import rnothtmlwhite from '../core/var/rnothtmlwhite';
import { attr } from './attributes';


function classesToArray(value) {
    if (Array.isArray(value)) {
        return value;
    }
    if (typeof value === 'string') {
        return value.match(rnothtmlwhite) || [];
    }
    return [];
}
export const getClass = (elem) => {
    return elem.getAttribute && elem.getAttribute('class') || '';
};

export const addClass = (elem, value) => {
    let classNames, cur, curValue, className, i, finalValue;

    if (typeof value === 'function') {
        return addClass(elem.value.call());
    }

    classNames = classesToArray(value);

    if (classNames.length) {
        curValue = getClass(elem);
        cur = elem.nodeType === 1 && (' ' + stripAndCollapse(curValue) + ' ');

        if (cur) {
            for (i = 0; i < classNames.length; i++) {
                className = classNames[ i ];
                if (cur.indexOf(' ' + className + ' ') < 0) {
                    cur += className + ' ';
                }
            }

            // Only assign if different to avoid unneeded rendering.
            finalValue = stripAndCollapse(cur);
            if (curValue !== finalValue) {
                elem.setAttribute('class', finalValue);
            }
        }
    }

    return elem;
};
export const removeClass = (elem, value) => {
    let classNames, cur, curValue, className, i, finalValue;

    if (typeof value === 'function') {
        return removeClass(elem, value.call());
    }

    if (typeof value === 'undefined') {
        attr(elem, 'class', '');
        return elem;
    }

    classNames = classesToArray(value);

    if (classNames.length) {
        curValue = getClass(elem);

        // This expression is here for better compressibility (see addClass)
        cur = elem.nodeType === 1 && (' ' + stripAndCollapse(curValue) + ' ');

        if (cur) {
            for (i = 0; i < classNames.length; i++) {
                className = classNames[ i ];

                // Remove *all* instances
                while (cur.indexOf(' ' + className + ' ') > -1) {
                    cur = cur.replace(' ' + className + ' ', ' ');
                }
            }

            // Only assign if different to avoid unneeded rendering.
            finalValue = stripAndCollapse(cur);
            if (curValue !== finalValue) {
                elem.setAttribute('class', finalValue);
            }
        }
    }

    return elem;
};

export const hasClass = (elem, selector) => {
    const className = ' ' + selector + ' ';

    if (elem.nodeType === 1 &&
        (' ' + stripAndCollapse(getClass(elem)) + ' ').indexOf(className) > -1) {
        return true;
    }

    return false;
};
