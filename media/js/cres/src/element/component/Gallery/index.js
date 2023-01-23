import Gallery from './Gallery';
import './index.scss';
const initGallery = (element) => {
    return new Gallery(element);
};

export {
    Gallery,
    initGallery
};
