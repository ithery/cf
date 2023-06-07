import { Alignment } from '../tune';
export const alignmentCallback = (editorConfig) => {
    editorConfig.tools.alignment = {
        class: Alignment,
        config: {
            default: 'left',
            blocks: {
                header: 'left',
                list: 'left'
            }
        }
    };
};
