export const imageCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.image.enabled === true) {

        const params = fieldConfig.toolSettings.image.isSimple ? {} : {
            config: {
                endpoints: {
                    byFile: fieldConfig.uploadImageByFileEndpoint,
                    byUrl: fieldConfig.uploadImageByUrlEndpoint,
                },
                additionalRequestHeaders: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
                },
            },
        }
        editorConfig.tools.image = {
            class: fieldConfig.toolSettings.image.isSimple ? SimpleImage : ImageTool,
            ...params

        };
    }
};
