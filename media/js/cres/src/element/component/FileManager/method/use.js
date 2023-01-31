export const useMethod = (items) => {
    let useTinymce3 = (url) => {
        if (!this.usingTinymce3()) {
            return;
        }

        let win = window.tinyMCEPopup.getWindowArg('window');
        win.document.getElementById(window.tinyMCEPopup.getWindowArg('input')).value = url;
        if (typeof (win.ImageDialog) != 'undefined') {
            // Update image dimensions
            if (win.ImageDialog.getImageData) {
                win.ImageDialog.getImageData();
            }

            // Preview if necessary
            if (win.ImageDialog.showPreviewImage) {
                win.ImageDialog.showPreviewImage(url);
            }
        }
        window.tinyMCEPopup.close();
    };

    let useTinymce4AndColorbox = (url) => {
        if (!window.cfm.usingTinymce4AndColorbox()) {
            return;
        }

        parent.document.getElementById(window.cfm.getUrlParam('field_name')).value = url;
        if (typeof parent.tinyMCE !== 'undefined') {
            parent.tinyMCE.activeEditor.windowManager.close();
        }
        if (typeof parent.$.fn.colorbox !== 'undefined') {
            parent.$.fn.colorbox.close();
        }
    };

    let useCkeditor3 = (url) => {
        if (!this.usingCkeditor3()) {
            return;
        }

        if (window.opener) {
            // Popup
            window.opener.CKEDITOR.tools.callFunction(window.cfm.getUrlParam('CKEditorFuncNum'), url);
        } else {
            // Modal (in iframe)
            parent.CKEDITOR.tools.callFunction(window.cfm.getUrlParam('CKEditorFuncNum'), url);
            parent.CKEDITOR.tools.callFunction(window.cfm.getUrlParam('CKEditorCleanUpFuncNum'));
        }
    };

    let useFckeditor2 = (url) => {
        if (!this.usingFckeditor2()) {
            return;
        }

        let p = url;
        let w = window.data.Properties.Width;
        let h = window.data.Properties.Height;
        window.opener.SetUrl(p, w, h);
    };
    let url;
    if(Array.isArray(items)) {
        url = items[0].url;
    } else {
        url = items.url;
    }


    if (typeof window.cfm !== 'undefined') {
        if (window.cfm.haveCallback('use')) {
            return window.cfm.doCallback('use', url);
        }
    }

    let callback = window.cfm.getUrlParam('callback');
    let useFileSucceeded = true;
    if (window.cfm.usingWysiwygEditor()) {
        useTinymce3(url);
        useTinymce4AndColorbox(url);
        useCkeditor3(url);
        useFckeditor2(url);
    } else if (callback && window[callback]) {
        window[callback](window.cfm.getSelectedItems());
    } else if (callback && parent[callback]) {
        parent[callback](window.cfm.getSelecteditems());
    } else if (window.opener) { // standalone button or other situations
        window.opener.SetUrl(window.cfm.getSelectedItems());
    } else {
        useFileSucceeded = false;
    }

    if (useFileSucceeded) {
        if (window.opener) {
            window.close();
        }
    } else {
        //console.log('window.opener not found');
        // No editor found, open/download file using browser's default method
        window.open(url);
    }
};
