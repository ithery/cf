export class CresQuery {
    static eventListeners = {};
    static generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(
            /[xy]/g,
            function (c) {
                const r = (Math.random() * 16) | 0,
                    v = c == 'x' ? r : (r & 0x3) | 0x8;
                return v.toString(16);
            },
        );
    }

    constructor(selector) {
        this.selector = this._getSelector(selector);
        this.firstElement = this._getFirstEl();
        this.cssVenderPrefixes = [
            'TransitionDuration',
            'TransitionTimingFunction',
            'Transform',
            'Transition',
        ];
        return this;
    }
    _getSelector(selector,context) {
        if (typeof selector !== 'string') {
            return selector;
        }
        context = context || document;
        const fl = selector.substring(0, 1);
        if (fl === '#') {
            return context.querySelector(selector);
        } else {
            return context.querySelectorAll(selector);
        }
    }
    _each(func) {
        if (!this.selector) {
            return this;
        }
        if (this.selector.length !== undefined) {
            [].forEach.call(this.selector, func);
        } else {
            func(this.selector, 0);
        }
        return this;
    }
    _setCssVendorPrefix(el,cssProperty,value) {
        // prettier-ignore
        const property = cssProperty.replace(/-([a-z])/gi, function (
            s,
            group1,
        ) {
            return group1.toUpperCase();
        });
        if (this.cssVenderPrefixes.indexOf(property) !== -1) {
            el.style[
                property.charAt(0).toLowerCase() + property.slice(1)
            ] = value;
            el.style['webkit' + property] = value;
            el.style['moz' + property] = value;
            el.style['ms' + property] = value;
            el.style['o' + property] = value;
        } else {
            el.style[property] = value;
        }
    }

    _getFirstEl() {
        if (this.selector && this.selector.length !== undefined) {
            return this.selector[0];
        } else {
            return this.selector;
        }
    }

    isEventMatched(event, eventName) {
        const eventNamespace = eventName.split('.');
        return event
            .split('.')
            .filter((e) => e)
            .every((e) => {
                return eventNamespace.indexOf(e) !== -1;
            });
    }
    attr(attr, value) {
        if (value === undefined) {
            if (!this.firstElement) {
                return '';
            }
            return this.firstElement.getAttribute(attr);
        }
        this._each((el) => {
            el.setAttribute(attr, value);
        });
        return this;
    }
    find(selector) {
        return cresQuery(this._getSelector(selector, this.selector));
    }
    first() {
        if (this.selector && this.selector.length !== undefined) {
            return cresQuery(this.selector[0]);
        } else {
            return cresQuery(this.selector);
        }
    }
    eq(index) {
        return cresQuery(this.selector[index]);
    }
    parent() {
        return cresQuery(this.selector.parentElement);
    }
    get() {
        return this._getFirstEl();
    }
    removeAttr(attributes) {
        const attrs = attributes.split(' ');
        this._each((el) => {
            attrs.forEach((attr) => el.removeAttribute(attr));
        });
        return this;
    }

    wrap(className) {
        if (!this.firstElement) {
            return this;
        }
        const wrapper = document.createElement('div');
        wrapper.className = className;
        this.firstElement.parentNode.insertBefore(wrapper, this.firstElement);
        this.firstElement.parentNode.removeChild(this.firstElement);
        wrapper.appendChild(this.firstElement);
        return this;
    }

    addClass(classNames = '') {
        this._each((el) => {
            // IE doesn't support multiple arguments
            classNames.split(' ').forEach((className) => {
                if (className) {
                    el.classList.add(className);
                }
            });
        });
        return this;
    }


    removeClass(classNames) {
        this._each((el) => {
            // IE doesn't support multiple arguments
            classNames.split(' ').forEach((className) => {
                if (className) {
                    el.classList.remove(className);
                }
            });
        });
        return this;
    }
    hasClass(className) {
        if (!this.firstElement) {
            return false;
        }
        return this.firstElement.classList.contains(className);
    }
    hasAttribute(attribute) {
        if (!this.firstElement) {
            return false;
        }
        return this.firstElement.hasAttribute(attribute);
    }
    toggleClass(className) {
        if (!this.firstElement) {
            return this;
        }
        if (this.hasClass(className)) {
            this.removeClass(className);
        } else {
            this.addClass(className);
        }
        return this;
    }

    css(property, value) {
        this._each((el) => {
            this._setCssVendorPrefix(el, property, value);
        });
        return this;
    }
      // Need to pass separate namespaces for separate elements
    on(events, listener) {
        if (!this.selector) {
            return this;
        }
        events.split(' ').forEach((event) => {
            if (!Array.isArray(CresQuery.eventListeners[event])) {
                CresQuery.eventListeners[event] = [];
            }
            CresQuery.eventListeners[event].push(listener);
            this.selector.addEventListener(event.split('.')[0], listener);
        });

        return this;
    }
    once(event, listener) {
        this.on(event, () => {
            this.off(event);
            listener(event);
        });
        return this;
    }
    off(event) {
        if (!this.selector) {
            return this;
        }
        Object.keys(CresQuery.eventListeners).forEach((eventName) => {
            if (this.isEventMatched(event, eventName)) {
                CresQuery.eventListeners[eventName].forEach((listener) => {
                    this.selector.removeEventListener(
                        eventName.split('.')[0],
                        listener,
                    );
                });
                CresQuery.eventListeners[eventName] = [];
            }
        });

        return this;
    }

    trigger(event, detail) {
        if (!this.firstElement) {
            return this;
        }

        const customEvent = new CustomEvent(event.split('.')[0], {
            detail: detail || null,
        });
        this.firstElement.dispatchEvent(customEvent);
        return this;
    }
    // Does not support IE
    load(url) {
        fetch(url)
            .then((res) => res.text())
            .then((html) => {
                this.selector.innerHTML = html;
            });
        return this;
    }
    html(html) {
        if (html === undefined) {
            if (!this.firstElement) {
                return '';
            }
            return this.firstElement.innerHTML;
        }
        this._each((el) => {
            el.innerHTML = html;
        });
        return this;
    }
    append(html) {
        this._each((el) => {
            if (typeof html === 'string') {
                el.insertAdjacentHTML('beforeend', html);
            } else {
                el.appendChild(html);
            }
        });
        return this;
    }
    prepend(html) {
        this._each((el) => {
            el.insertAdjacentHTML('afterbegin', html);
        });
        return this;
    }
    remove() {
        this._each((el) => {
            el.parentNode.removeChild(el);
        });
        return this;
    }
    empty() {
        this._each((el) => {
            el.innerHTML = '';
        });
        return this;
    }
    scrollTop(scrollTop) {
        if (scrollTop !== undefined) {
            document.body.scrollTop = scrollTop;
            document.documentElement.scrollTop = scrollTop;
            return this;
        } else {
            return (
                window.pageYOffset ||
                document.documentElement.scrollTop ||
                document.body.scrollTop ||
                0
            );
        }
    }
    scrollLeft(scrollLeft) {
        if (scrollLeft !== undefined) {
            document.body.scrollLeft = scrollLeft;
            document.documentElement.scrollLeft = scrollLeft;
            return this;
        } else {
            return (
                window.pageXOffset ||
                document.documentElement.scrollLeft ||
                document.body.scrollLeft ||
                0
            );
        }
    }
    offset() {
        if (!this.firstElement) {
            return {
                left: 0,
                top: 0,
            };
        }
        const rect = this.firstElement.getBoundingClientRect();
        const bodyMarginLeft = $LG('body').style().marginLeft;

        // Minus body margin - https://stackoverflow.com/questions/30711548/is-getboundingclientrect-left-returning-a-wrong-value
        return {
            left: rect.left - parseFloat(bodyMarginLeft) + this.scrollLeft(),
            top: rect.top + this.scrollTop(),
        };
    }
    style() {
        if (!this.firstElement) {
            return {};
        }
        return (
            this.firstElement.currentStyle ||
            window.getComputedStyle(this.firstElement)
        );
    }
     // Width without padding and border even if box-sizing is used.
     width() {
        const style = this.style();
        return (
            this.firstElement.clientWidth -
            parseFloat(style.paddingLeft) -
            parseFloat(style.paddingRight)
        );
    }
    // Height without padding and border even if box-sizing is used.
    height() {
        const style = this.style();
        return (
            this.firstElement.clientHeight -
            parseFloat(style.paddingTop) -
            parseFloat(style.paddingBottom)
        );
    }
}

/**
 * @param {*} selector
 * @returns {CresQuery}
 */
export const cresQuery = (selector) => {
    //initLgPolyfills();
    return new CresQuery(selector);
}
