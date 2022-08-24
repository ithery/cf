import { initShowMore, ShowMore } from "./ShowMore";
import { initShimmer, Shimmer } from "./Shimmer";
import { initRepeater, Repeater } from "./Repeater";
import { initGallery, Gallery } from "./Gallery";

const initComponent = (element) => {
    const elementName  = element.getAttribute('cres-element');
    if(elementName == 'component:ShowMore') {
        initShowMore(element);
    }
    if(elementName == 'component:Shimmer') {
        initShimmer(element);
    }
    if(elementName == 'component:Repeater') {
        initRepeater(element);
    }
    if(elementName == 'component:Gallery') {
        initGallery(element);
    }
}
const component = {
    ShowMore
    ,Shimmer
    ,Repeater
    ,Gallery
}
export {
    component,
    initComponent,
}
