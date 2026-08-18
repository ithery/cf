

const hideDelete = (element)=> {
    element.blocks.forEach((item)=>{
        const deletes = item.getElementsByClassName('cres-repeater-action-delete');
        for (let i = 0; i < deletes.length; i++) {
            const deleteAction = deletes[i];
            // const compStyles = getComputedStyle(deleteAction);
            // const displayBefore = compStyles.getPropertyValue('display');
            // deleteAction.setAttribute('data-display',displayBefore);
            deleteAction.style.display = 'none';
        }
    });
};

const showDelete = (element) => {
    element.blocks.forEach((item)=>{
        const deletes = item.getElementsByClassName('cres-repeater-action-delete');
        for (let i = 0; i < deletes.length; i++) {
            const deleteAction = deletes[i];
            // const display = deleteAction.getAttribute('data-display') ?? 'block';

            // deleteAction.removeAttribute('data-display');
            deleteAction.style.display = 'block';
        }
    });
};

const hideAdd = (element) => {
    for (let i = 0; i < element.addNew.length; i++) {
        element.addNew[i].style.display = 'none';
    }
};

const showAdd = (element) => {
    for (let i = 0; i < element.addNew.length; i++) {
        element.addNew[i].style.display = '';
    }
};

export const updateUi = (element) => {
    if (element.minItem >= element.blocks.length) {
        hideDelete(element);
    } else {
        showDelete(element);
    }

    if (element.maxItem && element.blocks.length >= element.maxItem) {
        hideAdd(element);
    } else {
        showAdd(element);
    }
};
