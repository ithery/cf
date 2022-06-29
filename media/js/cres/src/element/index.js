
import { elementRendered } from "../util/dom-observer";
import { initComponent, component } from "./component";

const getClassElement = (element) => {
    const classList = element.className.split(/\s+/);
    for (let i = 0; i < classList.length; i++) {
        if (classList[i].startsWith('cres:element')) {
            return classList[i];
        }
    }
    return null;

}

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
        elementRendered('[class*="cres\\:element\\:"]', (element)=>{
            if(!isElementInitialized(element)) {
                const className = getClassElement(element);
                if(className) {
                    if(className.startsWith('cres:element:component')) {
                        initComponent(element,className);
                    }
                }
                element.classList.add("cres:initialized");
            }
        });
    }
}
const element = {
    component
}
export {
    element,
    initElement,

}
