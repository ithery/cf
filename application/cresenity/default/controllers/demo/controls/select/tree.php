<?php

class Controller_Demo_Controls_Select_Tree extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $form = $app->addForm();
        $post = CApp_Base::getRequestPost();
        if ($post) {
            $app->addAlert()->setTypeSuccess()->addAlert()->add(json_encode($post, JSON_PRETTY_PRINT));
        }
        $app->setTitle('Select Tree');
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Simple Select Tree');

        $mydata = [
            [
                'id' => 1,
                'text' => 'USA',
                'inc' => [
                    [
                        'text' => 'west',
                        'inc' => [
                            [
                                'id' => 111,
                                'text' => 'California',
                                'inc' => [
                                    [
                                        'id' => 1111,
                                        'text' => 'Los Angeles',
                                        'inc' => [
                                            ['id' => 11111, 'text' => 'Hollywood']
                                        ]
                                    ],
                                    [
                                        'id' => 1112,
                                        'text' => 'San Diego',
                                        'selected' => 'true'
                                    ]
                                ]
                            ],
                            [
                                'id' => 112,
                                'text' => 'Oregon'
                            ]
                        ]
                    ]
                ]
            ],
            [
                'id' => 2,
                'text' => 'India'
            ],
            [
                'id' => 3,
                'text' => '中国'
            ]
        ];

        $selectSearch = $div->addSelectSearchControl('select_tree')
            ->setDataFromCollection(c::collect($mydata));
        $selectSearch->setKeyField('id');
        $selectSearch->setSearchField('text');


        return $app;
    }
}
