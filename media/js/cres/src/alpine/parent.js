

import {
    objectSetDeep,
    componentData,
    syncWithObservedComponent,
    updateOnMutation,
    waitUntilReady
} from './utils'
export default function (Alpine) {
    Alpine.magic('parent', (el) => {
        if (typeof el.$parent !== 'undefined') {
            return el.$parent
        }

        const parentComponent = $el.parentNode.closest('[x-data]')
        if (!parentComponent) throw new Error('Parent component not found')

        // If the parent component is not ready, we return a dummy proxy
        // that always prints out an empty string and we check again on the next frame
        // We are de facto deferring the value for a few ms but final users
        // shouldn't notice the delay
        return waitUntilReady(parentComponent, el, () => {
            el.$parent = syncWithObservedComponent(componentData(parentComponent), parentComponent, objectSetDeep)
            updateOnMutation(parentComponent, () => {
                el.$parent = syncWithObservedComponent(parentComponent.__x.getUnobservedData(), parentComponent, objectSetDeep)
                el.__x.updateElements(el)
            })
            return el.$parent
        })
    })

}
