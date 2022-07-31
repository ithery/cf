import { hasClass } from "./classes";

/* DOM manipulation */
export const getChildrenByClassName = (el, className) => {
    var children = el.children,
        childrenByClass = [];
    for (var i = 0; i < children.length; i++) {
        if (hasClass(children[i], className)) {
            childrenByClass.push(children[i]);
        }
    }
    return childrenByClass;
};
