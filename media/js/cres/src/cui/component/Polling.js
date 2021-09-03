import MethodAction from '@/cui/action/method'
import { cfDirectives } from '@/util'
import store from '@/cui/Store'

export default function() {
    store.registerHook('element.initialized', (el, component) => {
        let directives = cfDirectives(el)

        if (directives.missing('poll')) return

        let intervalId = fireActionOnInterval(el, component)

        component.addListenerForTeardown(() => {
            clearInterval(intervalId)
        })

        el.__cresenity_polling_interval = intervalId
    })

    store.registerHook('element.updating', (from, to, component) => {
        if (from.__cresenity_polling_interval !== undefined) return

        if (cfDirectives(from).missing('poll') && cfDirectives(to).has('poll')) {
            setTimeout(() => {
                let intervalId = fireActionOnInterval(from, component)

                component.addListenerForTeardown(() => {
                    clearInterval(intervalId)
                })

                from.__cresenity_polling_interval = intervalId
            }, 0)
        }
    })
}

function fireActionOnInterval(node, component) {
    let interval = cfDirectives(node).get('poll').durationOr(2000);

    return setInterval(() => {
        if (node.isConnected === false) return

        let directives = cfDirectives(node)

        // Don't poll when directive is removed from element.
        if (directives.missing('poll')) return

        const directive = directives.get('poll')
        const method = directive.method || '$refresh'

        // Don't poll when the tab is in the background.
        // (unless the "cf:poll.keep-alive" modifier is attached)
        if (store.cresenityIsInBackground && !directive.modifiers.includes('keep-alive')) {
            // This "Math.random" business effectivlly prevents 95% of requests
            // from executing. We still want "some" requests to get through.
            if (Math.random() < .95) return
        }

        // Don't poll if cresenity is offline as well.
        if (store.cresenityIsOffline) return

        component.addAction(new MethodAction(method, directive.params, node))
    }, interval);
}