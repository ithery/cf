<?php

class Controller_Demo_Controls_File extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('File');
        if (c::request()->method() == 'POST') {
            $app->addH4()->add('Submit Result');
            $app->addDiv()->add('Post Data');
            $app->addPre()->add(json_encode(c::request()->post()));
            $app->addDiv()->add('Files Data');
            $app->addPre()->add(json_encode($_FILES));

            //info
            if (isset(c::request()->post()['fileajax'])) {
                $info = CAjax::info()->getFileInfo(c::request()->post()['fileajax']);
                $app->addDiv()->add('Info Data');
                $app->addPre()->add(json_encode($info));
            }
            if (isset(c::request()->post()['fileajaxmultiple'])) {
                foreach (c::request()->post()['fileajaxmultiple'] as $fileId) {
                    $info = CAjax::info()->getFileInfo($fileId);
                    $app->addDiv()->add('Info Data ' . $fileId);
                    $app->addPre()->add(json_encode($info));
                }
            }
        }

        $form = $app->addForm()->setEncTypeMultiPartFormData();
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Normal File Input');
        $image = $div->addFileControl('file');
        $form->addHiddenControl('submit-normal')->setValue('normal-file');
        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        $form = $app->addForm()->setEncTypeMultiPartFormData();
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Ajax File Input');
        $image = $div->addFileAjaxControl('fileajax')->setWithInfo();
        $form->addHiddenControl('submit-ajax')->setValue('ajax-file');
        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        $form = $app->addForm()->setEncTypeMultiPartFormData();
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Multiple Ajax File Input');
        $image = $div->addMultipleFileAjaxControl('fileajaxmultiple')->setWithInfo();
        $form->addHiddenControl('submit-multiple-ajax')->setValue('multiple-ajax-file');
        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        return $app;
    }
}
