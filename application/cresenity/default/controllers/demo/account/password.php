<?php

class Controller_Demo_Account_Password extends \Cresenity\Demo\Controller {
    public function change() {
        $app = c::app();

        $app->setTitle('Change Password');
        $widget = $app->addWidget()->setIcon('ti ti-key')->setTitle('Change Password');
        $form = $widget->addForm();

        return $app;
    }
}
