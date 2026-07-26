import { initEditorJs, EditorJs } from "./EditorJs";
import { initPassword, Password } from "./Password";
import { initColorPicker, ColorPicker } from "./ColorPicker";
import { initAutoNumeric, AutoNumeric } from "./AutoNumeric";
import { initSortable, Sortable} from "./Sortable";
import { initSelectTwo, SelectTwo } from "./SelectTwo";
import { initText, Text } from "./Text";
import { initFile, File } from "./File";
import { initFileAjax, FileAjax } from "./FileAjax";

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
    if(controlName == 'control:AutoNumeric') {
        initAutoNumeric(control);
    }
    if(controlName == 'control:Sortable') {
        initSortable(control);
    }
    if(controlName == 'control:SelectTwo') {
        initSelectTwo(control);
    }
    if(controlName == 'control:Text') {
        initText(control);
    }
    if(controlName == 'control:File') {
        initFile(control);
    }
    if(controlName == 'control:FileAjax') {
        initFileAjax(control);
    }
}
const control = {
    EditorJs,
    Password,
    ColorPicker,
    AutoNumeric,
    Sortable,
    SelectTwo,
    Text,
    File,
    FileAjax
}
export {
    control,
    initControl,
}
