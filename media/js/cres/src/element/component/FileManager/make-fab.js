export const makeFab = function (menu, options) {
    menu.addClass('fab-wrapper');
    let toggler = $('<a>')
        .addClass('fab-button fab-toggle')
        .append($('<i>').addClass('fas fa-plus'))
        .click(function () {
            menu.toggleClass('fab-expand');
        });
    menu.append(toggler);
    options.buttons.forEach(function (button) {
        toggler.before(
            $('<a>').addClass('fab-button fab-action')
                .attr('data-label', button.label)
                .attr('id', button.attrs.id)
                .append($('<i>').addClass(button.icon))
                .click(function () {
                    menu.removeClass('fab-expand');
                })
        );
    });
};
