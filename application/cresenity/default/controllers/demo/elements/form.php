<?php

class Controller_Demo_Elements_Form extends \Cresenity\Demo\Controller {
    public function index() {
        $app = c::app();

        $name = '';
        $select = '';
        $email = '';
        $radio = 'radio1';
        $post = c::request()->post();
        $validationData = [
            'name' => ['required']
        ];

        if ($post) {
            c::msg('success', 'Form Submitted with data:<br/><pre>' . json_encode($post, JSON_PRETTY_PRINT) . '</pre>');
            $validator = c::validator($post, $validationData);
            if (!$validator->check()) {
                c::msg('error', $validator->errors()->first());
            }
            $name = carr::get($post, 'name');
            $select = carr::get($post, 'select');
            $email = carr::get($post, 'email');
            $radio = carr::get($post, 'field-radio');
        }

        $app->setTitle('Form');
        $widget = $app->addWidget()->setTitle('Form Demo');
        $form = $widget->addForm();
        $form->addField()->setLabel('Name')->addTextControl('name')->setPlaceholder('Your name')->setValue($name);
        $form->addField()->setLabel('Select')->addSelectControl('select')->setList(['apple' => 'Apple', 'orange' => 'Orange', 'grape' => 'Grape'])
            ->setValue($select);
        $form->addField()->setLabel('Email')->addEmailControl('email')->setPlaceholder('Input Email..')->setValue($email);
        $radioField = $form->addField()->setLabel('Radio');
        $radioField->addRadioControl()->setLabel('Radio 1')->setName('field-radio')->setValue('radio1')->setChecked($radio == 'radio1');
        $radioField->addRadioControl()->setLabel('Radio 2')->setName('field-radio')->setValue('radio2')->setChecked($radio == 'radio2');

        $form->addField()->setLabel('Label')->addControl(null, 'label')->setValue('Label Only');

        $form->addActionList()->addAction()->setSubmit()->setLabel('Submit');
        $form->setValidation($validationData);

        return $app;
    }
}
