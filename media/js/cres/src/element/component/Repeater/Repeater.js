import { addClass } from '../../../dom/classes';
import { getChildrenByClassName } from '../../../dom/finder';
const getIndexInArray = function(array, el) {
    return Array.prototype.indexOf.call(array, el);
};

function getBlocksList(element) {
    element.blocks = getChildrenByClassName(
        element.blockWrapper[0],
        'cres-repeater-row'
    );
}
function addBlock(element) {
    let clone;
    if (element.blocks.length > 0) {
        clone = element.blocks[element.blocks.length - 1].cloneNode(true);

    } else {
        clone = element.firstBlock.cloneNode(true);

    }

    if (element.cloneClass) {
        addClass(clone, element.cloneClass)
    };
    // modify name/for/id attributes
    updateBlockAttrs(clone);

    element.blockWrapper[0].appendChild(clone);
    // update blocks list
    getBlocksList(element);
}
function removeBlock(element, trigger) {
    var block = trigger.closest('.cres-repeater-row');
    if (block) {
        var index = getIndexInArray(element.blocks, block);
        block.remove();
        // update blocks list
        getBlocksList(element);
        // need to reset all blocks after that -> name/for/id values
        for (var i = index; i < element.blocks.length; i++) {
            updateBlockAttrs(
                element.blocks[i],
            );
        }
    }
}
function updateBlockAttrs(block) {

}
function initRepeater(element) {
    if (
        element.addNew.length < 1 ||
        element.blocks.length < 1 ||
        element.blockWrapper.length < 1
    )
        return;
    element.firstBlock = element.blocks[0].cloneNode(true);

    // detect click on a Remove button
    element.element.addEventListener('click', function (event) {
        var deleteBtn = event.target.closest('.cres-repeater-action-delete');
        if (deleteBtn) {
            event.preventDefault();
            removeBlock(element, deleteBtn);
        }
    });

    // detect click on Add button
    element.addNew[0].addEventListener('click', function (event) {
        event.preventDefault();
        addBlock(element);
    });
}
export default class Repeater {
    constructor(className, config = {}) {
        window.myrepeater = this;
        // all html elements
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];
        this.blockWrapper = this.element.getElementsByClassName(
            'cres-repeater-wrapper'
        );
        this.blocks = false;
        getBlocksList(this);
        this.firstBlock = false;
        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.config = { ...config, ...cresConfig };
        this.addNew = this.element.getElementsByClassName(
            'cres-repeater-action-add'
        );
        this.cloneClass = this.element.getAttribute('data-repeater-class');
        this.inputName = this.element.getAttribute('data-repeater-input-name');
        initRepeater(this);

    }
}
