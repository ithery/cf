import { initShowMore, ShowMore } from './ShowMore';
import { initShimmer, Shimmer } from './Shimmer';
import { initRepeater, Repeater } from './Repeater';
import { initGallery, Gallery } from './Gallery';
import { initProgressBar, ProgressBar } from './ProgressBar';
import { initNestable, Nestable } from './Nestable';
import { initImage, Image } from './Image';
import { initCountDownTimer, CountDownTimer } from './CountDownTimer';
import { initTreeView, TreeView } from './TreeView';
import { initCalendar, Calendar } from './Calendar';
import { initFileManager, FileManager } from './FileManager';

const initComponent = (element) => {
    const elementName = element.getAttribute('cres-element');
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
    if(elementName == 'component:Nestable') {
        initNestable(element);
    }
    if(elementName == 'component:Image') {
        initImage(element);
    }
    if(elementName == 'component:CountDownTimer') {
        initCountDownTimer(element);
    }
    if(elementName == 'component:TreeView') {
        initTreeView(element);
    }
    if(elementName == 'component:Calendar') {
        initCalendar(element);
    }
    if(elementName == 'component:FileManager') {
        initFileManager(element);
    }
};
const component = {
    ShowMore,
    Shimmer,
    Repeater,
    Gallery,
    ProgressBar,
    Nestable,
    Image,
    CountDownTimer,
    TreeView,
    Calendar,
    FileManager
};
export {
    component,
    initComponent
};
