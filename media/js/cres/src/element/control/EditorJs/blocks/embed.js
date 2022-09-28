export const embedCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.embed.enabled === true) {
        editorConfig.tools.embed = {
            class: Embed,
            inlineToolbar: fieldConfig.toolSettings.embed.inlineToolbar,
            config: {
                services: {
                    codepen: fieldConfig.toolSettings.embed.services.codepen,
                    imgur: fieldConfig.toolSettings.embed.services.imgur,
                    vimeo: fieldConfig.toolSettings.embed.services.vimeo,
                    youtube: fieldConfig.toolSettings.embed.services.youtube,
                },
            },
        };
    }
};
