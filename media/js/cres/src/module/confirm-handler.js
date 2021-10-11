

export const defaultConfirmHandler = (el, options, confirmCallback) => {
    if(window.bootbox) {
        return bootboxConfirmhandler(el, options, confirmCallback);
    }

    // eslint-disable-next-line no-alert
    let confirmed = window.confirm(options.message);
    confirmCallback(confirmed);
};

const bootboxConfirmhandler = (el, options, confirmCallback) => {
    window.bootbox.confirm({
        className: 'capp-modal-confirm',
        message: options.message,
        callback: confirmCallback
    });
};

export const confirmFromElement = (el, handler, defaultMessage) => {
    let ahref = $(el).attr('href');
    let message = $(el).attr('data-confirm-message');
    let noDouble = $(el).attr('data-no-double');
    let clicked = $(el).attr('data-clicked');


    let btn = $(el);
    btn.attr('data-clicked', '1');
    if(btn.attr('type') === 'submit') {
        btn.attr('data-submitted', '1');
    }
    if (noDouble) {
        if (clicked) {
            return false;
        }
    }

    const confirmCallback = (confirmed) => {
        if (confirmed) {
            if (ahref) {
                window.location.href = ahref;
            } else if (btn.attr('type') === 'submit') {
                btn.closest('form').submit();
            } else {
                btn.on('click');
            }
        } else {
            btn.removeAttr('data-clicked');
            btn.removeAttr('data-submitted');
        }
        setTimeout(() => {
            let modalExists = $('.modal:visible').length > 0;
            if (!modalExists) {
                $('body').removeClass('modal-open');
            } else {
                $('body').addClass('modal-open');
            }
        }, 750);
    };


    message = message ? message : (defaultMessage ? defaultMessage : 'Are you sure?');
    const options = {
        message
    };
    handler(btn, options, confirmCallback);
};
