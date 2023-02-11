<?php

class Controller_Demo_Controls_Image extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $app->setTitle('Image');
        if (c::request()->method() == 'POST') {
            $app->addH4()->add('Submit Result');
            $app->addDiv()->add('Post Data');
            $app->addPre()->add(json_encode(c::request()->post()));
            $app->addDiv()->add('Files Data');
            $app->addPre()->add(json_encode($_FILES));

            //info
            if (isset(c::request()->post()['imageajax'])) {
                $info = CAjax::info()->getImageInfo(c::request()->post()['imageajax']);
                $app->addDiv()->add('Info Data');
                $app->addPre()->add(json_encode($info));
            }
            if (isset(c::request()->post()['imageajaxmultiple'])) {
                foreach (c::request()->post()['imageajaxmultiple'] as $fileId) {
                    $info = CAjax::info()->getImageInfo($fileId);
                    $app->addDiv()->add('Info Data ' . $fileId);
                    $app->addPre()->add(json_encode($info));
                }
            }
        }

        $form = $app->addForm();
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Normal Image Input');
        $image = $div->addImageControl('image');
        $form->addHiddenControl('submit-type')->setValue('normal-image');
        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        $form = $app->addForm()->setEncTypeMultiPartFormData();
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Ajax Image Input');
        $image = $div->addImageAjaxControl('imageajax')->setWithInfo();
        $form->addHiddenControl('submit-ajax')->setValue('ajax-image');
        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        $form = $app->addForm()->setEncTypeMultiPartFormData();
        $div = $form->addDiv()->addClass('border-1 p-3 mb-3');
        $div->addH5()->add('Multiple Ajax File Input');
        $image = $div->addMultipleImageAjaxControl('imageajaxmultiple')->setWithInfo();
        $form->addHiddenControl('submit-multiple-ajax')->setValue('multiple-ajax-image');
        $form->addActionList()->addAction()->setLabel('Submit')->setSubmit();

        return $app;
    }
}
