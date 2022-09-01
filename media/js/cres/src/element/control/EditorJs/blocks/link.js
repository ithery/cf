export const linkCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.link.enabled === true) {
        editorConfig.tools.linkTool = {
            class: LinkTool,
            config: {
                endpoint: fieldConfig.fetchUrlEndpoint,
            },
        };
    }
};
