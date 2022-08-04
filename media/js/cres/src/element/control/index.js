import { initEditorJs, EditorJs } from "./EditorJs";

const initControl = (control) => {
    const controlName  = control.getAttribute('cres-element');
    if(controlName == 'control:EditorJs') {
        initEditorJs(control);
    }
}
const control = {
    EditorJs
}
export {
    control,
    initControl,
}
