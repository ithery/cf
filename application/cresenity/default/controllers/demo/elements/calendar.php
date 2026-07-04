<?php

class Controller_Demo_Elements_Calendar extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Calendar');

        $widget = $app->addWidget()->setTitle('Calendar Demo');
        $widget->addDiv()->add('Contoh CElement_Component_Calendar dengan data event tetap (tanpa ajax).');
        $widget->addBr();

        $today = date('Y-m-d');
        $calendar = $widget->addCalendar();
        $events = $calendar->getEvents();
        $events->setData([
            [
                'title' => 'Kickoff Meeting',
                'start' => $today . ' 09:00:00',
                'end' => $today . ' 10:00:00',
                'backgroundColor' => '#0d6efd',
                'borderColor' => '#0d6efd',
            ],
            [
                'title' => 'Code Review',
                'start' => date('Y-m-d', strtotime('+1 day')) . ' 13:00:00',
                'end' => date('Y-m-d', strtotime('+1 day')) . ' 14:30:00',
                'backgroundColor' => '#20c997',
                'borderColor' => '#20c997',
            ],
            [
                'title' => 'Product Launch',
                'start' => date('Y-m-d', strtotime('+5 day')),
                'end' => date('Y-m-d', strtotime('+6 day')),
                'backgroundColor' => '#fd7e14',
                'borderColor' => '#fd7e14',
                'allDay' => true,
            ],
        ]);
        $events->addData([
            'title' => 'Team Building',
            'start' => date('Y-m-d', strtotime('+10 day')) . ' 09:00:00',
            'end' => date('Y-m-d', strtotime('+10 day')) . ' 17:00:00',
            'backgroundColor' => '#6f42c1',
            'borderColor' => '#6f42c1',
        ]);

        $widgetAjax = $app->addWidget()->setTitle('Calendar Demo (Ajax)');
        $widgetAjax->addDiv()->add('Contoh CElement_Component_Calendar yang mengambil event via ajax dari query SQL, bersumber dari model \Cresenity\Demo\Model\CalendarEvent.');
        $widgetAjax->addBr();

        $calendarAjax = $widgetAjax->addCalendar();
        $calendarAjax->setEvents(function ($startDate, $endDate, CElement_Component_Calendar_CalendarEvents $events) {
            $dbEvents = \Cresenity\Demo\Model\CalendarEvent::where('start', '>=', $startDate)
                ->where('start', '<=', $endDate)
                ->get();
            foreach ($dbEvents as $event) {
                $events->addData([
                    'title' => $event->title,
                    'start' => $event->start,
                    'end' => $event->end,
                    'backgroundColor' => $event->background_color,
                    'borderColor' => $event->border_color,
                    'allDay' => $event->all_day,
                ]);
            }
        });

        return $app;
    }
}
