<?php

use Cresenity\Demo\Model\NestedCategory;

class Controller_Demo_Elements_Nestable extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Nestable');

        $widget = $app->addWidget()->setTitle('Nestable Demo (from Model)');
        $widget->addDiv()->add('Drag & drop item di bawah untuk mengubah urutan/hierarki.');
        $widget->addBr();

        $nestable = $widget->addNestable();
        $nestable->setIdKey('nested_category_id');
        $nestable->setValueKey('name');
        $nestable->setDataFromModel(NestedCategory::class, function ($q) {
            $q->orderBy('lft');
        });

        return $app;
    }
}
