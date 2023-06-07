
export default class ProgressBar {
    constructor(className, config = {}) {
        this.elements =
        className instanceof Element
            ? [className]
            : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];
        this.bar = this.element.querySelector('.cres-progress-bar');
        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.config = { ...config, ...cresConfig };
        this.value = this.config.value;
        this.initProgressBar();
    }

    initProgressBar() {
        this.updateBar();
        if(this.config.updateMethod) {
            window[this.config.updateMethod] = (data) => {
                this.value = data.value;
                this.maxValue = data.maxValue;
                this.minValue = data.minValue;
                this.updateBar();
            };
        }
    }

    updateBar() {
        const length = this.config.maxValue - this.config.minValue;
        const offset = this.value - this.config.minValue;
        const percent = length!= 0 ? offset/length * 100 : 0;
        this.bar.style.width = percent + '%';
    }
}
