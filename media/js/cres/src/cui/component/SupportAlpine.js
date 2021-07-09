import store from '@/cui/Store'

export default function() {
    window.addEventListener('cresenity:load', () => {
        if (!window.Alpine) return

        refreshAlpineAfterEveryCresenityRequest()

        addDollarSignWire()

        supportEntangle()
    })
}

function refreshAlpineAfterEveryCresenityRequest() {
    if (!window.Alpine.onComponentInitialized) return

    window.Alpine.onComponentInitialized(component => {
        let cresenityEl = component.$el.closest('[cf\\:id]')

        if (cresenityEl && cresenityEl.__cresenity) {
            store.registerHook('message.processed', (message, cresenityComponent) => {
                if (cresenityComponent === cresenityEl.__cresenity) {
                    component.updateElements(component.$el)
                }
            })
        }
    })
}

function addDollarSignWire() {
    if (!window.Alpine.addMagicProperty) return

    window.Alpine.addMagicProperty('cf', function(componentEl) {
        let cfEl = componentEl.closest('[cf\\:id]')

        if (!cfEl)
            console.warn(
                'Alpine: Cannot reference "$cf" outside a Cresenity component.'
            )

        let component = cfEl.__cresenity

        return component.$cf
    })
}

function supportEntangle() {
    if (!window.Alpine.onBeforeComponentInitialized) return

    window.Alpine.onBeforeComponentInitialized(component => {
        let cresenityEl = component.$el.closest('[cf\\:id]')

        if (cresenityEl && cresenityEl.__cresenity) {
            Object.entries(component.unobservedData).forEach(
                ([key, value]) => {
                    if (!!value &&
                        typeof value === 'object' &&
                        value.cresenityEntangle
                    ) {
                        // Ok, it looks like someone set an Alpine property to $cf.entangle or @entangle.
                        let cresenityProperty = value.cresenityEntangle
                        let isDeferred = value.isDeferred
                        let cresenityComponent = cresenityEl.__cresenity

                        // Let's set the initial value of the Alpine prop to the Cresenity prop's value.
                        component.unobservedData[key]
                            // We need to stringify and parse it though to get a deep clone.
                            = JSON.parse(JSON.stringify(cresenityEl.__cresenity.get(cresenityProperty)))

                        let blockAlpineWatcher = false

                        // Now, we'll watch for changes to the Alpine prop, and fire the update to Cresenity.
                        component.unobservedData.$watch(key, value => {
                            // Let's also make sure that this watcher isn't a result of a Cresenity response.
                            // If it is, we don't need to "re-update" Cresenity. (sending an extra useless) request.
                            if (blockAlpineWatcher === true) {
                                blockAlpineWatcher = false
                                return
                            }

                            // If the Alpine value is the same as the Cresenity value, we'll skip the update for 2 reasons:
                            // - It's just more efficient, why send needless requests.
                            // - This prevents a circular dependancy with the other watcher below.
                            if (
                                value ===
                                cresenityEl.__cresenity.getPropertyValueIncludingDefers(
                                    cresenityProperty
                                )
                            ) return

                            // We'll tell Cresenity to update the property, but we'll also tell Cresenity
                            // to not call the normal property watchers on the way back to prevent another
                            // circular dependancy.
                            cresenityComponent.set(
                                cresenityProperty,
                                value,
                                isDeferred,
                                true // Block firing of Cresenity watchers for this data key when the request comes back.
                            )
                        })

                        // We'll also listen for changes to the Cresenity prop, and set them in Alpine.
                        cresenityComponent.watch(
                            cresenityProperty,
                            value => {
                                blockAlpineWatcher = true
                                component.$data[key] = value
                            }
                        )
                    }
                }
            )
        }
    })
}

export function alpinifyElementsForMorphdom(from, to) {
    // If the element we are updating is an Alpine component...
    if (from.__x) {
        // Then temporarily clone it (with it's data) to the "to" element.
        // This should simulate backend Cresenity being aware of Alpine changes.
        window.Alpine.clone(from.__x, to)
    }

    // x-show elements require care because of transitions.
    if (
        Array.from(from.attributes)
        .map(attr => attr.name)
        .some(name => /x-show/.test(name))
    ) {
        if (from.__x_transition) {
            // This covers @entangle('something')
            from.skipElUpdatingButStillUpdateChildren = true
        } else {
            // This covers x-show="$cf.something"
            //
            // If the element has x-show, we need to "reverse" the damage done by "clone",
            // so that if/when the element has a transition on it, it will occur naturally.
            if (isHiding(from, to)) {
                let style = to.getAttribute('style')
                to.setAttribute('style', style.replace('display: none;', ''))
            } else if (isShowing(from, to)) {
                to.style.display = from.style.display
            }
        }
    }
}

function isHiding(from, to) {
    if (beforeAlpineTwoPointSevenPointThree()) {
        return from.style.display === '' && to.style.display === 'none'
    }

    return from.__x_is_shown && !to.__x_is_shown
}

function isShowing(from, to) {
    if (beforeAlpineTwoPointSevenPointThree()) {
        return from.style.display === 'none' && to.style.display === ''
    }

    return !from.__x_is_shown && to.__x_is_shown
}

function beforeAlpineTwoPointSevenPointThree() {
    let [major, minor, patch] = window.Alpine.version.split('.').map(i => Number(i))

    return major <= 2 && minor <= 7 && patch <= 2
}