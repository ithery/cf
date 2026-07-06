/**
 * Wires up CElement_List_TabList's tab switching (see its build() and the
 * cresenity.element.list.tab-list.index view for the markup this drives).
 * Ajax mode reuses cresenity.reload() (see Cresenity.js) the same way the
 * old inline jQuery did; non-ajax mode just shows/hides the matching
 * .tab-pane sibling.
 */
export default class TabList {
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
        if (this.element.cresTabListInstance) {
            return this.element.cresTabListInstance;
        }
        this.element.cresTabListInstance = this;

        const cresConfig = JSON.parse(this.element.getAttribute('cres-config') || '{}');
        this.ajax = !!cresConfig.ajax;
        this.paramRequest = cresConfig.paramRequest || {};
        this.widgetBodyClass = cresConfig.widgetBodyClass || '';

        // Delegated (not bound per-link) so ajax-injected tab content that
        // itself contains a nested TabList doesn't need re-binding, and so a
        // nested TabList's own links aren't handled twice (see the
        // ownerTabList check in activate()/the click handler below).
        this.element.addEventListener('click', (e) => {
            let link = e.target.closest('.tab-ajax-load');
            if (!link || this.ownerTabList(link) !== this.element) {
                return;
            }
            e.preventDefault();
            this.activate(link);
        });

        let activeLink = Array.from(this.element.querySelectorAll('.tab-ajax-load.active'))
            .find((link) => this.ownerTabList(link) === this.element);
        if (activeLink) {
            this.activate(activeLink);
        }
    }

    /**
     * The closest TabList root a given nav link actually belongs to --
     * distinguishes this instance's own links from a nested TabList's
     * (ajax-loaded tab content can itself contain another TabList).
     *
     * @param {Element} link
     * @return {null|Element}
     */
    ownerTabList(link) {
        return link.closest('[cres-element="component:TabList"]');
    }

    /**
     * @param {Element} link
     */
    activate(link) {
        let li = link.closest('li');
        let nav = li ? li.parentElement : link.parentElement;
        Array.from(nav.children).forEach((child) => child.classList.remove('active'));
        (li || link).classList.add('active');
        nav.querySelectorAll(':scope > li > a, :scope > a').forEach((a) => a.classList.remove('active'));
        link.classList.add('active');

        let widgetHeader = this.element.querySelector('.tab-widget-header');
        if (widgetHeader) {
            let dataIcon = link.getAttribute('data-icon');
            let dataText = link.textContent.trim();
            let iconEl = widgetHeader.querySelector('.icon i');
            if (dataIcon && iconEl) {
                iconEl.className = dataIcon;
            }
            let h5 = widgetHeader.querySelector('h5');
            if (dataText && h5) {
                h5.textContent = dataText;
            }
        }

        let widgetBody = this.element.querySelector('.tab-widget-body');
        if (widgetBody) {
            let dataClass = link.getAttribute('data-class');
            widgetBody.className = (this.widgetBodyClass + ' tab-widget-body ' + (dataClass || '')).trim();
        }

        if (this.ajax) {
            let target = link.getAttribute('data-target');
            window.cresenity.reload({
                selector: '#' + target,
                url: link.getAttribute('data-url'),
                method: link.getAttribute('data-method') || 'get',
                dataAddition: this.paramRequest
            });
        } else {
            let tabId = link.getAttribute('data-tab');
            let pane = document.getElementById(tabId);
            if (pane) {
                Array.from(pane.parentElement.children).forEach((sibling) => {
                    sibling.style.display = 'none';
                });
                pane.style.display = '';
            }
        }
    }
}
