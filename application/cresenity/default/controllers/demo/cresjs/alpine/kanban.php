<?php

class Controller_Demo_Cresjs_Alpine_Kanban extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Kanban');
        $data = [
            [
                'name' => 'Open',
                'tasks' => [
                    [
                        'code' => 'TASK1',
                        'category' => 'Development',
                        'subject' => 'Task Open',
                        'content' => 'Task Open for Demo',
                    ],
                    [
                        'code' => 'TASK2',
                        'category' => 'Development',
                        'subject' => 'Task Open No 2',
                        'content' => 'Task Open No 2for Demo',
                    ]
                ]
            ],
            [
                'name' => 'Read',
                'tasks' => [
                    [
                        'code' => 'TASK3',
                        'category' => 'Development',
                        'subject' => 'Task Read',
                        'content' => 'Task Read for Demo',
                    ],
                ]
            ],
            [
                'name' => 'Process',
                'tasks' => [
                    [
                        'code' => 'TASK4',
                        'category' => 'Development',
                        'subject' => 'Task Process',
                        'content' => 'Task Process for Demo',
                    ],
                ]
            ],
        ];
        c::manager()->registerModule('dragula');
        $app->addView('demo.page.cresjs.alpine.kanban', ['data' => $data]);

        return $app;
    }
}
