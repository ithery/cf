/* eslint-disable no-underscore-dangle */
const DEFAULT_NAMESPACE = 'cres';
const ATTR_UI = 'c-ui';
const ATTR_CLOAK = 'c-cloak';
import * as utils from '../../util/helper';
export default class CresAlpineUI {
    constructor(Alpine) {
        this.config = {debug: false};
        this.setups = {};
        this.components = {};
        this.Alpine = Alpine;
        this.allNamespaces = [DEFAULT_NAMESPACE];
        this.nextTick = Alpine.nextTick;
        this.effect = Alpine.effect;
        this.DIR_COMP = Alpine.prefixed('component');
        this.DIR_IMPORT = Alpine.prefixed('import');
        this.DIR_DATA = Alpine.prefixed('data');
        this.DIR_INIT = Alpine.prefixed('init');
        this.DIR_IGNORE = Alpine.prefixed('ignore');

        if(window.capp?.alpine?.ui) {
            this.config = utils.extend(this.config, window.capp.alpine.ui);
        }
    }
    elapse() {
        return new Date() - this.initAt;
    }

    addNamespace(ns) {
        if (!ns) {
            return;
        }
        ns = ns.trim();
        if (this.allNamespaces.indexOf(ns) === -1) {
            this.allNamespaces.push(ns);
        }
    }
    getNamespaceFromXcomponent(dirName) {
        let p1 = dirName.indexOf(':');
        if (p1 === -1) {
            return DEFAULT_NAMESPACE;
        }
        let p2 = dirName.indexOf('.', p1);
        return p2 === -1 ? dirName.substring(p1 + 1) : dirName.substring(p1 + 1, p2);
    }
    isComponent(el) {
        if (el._cui_type) {return true;}
        if (el.tagName) {
            let p = el.tagName.indexOf('-');
            if (p === -1) {return false;}
            let ns = el.tagName.substring(0, p).toLowerCase();
            if (this.allNamespaces.indexOf(ns) !== -1) {
                return true;
            }
        }
        return false;
    }
    getParentComponent(el) {
        if (!el.parentNode) {return null;}
        if (this.isComponent(el.parentNode)) {return el.parentNode;}
        return this.getParentComponent(el.parentNode);
    }
    visitComponents(elContainer, callback) {
        if (elContainer.tagName === 'TEMPLATE') {
            // eslint-disable-next-line no-underscore-dangle
            if (elContainer._x_teleport) {
                if (this.isComponent(elContainer._x_teleport)) {callback(elContainer._x_teleport);}
                return this.visitComponents(elContainer._x_teleport, callback);
            }
            return this.visitComponents(elContainer.content, callback);
        }
        utils.each(elContainer.querySelectorAll('*'), el => {
            if (this.isComponent(el)) {callback(el);}
            if (el.tagName === 'TEMPLATE') {
                if (el._x_teleport) {
                    if (this.isComponent(el._x_teleport)) {callback(el._x_teleport);}
                    return this.visitComponents(el._x_teleport, callback);
                }
                return this.visitComponents(el.content, callback);
            }
        });
    }
    findClosestComponent(el, filter) {
        if (!el) {return null;}
        if (el._cui_type) {
            if (utils.isString(filter)) {
                let type = filter;
                filter = (el) => el._cui_type === type;
            }
            if (!filter || filter(el)) {return el;}
        }
        if (el._x_teleportBack) {
            return this.findClosestComponent(el._x_teleportBack.parentNode, filter);
        }
        return this.findClosestComponent(el.parentNode, filter);
    }
    normalizeFilter(filter, defNamespace) {
        if (utils.isFunction(filter)) {return filter;}
        if (utils.isPlainObject(filter)) {
            return (el) => {
                if (el._cui_type !== filter.type) {return false;}
                if (filter.namespace && el._cui_namespace !== filter.namespace) {return false;}
                return true;
            };
        }
        let namespace = '';
        let type = filter;
        let parts = filter.split(':');
        if (parts.length > 1) {
            namespace = parts[0] || defNamespace;
            type = parts[1];
        }
        return (el) => {
            if (el._cui_type !== type) {return false;}
            if (namespace && el._cui_namespace !== namespace) {return false;}
            return true;
        };
    }

