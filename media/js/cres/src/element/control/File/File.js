import { matchesAcceptFile } from "../utils/acceptFile";

export default class File {
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
        this.element.addEventListener('change', (e) => this.validate(e));
    }

    validate(e) {
        const acceptFile = this.config.acceptFile;
        if (!acceptFile) {
            return;
        }
        const files = Array.prototype.slice.call(e.target.files || []);
        const invalidFile = files.find((file) => !matchesAcceptFile(file, acceptFile));
        if (invalidFile) {
            const messages = this.config.messages || {};
            const template = messages.acceptFileNotAllowed || 'File type not allowed: :fileName. Allowed: :acceptFile';
            const message = template
                .replace(':fileName', invalidFile.name)
                .replace(':acceptFile', acceptFile);
            window.cresenity.message('error', message);
            e.target.value = '';
        }
    }
}
