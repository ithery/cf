import { initEditorJs, EditorJs } from "./EditorJs";
import { initPassword, Password } from "./Password";

const initControl = (control) => {
    const controlName  = control.getAttribute('cres-element');
    if(controlName == 'control:EditorJs') {
        initEditorJs(control);
    }
    if(controlName == 'control:Password') {
        initPassword(control);
    }
}
const control = {
    EditorJs,
    Password
}
export {
    control,
    initControl,
}
