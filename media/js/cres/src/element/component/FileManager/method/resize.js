export function resizeMethod(item) {
    this.performFmRequest('resize', {img: item.name})
        .done(this.hideNavAndShowEditor);
}
