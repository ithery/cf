import Repeater from './Repeater';
import './index.scss';
const initRepeater = (element) => {
    return new Repeater(element);
};

export {
    Repeater,
    initRepeater
};
