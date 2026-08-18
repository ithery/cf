export default class TreeView {
    constructor(element) {
        this.element = element;

        const config = JSON.parse(element.getAttribute('cres-config') || '{}');
        this.config = config;

        this.init();
    }

    init() {
        const $ = window.jQuery;
        if (!$ || !$.fn.jstree) {
            return;
        }

        const core = {
            check_callback: true,
            themes: {
                responsive: false
            }
        };

        if (this.config.ajax) {
            core.data = {
                url: this.config.nodeUrl,
                dataType: 'json',
                data: (node) => ({
                    id: node.id,
                    parent: node.parent,
                    parents: node.parents
                })
            };
        } else {
            core.data = this.config.data || [];
        }

        $(this.element).jstree({ core }).on('changed.jstree', (e, data) => {
            this.element.dispatchEvent(new CustomEvent('cres:treeview:select', {
                detail: { selected: (data && data.selected) || [] }
            }));
        });
    }
}
