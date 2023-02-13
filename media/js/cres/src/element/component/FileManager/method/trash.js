export const trashMethod = (items) => {
    this.notify(this.settings.lang['message-delete'], () => {
        this.performFmRequest('delete', {
            items: items.map(function (item) {
                return item.name;
            })
        }).done(this.refreshFoldersAndItems);
    });
};
