/* eslint-disable no-console */
/* eslint-disable no-underscore-dangle */
import store from '@/ui/Store';
import { walk } from './../../util/walk';

export default function () {
    window.addEventListener('cresenity:ui:start', () => {
        if (!window.Alpine) {return;}
        refreshAlpineAfterEveryCresenityRequest();

        addDollarSignCres();

        supportEntangle();
    });
}

function refreshAlpineAfterEveryCresenityRequest() {
    if (isV3()) {
        store.registerHook('message.processed', (message, cresenityComponent) => {
            walk(cresenityComponent.el, el => {
                if (el._x_hidePromise) {return;}
                if (el._x_runEffects) {el._x_runEffects();}
            });
        });

        return;
    }

    if (!window.Alpine.onComponentInitialized) {return;}

    window.Alpine.onComponentInitialized(component => {
        let cresenityEl = component.$el.closest('[cres\\:id]');


        if (cresenityEl && cresenityEl.__cresenity) {
            store.registerHook('message.processed', (message, cresenityComponent) => {
                if (cresenityComponent === cresenityEl.__cresenity) {
                    component.updateElements(component.$el);
                }
            });
        }
    });
}

function addDollarSignCres() {
    if (isV3()) {
        window.Alpine.magic('cres', function (el) {
            let cresEl = el.closest('[cres\\:id]');

            if (!cresEl) {
                console.warn(
                    'Alpine: Cannot reference "$cres" outside a Cresenity component.'
                );
            }

            let component = cresEl.__cresenity;

            return component.$cres;
        });
        return;
    }

    if (!window.Alpine.addMagicProperty) {return;}

    window.Alpine.addMagicProperty('cres', function (componentEl) {
        let cresEl = componentEl.closest('[cres\\:id]');

        if (!cresEl) {
            console.warn(
                'Alpine: Cannot reference "$cres" outside a Cresenity component.'
            );
        }

        let component = cresEl.__cresenity;

        return component.$cres;
    });
}

function supportEntangle() {
    if (isV3()) {return;}

    if (!window.Alpine.onBeforeComponentInitialized) {return;}

    window.Alpine.onBeforeComponentInitialized(component => {
        let cresenityEl = component.$el.closest('[cres\\:id]');

        if (cresenityEl && cresenityEl.__cresenity) {
            Object.entries(component.unobservedData).forEach(
                ([key, value]) => {
                    if (
                        !!value &&
                        typeof value === 'object' &&
                        value.cresenityEntangle
                    ) {
                        // Ok, it looks like someone set an Alpine property to $cres.entangle or @entangle.
                        let cresenityProperty = value.cresenityEntangle;
                        let isDeferred = value.isDeferred;
                        let cresenityComponent = cresenityEl.__cresenity;

                        let cresenityPropertyValue = cresenityEl.__cresenity.get(cresenityProperty);

                        // Check to see if the Cresenity property exists and if not log a console error
                        // and return so everything else keeps running.
                        if (typeof cresenityPropertyValue === 'undefined') {
                            console.error(`Cresenity Component Entangle Error: Cresenity Component property '${cresenityProperty}' cannot be found`);
                            return;
                        }

                        // Let's set the initial value of the Alpine prop to the Cresenity prop's value.
                        component.unobservedData[key]
                            // We need to stringify and parse it though to get a deep clone.
                            = JSON.parse(JSON.stringify(cresenityPropertyValue));

                        let blockAlpineWatcher = false;

                        // Now, we'll watch for changes to the Alpine prop, and fire the update to Cresenity Component.
                        component.unobservedData.$watch(key, value => {
                            // Let's also make sure that this watcher isn't a result of a Cresenity Component response.
                            // If it is, we don't need to "re-update" Cresenity Component. (sending an extra useless) request.
                            if (blockAlpineWatcher === true) {
                                blockAlpineWatcher = false;
                                return;
                            }

                            // If the Alpine value is the same as the Cresenity Component value, we'll skip the update for 2 reasons:
                            // - It's just more efficient, why send needless requests.
                            // - This prevents a circular dependancy with the other watcher below.
                            // - Due to the deep clone using stringify, we need to do the same here to compare.
                            if (
                                JSON.stringify(value) ==
                                JSON.stringify(
                                    cresenityEl.__cresenity.getPropertyValueIncludingDefers(
                                        cresenityProperty
                                    )
                                )
                            ) {return;}

                            // We'll tell Cresenity Component to update the property, but we'll also tell Cresenity Component
                            // to not call the normal property watchers on the way back to prevent another
                            // circular dependancy.
                            cresenityComponent.set(
                                cresenityProperty,
                                value,
                                isDeferred,
                                // Block firing of Cresenity Component watchers for this data key when the request comes back.
                                // Unless it is deferred, in which cause we don't know if the state will be the same, so let it run.
                                isDeferred ? false : true
                            );
                        });

                        // We'll also listen for changes to the Cresenity Component prop, and set them in Alpine.
                        cresenityComponent.watch(
                            cresenityProperty,
                            value => {
                                // Ensure data is deep cloned otherwise Alpine mutates Cresenity Component data
                                component.$data[key] = typeof value !== 'undefined' ? JSON.parse(JSON.stringify(value)) : value;
                            }
                        );
                    }
                }
            );
        }
    });
}


