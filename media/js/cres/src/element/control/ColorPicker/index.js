import ColorPicker from "./ColorPicker";
import "./index.scss";
const initColorPicker = (element) => {
    new ColorPicker(element);
}

export {
    ColorPicker,
    initColorPicker
}
