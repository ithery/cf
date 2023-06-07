<?php

class Controller_Demo_Elements_CountDownTimer extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Count Down Timer');

        $widget = $app->addWidget()->setTitle('Count Down Timer Demo');
        $widget->addCountDownTimer()->setExpiredDate(c::now()->addDays(10))->setDisplayFormat('%HH jam %mm menit %ss detik');

        return $app;
    }
}
