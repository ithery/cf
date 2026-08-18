
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
        if(this.config.applyJs == 'minicolor') {
            this.applyMiniColor();
        }
    }
    applyMiniColor() {
        let minicolorsOptions = this.config.minicolorsOptions ?? {};
        const $el = $(this.element);
        $el.minicolors(minicolorsOptions);
    }
}
