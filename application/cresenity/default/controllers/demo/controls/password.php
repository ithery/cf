<?php

class Controller_Demo_Controls_Password extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Password');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Normal Password Input');
        $div->addPasswordControl();

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Password Input With Placeholder');
        $div->addPasswordControl()->setPlaceholder('My Placeholder');

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Password Input With Show Password');
        $div->addPasswordControl()->setValue('secret-password')->setToggleVisibility(true);

        return $app;
    }
}
