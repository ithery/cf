export const inlineCodeCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.inlineCode.enabled === true) {
        editorConfig.tools.inlineCode = {
            class: InlineCode,
            shortcut: fieldConfig.toolSettings.inlineCode.shortcut,
        };
    }
};
