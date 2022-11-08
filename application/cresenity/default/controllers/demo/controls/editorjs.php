<?php

class Controller_Demo_Controls_EditorJs extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();
        $app->title('Editor JS');
        $post = $_POST;
        $cacheKey = 'cresenity.demo.control.editorjs';
        $content = c::cache()->store()->get($cacheKey);
        if ($post != null) {
            $origPost = CBase::originalPostData();
            $content = trim(carr::get($origPost, 'content'));
            c::cache()->store()->forever($cacheKey, $content);
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
        $editorControl = $widget->addDiv()->addClass('app-editor-js-wrapper')->addEditorJsControl('content')->setValue($content);
        $editorControl->setInitialBlock(null);
        $editorControl->setPlaceholder('Let`s write an awesome story!');
        $form->addActionList()->addAction()->setLabel('Simpan')->setSubmit(true);

        $html = (string) c::manager()->editorJs()->generateHtmlOutput($content);

        $widget = $app->addWidget()->setTitle('Preview');
        $widget->add($html);

        return $app;
    }
}
