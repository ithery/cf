const findModifierArgument = (modifiers, target, offset = 1) => {
    return modifiers[modifiers.indexOf(target) + offset]
}

const buildConfigFromModifiers = (modifiers, expression, evaluate) => {
    const config = evaluate(expression);

    return config
}

const valueChangeCallback = (el) => {
    return () => {
        var value = $(el).autoNumeric('get');
        if (!el._x_model) {
            return
        }

        el._x_model.set(value)
    }
}

export default function (Alpine) {
    Alpine.magic('autonumeric', (el) => {
        if (el.__autonumeric) {
            return el.__autonumeric
        }
    })

    Alpine.directive('autonumeric', (el, { modifiers, expression }, { effect, evaluate, cleanup }) => {
        if(typeof jQuery ==='undefined' || typeof $ ==='undefined') {
            console.error('Error autonumeric need jquery');
            return;
        }

        if(typeof $.prototype.autoNumeric ==='undefined') {
            console.error('Error AutoNumeric is not defined');
            return;
        }
        if (el._x_model) {
            // Find the model directive (due to modifiers, we don't know the name upfront)
            // and remove the default behaviours
            const directive = Alpine.prefixed('model')
            Object.keys(el._x_attributeCleanups).forEach(key => {
                if (key.startsWith(directive)) {
                    el._x_attributeCleanups[directive][0]()
                    delete el._x_attributeCleanups[directive]
                }
            })
            el._x_forceModelUpdate = () => {}
        }
        const config = modifiers.length === 0
            ? expression ? evaluate(expression) : {}
            : buildConfigFromModifiers(modifiers, expression, evaluate)

        if (!el.__autonumeric) {

            $(el).autoNumeric('init',config);
            el.__autonumeric = $(el).data('autoNumeric');
            $(el).bind('blur focusout change', valueChangeCallback(el));


            if (el._x_model) {
                effect(() => {
                    Alpine.mutateDom(() => $(el).autoNumeric('set',el._x_model.get()))
                })
            }

            if(el._x_bindings && el._x_bindings['value']) {
                effect(() =>  {

                    Alpine.mutateDom(() => {
                        $(el).autoNumeric('set',el._x_bindings['value'])
                    });
                });

            }
            cleanup(()=>{
                $(el).unbind('blur focusout change', valueChangeCallback(el));
                $(el).autoNumeric('destroy');
            });



        }


    })
}
