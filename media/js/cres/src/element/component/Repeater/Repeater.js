
export default class Repeater {
    constructor(className, config = {}) {
        this.initDelete();
        this.initAdd();
    }

    initDelete() {

    }

    initAdd() {
        const deleteActions = document.querySelector('.cres-repeater-action-delete');
        deleteActions.forEach(function(item){
            item.addEventListener('click',(e)=>{
                e.preventDefault();

            });
        });
    }
}
