export function renameMethod(item) {
    this.dialog(this.settings.lang['message-rename'], item.name, (new_name) => {
        this.performFmRequest('rename', {
            file: item.name,
            new_name: new_name
        }).done((response) => this.refreshFoldersAndItems(response, this.settings.lang['message-rename-success']));
    });
}
