import EditorJs from "./EditorJs";
import "./index.scss";
const initEditorJs = (element) => {
    new EditorJs(element);
}

export {
    EditorJs,
    initEditorJs
}
