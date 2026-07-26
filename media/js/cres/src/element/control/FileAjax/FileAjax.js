import { matchesAcceptFile } from "../utils/acceptFile";

export default class FileAjax {
    constructor(className, config = {}) {
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];
        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.config = { ...config, ...cresConfig };
        this.id = this.config.id;
        this.ajaxName = this.config.ajaxName;
        this.ajaxUrl = this.config.ajaxUrl;
        this.maxUploadSize = (this.config.maxUploadSize || 0) * 1024 * 1024;
        this.acceptFile = this.config.acceptFile;
        this.bind();
    }

    find(selector) {
        return $(this.element).find(selector);
    }

    bind() {
        this.find('.fileupload-new, .btn-file span').on('click', () => {
            this.find('#input-temp-' + this.id).trigger('click');
        });

        this.find('.fileupload-remove').on('click', () => {
            this.find('.fileupload-preview span').html('');
            this.find('#' + this.id).val('').trigger('change');
            $(this.element).removeClass('fileupload-exists').addClass('fileupload-new');
        });

        this.find('#input-temp-' + this.id).on('change', (e) => this.onFileSelected(e));
    }

    onFileSelected(e) {
        const self = this;
        $.each(e.target.files, function (i, file) {
            if (!matchesAcceptFile(file, self.acceptFile)) {
                window.cresenity.message('error', 'File type not allowed: ' + file.name + '. Allowed: ' + self.acceptFile);

                return;
            }
            const reader = new FileReader();
            reader.fileName = file.name;
            reader.onload = (event) => {
                const filesize = event.total;
                if (self.maxUploadSize && filesize > self.maxUploadSize) {
                    window.cresenity.message('error', 'File Size is more than ' + self.config.maxUploadSize + ' MB');
                } else {
                    self.upload(file, event.target.fileName);
                }
            };
            reader.readAsDataURL(file);
        });
        $(e.target).val('');
    }

    upload(file, fileName) {
        const self = this;
        this.find('.fileupload-preview span').html(fileName);
        $(this.element).removeClass('fileupload-new').addClass('fileupload-exists');
        this.find('.fileupload-preview').addClass('loading spinner');
        this.find('.fileupload-preview').find('span').off('click').on('click', () => {
            this.find('#input-temp-' + this.id).trigger('click');
        });

        const data = new FormData();
        data.append(this.ajaxName + '[]', file);
        data.append(this.ajaxName + '_filename[]', file.name);

        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (this.readyState == 4 && this.status == 200) {
                const response = JSON.parse(this.responseText);
                window.cresenity.handleJsonResponse(response, (dataFile) => {
                    self.find('#' + self.id).val(dataFile.fileId);
                    self.find('.fileupload-preview span').html(dataFile.fileName);
                    self.find('.fileupload-preview').removeClass('loading').removeClass('spinner');
                    self.find('#' + self.id).trigger('change');
                });
            } else if (this.readyState == 4 && this.status != 200) {
                window.cresenity.message('error', 'File Upload Failed');
            }
        };
        xhr.open('post', this.ajaxUrl);
        xhr.send(data);
    }
}
