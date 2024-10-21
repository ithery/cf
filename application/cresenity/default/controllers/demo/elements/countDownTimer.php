<?php

class Controller_Demo_Elements_CountDownTimer extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Count Down Timer');

        $widget = $app->addWidget()->setTitle('Count Down Timer Demo');
        //$widget->addCountDownTimer()->setExpiredDate(c::now()->addDays(10))->setDisplayFormat('%HH jam %mm menit %ss detik');
        $widget->addDiv()->add('Count Down in 10 Days');
        $widget->addCountDownTimer()->setExpiredDate(c::now()->addDays(10))->setDisplayFormat('%HH jam %mm menit %ss detik');
        $widget->addBr();
        $widget->addBr();
        $widget->addDiv()->add('Count Up from 10 Days Ago');
        $widget->addCountDownTimer()->setExpiredDate(c::now()->subDays(10))->setDisplayFormat('%HH jam %mm menit %ss detik')
            ->setCountUp();

        return $app;
    }
}
