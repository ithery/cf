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

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Nested Select (Depends On Direct Select)');
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
        $parentSelect1 = $div->addSelectControl()->setName('parent_select_1')->setList($selectOptions);
        $childDiv = $div->addDiv();
        $childSelect1 = $childDiv->addSelectControl()->setName('child_select_1')->setList([]);
        $childSelect1->setDependsOn($parentSelect1, function ($value) use ($selectChildOptions) {
            $options = carr::get($selectChildOptions, $value, []);
            $list = [];
            foreach ($options as $k => $v) {
                $list[] = [
                    'key' => $k,
                    'value' => $v,
                ];
            }

            return $list;
        }, ['block' => false]);

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Nested Select (Depends On With Div)');

        $parentSelect2 = $div->addSelectControl()->setName('parent_select_2')->setList($selectOptions);
        $childDiv = $div->addDiv();
        $childDiv->setDependsOn($parentSelect2, function ($value) use ($selectChildOptions) {
            $options = carr::get($selectChildOptions, $value, []);
            $div = c::div();
            $div->addSelectControl('child_select_2')->setName('child_select_2')->setList($options);

            return $div;
        }, ['block' => false]);

        $div = $app->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Nested Select (Depends On Multiple Element)');

        $childSelect3 = $div->addSelectControl()->setName('child_select_3')->setList([]);
        $childSelect3->setDependsOn([$parentSelect1, $parentSelect2], function ($values) {
            list($value1, $value2) = $values;

            return [
                [
                    'key' => $value1 . '_' . $value2,
                    'value' => 'Selected 1:' . $value1 . ', Selected 2:' . $value2
                ]
            ];
        }, ['block' => false]);

        return $app;
    }
}
