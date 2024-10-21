
export default class ColorPicker {
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
        if(this.config.applyJs == 'autoNumeric') {
            this.applyAutoNumeric();
        }
    }
    applyAutoNumeric() {
        const $el = $(this.element);
        $el.autoNumeric('init');
    }
}
