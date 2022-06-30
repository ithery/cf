import { initShowMore, ShowMore } from "./ShowMore";

const initComponent = (element) => {
    const elementName  = element.getAttribute('cres-element');
    if(elementName == 'component:ShowMore') {
        initShowMore(element);
    }
}
const component = {
    ShowMore
}
export {
    component,
    initComponent,
}
