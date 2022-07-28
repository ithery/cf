export const imageCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.image.enabled === true) {
        editorConfig.tools.image = {
            class: ImageTool,
            config: {
                endpoints: {
                    byFile: fieldConfig.uploadImageByFileEndpoint,
                    byUrl: fieldConfig.uploadImageByUrlEndpoint,
                },
                additionalRequestHeaders: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
            },
        };
    }
};
