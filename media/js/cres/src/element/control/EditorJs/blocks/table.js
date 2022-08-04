export const tableCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.table.enabled === true) {
        editorConfig.tools.table = {
            class: Table,
            inlineToolbar: fieldConfig.toolSettings.table.inlineToolbar,
        };
    }
};
