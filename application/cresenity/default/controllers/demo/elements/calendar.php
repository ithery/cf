<?php

class Controller_Demo_Elements_Calendar extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Calendar');

        $widget = $app->addWidget()->setTitle('Calendar Demo');
        $widget->addDiv()->add('Contoh CElement_Calendar dengan data event tetap (tanpa ajax).');
        $widget->addBr();

        $today = date('Y-m-d');
        $widget->addCalendar()->setEvents([
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

        return $app;
    }
}
