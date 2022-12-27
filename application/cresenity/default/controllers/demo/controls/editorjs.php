<?php

class Controller_Demo_Controls_Editorjs extends \Cresenity\Demo\Controller {
    const CACHE_KEY = 'cresenity.demo.control.editorjs';

    public function index() {
        $app = c::app();
        $app->title('Editor JS');
        $post = $_POST;
        $content = c::cache()->store()->get(static::CACHE_KEY);
        if ($post != null) {
            $origPost = CBase::originalPostData();
            $content = trim(carr::get($origPost, 'content'));
            c::cache()->store()->forever(static::CACHE_KEY, $content);
        }

        $form = $app->addForm()->setMethod('post');

        $app->add(<<<HTML
        <style>
        .app-editor-js-wrapper {
            border-radius: 0;
            box-shadow: 0 2px 8px 0 rgb(0 0 0 / 9%);
            max-width: 1280px;
            margin-left: auto;
            margin-right: auto;
            position: relative;
            background-color: #eee;
            border: 0;
            padding: 20px 20px;
        }
        .cres-editor-js {
            max-width: 1000px;
            background: #fff;
            margin-left: auto;
            margin-right: auto;
            padding-top: 100px;
            min-height: 400px;
        }
        </style>
        HTML);
        $form->setMethod('post');
        $widget = $form->addWidget()->addClass('mb-3');
        $widget->setIcon('ti ti-write')->setTitle('Content')->setNopadding();
        $widget->addHeaderAction()->addClass('btn-primary')->setLabel('Clear')->setLink($this->controllerUrl() . 'clear');
        $editorControl = $widget->addDiv()->addClass('app-editor-js-wrapper')->addEditorJsControl('content')->setValue($content);
        $editorControl->setInitialBlock(null);
        $editorControl->setPlaceholder('Let`s write an awesome story!');
        $form->addActionList()->addAction()->setLabel('Simpan')->setSubmit(true);

        $html = (string) c::manager()->editorJs()->generateHtmlOutput($content);

        $widget = $app->addWidget()->setTitle('Preview');
        $widget->add($html);

        return $app;
    }

    public function clear() {
        c::cache()->store()->clear(static::CACHE_KEY);

        return c::redirect()->back();
    }
}
