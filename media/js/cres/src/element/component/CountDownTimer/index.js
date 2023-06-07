import CountDownTimer from './CountDownTimer';
import './index.scss';
const initCountDownTimer = (element) => {
    return new CountDownTimer(element);
};

export {
    CountDownTimer,
    initCountDownTimer
};
