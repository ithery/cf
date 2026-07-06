import { initHistory } from './init';
import ElementHistoryState from './ElementHistoryState';

let CresenityHistory = window.History = window.History||{}; // Public History Object


initHistory(CresenityHistory);

CresenityHistory.ElementState = ElementHistoryState;

export { ElementHistoryState };
export default CresenityHistory;
