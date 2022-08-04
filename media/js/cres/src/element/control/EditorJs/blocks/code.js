export const codeCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.code.enabled === true) {
        editorConfig.tools.code = {
            class: CodeTool,
            shortcut: fieldConfig.toolSettings.code.shortcut,
            config: {
                placeholder: fieldConfig.toolSettings.code.placeholder,
            },
        };
    }
};
