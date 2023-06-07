

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

export const updateUi = (element) =>{
    if(element.minItem >= element.blocks.length) {
        hideDelete(element);
    } else {
        showDelete(element);
    }
};
