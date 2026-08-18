import { getComponentName, isRequiredVersion } from '../util/alpine';
import AlpineCleave from '../alpine/cleave';
import AlpineAutoNumeric from '../alpine/autonumeric';
import AlpineTippy from '../alpine/tippy';
import AlpineReload from '../alpine/reload';
import CresAlpineUI from './CresAlpine/ui';
import AlpineSelect2 from '../alpine/select2';
import AlpineMessage from '../alpine/message';

const CRESALPINE_RENDER_ATTR_NAME = 'data-cresalpine-render';
const CRESALPINE_RENDER_BINDING_ATTR_NAME = `:${CRESALPINE_RENDER_ATTR_NAME}`;
const ADDED_ATTRIBUTES = [CRESALPINE_RENDER_ATTR_NAME, CRESALPINE_RENDER_BINDING_ATTR_NAME];


export default class CresAlpine {
    constructor(Alpine) {
        this.Alpine = Alpine;
        this.lastComponentCrawl = Date.now();
        this.components = [];
        this.uuid = 1;
        this.errorElements = [];
        this.observer = null;

        Alpine.plugin(AlpineCleave);
        Alpine.plugin(AlpineAutoNumeric);
        Alpine.plugin(AlpineTippy);
        Alpine.plugin(AlpineReload);
        Alpine.plugin(AlpineSelect2);
        Alpine.plugin(AlpineMessage);
        Alpine.directive('destroy', (el, { expression }, { evaluateLater, cleanup }) => {
            const clean = evaluateLater(expression);
            cleanup(() => clean());
        });

        this.ui = new CresAlpineUI(this.Alpine);
        this.ui.init();
    }
    get alpineVersion() {
        return this.Alpine.version || '';
    }

    get isV3() {
        return isRequiredVersion('3.0.0', this.alpineVersion);
    }

    getAlpineDataInstance(node) {
        if (this.isV3) {
            // eslint-disable-next-line no-underscore-dangle
            return node._x_dataStack ? node._x_dataStack[0] : null;
        }
        // eslint-disable-next-line no-underscore-dangle
        return node.__x;
    }

    getComponent(name) {
        let components = this.getComponents();
        for(let i=0; i<components.length; i++) {
            if(components[i].name==name) {
                return components[i];
            }
        }
        return null;
    }
    getComponents() {
        const alpineRoots = Array.from(document.querySelectorAll('[x-data]'));
        const allComponentsInitialized = Object.values(alpineRoots).every((e) => e.cresAlpine);
        if (allComponentsInitialized) {
            const lastAlpineRender = alpineRoots.reduce((acc, el) => {
                // we add `:data-devtools-render="Date.now()"` when initialising components
                const renderTimeStr = el.getAttribute(CRESALPINE_RENDER_ATTR_NAME);
                const renderTime = parseInt(renderTimeStr, 10);
                if (renderTime && renderTime > acc) {
                    return renderTime;
                }
                return acc;
            }, this.lastComponentCrawl);

            const someComponentHasUpdated = lastAlpineRender > this.lastComponentCrawl;
            if (someComponentHasUpdated) {
                this.lastComponentCrawl = Date.now();
            }

            // Exit early if no components have been added, removed and no data has changed
            if (!someComponentHasUpdated && this.components.length === alpineRoots.length) {
                return this.components;
            }
        }

        this.components = [];

        alpineRoots.forEach((rootEl, index) => {
            if (!this.getAlpineDataInstance(rootEl)) {
                // this component probably crashed during init
                return;
            }

            if (!rootEl.cresAlpine) {
                if (!this.isV3) {
                    // only necessary for Alpine v2
                    // add an attr to trigger the mutation observer and run this function
                    // that will send updated state to devtools
                    rootEl.setAttribute(CRESALPINE_RENDER_BINDING_ATTR_NAME, 'Date.now()');
                }
                rootEl.cresAlpine = {
                    id: this.uuid++
                };
                window[`$x${rootEl.cresAlpine.id - 1}`] = this.getAlpineDataInstance(rootEl);
            }

            if (rootEl.cresAlpine.id === this.selectedComponentId) {
                this.sendComponentData(this.selectedComponentId, rootEl);
            }

            if (this.isV3) {
                const componentData = this.getAlpineDataInstance(rootEl);
                this.Alpine.effect(() => {
                    Object.keys(componentData).forEach((key) => {
                        // since effects track which dependencies are accessed,
                        // run a fake component data access so that the effect runs
                        componentData[key];
                        if (rootEl.cresAlpine.id === this.selectedComponentId) {
                            // this re-computes the whole component data
                            // with effect we could send only the key-value of the field that's changed
                            this.sendComponentData(this.selectedComponentId, rootEl);
                        }
                    });
                });
            }

            const componentDepth =
                    index === 0
                        ? 0
                        : alpineRoots.reduce((depth, el, innerIndex) => {
                            if (index === innerIndex) {
                                return depth;
                            }

                            if (el.contains(rootEl)) {
                                return depth + 1;
                            }

                            return depth;
                        }, 0);

            this.components.push({
                name: getComponentName(rootEl),
                depth: componentDepth,
                index,
                id: rootEl.cresAlpine.id,
                getData: () => {
                    return this.getAlpineDataInstance(rootEl);
                }
            });
        });
        return this.components;
    }


    postMessage(payload) {
        window.postMessage(
            {
                source: 'cres-alpine-backend',
                payload
            },
            '*'
        );
    }
}
