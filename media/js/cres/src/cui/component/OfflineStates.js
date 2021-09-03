import store from '@/cui/Store'
import { cfDirectives } from '@/util'

var offlineEls = [];

export default function() {
    store.registerHook('element.initialized', el => {
        if (cfDirectives(el).missing('offline')) return

        offlineEls.push(el)
    })

    window.addEventListener('offline', () => {
        store.cresenityIsOffline = true

        offlineEls.forEach(el => {
            toggleOffline(el, true)
        })
    })

    window.addEventListener('online', () => {
        store.cresenityIsOffline = false

        offlineEls.forEach(el => {
            toggleOffline(el, false)
        })
    })

    store.registerHook('element.removed', el => {
        offlineEls = offlineEls.filter(el => !el.isSameNode(el))
    })
}

function toggleOffline(el, isOffline) {
    let directives = cfDirectives(el)
    let directive = directives.get('offline')

    if (directive.modifiers.includes('class')) {
        const classes = directive.value.split(' ')
        if (directive.modifiers.includes('remove') !== isOffline) {
            el.classList.add(...classes)
        } else {
            el.classList.remove(...classes)
        }
    } else if (directive.modifiers.includes('attr')) {
        if (directive.modifiers.includes('remove') !== isOffline) {
            el.setAttribute(directive.value, true)
        } else {
            el.removeAttribute(directive.value)
        }
    } else if (!directives.get('model')) {
        el.style.display = isOffline ? 'inline-block' : 'none'
    }
}