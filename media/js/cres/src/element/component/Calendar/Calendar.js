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

        const options = {
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            buttonText: {
                today: 'today',
                month: 'month',
                week: 'week',
                day: 'day'
            },
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
