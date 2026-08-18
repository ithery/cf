import SortableLib from "sortablejs";
export default class Sortable {
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
        this.container = document.getElementById(this.config.containerId);
        this.input = document.getElementById(this.config.inputId);
        this.ul = document.createElement("ul");
        this.container.appendChild(this.ul);
        if(this.config.list) {
            Object.entries(this.config.list).forEach(([key, value]) => {
                const li = document.createElement("li");
                li.setAttribute("data-id", key);
                li.textContent = value;
                this.ul.appendChild(li);
            });
        }
        this.ul.classList.add("sortable-list");
        this.makeSortable();
    }

    makeSortable() {
        const sortable = new SortableLib(this.ul, {
            animation: 150,
            onEnd: function() {
                updateOrder();
            }
        });

        const updateOrder = () => {
            let order = [];
            this.ul.querySelectorAll("li").forEach(function(el) {
                order.push(el.getAttribute("data-id"));
            });
            this.input.value = JSON.stringify(order);
        }

        // inisialisasi pertama kali
        updateOrder();
    }
}
