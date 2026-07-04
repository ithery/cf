const CODE_TYPES = [
    'text', 'txt', 'md', 'htaccess', 'log', 'sql', 'php', 'js', 'json', 'css', 'html'
];
const IMAGE_TYPES = ['png', 'jpg', 'jpeg', 'bmp', 'gif'];

export default class Tree {
    constructor(element) {
        this.element = element;

        const config = JSON.parse(element.getAttribute('cres-config') || '{}');
        this.nodeUrl = config.nodeUrl;
        this.contentUrl = config.contentUrl;

        this.init();
    }

    init() {
        const $ = window.jQuery;
        if (!$) {
            return;
        }

        this.$nav = $(this.element).find('.cres-tree-nav');
        this.$content = $(this.element).find('.cres-tree-content');

        this.showDefaultContent();

        this.$nav.jstree({
            core: {
                data: {
                    url: this.nodeUrl,
                    dataType: 'json',
                    data: (node) => ({
                        id: node.id,
                        parent: node.parent,
                        parents: node.parents
                    })
                },
                check_callback: true,
                multiple: false,
                themes: {
                    responsive: false
                }
            }
        }).on('changed.jstree', (e, data) => {
            if (data && data.selected && data.selected.length) {
                this.loadContent(data.selected.join(':'));
            } else {
                this.showDefaultContent();
            }
        });
    }

    loadContent(id) {
        const $ = window.jQuery;
        $.ajax({
            type: 'get',
            dataType: 'json',
            url: `${this.contentUrl}?operation=get_content&id=${id}`,
            success: (response) => {
                if (response && typeof response.type !== 'undefined') {
                    this.renderContent(response.type, response.content);
                }
            }
        });
    }

    showDefaultContent() {
        this.$content.html('<div class="cres-tree-content-default">Select a node from the tree.</div>');
    }

    renderContent(type, content) {
        if (CODE_TYPES.indexOf(type) !== -1) {
            const $pre = window.jQuery('<pre class="cres-tree-content-code"></pre>');
            $pre.text(content);
            this.$content.empty().append($pre);
        } else if (IMAGE_TYPES.indexOf(type) !== -1) {
            this.$content.html(`<img class="cres-tree-content-image" src="${content}" alt="" />`);
        } else {
            this.$content.html(content);
        }
    }
}
