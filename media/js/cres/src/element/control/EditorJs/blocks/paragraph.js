import { Paragraph } from "../tools";
export const paragraphCallback = (editorConfig, fieldConfig) => {
    console.log(fieldConfig.toolSettings.paragraph.placeholder);
    const params = {
        config : {}
    }
    if(fieldConfig.toolSettings.paragraph.placeholder) {
        params.config.placeholder = fieldConfig.toolSettings.paragraph.placeholder;
    }

    editorConfig.tools.paragraph = {
        class: Paragraph,
        inlineToolbar: true,
        ...params
    };
};
