
import { elementRendered } from "../util/dom-observer";
import { initComponent, component } from "./component";
import { initControl, control } from "./control";

const isElementInitialized = (element) => {
    const classList = element.className.split(/\s+/);
    for (let i = 0; i < classList.length; i++) {
        if (classList[i]=='cres:initialized') {
            return true;
        }
    }
    return false;

}

let inited = false;

const initElement = () => {
    if(!inited) {
        elementRendered('[cres-element]', (element)=>{
            if(!isElementInitialized(element)) {
                const elementName = element.getAttribute('cres-element');
                if(elementName.startsWith('component')) {
                    initComponent(element);
                }
                if(elementName.startsWith('control')) {
                    initControl(element);
                }
                element.classList.add("cres:initialized");
            }
        });
    }
}

const element = {
    component,
    control
}
export {
    element,
    initElement,
}
