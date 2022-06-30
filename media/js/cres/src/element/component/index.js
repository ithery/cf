import { initShowMore, ShowMore } from "./ShowMore";
import { initShimmer, Shimmer } from "./Shimmer";

const initComponent = (element) => {
    const elementName  = element.getAttribute('cres-element');
    if(elementName == 'component:ShowMore') {
        initShowMore(element);
    }
    if(elementName == 'component:Shimmer') {
        initShimmer(element);
    }
}
const component = {
    ShowMore
    ,Shimmer
}
export {
    component,
    initComponent,
}
