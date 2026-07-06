export function moveMethod(items) {
    if(items.length==0) {
        return this.displayErrorResponse('No items selected, please select item');
    }
    let itemNames = items.map(function (item) {
        return item.name;
    });
    this.performFmRequest('move', {items: itemNames}, 'html').done((data) => {
        let folders = JSON.parse(data).data.folders;
        this.showMovePicker(folders, itemNames);
    });
}
