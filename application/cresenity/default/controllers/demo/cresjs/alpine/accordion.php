<?php

class Controller_Demo_Cresjs_Alpine_Accordion extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Accordion');
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
        $app->addView('demo.page.cresjs.alpine.accordion', ['data' => $data]);

        return $app;
    }
}
