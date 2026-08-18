import Sortable from "./Sortable";
import "./index.scss";
const initSortable = (element) => {
    new Sortable(element);
}

export {
    Sortable,
    initSortable
}
