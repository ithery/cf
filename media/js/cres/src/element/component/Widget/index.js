import Widget from './Widget';
import './index.scss';

const initWidget = (element) => {
    return new Widget(element);
};

export {
    Widget,
    initWidget
};
