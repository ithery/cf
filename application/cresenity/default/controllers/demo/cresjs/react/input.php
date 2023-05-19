<?php

class Controller_Demo_Cresjs_React_Input extends \Cresenity\Demo\Controller {
    /**
     * @return CApp
     */
    public function index() {
        $app = c::app();
        $counter = c::request()->counter ?: 0;
        $data = [
            'counter' => $counter
        ];
        c::manager()->registerJs('https://unpkg.com/react@17/umd/react.development.js');
        c::manager()->registerJs('https://unpkg.com/react-dom@17/umd/react-dom.development.js');
        $app->addView('demo.page.cresjs.react.input', ['data' => $data]);

        return $app;
    }
}