export function getEntangleFunction(component) {
    if (isV3()) {
        return (name, defer = false) => {
            let isDeferred = defer;
            let cresenityProperty = name;
            let cresenityComponent = component;
            let cresenityPropertyValue = component.get(cresenityProperty);

            let interceptor = window.Alpine.interceptor((initialValue, getter, setter, path, key) => {
                // Check to see if the Cresenity Component property exists and if not log a console error
                // and return so everything else keeps running.
                if (typeof cresenityPropertyValue === 'undefined') {
                    console.error(`Cresenity Component Entangle Error: Cresenity Component property '${cresenityProperty}' cannot be found`);
                    return;
                }

                // Let's set the initial value of the Alpine prop to the Cresenity Component prop's value.
                let value
                    // We need to stringify and parse it though to get a deep clone.
                    = JSON.parse(JSON.stringify(cresenityPropertyValue));

                setter(value);

                // Now, we'll watch for changes to the Alpine prop, and fire the update to Cresenity Component.
                window.Alpine.effect(() => {
                    let value = getter();

                    if (
                        JSON.stringify(value) ==
                        JSON.stringify(
                            cresenityComponent.getPropertyValueIncludingDefers(
                                cresenityProperty
                            )
                        )
                    ) {return;}

                    // We'll tell Cresenity Component to update the property, but we'll also tell Cresenity Component
                    // to not call the normal property watchers on the way back to prevent another
                    // circular dependancy.
                    cresenityComponent.set(
                        cresenityProperty,
                        value,
                        isDeferred,
                        // Block firing of Cresenity Component watchers for this data key when the request comes back.
                        // Unless it is deferred, in which cause we don't know if the state will be the same, so let it run.
                        isDeferred ? false : true
                    );
                });

                // We'll also listen for changes to the Cresenity Component prop, and set them in Alpine.
                cresenityComponent.watch(
                    cresenityProperty,
                    value => {
                        // Ensure data is deep cloned otherwise Alpine mutates Cresenity Component data
                        window.Alpine.disableEffectScheduling(() => {
                            setter(typeof value !== 'undefined' ? JSON.parse(JSON.stringify(value)) : value);
                        });
                    }
                );

                return value;
            }, obj => {
                Object.defineProperty(obj, 'defer', {
                    get() {
                        isDeferred = true;

                        return obj;
                    }
                });
            });

            return interceptor(cresenityPropertyValue);
        };
    }

    return (name, defer = false) => ({
        isDeferred: defer,
        cresenityEntangle: name,
        get defer() {
            this.isDeferred = true;
            return this;
        }
    });
}


export function alpinifyElementsForMorphdom(from, to) {
    if (isV3()) {
        return alpinifyElementsForMorphdomV3(from, to);
    }

    // If the element we are updating is an Alpine component...
    if (from.__x) {
        // Then temporarily clone it (with it's data) to the "to" element.
        // This should simulate backend Cresenity being aware of Alpine changes.
        window.Alpine.clone(from.__x, to);
    }

    // x-show elements require care because of transitions.
    if (
        Array.from(from.attributes)
            .map(attr => attr.name)
            .some(name => /x-show/.test(name))
    ) {
        if (from.__x_transition) {
            // This covers @entangle('something')
            from.skipElUpdatingButStillUpdateChildren = true;
        } else {
            // This covers x-show="$cres.something"
            //
            // If the element has x-show, we need to "reverse" the damage done by "clone",
            // so that if/when the element has a transition on it, it will occur naturally.
            if (isHiding(from, to)) {
                let style = to.getAttribute('style');
                if (style) {
                    to.setAttribute('style', style.replace('display: none;', ''));
                }
            } else if (isShowing(from, to)) {
                to.style.display = from.style.display;
            }
        }
    }
}

function alpinifyElementsForMorphdomV3(from, to) {
    if (from.nodeType !== 1) {return;}

    // If the element we are updating is an Alpine component...
    if (from._x_dataStack) {
        // Then temporarily clone it (with it's data) to the "to" element.
        // This should simulate backend Cresenity Component being aware of Alpine changes.
        window.Alpine.clone(from, to);
    }
}
function isHiding(from, to) {
    if (beforeAlpineTwoPointSevenPointThree()) {
        return from.style.display === '' && to.style.display === 'none';
    }

    return from.__x_is_shown && !to.__x_is_shown;
}

function isShowing(from, to) {
    if (beforeAlpineTwoPointSevenPointThree()) {
        return from.style.display === 'none' && to.style.display === '';
    }

    return !from.__x_is_shown && to.__x_is_shown;
}

function beforeAlpineTwoPointSevenPointThree() {
    let [major, minor, patch] = window.Alpine.version.split('.').map(i => Number(i));

    return major <= 2 && minor <= 7 && patch <= 2;
}

function isV3() {
    return window.Alpine && window.Alpine.version && /^3\..+\..+$/.test(window.Alpine.version);
}
