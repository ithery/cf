import Calendar from './Calendar';
import './index.scss';
const initCalendar = (element) => {
    return new Calendar(element);
};

export {
    Calendar,
    initCalendar
};
