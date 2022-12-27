<?php

class Controller_Demo_Elements_Gallery extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Gallery');

        $widget = $app->addWidget()->setTitle('Single Image')->setIcon('ti ti-gallery');
        $gallery = $widget->addGallery();

        $image = c::media('img/demo/sample/' . 'image-001.jpg');
        $thumbnail = c::media('img/demo/sample/thumb/' . 'image-001.jpg');
        $gallery->addItem()->setSrc($image)->setThumbnail($thumbnail);

        $widget = $app->addWidget()->setTitle('Multiple Images')->setIcon('ti ti-gallery');
        $gallery = $widget->addGallery();

        for ($i = 1; $i <= 4; $i++) {
            $image = c::media('img/demo/sample/' . 'image-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.jpg');
            $thumbnail = c::media('img/demo/sample/thumb/' . 'image-' . str_pad($i, 3, '0', STR_PAD_LEFT) . '.jpg');
            $gallery->addItem()->setSrc($image)->setThumbnail($thumbnail);
        }

        return $app;
    }
}
