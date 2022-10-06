<?php

class Controller_Demo_Cresjs_Progressive extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('Progressive');
        $app->addH4()->add('From CApp');

        $app->addImage()
            ->setSrc(c::media('img/demo/sample/' . 'image-001.jpg'))
            ->setProgressiveThumbnail(c::media('img/demo/sample/thumb/' . 'image-001.jpg'));
        $app->addDiv()->addClass('py-5');
        $app->addImage()
            ->setSrc(c::media('img/demo/sample/' . 'image-002.jpg'))
            ->setProgressiveThumbnail(c::media('img/demo/sample/thumb/' . 'image-002.jpg'));
        $app->addDiv()->addClass('py-5');
        $app->addImage()
            ->setSrc(c::media('img/demo/sample/' . 'image-003.jpg'))
            ->setProgressiveThumbnail(c::media('img/demo/sample/thumb/' . 'image-003.jpg'));
        $app->addDiv()->addClass('py-5');
        $app->addImage()
            ->setSrc(c::media('img/demo/sample/' . 'image-004.jpg'))
            ->setProgressiveThumbnail(c::media('img/demo/sample/thumb/' . 'image-004.jpg'));
        $app->addDiv()->addClass('py-5');
        $app->addH4()->add('From View');
        $app->addView('demo.page.cresjs.progressive');

        return $app;
    }
}
