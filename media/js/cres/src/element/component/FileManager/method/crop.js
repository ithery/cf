export function cropMethod(item) {
    this.performFmRequest('crop', {img: item.name})
        .done(this.hideNavAndShowEditor);
}
