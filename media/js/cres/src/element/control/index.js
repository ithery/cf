import { initEditorJs, EditorJs } from "./EditorJs";
import { initPassword, Password } from "./Password";
import { initColorPicker, ColorPicker } from "./ColorPicker";

const initControl = (control) => {
    const controlName  = control.getAttribute('cres-element');
    if(controlName == 'control:EditorJs') {
        initEditorJs(control);
    }
    if(controlName == 'control:Password') {
        initPassword(control);
    }
    if(controlName == 'control:ColorPicker') {
        initColorPicker(control);
    }
}
const control = {
    EditorJs,
    Password,
    ColorPicker
}
export {
    control,
    initControl,
}
