import svgEyeSlash from '../../../svg/icon/eye-slash.svg';
import svgEye from '../../../svg/icon/eye.svg';

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
        $buttonEye.html(svgEyeSlash);
        // $eye
        //     .wrap('<span class="input-group-btn"></span>')
        //     .wrap('<button type="button" class="btn btn-view" tabindex="-1"></button>');

        $el.next('span.input-group-btn').find('button.btn').click(function (event) {
            // $eye.toggleClass('fa-eye-slash fa-eye');

            if ($el.attr('type') === 'password') {
                $buttonEye.html(svgEye);
                $el.attr('type', 'text');
            } else {
                $el.attr('type', 'password');
                $buttonEye.html(svgEyeSlash);
            }
            event.stopPropagation();
            event.preventDefault();
        });
    }
}
