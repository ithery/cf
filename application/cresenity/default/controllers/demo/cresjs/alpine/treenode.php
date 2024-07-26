<?php

class Controller_Demo_Cresjs_Alpine_Treenode extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $app->title('Alpine Tree Node');

        $app->addView('demo.page.cresjs.alpine.treenode');

        return $app;
    }
}