    getApiOf(el, filter) {
        const comp = this.findClosestComponent(el, filter);
        if (!comp) {return null;}
        const baseApis = {
            $of(type) {
                if (!type) {return null;}
                return this.getApiOf(
                    (comp._x_teleportBack || comp).parentNode, this.normalizeFilter(type, comp._cui_namespace));
            },
            get $meta() { return this.getComponentMeta(comp); },
            get $parent() { return this.getParentComponent(comp); },
            $closest(filter) {
                return this.findClosestComponent(comp, this.normalizeFilter(filter, comp._cui_namespace));
            },
            $find(filter) {
                return this.findChildComponents(comp, this.normalizeFilter(filter, comp._cui_namespace));
            },
            $findOne(filter) {
                let comps = this.findChildComponents(comp, this.normalizeFilter(filter, comp._cui_namespace));
                return comps.length > 0 ? comps[0] : null;
            }
        };
        return this.Alpine.mergeProxies([baseApis, comp._cui_api || {}, ...this.Alpine.closestDataStack(comp)]);
    }
    getComponentMeta(el) {
        return {
            // eslint-disable-next-line no-underscore-dangle
            type: el._cui_type,
            // eslint-disable-next-line no-underscore-dangle
            namespace: el._cui_namespace
        };
    }
    findChildComponents(elContainer, filter) {
        if (utils.isString(filter)) {
            let type = filter;
            filter = (el) => el._cui_type === type;
        }
        let result = [];
        this.visitComponents(elContainer, (el) => {
            if (!filter || filter(el)) {result.push(el);}
        });
        return result;
    }
    setHtml(el, html) {
        el.innerHTML = '';
        let dom = this.dom(html);
        if (utils.isArray(dom)) {
            el.append(...dom);
        } else {
            el.append(dom);
        }
    }
    defer(callback) {
        queueMicrotask(callback);
    }
    dom(html) {
        const elTemp = document.createElement('div');
        elTemp._x_ignore = true;
        elTemp.innerHTML = html;
        this.extractNamespaces(elTemp);
        this.prepareComponents(elTemp);
        return elTemp.childNodes.length === 1 ? elTemp.firstChild : [...elTemp.childNodes];
    }
    focus(el, options) {
        return el && el.focus && el.focus(options || { preventScroll: true });
    }
    scrollIntoView(el, options) {
        return el && el.scrollIntoView && el.scrollIntoView(options || { block: 'nearest' });
    }
    extractNamespaces(elContainer) {
        utils.each([elContainer, ...elContainer.querySelectorAll('*')], el => {
            if (el.tagName === 'TEMPLATE') {
                this.extractNamespaces(el.content);
            }
            utils.each(el.attributes, attr => {
                let name = attr.name;
                if (name.startsWith(this.DIR_COMP)) {
                    let ns = this.getNamespaceFromXcomponent(name);
                    this.addNamespace(ns);
                } else if (name.startsWith(this.DIR_IMPORT) && attr.value) {
                    let comps = attr.value.trim();
                    if (comps.startsWith('[') && comps.endsWith(']')) {
                        //comps = evaluate(el, attr.value)
                        return;
                    }
                    comps = comps.split(';');

                    utils.each(comps, comp => {
                        let p = comp.indexOf(':');
                        if (p !== -1) {
                            let ns = comp.substring(0, p);
                            this.addNamespace(ns);
                        }
                    });
                }
            });
        });
    }
    prepareComponents(elContainer) {
        this.visitComponents(elContainer, el => {
            el.setAttribute(ATTR_CLOAK, '');
            el.setAttribute(this.DIR_IGNORE, '');
        });
    }

