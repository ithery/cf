export const moveMethod = (items) => {
    if(items.length==0) {
        return this.displayErrorResponse('No items selected, please select item');
    }
    this.performFmRequest('move', {items: items.map(function (item) {
        return item.name;
    })}).done(this.refreshFoldersAndItems);
};
