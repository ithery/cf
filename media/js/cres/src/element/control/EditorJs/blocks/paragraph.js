import { Paragraph } from "../tools";
export const paragraphCallback = (editorConfig) => {
    editorConfig.tools.paragraph = {
        class: Paragraph,
        inlineToolbar: true,
    };
};