    init() {
        this.initAt = new Date();
        this.$api = (el) => this.getApiOf(el);
        this.$data = this.Alpine.$data;
        this.extractNamespaces(document);
        this.prepareComponents(document);
        this.Alpine.addRootSelector(() => `[${this.DIR_COMP}]`);
        this.Alpine.magic('api', el => this.getApiOf(el));
        this.Alpine.magic('prop', el => {
            return (name, fallback) => {
                let comp = this.findClosestComponent(el);
                if (!comp) {
                    return null;
                }
                return this.Alpine.bound(comp, name, fallback);
            };
        });
        this.Alpine.directive('shtml', (el, { expression }, { effect, evaluateLater }) => {
            let evaluate = evaluateLater(expression);
            effect(() => {
                evaluate(value => {
                    this.setHtml(el, value);
                });
            });
        });
        this.Alpine.directive('component', (el, { expression, value, modifiers }, { cleanup }) => {
            if (el.tagName.toLowerCase() !== 'template') {
                return console.warn('x-component can only be used on a <template> tag', el);
            }
            const namespace = value || this.config.namespace || DEFAULT_NAMESPACE;
            const compName = `${namespace}-${expression}`;
            const unwrap = modifiers.includes('unwrap');
            const elScript = el.content.querySelector('script');
            if (elScript) {
                const elExecute = document.createElement('script');
                utils.each(elScript.attributes, a => elExecute.setAttribute(a.name, a.value));
                elExecute.setAttribute('component', compName);
                elExecute.innerHTML = `
window.cresenity.alpine.ui.setups["${compName}"] = ($el)=>{
${elScript.innerHTML}
}
//# sourceURL=__cui__/${compName}.js
`;
                document.body.append(elExecute);
                elScript.remove();
            }

            const copyAttributes = (elFrom, elTo) => {
                utils.each(elFrom.attributes, attr => {
                    if (this.DIR_COMP === attr.name || attr.name.startsWith(this.DIR_COMP)) {return;}
                    try {
                        let name = attr.name;
                        if (name.startsWith('@')) {
                            name = `${this.Alpine.prefixed('on')}:${name.substring(1)}`;
                        } else if (name.startsWith(':')) {
                            name = `${this.Alpine.prefixed('bind')}:${name.substring(1)}`;
                        }
                        if (this.DIR_INIT === name && elTo.getAttribute(this.DIR_INIT)) {
                            elTo.setAttribute(name, attr.value + ';' + elTo.getAttribute(this.DIR_INIT));
                        } else if (this.DIR_DATA === name && elTo.getAttribute(this.DIR_DATA)) {
                            elTo.setAttribute(name, '{...' + attr.value + ',...' + elTo.getAttribute(this.DIR_DATA) + '}');
                        } else if (name === 'class') {
                            elTo.setAttribute(name, attr.value + ' ' + (elTo.getAttribute('class') || ''));
                        } else if (!elTo.hasAttribute(name)) {
                            elTo.setAttribute(name, attr.value);
                        }
                    } catch (ex) {
                        console.warn(`Fails to set attribute ${attr.name}=${attr.value} in ${elTo.tagName.toLowerCase()}`);
                    }
                });
            };
            const that = this;
            this.components[compName] = class extends HTMLElement {
                connectedCallback() {
                    let elComp = this;
                    let elTopComp = that.getParentComponent(elComp);
                    while (elTopComp) {
                        if (!elTopComp.hasAttribute(ATTR_UI) && !elTopComp._cui_type) {
                            if (that.config.debug) {console.log('Not ready to connect ' + this.tagName);}
                            return;
                        }
                        elTopComp = that.getParentComponent(elTopComp);
                    }
                    elComp.setAttribute(ATTR_UI, that.config.debug ? `${that.elapse()}` : '');
                    if (that.config.debug) {console.log('Connect ' + this.tagName);}
                    that.Alpine.mutateDom(() => {
                        const slotContents = {};
                        const defaultSlotContent = [];
                        utils.each(this.childNodes, elChild => {
                            if (elChild.tagName && elChild.hasAttribute('slot')) {
                                let slotName = elChild.getAttribute('slot') || '';
                                let content = elChild.tagName === 'TEMPLATE' ?
                                    elChild.content.cloneNode(true).childNodes :
                                    [elChild.cloneNode(true)];
                                if (slotContents[slotName]) {
                                    slotContents[slotName].push(...content);
                                } else {
                                    slotContents[slotName] = content;
                                }
                            } else {
                                defaultSlotContent.push(elChild.cloneNode(true));
                            }
                        });
                        if (unwrap) {
                            elComp = el.content.cloneNode(true).firstElementChild;
                            copyAttributes(this, elComp);
                            this.after(elComp);
                            this.remove();
                        } else {
                            elComp.innerHTML = el.innerHTML;
                        }
                        copyAttributes(el, elComp);

                        const elSlots = elComp.querySelectorAll('slot');
                        utils.each(elSlots, elSlot => {
                            const name = elSlot.getAttribute('name') || '';
                            elSlot.after(...(slotContents[name] ? slotContents[name] : defaultSlotContent));
                            elSlot.remove();
                        });
                        if (unwrap && that.isComponent(elComp)) {return;}

                        elComp._cui_type = expression;
                        elComp._cui_namespace = namespace;
                        let setup = that.setups[compName];
                        if (setup) {
                            elComp._cui_api = that.Alpine.reactive(setup(elComp));
                        }
                        if (!elComp.hasAttribute(that.DIR_DATA)) {elComp.setAttribute(that.DIR_DATA, '{}');}

                        let elParentComp = that.getParentComponent(elComp);
                        if (!elParentComp || elParentComp._cui_type) {
                            queueMicrotask(() => {
                                if (!elComp.isConnected) {return;}
                                elComp.removeAttribute(ATTR_CLOAK);
                                elComp.removeAttribute(that.DIR_IGNORE);
                                delete elComp._x_ignore;
                                if (that.config.debug) {console.log('Process initTree ' + this.tagName);}
                                that.Alpine.initTree(elComp);
                                if (elComp._cui_api) {
                                    let api = that.getApiOf(elComp);
                                    if (api.onMounted) {api.onMounted();}
                                }
                                utils.each(elComp._cui_deferred_elements, el => {
                                    if (el._cui_api) {
                                        let api = that.getApiOf(el);
                                        if (api.onMounted) {api.onMounted();}
                                    }
                                });
                                delete elComp._cui_deferred_elements;
                            });
                        } else {
                            // wait for parent component to be mounted
                            if (that.config.debug) {console.log('Defer initTree ' + this.tagName);}
                            if (!elParentComp._cui_deferred_elements) {elParentComp._cui_deferred_elements = [];}
                            elParentComp._cui_deferred_elements.push(elComp);
                            if (elComp._cui_deferred_elements) {elParentComp._cui_deferred_elements.push(...elComp._cui_deferred_elements);}
                            queueMicrotask(() => {
                                elComp.removeAttribute(ATTR_CLOAK);
                                elComp.removeAttribute(that.DIR_IGNORE);
                                delete elComp._x_ignore;
                            });
                        }
                    });
                }
                disconnectedCallback() {
                    if (that.config.debug) {console.log((this.hasAttribute(ATTR_UI) ? 'Disconnect ' : 'Not ready to disconnect ') + this.tagName);}

                    if (this._cui_api) {
                        let api = that.getApiOf(this);
                        if (api.onUnmounted) {api.onUnmounted();}
                    }
                }
                attributeChangedCallback(name, oldValue, newValue) {
                    if (this._cui_api) {
                        let api = that.getApiOf(this);
                        if (api.onAttributeChanged) {api.onAttributeChanged(name, oldValue, newValue);}
                    }
                }
            };
            customElements.define(compName.toLowerCase(), that.components[compName]);
        });
    }
}
