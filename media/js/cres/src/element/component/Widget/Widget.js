/**
 * Wires up CElement_Component_Widget's collapse/expand toggle (see
 * CElement_Component_Widget::setCollapse()). Only does anything when the
 * widget was actually built with collapse enabled -- otherwise no toggle
 * button exists in the header and this is a no-op.
 */
export default class Widget {
    constructor(className) {
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];

        // Guards against being constructed twice on the same root element.
        if (this.element.cresWidgetInstance) {
            return this.element.cresWidgetInstance;
        }
        this.element.cresWidgetInstance = this;

        const cresConfig = JSON.parse(this.element.getAttribute('cres-config') || '{}');
        this.collapse = !!cresConfig.collapse;

        if (!this.collapse) {
            return;
        }

        this.toggle = this.element.querySelector('.widget-collapse-toggle');
        if (!this.toggle) {
            return;
        }

        this.toggle.addEventListener('click', () => this.toggleCollapse());
    }

    toggleCollapse() {
        this.element.classList.toggle('widget-collapsed');
    }
}
