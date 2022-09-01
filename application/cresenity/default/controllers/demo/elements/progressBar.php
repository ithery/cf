<?php

class Controller_Demo_Elements_ProgressBar extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Progress Bar');

        $widget = $app->addWidget()->setTitle('Progress Bar Demo');
        $progressBar = $widget->addProgressBar()->setValue(0);

        $progressBar->withProcess(function (CElement_Component_ProgressBar_ProgressHandler $progressHandler) {
            for ($i = 1; $i <= 75; $i++) {
                $progressHandler->setValue($i);
                $progressHandler->notify();
                usleep(50000);
            }
            for ($i = 75; $i >= 25; $i--) {
                $progressHandler->setValue($i);
                $progressHandler->notify();
                usleep(50000);
            }
            for ($i = 25; $i <= 100; $i++) {
                $progressHandler->setValue($i);
                $progressHandler->notify();
                usleep(50000);
            }
        });

        return $app;
    }
}
