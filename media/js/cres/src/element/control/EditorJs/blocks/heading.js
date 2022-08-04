import { Header } from "../tools";

export const headingCallback = (editorConfig, fieldConfig) => {
    if (fieldConfig.toolSettings.header.enabled === true) {
        editorConfig.tools.header = {
            class: Header,
            config: {
                placeholder: fieldConfig.toolSettings.header.placeholder,
            },
            shortcut: fieldConfig.toolSettings.header.shortcut,
            tunes: ['alignment'],
        };
    }
};
