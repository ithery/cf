import { cfDirectives } from '@/util'
import get from 'get-value'
import store from '@/cui/Store'

/**
 * This is intended to isolate all native DOM operations. The operations that happen
 * one specific element will be instance methods, the operations you would normally
 * perform on the "document" (like "document.querySelector") will be static methods.
 */
export default {
    rootComponentElements() {
        return Array.from(document.querySelectorAll(`[cf\\:id]`))
    },

    rootComponentElementsWithNoParents(node = null) {
        if (node === null) {
            node = document
        }

        // In CSS, it's simple to select all elements that DO have a certain ancestor.
        // However, it's not simple (kinda impossible) to select elements that DONT have
        // a certain ancestor. Therefore, we will flip the logic: select all roots that DO have
        // have a root ancestor, then select all roots that DONT, then diff the two.

        // Convert NodeLists to Arrays so we can use ".includes()". Ew.
        const allEls = Array.from(node.querySelectorAll(`[cf\\:initial-data]`))
        const onlyChildEls = Array.from(node.querySelectorAll(`[cf\\:initial-data] [cf\\:initial-data]`))

        return allEls.filter(el => !onlyChildEls.includes(el))
    },

    allModelElementsInside(root) {
        return Array.from(root.querySelectorAll(`[cf\\:model]`))
    },

    getByAttributeAndValue(attribute, value) {
        return document.querySelector(`[cf\\:${attribute}="${value}"]`)
    },

    nextFrame(fn) {
        requestAnimationFrame(() => {
            requestAnimationFrame(fn.bind(this))
        })
    },

    closestRoot(el) {
        return this.closestByAttribute(el, 'id')
    },

    closestByAttribute(el, attribute) {
        const closestEl = el.closest(`[cf\\:${attribute}]`)

        if (!closestEl) {
            throw `
Cresenity Error:\n
Cannot find parent element in DOM tree containing attribute: [cf:${attribute}].\n
Usually this is caused by Cresenity's DOM-differ not being able to properly track changes.\n
Reference the following guide for common causes: https://laravel-cresenity.com/docs/troubleshooting \n
Referenced element:\n
${el.outerHTML}
`
        }

        return closestEl
    },

    isComponentRootEl(el) {
        return this.hasAttribute(el, 'id')
    },

    hasAttribute(el, attribute) {
        return el.hasAttribute(`cf:${attribute}`)
    },

    getAttribute(el, attribute) {
        return el.getAttribute(`cf:${attribute}`)
    },

    removeAttribute(el, attribute) {
        return el.removeAttribute(`cf:${attribute}`)
    },

    setAttribute(el, attribute, value) {
        return el.setAttribute(`cf:${attribute}`, value)
    },

    hasFocus(el) {
        return el === document.activeElement
    },

    isInput(el) {
        return ['INPUT', 'TEXTAREA', 'SELECT'].includes(
            el.tagName.toUpperCase()
        )
    },

    isTextInput(el) {
        return (
            ['INPUT', 'TEXTAREA'].includes(el.tagName.toUpperCase()) &&
            !['checkbox', 'radio'].includes(el.type)
        )
    },

    valueFromInput(el, component) {
        if (el.type === 'checkbox') {
            let modelName = cfDirectives(el).get('model').value
                // If there is an update from cf:model.defer in the chamber,
                // we need to pretend that is the actual data from the server.
            let modelValue = component.deferredActions[modelName] ?
                component.deferredActions[modelName].payload.value :
                get(component.data, modelName)

            if (Array.isArray(modelValue)) {
                return this.mergeCheckboxValueIntoArray(el, modelValue)
            }

            if (el.checked) {
                return el.getAttribute('value') || true
            } else {
                return false
            }
        } else if (el.tagName === 'SELECT' && el.multiple) {
            return this.getSelectValues(el)
        }

        return el.value
    },

    mergeCheckboxValueIntoArray(el, arrayValue) {
        if (el.checked) {
            return arrayValue.includes(el.value) ?
                arrayValue :
                arrayValue.concat(el.value)
        }

        return arrayValue.filter(item => item !== el.value)
    },

    setInputValueFromModel(el, component) {
        const modelString = cfDirectives(el).get('model').value
        const modelValue = get(component.data, modelString)

        // Don't manually set file input's values.
        if (
            el.tagName.toLowerCase() === 'input' &&
            el.type === 'file'
        )
            return

        this.setInputValue(el, modelValue)
    },

    setInputValue(el, value) {
        store.callHook('interceptWireModelSetValue', value, el)

        if (el.type === 'radio') {
            el.checked = el.value == value
        } else if (el.type === 'checkbox') {
            if (Array.isArray(value)) {
                // I'm purposely not using Array.includes here because it's
                // strict, and because of Numeric/String mis-casting, I
                // want the "includes" to be "fuzzy".
                let valueFound = false
                value.forEach(val => {
                    if (val == el.value) {
                        valueFound = true
                    }
                })

                el.checked = valueFound
            } else {
                el.checked = !!value
            }
        } else if (el.tagName === 'SELECT') {
            this.updateSelect(el, value)
        } else {
            value = value === undefined ? '' : value

            el.value = value
        }
    },

    getSelectValues(el) {
        return Array.from(el.options)
            .filter(option => option.selected)
            .map(option => {
                return option.value || option.text
            })
    },

    updateSelect(el, value) {
        const arrayWrappedValue = [].concat(value).map(value => {
            return value + ''
        })

        Array.from(el.options).forEach(option => {
            option.selected = arrayWrappedValue.includes(option.value)
        })
    }
}