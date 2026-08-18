export function trashMethod(items) {
    this.notify(this.settings.lang['message-delete'], () => {
        // Google Drive-style optimistic delete: hide the selected items right
        // away instead of waiting on the ajax round-trip. refreshFoldersAndItems()
        // below re-syncs with the server regardless, so anything that actually
        // failed to delete reappears once the real item list comes back.
        let selectedIds = this.selected.slice();
        selectedIds.forEach((id) => {
            this.find('[data-id=' + id + ']').addClass('d-none');
        });
        this.clearSelected();

        this.performFmRequest('delete', {
            items: items.map(function (item) {
                return item.name;
            })
        }).done((response) => {
            this.refreshFoldersAndItems(response, this.settings.lang['message-delete-success']);
        });
    });
}
