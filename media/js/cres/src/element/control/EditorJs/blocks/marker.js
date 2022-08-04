
export const markerCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.marker.enabled === true) {
        editorConfig.tools.marker = {
            class: Marker,
            shortcut: fieldConfig.toolSettings.marker.shortcut,
        };
    }
};
