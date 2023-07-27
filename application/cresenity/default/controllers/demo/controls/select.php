<?php

class Controller_Demo_Controls_Select extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $selectOptions = [
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three'
        ];

        $app->setTitle('Select');
        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Default Select');
        $div->addSelectControl()->setList($selectOptions);

        $div->addH5()->add('Nested Select');
        $selectParentOptions = [
            '1' => 'One',
            '2' => 'Two',
            '3' => 'Three'
        ];
        $selectChildOptions = [
            '1' => [
                'a' => 'A',
                'b' => 'B',
                'c' => 'C',
            ],
            '2' => [
                'd' => 'D',
                'e' => 'E',
            ],
            '3' => [
                'f' => 'F',
                'g' => 'G',
                'h' => 'H',
                'i' => 'I',
            ],
        ];
        $parentSelect = $div->addSelectControl()->setName('parent_select')->setList($selectOptions);
        $childDiv = $div->addDiv();
        $childDiv->setDependsOn($parentSelect, function ($value) use ($selectChildOptions) {
            $options = carr::get($selectChildOptions, $value, []);
            $div = c::div();
            $div->addSelectControl()->setName('child_select')->setList($options);

            return $div;
        }, ['block' => false]);

        return $app;
    }
}
