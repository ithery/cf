export const listCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.list.enabled === true) {
        editorConfig.tools.list = {
            class: List,
            inlineToolbar: fieldConfig.toolSettings.list.inlineToolbar,
            shortcut: fieldConfig.toolSettings.list.shortcut,
        };
    }
};
