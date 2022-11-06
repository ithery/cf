<?php

class Controller_Demo_Elements_Image extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Image');
        $app->addH4()->add('Normal Image');
        $app->addImage()
            ->setSrc(c::media('img/demo/sample/' . 'image-001.jpg'));

        $app->addDiv()->addClass('py-5');
        $app->addH4()->add('Progressive Image');
        $app->addImage()
            ->setSrc(c::media('img/demo/sample/' . 'image-001.jpg'))
            ->setProgressiveThumbnail(c::media('img/demo/sample/thumb/' . 'image-001.jpg'));

        return $app;
    }
}
