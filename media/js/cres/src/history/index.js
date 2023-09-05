import { initHistory } from './init';

let CresenityHistory = window.History = window.History||{}; // Public History Object


initHistory(CresenityHistory);

export default CresenityHistory;
