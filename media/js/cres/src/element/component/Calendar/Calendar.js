export default class Calendar {
    constructor(element) {
        this.element = element;

        const config = JSON.parse(element.getAttribute('cres-config') || '{}');
        this.config = config;

        this.init();
    }

    init() {
        const $ = window.jQuery;
        if (!$ || !$.fn.fullCalendar) {
            return;
        }

        const buttonText = Object.assign(
            { today: 'today', month: 'month', week: 'week', day: 'day' },
            this.config.buttonText || {}
        );

        if (this.config.locale) {
            // registers the locale (deriving month/weekday names from moment's locale data)
            // and its default button text, before it's requested by name below
            $.fullCalendar.lang(this.config.locale, { buttonText });
        }

        const options = {
            lang: this.config.locale || 'en',
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText,
            timeFormat: 'H:mm',
            eventLimit: true
        };

        if (this.config.ajax) {
            options.eventSources = [
                {
                    url: this.config.ajaxUrl,
                    type: 'POST'
                }
            ];
        } else {
            options.events = this.config.events || [];
        }

        $(this.element).fullCalendar(options);
    }
}
