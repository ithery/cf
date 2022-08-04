export const checklistCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.checklist.enabled === true) {
        editorConfig.tools.checklist = {
            class: Checklist,
            inlineToolbar: fieldConfig.toolSettings.checklist.inlineToolbar,
            shortcut: fieldConfig.toolSettings.checklist.shortcut,
        };
    }
};
