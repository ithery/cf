import ProgressBar from './ProgressBar';
import './index.scss';
const initProgressBar = (element) => {
    return new ProgressBar(element);
};

export {
    ProgressBar,
    initProgressBar
};
