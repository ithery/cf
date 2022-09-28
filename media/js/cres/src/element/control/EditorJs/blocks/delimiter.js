export const delimiterCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.delimiter.enabled === true) {
        editorConfig.tools.delimiter = {
            class: Delimiter,
        };
    }
};
