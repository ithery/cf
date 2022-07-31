<?php

class Controller_Demo_Elements_Repeater extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Repeater');

        $widget = $app->addWidget();
        $repeater = $widget->addRepeater();
        $repeater->setItemBuilder(function (CElement $element) {
            $element->addTextControl('name')->setName('name[]')->setPlaceholder('Name')->addClass('mb-3');

            $element->addTextControl('address')->setName('address[]')->setPlaceholder('Address');
        })->setMinItem(2);

        return $app;
    }
}
