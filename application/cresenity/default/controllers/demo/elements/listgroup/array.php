<?php

class Controller_Demo_Elements_Listgroup_Array extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Array List Group');
        $array = [
            ['items_id' => 1, 'category_id' => 1, 'name' => 'Item 1'],
            ['items_id' => 2, 'category_id' => 1, 'name' => 'Item 2'],
            ['items_id' => 3, 'category_id' => 1, 'name' => 'Item 3'],
            ['items_id' => 4, 'category_id' => 2, 'name' => 'Item 4'],
            ['items_id' => 5, 'category_id' => 2, 'name' => 'Item 5'],
            ['items_id' => 6, 'category_id' => 2, 'name' => 'Item 6'],
        ];

        $listGroup = $app->addListGroup();
        $listGroup->setDataFromArray($array);
        $listGroup->setItemCallback(function ($item, $data) {
            $item->addDiv()->addClass('mb-3')->add('ID: ' . carr::get($data, 'items_id'));
            $item->addDiv()->addClass('mb-3')->add('Category ID: ' . carr::get($data, 'category_id'));
            $item->addDiv()->addClass('mb-3')->add('Name: ' . carr::get($data, 'name'));
        });

        return $app;
    }
}
