import { initShowMore, ShowMore } from "./ShowMore";

const initComponent = (element,className) => {
    if(className=="cres:element:component:ShowMore") {
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
