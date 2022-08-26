import { initShowMore, ShowMore } from "./ShowMore";
import { initShimmer, Shimmer } from "./Shimmer";
import { initRepeater, Repeater } from "./Repeater";
import { initGallery, Gallery } from "./Gallery";
import { initProgressBar, ProgressBar } from "./ProgressBar";

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
    if(elementName == 'component:ProgressBar') {
        initProgressBar(element);
    }
}
const component = {
    ShowMore
    ,Shimmer
    ,Repeater
    ,Gallery
    ,ProgressBar
}
export {
    component,
    initComponent,
}
