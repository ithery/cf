
export default class Password {
    constructor(className, config = {}) {
        this.elements =
            className instanceof Element
                ? [className]
                : [].slice.call(document.querySelectorAll(className));
        if (this.elements.length < 1) {
            return;
        }
        this.element = this.elements[0];
        const cresConfig = JSON.parse(this.element.getAttribute('cres-config'));
        this.config = { ...config, ...cresConfig };
        if(this.config.toggleVisibility) {
            this.makeToggleable();
        }
    }

    makeToggleable() {
        const $el = $(this.element);
        //const $eye = $('<i class="fas fa-eye" aria-hidden="true"></i>');

        if ($el.parent('div.input-group').length == 0) {
            $el.wrap('<div class="input-group"></div>');
        }

        // insert into DOM first, as wrap() only works on DOM attached nodes.
        //$el.after($eye);
        const $spanEye = $('<span class="input-group-btn input-group-append"></span>');
        const $buttonEye = $('<button type="button" class="btn btn-view" tabindex="-1"></button>');
        $el.after($spanEye.append($buttonEye));
        $buttonEye.html(`
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="15" height="15">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>`
        );
        // $eye
        //     .wrap('<span class="input-group-btn"></span>')
        //     .wrap('<button type="button" class="btn btn-view" tabindex="-1"></button>');

        $el.next('span.input-group-btn').find('button.btn').click(function (event) {
            // $eye.toggleClass('fa-eye-slash fa-eye');

            if ($el.attr('type') === 'password') {
                $buttonEye.html(`
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                    </svg>`)
                $el.attr('type', 'text');
            } else {
                $el.attr('type', 'password');
                $buttonEye.html(`
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" width="15" height="15">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>`
                )
            }
            event.stopPropagation();
            event.preventDefault();
        });
    }
}
