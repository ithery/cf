<?php

class Controller_Demo_Cresjs_React_Accordion extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $data = [
            [
                'title' => 'Section 1',
                'content' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quis sapiente laborum cupiditate possimus labore, hic temporibus velit dicta earum suscipit commodi eum enim atque at? Et perspiciatis dolore iure voluptatem.'
            ],
            [
                'title' => 'Section 2',
                'content' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quis sapiente laborum cupiditate possimus labore, hic temporibus velit dicta earum suscipit commodi eum enim atque at? Et perspiciatis dolore iure voluptatem.'
            ],
            [
                'title' => 'Section 3',
                'content' => 'Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quis sapiente laborum cupiditate possimus labore, hic temporibus velit dicta earum suscipit commodi eum enim atque at? Et perspiciatis dolore iure voluptatem.'
            ],
        ];
        c::manager()->registerJs('https://unpkg.com/react@17/umd/react.development.js');
        c::manager()->registerJs('https://unpkg.com/react-dom@17/umd/react-dom.development.js');
        $app->addView('demo.page.cresjs.react.accordion', ['data' => $data]);

        return $app;
    }
}
