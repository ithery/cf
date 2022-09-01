import cresEditorJs from "./CresEditorJs";

export default class EditorJs {
    constructor(className, config = {}) {

        // all html elements
        const elements = className instanceof Element ? [className] : [].slice.call(document.querySelectorAll(className));


        elements.map((item, index) => {
            const editorjsSave = async (holderId) => {
                const editorHolder = document.getElementById(holderId);
                const editorInput = document.getElementById(editorHolder.getAttribute('data-input-id'));
                const editor = editorHolder.editor;

                const savePromise = editor.save().then((outputData) => {
                    editorInput.value = JSON.stringify(outputData);
                });

                await savePromise;
            }

            const listenForFormSubmit = (holderId) => {
                const editorHolder = document.getElementById(holderId);
                const editorForm = editorHolder.closest('form');


                if (!editorForm.hasEditorjsListener) {
                    editorForm.addEventListener('submit', (event) => {
                        event.preventDefault();

                        const form = event.target;
                        form.hasEditorjsListener = true;

                        // save all editors in form
                        const editorHolders = form.querySelectorAll('.editorjs-holder');
                        const savePromises = [];
                        editorHolders.forEach((holder) => {
                            savePromises.push(editorjsSave(holder.id));
                        });

                        Promise.all(savePromises).then(() => {
                            form.submit();
                        });
                    });
                }
            }
            const configData = JSON.parse(item.getAttribute('cres-config'));
            const configGlobal = config;

            const mergedConfig = { ...configGlobal, ...configData };

            const {editorSettings, toolSettings} = mergedConfig;
            let currentContent = item.value;
            if(typeof currentContent == 'string') {
                currentContent = JSON.parse(currentContent);
            }
            const holder = item.getAttribute('data-holder-id');
            const editorHolder = document.getElementById(holder);
            const editorConfig = {
                /**
                 * Wrapper of Editor
                 */
                holder,
                /**
                     * Default placeholder
                     */
                placeholder: editorSettings.placeholder,
                /**
                     * Enable autofocus
                     */
                autofocus: editorSettings.autofocus,
                /**
                     * Initial Editor data
                     */
                data: currentContent,
                /**
                     * Min height of editor
                     */
                minHeight: 35,
                onReady() {
                },
                onChange: async function () {
                    await editorjsSave(this.holder);
                }
            }
            if(editorSettings.initialBlock) {
                editorConfig.defaultBlock = editorSettings.initialBlock;
            }
            listenForFormSubmit(holder);

            editorHolder.editor = cresEditorJs.getInstance(editorConfig,mergedConfig);

        });
    }




}
