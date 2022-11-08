import ProgressBar from "./ProgressBar";
import "./index.scss";
const initProgressBar = (element) => {
    new ProgressBar(element);
}

export {
    ProgressBar,
    initProgressBar
}
