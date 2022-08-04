export const rawCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.raw.enabled === true) {
        editorConfig.tools.raw = {
            class: RawTool,
            config: {
                placeholder: fieldConfig.toolSettings.raw.placeholder,
            },
        };
    }
};
